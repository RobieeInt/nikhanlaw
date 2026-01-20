<div class="mx-auto max-w-4xl p-6">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Ajukan Konsultasi / Case</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">Ceritakan masalah Anda secara ringkas dan jelas.</p>
        </div>
        <a href="{{ route('client.cases.index') }}"
            class="rounded-2xl border border-zinc-200 bg-white/60 px-4 py-2 text-sm font-semibold text-zinc-800 hover:bg-white dark:border-white/10 dark:bg-black/25 dark:text-zinc-100 dark:hover:bg-white/10">
            Kembali
        </a>
    </div>

    <div class="mt-6 rounded-3xl border border-zinc-200 bg-white/60 p-6 dark:border-white/10 dark:bg-black/25">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="text-sm font-semibold">Judul</label>
                <input wire:model.defer="title" type="text"
                    class="mt-2 w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 dark:border-white/10 dark:bg-white/5"
                    placeholder="Contoh: Sengketa kontrak kerja sama" />
                @error('title')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="text-sm font-semibold">Kategori</label>
                <input wire:model.defer="category" type="text"
                    class="mt-2 w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 dark:border-white/10 dark:bg-white/5"
                    placeholder="Perdata / Pidana / Keluarga / Ketenagakerjaan..." />
                @error('category')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="text-sm font-semibold">Jenis Layanan</label>
                <select wire:model.defer="type"
                    class="mt-2 w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 dark:border-white/10 dark:bg-white/5">
                    <option value="consultation">Konsultasi</option>
                    <option value="non_litigation">Non-Litigasi (Dokumen / Negosiasi)</option>
                    <option value="litigation">Litigasi (Pengadilan)</option>
                </select>
                @error('type')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="text-sm font-semibold">Bukti / Dokumen (opsional)</label>
                <input wire:model="files" type="file" multiple
                    class="mt-2 w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm dark:border-white/10 dark:bg-white/5" />
                @error('files.*')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
                <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Max 5MB per file.</div>
            </div>
        </div>

        <div class="mt-4">
            <label class="text-sm font-semibold">Kronologi / Cerita Singkat</label>
            <textarea wire:model.defer="summary" rows="7"
                class="mt-2 w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 dark:border-white/10 dark:bg-white/5"
                placeholder="Tulis kronologi, pihak terkait, tanggal penting, dan apa tujuan Anda."></textarea>
            @error('summary')
                <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                Dengan mengirim case, Anda menyetujui bahwa ini bukan nasihat hukum final sebelum konsultasi lanjutan.
            </div>

            <button wire:click="save" wire:loading.attr="disabled"
                class="inline-flex items-center justify-center rounded-2xl px-6 py-3 text-sm font-semibold text-white disabled:opacity-60"
                style="background: linear-gradient(135deg, var(--brand-red), var(--brand-gold));">
                <span wire:loading.remove>Kirim Case</span>
                <span wire:loading>Mengirim...</span>
            </button>
        </div>
    </div>
</div>
