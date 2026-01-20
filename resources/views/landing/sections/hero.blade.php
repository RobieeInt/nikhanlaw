<section class="relative">
    <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:py-24">
        <div>
            <p
                class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white/60 px-3 py-1 text-xs font-semibold text-zinc-700 dark:border-white/10 dark:bg-black/30 dark:text-zinc-200">
                <span class="h-2 w-2 rounded-full bg-[color:var(--brand-gold)]"></span>
                Attorney & Counsellor at Law • Audit • Receiver & Administrator for Bankruptcy
            </p>

            <h1 class="mt-5 text-4xl font-semibold leading-tight tracking-tight sm:text-5xl">
                Pendampingan hukum yang
                <span class="text-[color:var(--brand-red)] dark:text-[color:var(--brand-gold)]">tegas</span>,
                <span class="text-[color:var(--brand-red)] dark:text-[color:var(--brand-gold)]">rapi</span>,
                dan bisa dipantau.
            </h1>

            <p class="mt-5 max-w-xl text-base leading-relaxed text-zinc-600 dark:text-zinc-300">
                Nikhan & Associates Law Office membantu Anda dari konsultasi awal, penyusunan strategi, hingga
                penyelesaian perkara.
                Komunikasi jelas, dokumen tertata, progres transparan.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-3">
                <a href="#cta"
                    class="inline-flex items-center justify-center rounded-2xl bg-[color:var(--brand-red)] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:opacity-95">
                    Ajukan Konsultasi
                </a>
                <a href="#services"
                    class="inline-flex items-center justify-center rounded-2xl border border-zinc-200 bg-white/60 px-6 py-3 text-sm font-semibold text-zinc-900 hover:bg-white dark:border-white/10 dark:bg-black/30 dark:text-zinc-100 dark:hover:bg-white/10">
                    Lihat Layanan
                </a>
            </div>

            <div class="mt-10 grid grid-cols-3 gap-4 sm:max-w-lg">
                <div class="rounded-3xl border border-zinc-200 bg-white/60 p-4 dark:border-white/10 dark:bg-black/30">
                    <div class="text-lg font-semibold">Jelas</div>
                    <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Proses & estimasi langkah</div>
                </div>
                <div class="rounded-3xl border border-zinc-200 bg-white/60 p-4 dark:border-white/10 dark:bg-black/30">
                    <div class="text-lg font-semibold">Aman</div>
                    <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Dokumen tersentral</div>
                </div>
                <div class="rounded-3xl border border-zinc-200 bg-white/60 p-4 dark:border-white/10 dark:bg-black/30">
                    <div class="text-lg font-semibold">Terukur</div>
                    <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Update milestone</div>
                </div>
            </div>
        </div>

        {{-- Showcase card --}}
        <div class="relative">
            <div
                class="rounded-[28px] border border-zinc-200 bg-white/70 p-6 shadow-sm backdrop-blur dark:border-white/10 dark:bg-black/35">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-semibold">Case Progress (Sample)</div>
                        <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Konsultasi → Strategi → Eksekusi →
                            Selesai</div>
                    </div>
                    <span
                        class="rounded-full bg-[color:var(--brand-gold)]/15 px-3 py-1 text-xs font-semibold text-[color:var(--brand-gold)]">
                        Active
                    </span>
                </div>

                <div class="mt-6 space-y-4">
                    <div class="rounded-2xl border border-zinc-200 bg-white p-4 dark:border-white/10 dark:bg-white/5">
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Next action</div>
                        <div class="mt-1 font-medium">Review dokumen & penentuan strategi</div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
                            <span>Progress</span><span>65%</span>
                        </div>
                        <div class="mt-2 h-2 w-full rounded-full bg-zinc-200 dark:bg-white/10">
                            <div class="h-2 w-[65%] rounded-full"
                                style="background: linear-gradient(90deg, var(--brand-red), var(--brand-gold));"></div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @php
                            $events = [
                                ['t' => 'Konsultasi awal', 'd' => 'Klarifikasi kronologi & tujuan', 's' => 'done'],
                                ['t' => 'Analisis & strategi', 'd' => 'Opsi langkah & risiko', 's' => 'doing'],
                                [
                                    't' => 'Eksekusi tindakan',
                                    'd' => 'Dokumen / negosiasi / proses lanjutan',
                                    's' => 'todo',
                                ],
                            ];
                        @endphp
                        @foreach ($events as $e)
                            <div class="flex items-start gap-3">
                                <div
                                    class="mt-1 h-3 w-3 rounded-full
                                    @if ($e['s'] === 'done') bg-[color:var(--brand-gold)]
                                    @elseif($e['s'] === 'doing') bg-[color:var(--brand-red)]
                                    @else bg-zinc-300 dark:bg-white/10 @endif
                                ">
                                </div>
                                <div>
                                    <div class="text-sm font-semibold">{{ $e['t'] }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $e['d'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="pointer-events-none absolute -right-6 -top-6 h-28 w-28 rounded-[28px] blur-xl"
                style="background: rgba(161,14,20,.12)"></div>
            <div class="pointer-events-none absolute -bottom-8 -left-8 h-28 w-28 rounded-[28px] blur-xl"
                style="background: rgba(176,141,47,.12)"></div>
        </div>
    </div>
</section>
