<section id="areas" class="mx-auto max-w-7xl px-4 pb-16 sm:px-6">
    <div class="rounded-3xl border border-zinc-200 bg-white/60 p-8 dark:border-zinc-800 dark:bg-zinc-950/40">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">Layanan</h2>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">
                    Konsultasi, non-litigasi, hingga litigasi. Kategori membantu routing penanganan.
                </p>
            </div>
            <a href="#cta"
                class="mt-4 inline-flex text-sm font-semibold text-[#A11217] hover:text-[#8C0F13] dark:text-[#C6A24A] dark:hover:text-[#D9B866] sm:mt-0">
                Ajukan konsultasi →
            </a>
        </div>

        @php
            $areas = [
                'Perdata & Kontrak',
                'Pidana',
                'Keluarga (Perceraian/Warisan)',
                'Ketenagakerjaan',
                'Bisnis & Korporasi',
                'Properti & Sengketa Tanah',
                'Kepailitan & PKPU',
                'Perizinan & Kepatuhan',
            ];
        @endphp

        <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($areas as $a)
                <div
                    class="rounded-2xl border border-zinc-200 bg-white p-4 text-sm font-medium text-zinc-800 hover:border-[#C6A24A]/60 hover:bg-[#C6A24A]/10 dark:border-zinc-800 dark:bg-zinc-900/30 dark:text-zinc-100 dark:hover:border-[#C6A24A]/40 dark:hover:bg-[#C6A24A]/10">
                    {{ $a }}
                </div>
            @endforeach
        </div>
    </div>
</section>
