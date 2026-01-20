<section id="how" class="mx-auto max-w-7xl px-4 pb-16 sm:px-6">
    <div class="rounded-3xl border border-black/10 bg-white/60 p-8 dark:border-white/10 dark:bg-white/5">
        <h2 class="text-2xl font-semibold tracking-tight sm:text-3xl">Cara kerja yang manusiawi</h2>
        <p class="mt-2 max-w-2xl text-sm text-zinc-700 dark:text-zinc-300">
            Anda tidak akan dibiarkan menebak-nebak progres. Sistem dibuat supaya jelas tahapan dan next action.
        </p>

        @php
            $steps = [
                ['n' => '01', 't' => 'Ceritakan kasus', 'd' => 'Isi kronologi dan unggah bukti awal.'],
                [
                    'n' => '02',
                    't' => 'Verifikasi & penugasan',
                    'd' => 'Admin review kelengkapan lalu assign lawyer yang relevan.',
                ],
                [
                    'n' => '03',
                    't' => 'Penanganan terukur',
                    'd' => 'Lawyer bekerja berdasarkan milestone. Anda pantau status dan dokumen.',
                ],
                [
                    'n' => '04',
                    't' => 'Selesai & dokumentasi',
                    'd' => 'Output diserahkan, ringkasan hasil dibuat, case ditutup rapi.',
                ],
            ];
        @endphp

        <div class="mt-8 grid gap-6 lg:grid-cols-4">
            @foreach ($steps as $s)
                <div class="rounded-3xl border border-black/10 bg-white p-6 dark:border-white/10 dark:bg-black/20">
                    <div
                        class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-[#9B0F12] text-sm font-bold text-white dark:bg-[#D7B660] dark:text-black">
                        {{ $s['n'] }}
                    </div>
                    <div class="mt-4 text-base font-semibold">{{ $s['t'] }}</div>
                    <div class="mt-2 text-sm text-zinc-700 dark:text-zinc-300">{{ $s['d'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
