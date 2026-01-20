<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Client Dashboard</h1>
            <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-200/80">
                Selamat datang, <span class="font-semibold">{{ auth()->user()->name }}</span>. Pantau progres case Anda
                di sini.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('client.cases.create') }}"
                class="inline-flex items-center justify-center rounded-2xl px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition
                      focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-gold)]/50 ring-1 ring-black/10 dark:ring-white/10"
                style="background: linear-gradient(135deg, #8E0C12, #C39A3A);">
                + Buat Case
            </a>

            <a href="{{ route('client.cases.index') }}"
                class="inline-flex items-center justify-center rounded-2xl border border-zinc-300/70 bg-white/70 px-4 py-2.5 text-sm font-semibold text-zinc-900 hover:bg-white transition
                      dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
                Lihat Semua Case
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-5 dark:border-white/15 dark:bg-white/5">
            <div class="text-xs text-zinc-600 dark:text-zinc-300">Total Case</div>
            <div class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $totalCases }}</div>
        </div>

        <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-5 dark:border-white/15 dark:bg-white/5">
            <div class="text-xs text-zinc-600 dark:text-zinc-300">Aktif</div>
            <div class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $activeCases }}</div>
        </div>

        <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-5 dark:border-white/15 dark:bg-white/5">
            <div class="text-xs text-zinc-600 dark:text-zinc-300">Butuh Aksi Anda</div>
            <div class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $needActionCases }}</div>
        </div>
    </div>

    {{-- Recent cases --}}
    <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-6 dark:border-white/15 dark:bg-white/5">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-lg font-semibold text-zinc-900 dark:text-white">Case Terbaru</div>
                <div class="mt-1 text-sm text-zinc-700 dark:text-zinc-200/80">Ringkasan 5 case terakhir.</div>
            </div>
        </div>

        @if (count($recentCases) === 0)
            <div
                class="mt-6 rounded-3xl border border-dashed border-zinc-300/80 bg-white/60 p-8 text-center dark:border-white/15 dark:bg-black/20">
                <div class="text-base font-semibold text-zinc-900 dark:text-white">Belum ada case</div>
                <div class="mt-2 text-sm text-zinc-700 dark:text-zinc-200/80">
                    Mulai dari konsultasi, nanti kalau perlu bisa lanjut ke pendampingan.
                </div>
                <div class="mt-5">
                    <a href="{{ route('client.cases.create') }}"
                        class="inline-flex items-center justify-center rounded-2xl px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition
                              focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-gold)]/50 ring-1 ring-black/10 dark:ring-white/10"
                        style="background: linear-gradient(135deg, #8E0C12, #C39A3A);">
                        Buat Case Pertama
                    </a>
                </div>
            </div>
        @else
            <div class="mt-6 space-y-3">
                @foreach ($recentCases as $c)
                    <a href="{{ route('client.cases.show', $c['id']) }}"
                        class="block rounded-3xl border border-zinc-300/70 bg-white/70 p-4 hover:bg-white transition
                              dark:border-white/15 dark:bg-black/20 dark:hover:bg-white/5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="text-sm font-semibold text-zinc-900 dark:text-white">
                                    {{ $c['title'] }}
                                </div>
                                <div class="mt-1 text-xs text-zinc-600 dark:text-zinc-300">
                                    {{ $c['case_no'] ?? 'CASE-' . $c['id'] }}
                                    • {{ \Carbon\Carbon::parse($c['created_at'])->format('d M Y') }}
                                </div>
                            </div>

                            <span
                                class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $this->statusClasses($c['status']) }}">
                                {{ $this->statusLabel($c['status']) }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
