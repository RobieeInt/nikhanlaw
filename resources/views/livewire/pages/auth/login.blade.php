<?php

use App\Livewire\Forms\LoginForm;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        // ✅ jangan pakai navigate:true kalau Alpine Navigate plugin belum diinstall
        $this->redirectIntended(default: RouteServiceProvider::HOME);
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

            <form wire:submit="login" class="mt-6 space-y-4">
                {{-- Email --}}
                <div>
                    <label for="email" class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Email</label>
                    <input id="email" type="email" wire:model.defer="form.email" autocomplete="username" required
                        autofocus
                        class="mt-2 w-full rounded-2xl border border-zinc-300/70 bg-white px-4 py-3 text-sm text-zinc-900 outline-none
                               placeholder:text-zinc-400
                               focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                               dark:border-white/15 dark:bg-black/30 dark:text-zinc-100 dark:placeholder:text-zinc-500"
                        placeholder="nama@email.com" />
                    @error('form.email')
                        <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password"
                        class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Password</label>
                    <input id="password" type="password" wire:model.defer="form.password"
                        autocomplete="current-password" required
                        class="mt-2 w-full rounded-2xl border border-zinc-300/70 bg-white px-4 py-3 text-sm text-zinc-900 outline-none
                               placeholder:text-zinc-400
                               focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                               dark:border-white/15 dark:bg-black/30 dark:text-zinc-100 dark:placeholder:text-zinc-500"
                        placeholder="Password Anda" />
                    @error('form.password')
                        <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember + Forgot --}}
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-200/80">
                        <input type="checkbox" wire:model="form.remember"
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
                           text-white disabled:opacity-60
                           shadow-md hover:shadow-lg transition
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
