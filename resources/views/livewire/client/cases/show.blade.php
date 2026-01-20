<div class="space-y-6">
    {{-- Toast --}}
    @if (session('toast'))
        <div
            class="rounded-2xl border border-zinc-300/70 bg-white/80 px-4 py-3 text-sm text-zinc-800
                    dark:border-white/15 dark:bg-white/5 dark:text-zinc-100">
            {{ session('toast') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <div class="text-xs text-zinc-600 dark:text-zinc-400">{{ $legalCase->case_no }}</div>
            <h1 class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $legalCase->title }}</h1>
            <div class="mt-2 text-sm text-zinc-700 dark:text-zinc-200/80">
                @if ($legalCase->category)
                    <span class="font-semibold">Kategori:</span> {{ $legalCase->category }} •
                @endif
                <span class="font-semibold">Jenis:</span> {{ ucfirst(str_replace('_', ' ', $legalCase->type)) }}
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('client.cases.index') }}"
                class="inline-flex items-center justify-center rounded-2xl border border-zinc-300/70 bg-white/70 px-4 py-2.5 text-sm font-semibold text-zinc-900 hover:bg-white transition
                      dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
                ← Kembali
            </a>

            <button type="button" wire:click="toggleEdit"
                class="inline-flex items-center justify-center rounded-2xl border border-zinc-300/70 bg-white/70 px-4 py-2.5 text-sm font-semibold text-zinc-900 hover:bg-white transition
                       dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
                {{ $editMode ? 'Tutup Edit' : 'Edit Case' }}
            </button>

            <button type="button" wire:click="$set('confirmDelete', true)"
                class="inline-flex items-center justify-center rounded-2xl border border-red-500/30 bg-red-500/10 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-500/15 transition
                       dark:text-red-200">
                Hapus Case
            </button>
        </div>
    </div>

    {{-- Main grid --}}
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Summary / Edit --}}
        <div
            class="lg:col-span-2 rounded-3xl border border-zinc-300/70 bg-white/80 p-6 dark:border-white/15 dark:bg-white/5">
            <div class="flex items-center justify-between gap-3">
                <div class="text-sm font-semibold text-zinc-900 dark:text-white">Kronologi / Ringkasan</div>

                @if ($editMode)
                    <button type="button" wire:click="saveCase" wire:loading.attr="disabled" wire:target="saveCase"
                        class="group relative rounded-2xl px-4 py-2 text-sm font-semibold text-white disabled:opacity-60
                               shadow-md hover:shadow-lg transition
                               focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-gold)]/50
                               ring-1 ring-black/10 dark:ring-white/10"
                        style="background: linear-gradient(135deg, #8E0C12, #C39A3A);">
                        <span wire:loading.remove wire:target="saveCase"
                            style="text-shadow: 0 1px 2px rgba(0,0,0,.35);">Simpan</span>
                        <span wire:loading wire:target="saveCase"
                            style="text-shadow: 0 1px 2px rgba(0,0,0,.35);">Menyimpan...</span>
                    </button>
                @endif
            </div>

            @if (!$editMode)
                <div class="mt-4 whitespace-pre-line text-sm leading-relaxed text-zinc-700 dark:text-zinc-200/80">
                    {{ $legalCase->summary }}
                </div>
            @else
                <div class="mt-4 space-y-4">
                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Judul</label>
                            <input type="text" wire:model.defer="title"
                                class="mt-2 w-full rounded-2xl border border-zinc-300/70 bg-white px-4 py-3 text-sm text-zinc-900 outline-none
                                       placeholder:text-zinc-400
                                       focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                                       dark:border-white/15 dark:bg-black/30 dark:text-zinc-100 dark:placeholder:text-zinc-500"
                                placeholder="Judul case" />
                            @error('title')
                                <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Kategori</label>
                            <input type="text" wire:model.defer="category"
                                class="mt-2 w-full rounded-2xl border border-zinc-300/70 bg-white px-4 py-3 text-sm text-zinc-900 outline-none
                                       focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                                       dark:border-white/15 dark:bg-black/30 dark:text-zinc-100"
                                placeholder="Perdata / Pidana / Keluarga / ..." />
                            @error('category')
                                <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Jenis</label>
                            <select wire:model.defer="type"
                                class="mt-2 w-full rounded-2xl border border-zinc-300/70 bg-white px-4 py-3 text-sm text-zinc-900 outline-none
                                       focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                                       dark:border-white/15 dark:bg-black/30 dark:text-zinc-100">
                                <option value="consultation">Konsultasi</option>
                                <option value="non_litigation">Non-Litigasi (Dokumen / Negosiasi)</option>
                                <option value="litigation">Litigasi (Pengadilan)</option>
                            </select>
                            @error('type')
                                <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Kronologi /
                            Ringkasan</label>
                        <textarea wire:model.defer="summary" rows="8"
                            class="mt-2 w-full rounded-2xl border border-zinc-300/70 bg-white px-4 py-3 text-sm text-zinc-900 outline-none
                                   focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                                   dark:border-white/15 dark:bg-black/30 dark:text-zinc-100"
                            placeholder="Tulis kronologi, pihak terkait, tanggal penting, tujuan Anda..."></textarea>
                        @error('summary')
                            <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-xs text-zinc-600 dark:text-zinc-400">
                        Catatan: edit hanya tersedia saat status <span class="font-semibold">Submitted / Need
                            Info</span>.
                    </div>
                </div>
            @endif
        </div>

        {{-- Status --}}
        <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-6 dark:border-white/15 dark:bg-white/5">
            <div class="text-sm font-semibold text-zinc-900 dark:text-white">Status Case</div>

            <div class="mt-3 text-base font-semibold" style="color: var(--brand-gold);">
                {{ $this->statusLabel($legalCase->status) }}
            </div>

            <div class="mt-4 text-xs text-zinc-600 dark:text-zinc-400">
                Diajukan:
                {{ $legalCase->submitted_at?->format('d M Y, H:i') ?? $legalCase->created_at->format('d M Y, H:i') }}
            </div>

            <div class="mt-4 text-xs text-zinc-600 dark:text-zinc-400">
                File & delete case hanya bisa saat status <span class="font-semibold">Submitted / Need Info</span>.
            </div>
        </div>
    </div>

    {{-- Files (SECURE + THUMB + INLINE PREVIEW) --}}
    <div x-data="filePreviewModal()"
        class="rounded-3xl border border-zinc-300/70 bg-white/80 p-6 dark:border-white/15 dark:bg-white/5">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="text-sm font-semibold text-zinc-900 dark:text-white">Dokumen Pendukung</div>
                <div class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">{{ $legalCase->files->count() }} file</div>
            </div>

            {{-- Upload tambahan --}}
            <form wire:submit="addFiles" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <input type="file" multiple wire:model="newFiles" accept=".pdf,.jpg,.jpeg,.png"
                    class="block w-full sm:max-w-xs text-sm
                           file:mr-3 file:rounded-xl file:border-0 file:px-4 file:py-2 file:text-sm file:font-semibold
                           file:bg-[color:var(--brand-gold)]/15 file:text-[color:var(--brand-gold)]
                           hover:file:bg-[color:var(--brand-gold)]/20
                           dark:file:bg-white/10 dark:file:text-zinc-100" />

                <button type="submit" wire:loading.attr="disabled" wire:target="addFiles,newFiles"
                    class="group relative rounded-2xl px-4 py-2 text-sm font-semibold text-white disabled:opacity-60
                           shadow-md hover:shadow-lg transition
                           focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-gold)]/50
                           ring-1 ring-black/10 dark:ring-white/10"
                    style="background: linear-gradient(135deg, #8E0C12, #C39A3A);">
                    <span class="sm:hidden" wire:loading.remove wire:target="addFiles"
                        style="text-shadow: 0 1px 2px rgba(0,0,0,.35);">Upload</span>
                    <span class="hidden sm:inline" wire:loading.remove wire:target="addFiles"
                        style="text-shadow: 0 1px 2px rgba(0,0,0,.35);">Upload File Terpilih</span>
                    <span wire:loading wire:target="addFiles,newFiles"
                        style="text-shadow: 0 1px 2px rgba(0,0,0,.35);">Uploading...</span>
                </button>
            </form>
        </div>

        @error('newFiles')
            <div class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
        @enderror
        @error('newFiles.*')
            <div class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
        @enderror

        <div class="mt-5 space-y-3">
            @forelse($legalCase->files as $file)
                @php
                    $mime = strtolower($file->mime_type ?? '');
                    $isImage = (bool) ($file->is_image ?? false) || str_starts_with($mime, 'image/');
                    $isPdf = $mime === 'application/pdf' || str_ends_with(strtolower($file->original_name), '.pdf');

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
                    $thumbUrl = $file->thumb_path
                        ? \Illuminate\Support\Facades\URL::temporarySignedRoute(
                            'case-files.thumb',
                            now()->addMinutes(15),
                            ['caseFile' => $file->id],
                        )
                        : null;
                @endphp

                <div wire:key="case-file-{{ $file->id }}"
                    class="rounded-2xl border border-zinc-300/70 bg-white/70 p-4 dark:border-white/15 dark:bg-black/20">

                    {{-- top --}}
                    <div class="flex items-start gap-3">
                        <div class="shrink-0">
                            @if ($isImage && $thumbUrl)
                                <button type="button"
                                    @click="openPreview({ url: '{{ $viewUrl }}', type: 'image', name: @js($file->original_name) })"
                                    class="rounded-xl ring-1 ring-black/10 dark:ring-white/10 overflow-hidden">
                                    <img src="{{ $thumbUrl }}" alt="" class="h-12 w-12 object-cover" />
                                </button>
                            @else
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl border border-zinc-300/70 bg-white/70 text-xs font-bold text-zinc-700
                                            dark:border-white/15 dark:bg-white/5 dark:text-zinc-200">
                                    {{ $isPdf ? 'PDF' : 'FILE' }}
                                </div>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="truncate font-semibold text-zinc-900 dark:text-white">
                                {{ $file->original_name }}
                            </div>
                            <div class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">
                                {{ $file->mime_type ?? 'file' }} •
                                {{ $file->size ? number_format($file->size / 1024, 1) . ' KB' : '-' }}
                            </div>

                            @if ($isImage || $isPdf)
                                <button type="button"
                                    @click="openPreview({ url: '{{ $viewUrl }}', type: '{{ $isPdf ? 'pdf' : 'image' }}', name: @js($file->original_name) })"
                                    class="mt-3 inline-flex items-center justify-center rounded-xl border border-zinc-300/70 bg-white/70 px-3 py-1.5 text-xs font-semibold text-zinc-900
                                           hover:bg-white transition
                                           dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
                                    Preview
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- actions --}}
                    <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end">
                        <a href="{{ $viewUrl }}" target="_blank"
                            class="inline-flex items-center justify-center rounded-xl border border-zinc-300/70 bg-white/70 px-3 py-2 text-xs font-semibold text-zinc-900
                                  hover:bg-white transition
                                  dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
                            Open
                        </a>

                        <a href="{{ $downloadUrl }}"
                            class="inline-flex items-center justify-center rounded-xl border border-zinc-300/70 bg-white/70 px-3 py-2 text-xs font-semibold text-zinc-900
                                  hover:bg-white transition
                                  dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
                            Download
                        </a>

                        <button type="button" wire:click="deleteFile({{ $file->id }})"
                            wire:loading.attr="disabled" wire:target="deleteFile({{ $file->id }})"
                            class="inline-flex items-center justify-center rounded-xl border border-red-500/30 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-700
                                   hover:bg-red-500/15 transition disabled:opacity-60 dark:text-red-200">
                            <span wire:loading.remove wire:target="deleteFile({{ $file->id }})">Hapus</span>
                            <span wire:loading wire:target="deleteFile({{ $file->id }})">...</span>
                        </button>
                    </div>
                </div>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-zinc-300/70 bg-white/60 p-6 text-center text-sm text-zinc-600
                            dark:border-white/15 dark:bg-black/20 dark:text-zinc-400">
                    Belum ada dokumen yang di-upload.
                </div>
            @endforelse
        </div>

        {{-- Preview Modal --}}
        <template x-if="open">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/50" @click="close()"></div>

                <div
                    class="relative w-full max-w-5xl rounded-3xl border border-zinc-300/70 bg-white p-4 shadow-xl
                            dark:border-white/15 dark:bg-[#0A0A0B]">
                    <div class="flex items-center justify-between gap-3 px-2 py-2">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-zinc-900 dark:text-white" x-text="name">
                            </div>
                            <div class="mt-0.5 text-xs text-zinc-600 dark:text-zinc-400"
                                x-text="type === 'pdf' ? 'PDF Preview' : 'Image Preview'"></div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a :href="url" target="_blank"
                                class="rounded-2xl border border-zinc-300/70 bg-white/70 px-3 py-2 text-xs font-semibold text-zinc-900 hover:bg-white transition
                                      dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
                                Open
                            </a>
                            <button type="button" @click="close()"
                                class="rounded-2xl border border-zinc-300/70 bg-white/70 px-3 py-2 text-xs font-semibold text-zinc-900 hover:bg-white transition
                                      dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
                                Tutup
                            </button>
                        </div>
                    </div>

                    <div
                        class="mt-3 rounded-2xl border border-zinc-300/70 bg-zinc-50 p-2 dark:border-white/15 dark:bg-black/30">
                        <template x-if="type === 'image'">
                            <div class="flex justify-center">
                                <img :src="url" alt=""
                                    class="max-h-[75vh] w-auto rounded-xl object-contain ring-1 ring-black/10 dark:ring-white/10">
                            </div>
                        </template>

                        <template x-if="type === 'pdf'">
                            <div class="h-[75vh]">
                                <iframe :src="url" class="h-full w-full rounded-xl bg-white dark:bg-black"
                                    frameborder="0"></iframe>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        <script>
            function filePreviewModal() {
                return {
                    open: false,
                    url: '',
                    type: 'image',
                    name: '',
                    openPreview(payload) {
                        this.url = payload.url;
                        this.type = payload.type;
                        this.name = payload.name || 'Preview';
                        this.open = true;
                    },
                    close() {
                        this.open = false;
                        this.url = '';
                        this.name = '';
                        this.type = 'image';
                    },
                }
            }
        </script>
    </div>

    {{-- Timeline --}}
    <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-6 dark:border-white/15 dark:bg-white/5">
        <div class="flex items-center justify-between">
            <div class="text-sm font-semibold text-zinc-900 dark:text-white">Timeline</div>
            <div class="text-xs text-zinc-600 dark:text-zinc-400">
                {{ $legalCase->events->count() }} event
            </div>
        </div>

        <div class="mt-5 space-y-4">
            @forelse($legalCase->events as $ev)
                <div class="flex items-start gap-3">
                    <div class="mt-1.5 h-3 w-3 rounded-full"
                        style="background: {{ in_array($ev->event, ['case_created', 'files_added']) ? 'var(--brand-gold)' : 'var(--brand-red)' }};">
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
                            <div class="mt-1 text-sm text-zinc-700 dark:text-zinc-200/80">
                                {{ $ev->note }}
                            </div>
                        @endif

                        @if ($ev->status_from || $ev->status_to)
                            <div class="mt-2 text-xs text-zinc-600 dark:text-zinc-400">
                                Status:
                                <span class="font-semibold">{{ $ev->status_from ?? '-' }}</span>
                                →
                                <span class="font-semibold">{{ $ev->status_to ?? '-' }}</span>
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

    {{-- Confirm delete modal --}}
    @if ($confirmDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40" wire:click="$set('confirmDelete', false)"></div>

            <div
                class="relative w-full max-w-md rounded-3xl border border-zinc-300/70 bg-white/95 p-6 shadow-xl
                        dark:border-white/15 dark:bg-[#0A0A0B]">
                <div class="text-lg font-semibold text-zinc-900 dark:text-white">Hapus case ini?</div>
                <div class="mt-2 text-sm text-zinc-700 dark:text-zinc-200/80">
                    Ini akan menghapus case beserta semua dokumen yang sudah di-upload. Tidak bisa dibatalkan.
                </div>

                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" wire:click="$set('confirmDelete', false)"
                        class="rounded-2xl border border-zinc-300/70 bg-white/70 px-4 py-2 text-sm font-semibold text-zinc-900 hover:bg-white transition
                               dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
                        Batal
                    </button>

                    <button type="button" wire:click="deleteCase" wire:loading.attr="disabled"
                        wire:target="deleteCase"
                        class="rounded-2xl border border-red-500/30 bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition disabled:opacity-60">
                        <span wire:loading.remove wire:target="deleteCase">Ya, Hapus</span>
                        <span wire:loading wire:target="deleteCase">Menghapus...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
