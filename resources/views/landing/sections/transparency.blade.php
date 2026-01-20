<section id="progress" class="mx-auto max-w-7xl px-4 pb-16 sm:px-6">
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-zinc-200 bg-white/60 p-8 dark:border-zinc-800 dark:bg-zinc-950/40">
            <h2 class="text-2xl font-semibold tracking-tight">Progress case bisa dipantau</h2>
            <p class="mt-3 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">
                Setiap case punya status utama dan milestone. Anda tahu posisi proses, lawyer tahu next action,
                dan admin menjaga ketertiban alur.
            </p>

            <ul class="mt-6 space-y-3 text-sm text-zinc-700 dark:text-zinc-200">
                <li class="flex gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full bg-[#C6A24A]"></span>
                    Status: Submitted → Verified → Assigned → Active → Resolved
                </li>
                <li class="flex gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full bg-[#C6A24A]"></span>
                    Milestone: Intake → Strategy → Draft → Review → Delivery
                </li>
                <li class="flex gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full bg-[#C6A24A]"></span>
                    Audit trail: catatan & waktu perubahan tersimpan
                </li>
            </ul>
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white/60 p-8 dark:border-zinc-800 dark:bg-zinc-950/40">
            <div class="text-sm font-semibold">Contoh timeline</div>
            <div class="mt-6 space-y-4">
                @php
                    $events = [
                        ['t' => 'Submitted', 'd' => 'Client submit kronologi & dokumen', 's' => 'done'],
                        ['t' => 'Verified', 'd' => 'Admin verifikasi & kategorisasi', 's' => 'done'],
                        ['t' => 'Assigned', 'd' => 'Penunjukan lawyer', 's' => 'doing'],
                        ['t' => 'Handling', 'd' => 'Penanganan & dokumen kerja', 's' => 'todo'],
                    ];
                @endphp

                @foreach ($events as $e)
                    <div class="flex items-start gap-3">
                        <div
                            class="mt-1 h-3 w-3 rounded-full
                            @if ($e['s'] === 'done') bg-[#C6A24A]
                            @elseif($e['s'] === 'doing') bg-[#A11217]
                            @else bg-zinc-300 dark:bg-zinc-700 @endif
                        ">
                        </div>
                        <div>
                            <div class="font-medium">{{ $e['t'] }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $e['d'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
