<section id="testimonials" class="mx-auto max-w-7xl px-4 pb-16 sm:px-6">
    <div class="flex items-end justify-between gap-6">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight sm:text-3xl">Client stories</h2>
            <p class="mt-2 max-w-2xl text-sm text-zinc-600 dark:text-zinc-300">
                Beberapa contoh pengalaman client. (Nanti isi aslinya tinggal ganti.)
            </p>
        </div>
    </div>

    @php
        $quotes = [
            [
                'n' => 'A',
                't' => 'Komunikasinya jelas',
                'd' => '“Langkahnya rapi, progress selalu update. Saya jadi ngerti posisi kasus saya.”',
            ],
            [
                'n' => 'B',
                't' => 'Dokumen tertata',
                'd' => '“Draft dan revisi kebaca jelas, tidak ada yang tercecer. Enak buat follow-up.”',
            ],
            [
                'n' => 'C',
                't' => 'Strateginya masuk akal',
                'd' => '“Dikasih pilihan opsi + risikonya. Jadi keputusan saya lebih aman.”',
            ],
        ];
    @endphp

    <div class="mt-10 grid gap-4 md:grid-cols-3">
        @foreach ($quotes as $q)
            <div class="rounded-3xl border border-zinc-200 bg-white/60 p-6 dark:border-white/10 dark:bg-black/25">
                <div class="flex items-center gap-3">
                    <span class="h-10 w-10 rounded-2xl flex items-center justify-center font-bold text-white"
                        style="background: var(--brand-red);">
                        {{ $q['n'] }}
                    </span>
                    <div class="font-semibold">{{ $q['t'] }}</div>
                </div>
                <p class="mt-4 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">{{ $q['d'] }}</p>
            </div>
        @endforeach
    </div>
</section>
