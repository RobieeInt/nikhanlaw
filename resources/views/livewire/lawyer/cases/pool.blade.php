<div class="space-y-6">
    @if (session('toast'))
        <div
            class="rounded-2xl border border-zinc-300/70 bg-white/80 px-4 py-3 text-sm text-zinc-800
                    dark:border-white/15 dark:bg-white/5 dark:text-zinc-100">
            {{ session('toast') }}
        </div>
    @endif

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Case Pool</h1>
            <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-200/80">
                Case yang eligible untuk di-apply (status: qualified) dan belum pernah Anda apply.
            </p>
        </div>

        <input wire:model.live.debounce.350ms="q" type="text"
            class="w-full sm:w-80 rounded-2xl border border-zinc-300/70 bg-white/80 px-4 py-3 text-sm text-zinc-900 outline-none
                   placeholder:text-zinc-400
                   focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                   dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:placeholder:text-zinc-500"
            placeholder="Cari case..." />
    </div>

    <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-4 dark:border-white/15 dark:bg-white/5">
        <div class="space-y-3">
            @forelse($cases as $c)
                <div
                    class="rounded-3xl border border-zinc-300/70 bg-white/70 p-4 dark:border-white/15 dark:bg-black/20">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $c->title }}</div>
                            <div class="mt-1 text-xs text-zinc-600 dark:text-zinc-300">
                                {{ $c->case_no }} • {{ $c->category ?? '-' }} • Client: {{ $c->client?->name ?? '-' }}
                            </div>
                        </div>

                        <button type="button" wire:click="apply({{ $c->id }})" wire:loading.attr="disabled"
                            wire:target="apply({{ $c->id }})"
                            class="rounded-2xl px-4 py-2 text-sm font-semibold text-white shadow-sm disabled:opacity-60"
                            style="background: linear-gradient(135deg, #8E0C12, #C39A3A);">
                            <span wire:loading.remove wire:target="apply({{ $c->id }})">Apply</span>
                            <span wire:loading wire:target="apply({{ $c->id }})">...</span>
                        </button>
                    </div>
                </div>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-zinc-300/70 bg-white/60 p-8 text-center text-sm text-zinc-600
                            dark:border-white/15 dark:bg-black/20 dark:text-zinc-400">
                    Tidak ada case di pool.
                </div>
            @endforelse
        </div>

        <div class="mt-4 px-2">
            {{ $cases->links() }}
        </div>
    </div>
</div>
