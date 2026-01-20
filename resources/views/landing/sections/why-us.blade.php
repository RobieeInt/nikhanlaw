<section id="why" class="mx-auto max-w-7xl px-4 pb-16 sm:px-6">
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-black/10 bg-white/60 p-8 dark:border-white/10 dark:bg-white/5">
            <h2 class="text-2xl font-semibold tracking-tight">Kenapa Nikhan & Associates?</h2>
            <p class="mt-3 text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">
                Karena klien butuh kepastian langkah, bukan drama. Kami fokus ke strategi, dokumen rapi, dan progres
                yang bisa Anda cek.
            </p>

            <ul class="mt-6 space-y-3 text-sm text-zinc-800 dark:text-zinc-200">
                <li class="flex gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full bg-[#9B0F12] dark:bg-[#D7B660]"></span>
                    Komunikasi jelas: apa yang dikerjakan, kapan selesai, apa yang dibutuhkan dari Anda.
                </li>
                <li class="flex gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full bg-[#9B0F12] dark:bg-[#D7B660]"></span>
                    Dokumen terpusat: mudah dicari, versi tertib, siap kalau dibutuhkan sewaktu-waktu.
                </li>
                <li class="flex gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full bg-[#9B0F12] dark:bg-[#D7B660]"></span>
                    Tahapan terukur: konsultasi → strategi → tindakan → hasil → penutupan.
                </li>
            </ul>
        </div>

        <div class="rounded-3xl border border-black/10 bg-white/60 p-8 dark:border-white/10 dark:bg-white/5">
            <div class="text-sm font-semibold">Transparansi progres (contoh)</div>
            <div class="mt-6 space-y-4">
                @php
                    $events = [
                        ['t' => 'Submitted', 'd' => 'Case diajukan oleh client', 's' => 'done'],
                        ['t' => 'Admin Review', 'd' => 'Validasi bukti & kategori', 's' => 'done'],
                        ['t' => 'Assigned', 'd' => 'Lawyer ditugaskan', 's' => 'done'],
                        ['t' => 'In Progress', 'd' => 'Strategi / dokumen / follow-up', 's' => 'doing'],
                        ['t' => 'Resolved', 'd' => 'Output & ringkasan hasil', 's' => 'todo'],
                    ];
                @endphp

                @foreach ($events as $e)
                    <div class="flex items-start gap-3">
                        <div
                            class="mt-1 h-3 w-3 rounded-full
                            @if ($e['s'] === 'done') bg-[#9B0F12] dark:bg-[#D7B660]
                            @elseif($e['s'] === 'doing') bg-zinc-900 dark:bg-zinc-200
                            @else bg-zinc-300 dark:bg-white/20 @endif
                        ">
                        </div>
                        <div>
                            <div class="font-medium">{{ $e['t'] }}</div>
                            <div class="text-xs text-zinc-700 dark:text-zinc-400">{{ $e['d'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div
                class="mt-8 rounded-2xl border border-black/10 bg-white p-5 text-sm text-zinc-700 dark:border-white/10 dark:bg-black/20 dark:text-zinc-300">
                <span class="font-semibold text-zinc-900 dark:text-zinc-100">Next action:</span>
                Client upload dokumen tambahan (opsional) agar strategi bisa dipastikan.
            </div>
        </div>
    </div>
</section>
