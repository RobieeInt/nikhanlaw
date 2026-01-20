<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            \DB::beginTransaction();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // ✅ default role: client
            $user->assignRole('client');

            // ⚠️ Sering bikin request ngadat kalau mail/listener belum ready
            // event(new \Illuminate\Auth\Events\Registered($user));

            Auth::login($user);

            \DB::commit();

            // $this->redirectRoute('client.dashboard', navigate: true);
            $this->redirectRoute('client.dashboard');
        } catch (\Throwable $e) {
            \DB::rollBack();

            report($e);

            // Biar user dapat feedback & Livewire gak stuck loading
            $this->addError('email', 'Registrasi gagal diproses. Coba lagi beberapa saat.');
        }
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

            <form wire:submit="register" class="mt-6 space-y-4">
                <div>
                    <label for="name" class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Nama</label>
                    <input id="name" type="text" wire:model.defer="name" autocomplete="name"
                        class="mt-2 w-full rounded-2xl border border-zinc-300/70 bg-white px-4 py-3 text-sm text-zinc-900 outline-none
                               placeholder:text-zinc-400
                               focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                               dark:border-white/15 dark:bg-black/30 dark:text-zinc-100 dark:placeholder:text-zinc-500"
                        placeholder="Nama lengkap" />
                    @error('name')
                        <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="email" class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Email</label>
                    <input id="email" type="email" wire:model.defer="email" autocomplete="username"
                        class="mt-2 w-full rounded-2xl border border-zinc-300/70 bg-white px-4 py-3 text-sm text-zinc-900 outline-none
                               placeholder:text-zinc-400
                               focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                               dark:border-white/15 dark:bg-black/30 dark:text-zinc-100 dark:placeholder:text-zinc-500"
                        placeholder="nama@email.com" />
                    @error('email')
                        <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="password"
                        class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Password</label>
                    <input id="password" type="password" wire:model.defer="password" autocomplete="new-password"
                        class="mt-2 w-full rounded-2xl border border-zinc-300/70 bg-white px-4 py-3 text-sm text-zinc-900 outline-none
                               placeholder:text-zinc-400
                               focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                               dark:border-white/15 dark:bg-black/30 dark:text-zinc-100 dark:placeholder:text-zinc-500"
                        placeholder="Minimal 8 karakter" />
                    @error('password')
                        <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                    @enderror
                </div>

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
                        placeholder="Ulangi password" />
                </div>

                <button type="submit" wire:loading.attr="disabled" wire:target="register"
                    class="group relative w-full rounded-2xl px-6 py-3 text-sm font-semibold
                           text-white disabled:opacity-60
                           shadow-md hover:shadow-lg transition
                           focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-gold)]/50
                           ring-1 ring-black/10 dark:ring-white/10"
                    style="background: linear-gradient(135deg, #8E0C12, #C39A3A);">
                    <span
                        class="pointer-events-none absolute inset-0 rounded-2xl bg-white/10 opacity-0 group-hover:opacity-100 transition"></span>

                    <span class="relative z-10" wire:loading.remove wire:target="register"
                        style="text-shadow: 0 1px 2px rgba(0,0,0,.35);">
                        Buat Akun
                    </span>

                    <span class="relative z-10" wire:loading wire:target="register"
                        style="text-shadow: 0 1px 2px rgba(0,0,0,.35);">
                        Memproses...
                    </span>
                </button>
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
