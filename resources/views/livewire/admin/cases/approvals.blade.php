<div class="space-y-6">
    @if (session('toast'))
        <div
            class="rounded-2xl border border-zinc-300/70 bg-white/80 px-4 py-3 text-sm text-zinc-800
                    dark:border-white/15 dark:bg-white/5 dark:text-zinc-100">
            {{ session('toast') }}
        </div>
    @endif

    <div>
        <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Approvals</h1>
        <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-200/80">
            Lawyer applications yang menunggu approval.
        </p>
    </div>

    <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-4 dark:border-white/15 dark:bg-white/5">
        <div class="space-y-3">
            @forelse($apps as $a)
                <div class="rounded-3xl border border-zinc-300/70 bg-white/70 p-4 dark:border-white/15 dark:bg-black/20">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-zinc-900 dark:text-white">
                                {{ $a->legalCase?->title }}
                            </div>
                            <div class="mt-1 text-xs text-zinc-600 dark:text-zinc-300">
                                {{ $a->legalCase?->case_no }} • Client: {{ $a->legalCase?->client?->name ?? '-' }}
                            </div>
                            <div class="mt-1 text-xs text-zinc-600 dark:text-zinc-300">
                                Lawyer: <span class="font-semibold">{{ $a->lawyer?->name }}</span>
                                ({{ $a->lawyer?->email }})
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="approve({{ $a->id }})"
                                wire:loading.attr="disabled" wire:target="approve({{ $a->id }})"
                                class="rounded-2xl px-4 py-2 text-sm font-semibold text-white shadow-sm disabled:opacity-60"
                                style="background: linear-gradient(135deg, #8E0C12, #C39A3A);">
                                Approve
                            </button>

                            <button type="button" wire:click="reject({{ $a->id }})" wire:loading.attr="disabled"
                                wire:target="reject({{ $a->id }})"
                                class="rounded-2xl border border-red-500/30 bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-500/15 transition disabled:opacity-60 dark:text-red-200">
                                Reject
                            </button>

                            <a href="{{ route('admin.cases.show', $a->legal_case_id) }}"
                                class="rounded-2xl border border-zinc-300/70 bg-white/70 px-4 py-2 text-sm font-semibold text-zinc-900 hover:bg-white transition
                                      dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-zinc-300/70 bg-white/60 p-8 text-center text-sm text-zinc-600
                            dark:border-white/15 dark:bg-black/20 dark:text-zinc-400">
                    Tidak ada approvals.
                </div>
            @endforelse
        </div>

        <div class="mt-4 px-2">
            {{ $apps->links() }}
        </div>
    </div>
</div>
