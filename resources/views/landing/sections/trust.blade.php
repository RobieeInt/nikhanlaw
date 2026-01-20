<section class="border-y border-zinc-200/70 bg-white/50 dark:border-white/10 dark:bg-white/0">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $items = [
                    ['t' => 'Kerahasiaan', 'd' => 'Informasi & dokumen tertata dan terkontrol.'],
                    ['t' => 'Komunikasi jelas', 'd' => 'Update langkah dan kebutuhan dokumen.'],
                    ['t' => 'Dokumen rapi', 'd' => 'Satu tempat untuk semua file & revisi.'],
                    ['t' => 'Proses terukur', 'd' => 'Milestone yang bisa dipantau.'],
                ];
            @endphp
            @foreach ($items as $i)
                <div class="rounded-3xl border border-zinc-200 bg-white/60 p-5 dark:border-white/10 dark:bg-black/25">
                    <div class="flex items-center gap-3">
                        <span class="h-9 w-9 rounded-2xl flex items-center justify-center"
                            style="background: rgba(176,141,47,.14); color: var(--brand-gold);">
                            ✓
                        </span>
                        <div class="font-semibold">{{ $i['t'] }}</div>
                    </div>
                    <div class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ $i['d'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
