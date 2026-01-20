<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Nikhan & Associates Law Office</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Brand tokens (ngikut logo) --}}
    <style>
        :root {
            --brand-red: #A10E14;
            --brand-gold: #B08D2F;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body x-data="landingUX()" x-init="init()" @mousemove="mx = $event.clientX; my = $event.clientY"
    class="bg-zinc-50 text-zinc-900 dark:bg-[#0A0A0B] dark:text-zinc-100">
    {{-- ===== Background + Parallax layers ===== --}}
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        {{-- Gradient wash --}}
        <div
            class="absolute inset-0 bg-gradient-to-b from-white via-zinc-50 to-zinc-50/70 dark:from-[#0A0A0B] dark:via-[#0A0A0B] dark:to-[#0A0A0B]">
        </div>

        {{-- Parallax blobs --}}
        <div class="absolute -top-52 left-1/2 h-[720px] w-[720px] -translate-x-1/2 rounded-full blur-3xl opacity-70 dark:opacity-50"
            :style="`transform: translateX(-50%) translateY(${scrollY * 0.08}px); background: radial-gradient(circle at 30% 30%, rgba(161,14,20,.20), rgba(176,141,47,.12), transparent 62%);`">
        </div>

        <div class="absolute -bottom-56 right-[-120px] h-[760px] w-[760px] rounded-full blur-3xl opacity-70 dark:opacity-45"
            :style="`transform: translateY(${scrollY * -0.06}px); background: radial-gradient(circle at 60% 50%, rgba(176,141,47,.18), rgba(161,14,20,.10), transparent 60%);`">
        </div>

        {{-- Mouse glow (parallax feel) --}}
        <div class="absolute h-[520px] w-[520px] -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl transition-opacity duration-200"
            :style="`left:${mx}px; top:${my}px; opacity:${glowOpacity}; background: radial-gradient(circle, rgba(176,141,47,.22), rgba(161,14,20,.12), transparent 60%);`">
        </div>

        {{-- Subtle grid --}}
        <div class="absolute inset-0 opacity-[0.08] dark:opacity-[0.10]"
            style="background-image: radial-gradient(currentColor 1px, transparent 1px); background-size: 28px 28px; color: #111827;">
        </div>
    </div>

    {{-- @include('landing.partials.navbar') --}}
    @include('landing.partials.navbar', ['variant' => 'landing'])

    <main>
        @include('landing.sections.hero')
        @include('landing.sections.trust')
        @include('landing.sections.services')
        @include('landing.sections.process')
        @include('landing.sections.testimonials')
        @include('landing.sections.faq')
        @include('landing.sections.cta')
    </main>

    @include('landing.partials.footer')

    <script>
        function landingUX() {
            return {
                mx: window.innerWidth / 2,
                my: 160,
                scrollY: 0,
                glowOpacity: 0.65,

                init() {
                    const onScroll = () => {
                        this.scrollY = window.scrollY || 0;
                    };
                    onScroll();
                    window.addEventListener('scroll', onScroll, {
                        passive: true
                    });
                },

                toggleTheme() {
                    // ✅ now global
                    window.Theme.toggle();
                },
            }
        }
    </script>
</body>

</html>
