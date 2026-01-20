@props([
    'variant' => 'landing', // landing | app
])

<header
    class="sticky top-0 z-50 border-b border-zinc-200/70 bg-white/70 backdrop-blur
              dark:border-white/10 dark:bg-black/40">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6">

        {{-- Brand --}}
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ asset('images/nikhan-logo.jpeg') }}" class="h-10 w-auto" alt="Nikhan & Associates" />
            <div class="leading-tight">
                <div class="text-sm font-semibold tracking-wide text-zinc-900 dark:text-white">
                    Nikhan & Associates
                </div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                    Law Office
                </div>
            </div>
        </a>

        @php
            $user = auth()->user();
            $isAuth = auth()->check();
            $isAdmin = $isAuth && $user->hasRole('admin');
            $isLawyer = $isAuth && $user->hasRole('lawyer');
            $isClient = $isAuth && !$isAdmin && !$isLawyer;

            $dashboardRoute = $isAdmin
                ? route('admin.dashboard')
                : ($isLawyer
                    ? route('lawyer.dashboard')
                    : ($isAuth
                        ? route('client.dashboard')
                        : route('home')));

            $roleLabel = $isAdmin ? 'Admin' : ($isLawyer ? 'Lawyer' : ($isAuth ? 'Client' : ''));
        @endphp

        {{-- DESKTOP NAV --}}
        <nav class="hidden md:flex items-center gap-6 text-sm">
            @if ($variant === 'landing')
                <a href="#services"
                    class="text-zinc-700 hover:text-[color:var(--brand-red)] dark:text-zinc-300">Layanan</a>
                <a href="#process"
                    class="text-zinc-700 hover:text-[color:var(--brand-red)] dark:text-zinc-300">Proses</a>
                <a href="#testimonials"
                    class="text-zinc-700 hover:text-[color:var(--brand-red)] dark:text-zinc-300">Testimoni</a>
                <a href="#faq" class="text-zinc-700 hover:text-[color:var(--brand-red)] dark:text-zinc-300">FAQ</a>
            @else
                @auth
                    <a href="{{ $dashboardRoute }}"
                        class="text-zinc-700 hover:text-[color:var(--brand-red)] dark:text-zinc-300">
                        Dashboard
                    </a>

                    @if ($isAdmin)
                        <a href="{{ route('admin.cases.index') }}"
                            class="text-zinc-700 hover:text-[color:var(--brand-red)] dark:text-zinc-300">Cases</a>
                        <a href="{{ route('admin.cases.approvals') }}"
                            class="text-zinc-700 hover:text-[color:var(--brand-red)] dark:text-zinc-300">Approvals</a>
                        <a href="{{ route('admin.users.index') }}"
                            class="text-zinc-700 hover:text-[color:var(--brand-red)] dark:text-zinc-300">Users</a>
                    @elseif($isLawyer)
                        <a href="{{ route('lawyer.cases.pool') }}"
                            class="text-zinc-700 hover:text-[color:var(--brand-red)] dark:text-zinc-300">Case Pool</a>
                        <a href="{{ route('lawyer.cases.index') }}"
                            class="text-zinc-700 hover:text-[color:var(--brand-red)] dark:text-zinc-300">My Cases</a>
                    @elseif($isClient)
                        <a href="{{ route('client.cases.index') }}"
                            class="text-zinc-700 hover:text-[color:var(--brand-red)] dark:text-zinc-300">My Cases</a>
                        <a href="{{ route('client.cases.create') }}"
                            class="text-zinc-700 hover:text-[color:var(--brand-red)] dark:text-zinc-300">Buat Case</a>
                    @endif
                @endauth
            @endif
        </nav>

        {{-- RIGHT --}}
        <div class="flex items-center gap-2">
            {{-- Theme --}}
            <button type="button" onclick="window.Theme && window.Theme.toggle && window.Theme.toggle()"
                class="inline-flex h-10 items-center gap-2 rounded-2xl border border-zinc-200 bg-white px-3
                       text-sm font-semibold text-zinc-800 hover:bg-zinc-50
                       dark:border-white/10 dark:bg-black/30 dark:text-zinc-100 dark:hover:bg-white/10">
                <span
                    class="inline-flex h-6 w-6 items-center justify-center rounded-xl
                             bg-[color:var(--brand-gold)]/15 text-[color:var(--brand-gold)]">◐</span>
                <span class="hidden sm:inline">Tema</span>
            </button>

            {{-- DESKTOP AUTH DROPDOWN (NO ALPINE) --}}
            @auth
                <details class="relative hidden md:block">
                    <summary
                        class="list-none cursor-pointer select-none flex items-center gap-2 rounded-2xl border border-zinc-300/70
                               bg-white/70 px-3 py-2 text-sm font-semibold text-zinc-900
                               dark:border-white/15 dark:bg-white/5 dark:text-zinc-100">
                        <span class="hidden sm:inline">{{ $user->name }}</span>
                        <span
                            class="rounded-full bg-[color:var(--brand-gold)]/20 px-2 py-0.5 text-[11px] font-bold text-[color:var(--brand-gold)]">
                            {{ $roleLabel }}
                        </span>
                        <span class="ml-1 text-xs opacity-70">▾</span>
                    </summary>

                    <div
                        class="absolute right-0 mt-2 w-56 rounded-2xl border border-zinc-300/70 bg-white shadow-lg
                               dark:border-white/15 dark:bg-[#0A0A0B]">
                        <a href="{{ $dashboardRoute }}"
                            class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">Dashboard</a>

                        @if ($isAdmin)
                            <a href="{{ route('admin.cases.index') }}"
                                class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">Cases</a>
                            <a href="{{ route('admin.cases.approvals') }}"
                                class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">Approvals</a>
                            <a href="{{ route('admin.users.index') }}"
                                class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">Users</a>
                        @elseif($isLawyer)
                            <a href="{{ route('lawyer.cases.pool') }}"
                                class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">Case Pool</a>
                            <a href="{{ route('lawyer.cases.index') }}"
                                class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">My Cases</a>
                        @elseif($isClient)
                            <a href="{{ route('client.cases.index') }}"
                                class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">My Cases</a>
                            <a href="{{ route('client.cases.create') }}"
                                class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">Buat Case</a>
                        @endif

                        <div class="border-t border-zinc-200/70 dark:border-white/10"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50
                                       dark:text-red-400 dark:hover:bg-red-500/10">
                                Logout
                            </button>
                        </form>
                    </div>
                </details>
            @endauth

            {{-- DESKTOP GUEST BUTTONS --}}
            @guest
                <a href="{{ route('login') }}"
                    class="hidden md:inline-flex rounded-2xl px-4 py-2 text-sm font-semibold
                          text-zinc-700 hover:text-zinc-900 dark:text-zinc-200 dark:hover:text-white">
                    Masuk
                </a>
                <a href="{{ route('register') }}"
                    class="hidden md:inline-flex items-center justify-center rounded-2xl
                          bg-[color:var(--brand-red)] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-95">
                    Konsultasi
                </a>
            @endguest

            {{-- MOBILE MENU (NO ALPINE) --}}
            <details class="md:hidden relative">
                <summary
                    class="list-none cursor-pointer select-none inline-flex h-10 w-10 items-center justify-center rounded-2xl
                           border border-zinc-200 bg-white text-zinc-800 hover:bg-zinc-50
                           dark:border-white/10 dark:bg-black/30 dark:text-zinc-100 dark:hover:bg-white/10">
                    ☰
                </summary>

                <div
                    class="absolute right-0 mt-2 w-64 rounded-2xl border border-zinc-300/70 bg-white shadow-lg
                            dark:border-white/15 dark:bg-[#0A0A0B]">
                    @if ($variant === 'landing')
                        <a href="#services"
                            class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">Layanan</a>
                        <a href="#process"
                            class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">Proses</a>
                        <a href="#testimonials"
                            class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">Testimoni</a>
                        <a href="#faq" class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">FAQ</a>
                        <div class="border-t border-zinc-200/70 dark:border-white/10"></div>
                    @endif

                    @guest
                        <a href="{{ route('login') }}"
                            class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">Masuk</a>
                        <a href="{{ route('register') }}"
                            class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">Konsultasi</a>
                    @endguest

                    @auth
                        <a href="{{ $dashboardRoute }}"
                            class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">Dashboard</a>

                        @if ($isAdmin)
                            <a href="{{ route('admin.cases.index') }}"
                                class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">Cases</a>
                            <a href="{{ route('admin.cases.approvals') }}"
                                class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">Approvals</a>
                            <a href="{{ route('admin.users.index') }}"
                                class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">Users</a>
                        @elseif($isLawyer)
                            <a href="{{ route('lawyer.cases.pool') }}"
                                class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">Case Pool</a>
                            <a href="{{ route('lawyer.cases.index') }}"
                                class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">My Cases</a>
                        @elseif($isClient)
                            <a href="{{ route('client.cases.index') }}"
                                class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">My Cases</a>
                            <a href="{{ route('client.cases.create') }}"
                                class="block px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">Buat Case</a>
                        @endif

                        <div class="border-t border-zinc-200/70 dark:border-white/10"></div>

                        <div class="px-4 py-2 text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $user->name }} • {{ $roleLabel }}
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="px-2 pb-2">
                            @csrf
                            <button type="submit"
                                class="w-full rounded-xl px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50
                                       dark:text-red-400 dark:hover:bg-red-500/10">
                                Logout
                            </button>
                        </form>
                    @endauth
                </div>
            </details>
        </div>
    </div>
</header>
