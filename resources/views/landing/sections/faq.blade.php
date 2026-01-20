<section id="faq" class="mx-auto max-w-7xl px-4 pb-16 sm:px-6">
    <div class="rounded-[32px] border border-zinc-200 bg-white/60 p-8 dark:border-white/10 dark:bg-black/25">
        <h2 class="text-2xl font-semibold tracking-tight sm:text-3xl">FAQ</h2>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Pertanyaan yang biasanya ditanyain sebelum orang berani
            cerita masalahnya.</p>

        @php
            $faqs = [
                [
                    'q' => 'Apakah konsultasi bisa online?',
                    'a' => 'Bisa. Konsultasi dapat dilakukan online sesuai jadwal yang disepakati.',
                ],
                [
                    'q' => 'Bagaimana kerahasiaan data saya?',
                    'a' => 'Kami menjaga kerahasiaan informasi dan dokumen. Akses dan riwayat perubahan tercatat.',
                ],
                [
                    'q' => 'Apa saya bisa tahu progres penanganan?',
                    'a' => 'Bisa. Anda akan melihat milestone dan update langkah yang sedang berjalan.',
                ],
                [
                    'q' => 'Apakah semua masalah harus dibawa ke pengadilan?',
                    'a' =>
                        'Tidak. Banyak perkara bisa selesai lewat negosiasi/mediasi atau penyusunan dokumen yang tepat.',
                ],
            ];
        @endphp

        <div class="mt-8 space-y-3" x-data="{ open: 0 }">
            @foreach ($faqs as $i => $f)
                <div class="rounded-3xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/5">
                    <button class="flex w-full items-center justify-between gap-4 text-left"
                        @click="open = (open === {{ $i + 1 }} ? 0 : {{ $i + 1 }})">
                        <div class="font-semibold">{{ $f['q'] }}</div>
                        <span
                            class="h-9 w-9 rounded-2xl flex items-center justify-center border border-zinc-200 bg-white text-zinc-900 dark:border-white/10 dark:bg-black/30 dark:text-white"
                            :style="open === {{ $i + 1 }} ? 'border-color: rgba(176,141,47,.55)' : ''">
                            <span x-text="open === {{ $i + 1 }} ? '−' : '+'"></span>
                        </span>
                    </button>
                    <div x-show="open === {{ $i + 1 }}" x-collapse
                        class="mt-3 text-sm text-zinc-600 dark:text-zinc-300">
                        {{ $f['a'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
