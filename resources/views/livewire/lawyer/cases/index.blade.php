<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">My Cases</h1>
        <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-200/80">
            Case yang sudah di-assign, plus aplikasi yang masih pending approval admin.
        </p>
    </div>

    {{-- Pending Applications --}}
    <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-6 dark:border-white/15 dark:bg-white/5">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-lg font-semibold text-zinc-900 dark:text-white">Pending Applications</div>
                <div class="mt-1 text-sm text-zinc-700 dark:text-zinc-200/80">
                    Menunggu admin approve.
                </div>
            </div>

            <span
                class="inline-flex items-center rounded-full border border-[color:var(--brand-gold)]/35 bg-[color:var(--brand-gold)]/15 px-3 py-1 text-xs font-semibold text-[color:var(--brand-gold)]">
                {{ $pendingCount }} pending
            </span>
        </div>

        <div class="mt-5 space-y-3">
            @forelse($pendingApps as $app)
                <div class="rounded-3xl border border-zinc-300/70 bg-white/70 p-4 dark:border-white/15 dark:bg-black/20">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-zinc-900 dark:text-white">
                                {{ $app->legalCase?->title }}
                            </div>
                            <div class="mt-1 text-xs text-zinc-600 dark:text-zinc-300">
                                {{ $app->legalCase?->case_no }} • {{ $app->legalCase?->category ?? '-' }} •
                                Client: {{ $app->legalCase?->client?->name ?? '-' }}
                            </div>
                        </div>

                        <span
                            class="inline-flex items-center rounded-full border border-[color:var(--brand-gold)]/35 bg-[color:var(--brand-gold)]/15 px-3 py-1 text-xs font-semibold text-[color:var(--brand-gold)]">
                            Pending Approval
                        </span>
                    </div>
                </div>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-zinc-300/70 bg-white/60 p-6 text-center text-sm text-zinc-600
                            dark:border-white/15 dark:bg-black/20 dark:text-zinc-400">
                    Tidak ada pending application.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Assigned Cases --}}
    <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-6 dark:border-white/15 dark:bg-white/5">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-lg font-semibold text-zinc-900 dark:text-white">Assigned Cases</div>
                <div class="mt-1 text-sm text-zinc-700 dark:text-zinc-200/80">
                    Case yang sudah resmi ditangani oleh Anda.
                </div>
            </div>
        </div>

        <div class="mt-5 space-y-3">
            @forelse($cases as $c)
                {{-- Nanti kita bikin route lawyer.cases.show sendiri, untuk sementara lihat admin case detail --}}
                <a href="{{ route('lawyer.cases.show', $c->id) }}"
                    class="block rounded-3xl border border-zinc-300/70 bg-white/70 p-4 hover:bg-white transition
                          dark:border-white/15 dark:bg-black/20 dark:hover:bg-white/5">
                    <div class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $c->title }}</div>
                    <div class="mt-1 text-xs text-zinc-600 dark:text-zinc-300">
                        {{ $c->case_no }} • {{ $c->category ?? '-' }} • Client: {{ $c->client?->name ?? '-' }}
                    </div>
                </a>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-zinc-300/70 bg-white/60 p-8 text-center text-sm text-zinc-600
                            dark:border-white/15 dark:bg-black/20 dark:text-zinc-400">
                    Belum ada case yang di-assign.
                </div>
            @endforelse
        </div>

        <div class="mt-4 px-2">
            {{ $cases->links() }}
        </div>
    </div>
</div>
