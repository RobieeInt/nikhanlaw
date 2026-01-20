<section id="services" class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight sm:text-3xl">Layanan untuk client</h2>
            <p class="mt-2 max-w-2xl text-sm text-zinc-600 dark:text-zinc-300">
                Mulai dari konsultasi, drafting, negosiasi, sampai pendampingan proses hukum sesuai kebutuhan Anda.
            </p>
        </div>
        <a href="#cta"
            class="mt-4 inline-flex text-sm font-semibold text-[color:var(--brand-red)] hover:opacity-90 dark:text-[color:var(--brand-gold)] sm:mt-0">
            Ajukan konsultasi →
        </a>
    </div>

    @php
        $cards = [
            ['t' => 'Konsultasi Hukum', 'd' => 'Klarifikasi masalah, tujuan, dan opsi langkah terbaik.'],
            ['t' => 'Review & Draft Dokumen', 'd' => 'Kontrak, somasi, perjanjian, legal opinion, dan lain-lain.'],
            ['t' => 'Negosiasi & Mediasi', 'd' => 'Pendampingan komunikasi dan penyusunan posisi hukum.'],
            ['t' => 'Litigasi', 'd' => 'Pendampingan proses pengadilan (sesuai perkara).'],
            ['t' => 'Audit Legal', 'd' => 'Penilaian kepatuhan & risiko dari sisi hukum.'],
            ['t' => 'Kepailitan', 'd' => 'Receiver & administrator untuk proses terkait.'],
        ];
    @endphp

    <div class="mt-10 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($cards as $c)
            <div
                class="group rounded-3xl border border-zinc-200 bg-white/60 p-6 transition hover:-translate-y-0.5 hover:border-[color:var(--brand-gold)]/50 dark:border-white/10 dark:bg-black/25">
                <div class="flex items-start justify-between gap-3">
                    <div class="text-lg font-semibold">{{ $c['t'] }}</div>
                    <span
                        class="h-10 w-10 rounded-2xl flex items-center justify-center border border-zinc-200 bg-white text-zinc-900 group-hover:border-[color:var(--brand-gold)]/60 dark:border-white/10 dark:bg-white/5 dark:text-white"
                        style="box-shadow: 0 0 0 1px rgba(176,141,47,.08) inset;">
                        ↗
                    </span>
                </div>
                <p class="mt-3 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">{{ $c['d'] }}</p>
            </div>
        @endforeach
    </div>
</section>
