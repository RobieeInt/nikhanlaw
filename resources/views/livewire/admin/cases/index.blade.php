<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Admin Case Inbox</h1>
            <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-200/80">
                Review case yang masuk, ubah status, dan assign lawyer.
            </p>
        </div>
    </div>

    <div class="grid gap-3 md:grid-cols-4">
        <div class="md:col-span-2">
            <input wire:model.live.debounce.350ms="q" type="text"
                class="w-full rounded-2xl border border-zinc-300/70 bg-white/80 px-4 py-3 text-sm text-zinc-900 outline-none
                          placeholder:text-zinc-400
                          focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                          dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:placeholder:text-zinc-500"
                placeholder="Cari: case no / judul / kategori..." />
        </div>

        <div>
            <select wire:model.live="status"
                class="w-full rounded-2xl border border-zinc-300/70 bg-white/80 px-4 py-3 text-sm text-zinc-900 outline-none
                           focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                           dark:border-white/15 dark:bg-white/5 dark:text-zinc-100">
                <option value="submitted">Submitted</option>
                <option value="need_info">Need Info</option>
                <option value="qualified">Qualified</option>
                <option value="assigned">Assigned</option>
                <option value="active">Active</option>
                <option value="resolved">Resolved</option>
                <option value="closed">Closed</option>
                <option value="rejected">Rejected</option>
                <option value="cancelled">Cancelled</option>
                <option value="all">All</option>
            </select>
        </div>

        <div>
            <select wire:model.live="type"
                class="w-full rounded-2xl border border-zinc-300/70 bg-white/80 px-4 py-3 text-sm text-zinc-900 outline-none
                           focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                           dark:border-white/15 dark:bg-white/5 dark:text-zinc-100">
                <option value="all">All type</option>
                <option value="consultation">Consultation</option>
                <option value="non_litigation">Non Litigation</option>
                <option value="litigation">Litigation</option>
            </select>
        </div>
    </div>

    <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-4 dark:border-white/15 dark:bg-white/5">
        @if ($cases->count() === 0)
            <div
                class="rounded-2xl border border-dashed border-zinc-300/70 bg-white/60 p-8 text-center text-sm text-zinc-600
                        dark:border-white/15 dark:bg-black/20 dark:text-zinc-400">
                Tidak ada case sesuai filter.
            </div>
        @else
            <div class="space-y-3">
                @foreach ($cases as $c)
                    <a href="{{ route('admin.cases.show', $c->id) }}"
                        class="block rounded-3xl border border-zinc-300/70 bg-white/70 p-4 hover:bg-white transition
                              dark:border-white/15 dark:bg-black/20 dark:hover:bg-white/5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-semibold text-zinc-900 dark:text-white">
                                    {{ $c->title }}
                                </div>
                                <div class="mt-1 text-xs text-zinc-600 dark:text-zinc-300">
                                    {{ $c->case_no }} •
                                    {{ $c->category ?? '-' }} •
                                    {{ $c->client?->name ?? '-' }} •
                                    {{ $c->created_at->format('d M Y') }}
                                </div>
                                <div class="mt-1 text-xs text-zinc-600 dark:text-zinc-300">
                                    Lawyer: {{ $c->lawyer?->name ?? '—' }}
                                </div>
                            </div>

                            <span
                                class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $this->badgeClass($c->status) }}">
                                {{ $this->statusLabel($c->status) }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-4 px-2">
                {{ $cases->links() }}
            </div>
        @endif
    </div>
</div>
