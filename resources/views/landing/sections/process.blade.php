<section id="process" class="mx-auto max-w-7xl px-4 pb-16 sm:px-6">
    <div class="rounded-[32px] border border-zinc-200 bg-white/60 p-8 dark:border-white/10 dark:bg-black/25">
        <h2 class="text-2xl font-semibold tracking-tight sm:text-3xl">Proses yang transparan</h2>
        <p class="mt-2 max-w-2xl text-sm text-zinc-600 dark:text-zinc-300">
            Biar Anda tidak merasa “jalan sendiri”. Kami pakai milestone agar jelas sudah sampai mana.
        </p>

        @php
            $steps = [
                [
                    'n' => '01',
                    't' => 'Konsultasi awal',
                    'd' => 'Ceritakan kronologi, tujuan, dan bukti yang sudah ada.',
                ],
                ['n' => '02', 't' => 'Analisis & strategi', 'd' => 'Kami susun opsi langkah, risiko, dan rekomendasi.'],
                [
                    'n' => '03',
                    't' => 'Eksekusi tindakan',
                    'd' => 'Draft dokumen/negosiasi/penanganan proses sesuai jalur.',
                ],
                [
                    'n' => '04',
                    't' => 'Update & penutupan',
                    'd' => 'Ringkasan hasil, penyerahan dokumen final, case ditutup.',
                ],
            ];
        @endphp

        <div class="mt-10 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            @foreach ($steps as $s)
                <div class="rounded-3xl border border-zinc-200 bg-white p-6 dark:border-white/10 dark:bg-white/5">
                    <div class="inline-flex items-center gap-3">
                        <span
                            class="h-10 w-10 rounded-2xl flex items-center justify-center text-sm font-extrabold text-white"
                            style="background: linear-gradient(135deg, var(--brand-red), var(--brand-gold));">
                            {{ $s['n'] }}
                        </span>
                        <div class="text-base font-semibold">{{ $s['t'] }}</div>
                    </div>
                    <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-300">{{ $s['d'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
