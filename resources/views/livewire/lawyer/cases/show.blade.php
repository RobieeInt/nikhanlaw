<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <div class="text-xs text-zinc-600 dark:text-zinc-400">{{ $legalCase->case_no }}</div>
            <h1 class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $legalCase->title }}</h1>

            <div class="mt-2 text-sm text-zinc-700 dark:text-zinc-200/80">
                <span class="font-semibold">Client:</span> {{ $legalCase->client?->name ?? '-' }}
                @if ($legalCase->client?->email)
                    ({{ $legalCase->client->email }})
                @endif
                • <span class="font-semibold">Kategori:</span> {{ $legalCase->category ?? '-' }}
                • <span class="font-semibold">Type:</span> {{ ucfirst(str_replace('_', ' ', $legalCase->type)) }}
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('lawyer.cases.index') }}"
                class="inline-flex items-center justify-center rounded-2xl border border-zinc-300/70 bg-white/70 px-4 py-2.5 text-sm font-semibold text-zinc-900 hover:bg-white transition
                      dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
                ← My Cases
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div
            class="lg:col-span-2 rounded-3xl border border-zinc-300/70 bg-white/80 p-6 dark:border-white/15 dark:bg-white/5">
            <div class="text-sm font-semibold text-zinc-900 dark:text-white">Kronologi / Ringkasan</div>
            <div class="mt-4 whitespace-pre-line text-sm leading-relaxed text-zinc-700 dark:text-zinc-200/80">
                {{ $legalCase->summary }}
            </div>
        </div>

        <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-6 dark:border-white/15 dark:bg-white/5">
            <div class="text-sm font-semibold text-zinc-900 dark:text-white">Status</div>
            <div class="mt-3 text-base font-semibold" style="color: var(--brand-gold);">
                {{ $this->statusLabel($legalCase->status) }}
            </div>
            <div class="mt-4 text-xs text-zinc-600 dark:text-zinc-400">
                Dibuat: {{ $legalCase->created_at->format('d M Y, H:i') }}
            </div>
        </div>
    </div>

    {{-- Files (secure links) --}}
    <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-6 dark:border-white/15 dark:bg-white/5">
        <div class="text-sm font-semibold text-zinc-900 dark:text-white">Dokumen</div>

        <div class="mt-4 space-y-2">
            @forelse($legalCase->files as $file)
                @php
                    $viewUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                        'case-files.view',
                        now()->addMinutes(15),
                        ['caseFile' => $file->id],
                    );

                    $downloadUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                        'case-files.download',
                        now()->addMinutes(15),
                        ['caseFile' => $file->id],
                    );
                @endphp

                <div
                    class="flex items-center justify-between gap-3 rounded-2xl border border-zinc-300/70 bg-white/70 px-4 py-3
                            dark:border-white/15 dark:bg-black/20">
                    <div class="min-w-0">
                        <div class="truncate font-semibold text-zinc-900 dark:text-white">{{ $file->original_name }}
                        </div>
                        <div class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">
                            {{ $file->mime_type ?? 'file' }} •
                            {{ $file->size ? number_format($file->size / 1024, 1) . ' KB' : '-' }}
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ $viewUrl }}" target="_blank"
                            class="text-xs font-semibold text-[color:var(--brand-red)] hover:underline dark:text-[color:var(--brand-gold)]">
                            Open
                        </a>
                        <a href="{{ $downloadUrl }}"
                            class="rounded-xl border border-zinc-300/70 bg-white/70 px-3 py-1.5 text-xs font-semibold text-zinc-900
                                  hover:bg-white transition dark:border-white/15 dark:bg-white/5 dark:text-zinc-100">
                            Download
                        </a>
                    </div>
                </div>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-zinc-300/70 bg-white/60 p-6 text-center text-sm text-zinc-600
                            dark:border-white/15 dark:bg-black/20 dark:text-zinc-400">
                    Belum ada dokumen.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Timeline --}}
    <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-6 dark:border-white/15 dark:bg-white/5">
        <div class="flex items-center justify-between">
            <div class="text-sm font-semibold text-zinc-900 dark:text-white">Timeline</div>
            <div class="text-xs text-zinc-600 dark:text-zinc-400">{{ $legalCase->events->count() }} event</div>
        </div>

        <div class="mt-5 space-y-4">
            @forelse($legalCase->events as $ev)
                <div class="flex items-start gap-3">
                    <div class="mt-1.5 h-3 w-3 rounded-full"
                        style="background: {{ in_array($ev->event, ['case_created', 'files_added', 'lawyer_assigned', 'lawyer_approved']) ? 'var(--brand-gold)' : 'var(--brand-red)' }};">
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                            <div class="text-sm font-semibold text-zinc-900 dark:text-white">
                                {{ str_replace('_', ' ', strtoupper($ev->event)) }}
                            </div>

                            <span
                                class="rounded-full border border-zinc-300/70 bg-white/60 px-2 py-0.5 text-[11px] font-semibold text-zinc-700
                                         dark:border-white/15 dark:bg-white/5 dark:text-zinc-200">
                                {{ $ev->actor_role ?? 'system' }}
                            </span>

                            <span class="text-xs text-zinc-600 dark:text-zinc-400">
                                {{ $ev->created_at->format('d M Y, H:i') }}
                            </span>
                        </div>

                        @if ($ev->note)
                            <div class="mt-1 text-sm text-zinc-700 dark:text-zinc-200/80">{{ $ev->note }}</div>
                        @endif

                        @if ($ev->status_from || $ev->status_to)
                            <div class="mt-2 text-xs text-zinc-600 dark:text-zinc-400">
                                Status: <span class="font-semibold">{{ $ev->status_from ?? '-' }}</span> → <span
                                    class="font-semibold">{{ $ev->status_to ?? '-' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-zinc-300/70 bg-white/60 p-6 text-center text-sm text-zinc-600
                            dark:border-white/15 dark:bg-black/20 dark:text-zinc-400">
                    Belum ada timeline.
                </div>
            @endforelse
        </div>
    </div>
</div>
