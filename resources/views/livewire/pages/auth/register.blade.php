<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    // =========================
    // Form fields
    // =========================
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';

    // OTP fields
    public string $otp = '';
    public bool $otpSent = false;
    public bool $otpVerified = false;

    // Pending user ID
    public ?int $pendingUserId = null;

    // =========================
    // Helpers
    // =========================
    private function normalizePhone(string $phone): string
    {
        $p = trim($phone);
        $p = preg_replace('/\s+|-|\(|\)/', '', $p);

        // 08xxxx -> +628xxxx
        if (str_starts_with($p, '08')) {
            $p = '62' . substr($p, 1);
        }
        // 62xxxx -> +62xxxx
        if (str_starts_with($p, '62')) {
            $p = '' . $p;
        }

        return $p;
    }

    private function generateOtp(): string
    {
        return (string) random_int(100000, 999999);
    }

    private function loadPendingUser(): ?User
    {
        if (!$this->pendingUserId) {
            return null;
        }
        return User::query()->find($this->pendingUserId);
    }

    private function throttleMessage(User $user): ?string
    {
        // resend throttle 60 detik
        if ($user->phone_otp_sent_at && now()->diffInSeconds($user->phone_otp_sent_at) < 60) {
            return 'Terlalu cepat. Tunggu 60 detik untuk kirim OTP lagi.';
        }
        return null;
    }

    private function sendOtpEmail(User $user, string $code, bool $isResend = false): void
    {
        $subject = $isResend ? 'Kode OTP Registrasi (Ulang)' : 'Kode OTP Registrasi';

        Mail::raw("Kode OTP kamu: {$code}\nBerlaku 5 menit. Jangan share ke siapa pun.", function ($m) use ($user, $subject) {
            $m->to($user->email)->subject($subject);
        });
    }

    // =========================
    // Actions
    // =========================

    /**
     * Step 1: Create pending user + send OTP
     */
    public function sendOtp(): void
    {
        $this->phone = $this->normalizePhone($this->phone);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:32', 'unique:users,phone'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            DB::beginTransaction();

            // ✅ create pending user (belum login) + simpan phone ke DB + inactive dulu
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'], // ✅ nomor telpon masuk DB
                'phone_verified_at' => null,
                'is_active' => false, // ✅ pending sampai OTP verified
                'password' => Hash::make($validated['password']),
            ]);

            // default role: client
            $user->assignRole('client');

            if ($msg = $this->throttleMessage($user)) {
                DB::rollBack();
                $this->addError('phone', $msg);
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

            // kirim OTP (gratisan) via email
            $this->sendOtpEmail($user, $code, false);

            DB::commit();

            $this->pendingUserId = $user->id;
            $this->otpSent = true;
            $this->otpVerified = false;
            $this->reset('otp');
            $this->resetErrorBag('otp');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            $this->addError('email', 'Gagal kirim OTP. Coba lagi beberapa saat.');
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp(): void
    {
        $user = $this->loadPendingUser();

        if (!$user) {
            $this->addError('otp', 'Registrasi pending tidak ditemukan. Coba daftar ulang.');
            return;
        }

        if ($user->phone_verified_at && $user->is_active) {
            $this->otpVerified = true;
            return;
        }

        if ($msg = $this->throttleMessage($user)) {
            $this->addError('otp', $msg);
            return;
        }

        try {
            $code = $this->generateOtp();

            $user
                ->forceFill([
                    'phone_otp_hash' => Hash::make($code),
                    'phone_otp_expires_at' => now()->addMinutes(5),
                    'phone_otp_attempts' => 0,
                    'phone_otp_sent_at' => now(),
                ])
                ->save();

            $this->sendOtpEmail($user, $code, true);

            $this->resetErrorBag('otp');
        } catch (\Throwable $e) {
            report($e);
            $this->addError('otp', 'Gagal kirim ulang OTP.');
        }
    }

    /**
     * Step 2: Verify OTP
     */
    public function verifyOtp(): void
    {
        $this->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $user = $this->loadPendingUser();

        if (!$user) {
            $this->addError('otp', 'Registrasi pending tidak ditemukan. Coba daftar ulang.');
            return;
        }

        if ($user->phone_verified_at && $user->is_active) {
            $this->otpVerified = true;
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

        // ✅ verified -> aktifkan user
        $user
            ->forceFill([
                'phone_verified_at' => now(),
                'is_active' => true, // ✅ boleh login setelah ini
                'phone_otp_hash' => null,
                'phone_otp_expires_at' => null,
                'phone_otp_attempts' => 0,
                'phone_otp_sent_at' => null,
            ])
            ->save();

        $this->otpVerified = true;
        $this->resetErrorBag('otp');
    }

    /**
     * Step 3: Login and redirect
     */
    public function finish(): void
    {
        $user = $this->loadPendingUser();

        if (!$user) {
            $this->addError('otp', 'Registrasi pending tidak ditemukan.');
            return;
        }

        if (!$user->phone_verified_at || !$user->is_active) {
            $this->addError('otp', 'Akun belum aktif. Verifikasi OTP dulu.');
            return;
        }

        Auth::login($user);
        $this->redirectRoute('client.dashboard');
    }

    /**
     * Optional: cancel pending registration and cleanup
     */
    public function cancelPending(): void
    {
        if ($this->pendingUserId) {
            User::query()->where('id', $this->pendingUserId)->whereNull('phone_verified_at')->where('is_active', false)->delete();
        }

        $this->reset(['name', 'email', 'phone', 'password', 'password_confirmation', 'otp', 'otpSent', 'otpVerified', 'pendingUserId']);

        $this->resetErrorBag();
    }
};
?>

<div class="grid items-center gap-10 lg:grid-cols-2">
    {{-- Left branding --}}
    <div class="hidden lg:block">
        <h1 class="text-4xl font-semibold leading-tight tracking-tight text-zinc-900 dark:text-white">
            Daftar sebagai client, ajukan konsultasi dengan rapi.
        </h1>

        <p class="mt-4 max-w-xl text-sm leading-relaxed text-zinc-700 dark:text-zinc-200/80">
            Setelah akun dibuat, Anda bisa membuat case, upload dokumen pendukung, dan memantau milestone penanganan
            secara transparan.
        </p>

        <div class="mt-8 flex flex-wrap gap-2 text-xs text-zinc-700 dark:text-zinc-200/80">
            <span
                class="rounded-full border border-zinc-300/70 bg-white/70 px-3 py-1 text-zinc-800
                         dark:border-white/15 dark:bg-white/5 dark:text-zinc-100">
                Timeline transparan
            </span>
            <span
                class="rounded-full border border-zinc-300/70 bg-white/70 px-3 py-1 text-zinc-800
                         dark:border-white/15 dark:bg-white/5 dark:text-zinc-100">
                Dokumen terpusat
            </span>
            <span
                class="rounded-full border border-zinc-300/70 bg-white/70 px-3 py-1 text-zinc-800
                         dark:border-white/15 dark:bg-white/5 dark:text-zinc-100">
                Kerahasiaan terjaga
            </span>
        </div>
    </div>

    {{-- Register card --}}
    <div class="mx-auto w-full max-w-md">
        <div
            class="rounded-[32px] border border-zinc-300/70 bg-white/80 p-8 shadow-sm backdrop-blur
                    dark:border-white/15 dark:bg-white/5">
            <div>
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">Daftar (Client)</h2>
                <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-200/80">
                    Akun ini otomatis role:
                    <span class="font-semibold" style="color: var(--brand-gold);">client</span>.
                </p>
            </div>

            <form class="mt-6 space-y-4">
                {{-- Nama --}}
                <div>
                    <label for="name" class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Nama</label>
                    <input id="name" type="text" wire:model.defer="name" autocomplete="name"
                        class="mt-2 w-full rounded-2xl border border-zinc-300/70 bg-white px-4 py-3 text-sm text-zinc-900 outline-none
                               placeholder:text-zinc-400
                               focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                               dark:border-white/15 dark:bg-black/30 dark:text-zinc-100 dark:placeholder:text-zinc-500"
                        placeholder="Nama lengkap" @if ($otpSent) disabled @endif />
                    @error('name')
                        <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Email</label>
                    <input id="email" type="email" wire:model.defer="email" autocomplete="username"
                        class="mt-2 w-full rounded-2xl border border-zinc-300/70 bg-white px-4 py-3 text-sm text-zinc-900 outline-none
                               placeholder:text-zinc-400
                               focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                               dark:border-white/15 dark:bg-black/30 dark:text-zinc-100 dark:placeholder:text-zinc-500"
                        placeholder="nama@email.com" @if ($otpSent) disabled @endif />
                    @error('email')
                        <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">No.
                        WhatsApp</label>
                    <input id="phone" type="text" wire:model.defer="phone" autocomplete="tel"
                        class="mt-2 w-full rounded-2xl border border-zinc-300/70 bg-white px-4 py-3 text-sm text-zinc-900 outline-none
                               placeholder:text-zinc-400
                               focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                               dark:border-white/15 dark:bg-black/30 dark:text-zinc-100 dark:placeholder:text-zinc-500"
                        placeholder="Contoh: 0812xxxxxxx" @if ($otpSent) disabled @endif />
                    @error('phone')
                        <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password"
                        class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Password</label>
                    <input id="password" type="password" wire:model.defer="password" autocomplete="new-password"
                        class="mt-2 w-full rounded-2xl border border-zinc-300/70 bg-white px-4 py-3 text-sm text-zinc-900 outline-none
                               placeholder:text-zinc-400
                               focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                               dark:border-white/15 dark:bg-black/30 dark:text-zinc-100 dark:placeholder:text-zinc-500"
                        placeholder="Minimal 8 karakter" @if ($otpSent) disabled @endif />
                    @error('password')
                        <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Confirm --}}
                <div>
                    <label for="password_confirmation" class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                        Konfirmasi Password
                    </label>
                    <input id="password_confirmation" type="password" wire:model.defer="password_confirmation"
                        autocomplete="new-password"
                        class="mt-2 w-full rounded-2xl border border-zinc-300/70 bg-white px-4 py-3 text-sm text-zinc-900 outline-none
                               placeholder:text-zinc-400
                               focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                               dark:border-white/15 dark:bg-black/30 dark:text-zinc-100 dark:placeholder:text-zinc-500"
                        placeholder="Ulangi password" @if ($otpSent) disabled @endif />
                </div>

                {{-- ACTIONS --}}
                @if (!$otpSent)
                    <button type="button" wire:click="sendOtp" wire:loading.attr="disabled" wire:target="sendOtp"
                        class="group relative w-full rounded-2xl px-6 py-3 text-sm font-semibold
                               text-white disabled:opacity-60
                               shadow-md hover:shadow-lg transition
                               focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-gold)]/50
                               ring-1 ring-black/10 dark:ring-white/10"
                        style="background: linear-gradient(135deg, #8E0C12, #C39A3A);">
                        <span
                            class="pointer-events-none absolute inset-0 rounded-2xl bg-white/10 opacity-0 group-hover:opacity-100 transition"></span>
                        <span class="relative z-10" wire:loading.remove wire:target="sendOtp"
                            style="text-shadow: 0 1px 2px rgba(0,0,0,.35);">
                            Kirim OTP
                        </span>
                        <span class="relative z-10" wire:loading wire:target="sendOtp"
                            style="text-shadow: 0 1px 2px rgba(0,0,0,.35);">
                            Mengirim...
                        </span>
                    </button>
                @else
                    <div
                        class="rounded-2xl border border-zinc-300/60 bg-white/60 p-4 dark:border-white/15 dark:bg-white/5">
                        <div class="text-xs text-zinc-700 dark:text-zinc-200/80">
                            OTP dikirim ke email <span class="font-semibold">{{ $email }}</span> (berlaku 5
                            menit). Cek SPAM kalau ga ketemu.
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

                        @if ($otpVerified)
                            <div class="mt-3 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                                OTP terverifikasi ✅
                            </div>
                        @endif
                    </div>

                    <button type="button" wire:click="finish" wire:loading.attr="disabled" wire:target="finish"
                        class="group relative w-full rounded-2xl px-6 py-3 text-sm font-semibold
                               text-white disabled:opacity-60
                               shadow-md hover:shadow-lg transition
                               focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-gold)]/50
                               ring-1 ring-black/10 dark:ring-white/10
                               {{ !$otpVerified ? 'opacity-60 cursor-not-allowed' : '' }}"
                        style="background: linear-gradient(135deg, #8E0C12, #C39A3A);"
                        @if (!$otpVerified) disabled @endif>
                        <span
                            class="pointer-events-none absolute inset-0 rounded-2xl bg-white/10 opacity-0 group-hover:opacity-100 transition"></span>
                        <span class="relative z-10" wire:loading.remove wire:target="finish"
                            style="text-shadow: 0 1px 2px rgba(0,0,0,.35);">
                            Selesai & Masuk
                        </span>
                        <span class="relative z-10" wire:loading wire:target="finish"
                            style="text-shadow: 0 1px 2px rgba(0,0,0,.35);">
                            Memproses...
                        </span>
                    </button>

                    <button type="button" wire:click="cancelPending"
                        class="w-full rounded-2xl px-6 py-3 text-sm font-semibold
                               border border-zinc-300/70 bg-white/70 text-zinc-900 hover:bg-white transition
                               dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
                        Batal
                    </button>
                @endif
            </form>

            <div class="mt-6 flex items-center justify-between gap-3">
                <div class="text-sm text-zinc-700 dark:text-zinc-200/80">
                    Sudah punya akun?
                </div>

                <a href="{{ route('login') }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-zinc-300/70 bg-white/70 px-4 py-2 text-sm font-semibold text-zinc-900
                           hover:bg-white transition
                           dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
                    Masuk
                </a>
            </div>
        </div>

        <div class="mt-4 text-center text-xs text-zinc-600 dark:text-zinc-400">
            Disclaimer: pendaftaran tidak menggantikan konsultasi hukum formal.
        </div>
    </div>
</div>
