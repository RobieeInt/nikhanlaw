<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    // =========================
    // Login fields
    // =========================
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    // OTP state
    public bool $needsOtp = false;
    public string $otp = '';
    public ?int $pendingUserId = null;

    // =========================
    // Helpers
    // =========================
    private function generateOtp(): string
    {
        return (string) random_int(100000, 999999);
    }

    private function throttleMessage(User $user): ?string
    {
        // throttle resend 60 detik
        if ($user->phone_otp_sent_at && now()->diffInSeconds($user->phone_otp_sent_at) < 60) {
            return 'Terlalu cepat. Tunggu 60 detik untuk kirim OTP lagi.';
        }
        return null;
    }

    private function sendOtpEmail(User $user, string $code, bool $isResend = false): void
    {
        $subject = $isResend ? 'Kode OTP Aktivasi (Ulang)' : 'Kode OTP Aktivasi Akun';

        Mail::raw("Kode OTP kamu: {$code}\nBerlaku 5 menit. Jangan share ke siapa pun.", function ($m) use ($user, $subject) {
            $m->to($user->email)->subject($subject);
        });
    }

    private function loadPendingUser(): ?User
    {
        if (!$this->pendingUserId) {
            return null;
        }
        return User::query()->find($this->pendingUserId);
    }

    private function issueOtpFor(User $user, bool $isResend = false): void
    {
        if ($msg = $this->throttleMessage($user)) {
            $this->addError('otp', $msg);
            return;
        }

        $code = $this->generateOtp();

        $user
            ->forceFill([
                'phone_otp_hash' => Hash::make($code),
                'phone_otp_expires_at' => now()->addMinutes(5),
                'phone_otp_attempts' => 0,
                'phone_otp_sent_at' => now(),
            ])
            ->save();

        $this->sendOtpEmail($user, $code, $isResend);

        $this->needsOtp = true;
        $this->pendingUserId = $user->id;
        $this->reset('otp');
        $this->resetErrorBag('otp');
    }

    // =========================
    // Actions
    // =========================
    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // attempt login dulu
        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        // kalau aktif, lanjut normal
        if ($user->is_active) {
            Session::regenerate();
            $this->redirectIntended(default: RouteServiceProvider::HOME);
            return;
        }

        // kalau belum aktif: logout dulu, lalu mulai OTP flow
        Auth::logout();

        $this->needsOtp = true;
        $this->pendingUserId = $user->id;

        // kirim OTP otomatis saat ketahuan belum aktif (biar user gak klik2 dulu)
        $this->issueOtpFor($user, false);

        // kasih feedback
        $this->addError('email', 'Akun belum aktif. Kami kirim OTP ke email kamu. Verifikasi dulu ya.');
    }

    public function resendOtp(): void
    {
        $user = $this->loadPendingUser();

        if (!$user) {
            $this->addError('otp', 'User tidak ditemukan. Coba login ulang.');
            return;
        }

        if ($user->is_active) {
            $this->needsOtp = false;
            $this->pendingUserId = null;
            $this->reset('otp');
            return;
        }

        $this->issueOtpFor($user, true);
    }

    public function verifyOtp(): void
    {
        $this->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $user = $this->loadPendingUser();

        if (!$user) {
            $this->addError('otp', 'User tidak ditemukan. Coba login ulang.');
            return;
        }

        if ($user->is_active) {
            $this->needsOtp = false;
            return;
        }

        if (!$user->phone_otp_expires_at || now()->greaterThan($user->phone_otp_expires_at)) {
            $this->addError('otp', 'OTP kedaluwarsa. Kirim ulang OTP.');
            return;
        }

        if (($user->phone_otp_attempts ?? 0) >= 5) {
            $this->addError('otp', 'Terlalu banyak percobaan. Kirim ulang OTP.');
            return;
        }

        $ok = $user->phone_otp_hash && Hash::check($this->otp, $user->phone_otp_hash);

        $user->increment('phone_otp_attempts');

        if (!$ok) {
            $this->addError('otp', 'OTP salah.');
            return;
        }

        // ✅ aktifkan akun
        $user
            ->forceFill([
                'phone_verified_at' => now(),
                'is_active' => true,
                'phone_otp_hash' => null,
                'phone_otp_expires_at' => null,
                'phone_otp_attempts' => 0,
                'phone_otp_sent_at' => null,
            ])
            ->save();

        // ✅ login setelah aktif
        Auth::login($user, $this->remember);
        Session::regenerate();

        $this->needsOtp = false;
        $this->pendingUserId = null;

        $this->redirectIntended(default: RouteServiceProvider::HOME);
    }

    public function cancelOtp(): void
    {
        $this->needsOtp = false;
        $this->pendingUserId = null;
        $this->reset('otp');
        $this->resetErrorBag();
    }
};
?>

<div class="grid items-center gap-10 lg:grid-cols-2">
    {{-- Left branding --}}
    <div class="hidden lg:block">
        <h1 class="text-4xl font-semibold leading-tight tracking-tight text-zinc-900 dark:text-white">
            Masuk ke akun Anda.
        </h1>

        <p class="mt-4 max-w-xl text-sm leading-relaxed text-zinc-700 dark:text-zinc-200/80">
            Pantau status case, komunikasi dengan lawyer, dan kelola dokumen hukum Anda
            secara aman dan terstruktur.
        </p>

        <div class="mt-8 flex flex-wrap gap-2 text-xs text-zinc-700 dark:text-zinc-200/80">
            <span
                class="rounded-full border border-zinc-300/70 bg-white/70 px-3 py-1 text-zinc-800
                         dark:border-white/15 dark:bg-white/5 dark:text-zinc-100">
                Akses case real-time
            </span>
            <span
                class="rounded-full border border-zinc-300/70 bg-white/70 px-3 py-1 text-zinc-800
                         dark:border-white/15 dark:bg-white/5 dark:text-zinc-100">
                Dokumen aman
            </span>
            <span
                class="rounded-full border border-zinc-300/70 bg-white/70 px-3 py-1 text-zinc-800
                         dark:border-white/15 dark:bg-white/5 dark:text-zinc-100">
                Riwayat konsultasi
            </span>
        </div>
    </div>

    {{-- Login card --}}
    <div class="mx-auto w-full max-w-md">
        <div
            class="rounded-[32px] border border-zinc-300/70 bg-white/80 p-8 shadow-sm backdrop-blur
                    dark:border-white/15 dark:bg-white/5">

            <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">Masuk</h2>
            <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-200/80">
                Gunakan email dan password yang terdaftar.
            </p>

            <x-auth-session-status class="mt-4" :status="session('status')" />

            {{-- FORM LOGIN --}}
            <form wire:submit="login" class="mt-6 space-y-4"
                @if ($needsOtp) style="display:none" @endif>
                {{-- Email --}}
                <div>
                    <label for="email" class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Email</label>
                    <input id="email" type="email" wire:model.defer="email" autocomplete="username" required
                        autofocus
                        class="mt-2 w-full rounded-2xl border border-zinc-300/70 bg-white px-4 py-3 text-sm text-zinc-900 outline-none
                               placeholder:text-zinc-400
                               focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                               dark:border-white/15 dark:bg-black/30 dark:text-zinc-100 dark:placeholder:text-zinc-500"
                        placeholder="nama@email.com" />
                    @error('email')
                        <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password"
                        class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Password</label>
                    <input id="password" type="password" wire:model.defer="password" autocomplete="current-password"
                        required
                        class="mt-2 w-full rounded-2xl border border-zinc-300/70 bg-white px-4 py-3 text-sm text-zinc-900 outline-none
                               placeholder:text-zinc-400
                               focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                               dark:border-white/15 dark:bg-black/30 dark:text-zinc-100 dark:placeholder:text-zinc-500"
                        placeholder="Password Anda" />
                    @error('password')
                        <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember + Forgot --}}
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-200/80">
                        <input type="checkbox" wire:model="remember"
                            class="rounded border-zinc-300 text-[color:var(--brand-red)]
                                   focus:ring-[color:var(--brand-gold)]/40
                                   dark:border-white/20 dark:bg-black/40" />
                        Remember me
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm font-semibold text-[color:var(--brand-red)] hover:opacity-90 dark:text-[color:var(--brand-gold)]">
                            Lupa password?
                        </a>
                    @endif
                </div>

                {{-- Login button --}}
                <button type="submit" wire:loading.attr="disabled" wire:target="login"
                    class="group relative w-full rounded-2xl px-6 py-3 text-sm font-semibold
                           text-white disabled:opacity-60 shadow-md hover:shadow-lg transition
                           focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-gold)]/50
                           ring-1 ring-black/10 dark:ring-white/10"
                    style="background: linear-gradient(135deg, #8E0C12, #C39A3A);">
                    <span
                        class="pointer-events-none absolute inset-0 rounded-2xl bg-white/10 opacity-0 group-hover:opacity-100 transition"></span>

                    <span class="relative z-10" wire:loading.remove wire:target="login"
                        style="text-shadow: 0 1px 2px rgba(0,0,0,.35);">
                        Masuk
                    </span>
                    <span class="relative z-10" wire:loading wire:target="login"
                        style="text-shadow: 0 1px 2px rgba(0,0,0,.35);">
                        Memproses...
                    </span>
                </button>
            </form>

            {{-- OTP PANEL (muncul kalau user belum aktif) --}}
            @if ($needsOtp)
                <div class="mt-6 space-y-4">
                    <div
                        class="rounded-2xl border border-zinc-300/60 bg-white/60 p-4 dark:border-white/15 dark:bg-white/5">
                        <div class="text-xs text-zinc-700 dark:text-zinc-200/80">
                            Akun kamu belum aktif. OTP sudah dikirim ke email <span
                                class="font-semibold">{{ $email }}</span>
                            (berlaku 5 menit). Cek SPAM kalau ga ketemu.
                        </div>

                        <div class="mt-3">
                            <label for="otp" class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Kode
                                OTP</label>
                            <input id="otp" type="text" inputmode="numeric" maxlength="6"
                                wire:model.defer="otp"
                                class="mt-2 w-full rounded-2xl border border-zinc-300/70 bg-white px-4 py-3 text-sm text-zinc-900 outline-none
                                       placeholder:text-zinc-400
                                       focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                                       dark:border-white/15 dark:bg-black/30 dark:text-zinc-100 dark:placeholder:text-zinc-500"
                                placeholder="6 digit" />
                            @error('otp')
                                <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-3">
                            <button type="button" wire:click="verifyOtp" wire:loading.attr="disabled"
                                wire:target="verifyOtp"
                                class="w-full rounded-2xl px-4 py-3 text-sm font-semibold text-white disabled:opacity-60
                                       ring-1 ring-black/10 dark:ring-white/10"
                                style="background: linear-gradient(135deg, #1f2937, #111827);">
                                <span wire:loading.remove wire:target="verifyOtp">Verifikasi</span>
                                <span wire:loading wire:target="verifyOtp">Memeriksa...</span>
                            </button>

                            <button type="button" wire:click="resendOtp" wire:loading.attr="disabled"
                                wire:target="resendOtp"
                                class="w-full rounded-2xl px-4 py-3 text-sm font-semibold
                                       border border-zinc-300/70 bg-white/70 text-zinc-900 hover:bg-white transition disabled:opacity-60
                                       dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
                                <span wire:loading.remove wire:target="resendOtp">Kirim Ulang</span>
                                <span wire:loading wire:target="resendOtp">Mengirim...</span>
                            </button>
                        </div>
                    </div>

                    <button type="button" wire:click="cancelOtp"
                        class="w-full rounded-2xl px-6 py-3 text-sm font-semibold
                               border border-zinc-300/70 bg-white/70 text-zinc-900 hover:bg-white transition
                               dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
                        Kembali ke Login
                    </button>
                </div>
            @endif

            <div class="mt-6 flex items-center justify-between gap-3">
                <div class="text-sm text-zinc-700 dark:text-zinc-200/80">
                    Belum punya akun?
                </div>

                <a href="{{ route('register') }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-zinc-300/70 bg-white/70 px-4 py-2
                           text-sm font-semibold text-zinc-900 hover:bg-white transition
                           dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
                    Daftar
                </a>
            </div>
        </div>
    </div>
</div>
