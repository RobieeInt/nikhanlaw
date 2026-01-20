<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Lawyer Dashboard</h1>
        <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-200/80">Ringkasan pekerjaan Anda.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-5 dark:border-white/15 dark:bg-white/5">
            <div class="text-xs text-zinc-600 dark:text-zinc-300">My Active Cases</div>
            <div class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $myCases }}</div>
        </div>

        <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-5 dark:border-white/15 dark:bg-white/5">
            <div class="text-xs text-zinc-600 dark:text-zinc-300">Pending Applications</div>
            <div class="mt-2 text-2xl font-semibold" style="color: var(--brand-gold);">{{ $pendingApplications }}</div>
        </div>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('lawyer.cases.pool') }}"
            class="rounded-2xl px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition
                  ring-1 ring-black/10 dark:ring-white/10"
            style="background: linear-gradient(135deg, #8E0C12, #C39A3A);">
            Lihat Case Pool →
        </a>

        <a href="{{ route('lawyer.cases.index') }}"
            class="rounded-2xl border border-zinc-300/70 bg-white/70 px-4 py-2.5 text-sm font-semibold text-zinc-900
                  hover:bg-white transition dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
            My Cases
        </a>
    </div>
</div>
