<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Admin Dashboard</h1>
            <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-200/80">
                Ringkasan case masuk + aktivitas terbaru.
            </p>
        </div>

        <a href="{{ route('admin.cases.index') }}"
            class="inline-flex items-center justify-center rounded-2xl px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition
                  focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-gold)]/50 ring-1 ring-black/10 dark:ring-white/10"
            style="background: linear-gradient(135deg, #8E0C12, #C39A3A);">
            Buka Case Inbox →
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-5 dark:border-white/15 dark:bg-white/5">
            <div class="text-xs text-zinc-600 dark:text-zinc-300">Submitted</div>
            <div class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $submitted }}</div>
        </div>
        <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-5 dark:border-white/15 dark:bg-white/5">
            <div class="text-xs text-zinc-600 dark:text-zinc-300">Need Info</div>
            <div class="mt-2 text-2xl font-semibold" style="color: var(--brand-gold);">{{ $needInfo }}</div>
        </div>
        <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-5 dark:border-white/15 dark:bg-white/5">
            <div class="text-xs text-zinc-600 dark:text-zinc-300">Qualified</div>
            <div class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $qualified }}</div>
        </div>
        <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-5 dark:border-white/15 dark:bg-white/5">
            <div class="text-xs text-zinc-600 dark:text-zinc-300">Assigned</div>
            <div class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $assigned }}</div>
        </div>
        <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-5 dark:border-white/15 dark:bg-white/5">
            <div class="text-xs text-zinc-600 dark:text-zinc-300">Active</div>
            <div class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $active }}</div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Recent cases --}}
        <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-6 dark:border-white/15 dark:bg-white/5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-lg font-semibold text-zinc-900 dark:text-white">Case Terbaru</div>
                    <div class="mt-1 text-sm text-zinc-700 dark:text-zinc-200/80">8 case terakhir yang masuk.</div>
                </div>
            </div>

            <div class="mt-5 space-y-3">
                @forelse($recentCases as $c)
                    <a href="{{ route('admin.cases.show', $c['id']) }}"
                        class="block rounded-3xl border border-zinc-300/70 bg-white/70 p-4 hover:bg-white transition
                              dark:border-white/15 dark:bg-black/20 dark:hover:bg-white/5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-semibold text-zinc-900 dark:text-white">
                                    {{ $c['title'] }}
                                </div>
                                <div class="mt-1 text-xs text-zinc-600 dark:text-zinc-300">
                                    {{ $c['case_no'] }} • {{ $c['client_name'] ?? '-' }} •
                                    {{ \Carbon\Carbon::parse($c['created_at'])->format('d M Y') }}
                                </div>
                                <div class="mt-1 text-xs text-zinc-600 dark:text-zinc-300">
                                    Lawyer: {{ $c['lawyer_name'] ?? '—' }}
                                </div>
                            </div>

                            <span
                                class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $this->badgeClass($c['status']) }}">
                                {{ $this->statusLabel($c['status']) }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div
                        class="rounded-2xl border border-dashed border-zinc-300/70 bg-white/60 p-6 text-center text-sm text-zinc-600
                                dark:border-white/15 dark:bg-black/20 dark:text-zinc-400">
                        Belum ada case.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent events --}}
        <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-6 dark:border-white/15 dark:bg-white/5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-lg font-semibold text-zinc-900 dark:text-white">Aktivitas Terbaru</div>
                    <div class="mt-1 text-sm text-zinc-700 dark:text-zinc-200/80">10 event terakhir (timeline).</div>
                </div>
            </div>

            <div class="mt-5 space-y-3">
                @forelse($recentEvents as $e)
                    <a href="{{ route('admin.cases.show', $e['case_id']) }}"
                        class="block rounded-3xl border border-zinc-300/70 bg-white/70 p-4 hover:bg-white transition
                              dark:border-white/15 dark:bg-black/20 dark:hover:bg-white/5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-zinc-900 dark:text-white">
                                    {{ strtoupper(str_replace('_', ' ', $e['event'])) }}
                                </div>
                                <div class="mt-1 text-xs text-zinc-600 dark:text-zinc-300">
                                    Case: {{ $e['case_no'] ?? '#' . $e['case_id'] }}
                                    • By: {{ $e['actor_role'] ?? 'system' }}
                                    {{ $e['actor_name'] ? '(' . $e['actor_name'] . ')' : '' }}
                                    • {{ \Carbon\Carbon::parse($e['created_at'])->format('d M Y, H:i') }}
                                </div>

                                @if (!empty($e['note']))
                                    <div class="mt-2 text-sm text-zinc-700 dark:text-zinc-200/80">
                                        {{ $e['note'] }}
                                    </div>
                                @endif
                            </div>

                            <span class="text-xs font-semibold text-[color:var(--brand-gold)]">
                                Open →
                            </span>
                        </div>
                    </a>
                @empty
                    <div
                        class="rounded-2xl border border-dashed border-zinc-300/70 bg-white/60 p-6 text-center text-sm text-zinc-600
                                dark:border-white/15 dark:bg-black/20 dark:text-zinc-400">
                        Belum ada event.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
