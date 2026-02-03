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

    <a href="https://wa.me/6288293280888?text=Hi%2C%20Nikhan%20Law%20saya%20ingin%20konsultasi%20hukum" target="_blank"
        rel="noopener noreferrer" aria-label="Chat WhatsApp Nikhan Law" class="wa-float">
        <!-- Badge -->
        <span class="wa-badge">Konsultasi Gratis</span>

        <!-- Icon -->
        <svg viewBox="0 0 32 32" class="wa-icon" aria-hidden="true">
            <path
                d="M19.11 17.53c-.27-.14-1.6-.79-1.85-.88-.25-.09-.43-.14-.61.14-.18.27-.7.88-.86 1.06-.16.18-.32.2-.59.07-.27-.14-1.13-.42-2.16-1.34-.8-.71-1.34-1.59-1.5-1.86-.16-.27-.02-.41.12-.55.12-.12.27-.32.41-.48.14-.16.18-.27.27-.45.09-.18.05-.34-.02-.48-.07-.14-.61-1.47-.84-2.01-.22-.54-.45-.47-.61-.48h-.52c-.18 0-.48.07-.73.34-.25.27-.95.93-.95 2.27 0 1.34.97 2.64 1.11 2.82.14.18 1.91 2.92 4.63 4.09.65.28 1.16.45 1.55.58.65.21 1.24.18 1.71.11.52-.08 1.6-.65 1.83-1.28.23-.63.23-1.17.16-1.28-.07-.11-.25-.18-.52-.32z" />
            <path
                d="M16.02 3C8.83 3 3 8.83 3 16c0 2.28.6 4.52 1.74 6.5L3 29l6.69-1.75A12.95 12.95 0 0 0 16.02 29C23.2 29 29 23.17 29 16S23.2 3 16.02 3zm0 23.57c-2.05 0-4.06-.55-5.83-1.6l-.42-.25-3.97 1.04 1.06-3.86-.27-.4a10.54 10.54 0 0 1-1.71-5.75c0-5.8 4.72-10.52 10.54-10.52 5.81 0 10.54 4.72 10.54 10.52 0 5.8-4.72 10.52-10.54 10.52z" />
        </svg>
    </a>
    <style>
        /* ===== WhatsApp Floating Button ===== */
        .wa-float {
            position: fixed;
            right: 22px;
            bottom: 22px;
            width: 56px;
            height: 56px;
            border-radius: 9999px;
            background: #25D366;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .18);
            z-index: 999999;
            text-decoration: none;
            transition: transform .25s ease, box-shadow .25s ease;
        }

        /* icon */
        .wa-icon {
            width: 28px;
            height: 28px;
            fill: currentColor;
            position: relative;
            z-index: 2;
        }

        /* badge */
        .wa-badge {
            position: absolute;
            right: 64px;
            bottom: 50%;
            transform: translateY(50%);
            background: linear-gradient(135deg, #B08D2F, #D4AF37);
            color: #111;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 10px;
            border-radius: 999px;
            white-space: nowrap;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .25);
            opacity: .95;
            transition: transform .25s ease, opacity .25s ease;
        }

        /* hover effect */
        .wa-float:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 35px rgba(0, 0, 0, .28);
        }

        .wa-float:hover .wa-badge {
            transform: translateY(50%) translateX(-4px);
            opacity: 1;
        }

        /* subtle ripple glow */
        .wa-float::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 9999px;
            background: radial-gradient(circle at center, rgba(255, 255, 255, .35), transparent 60%);
            opacity: 0;
            transition: opacity .25s ease;
        }

        .wa-float:hover::after {
            opacity: .6;
        }

        /* mobile safety */
        @media (max-width: 480px) {
            .wa-badge {
                font-size: 11px;
                padding: 5px 9px;
            }
        }
    </style>
</body>


</html>
