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
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">User Management</h1>
            <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-200/80">
                Ubah role user menjadi client / lawyer / admin.
            </p>
        </div>
    </div>

    <div class="grid gap-3 md:grid-cols-4">
        <div class="md:col-span-3">
            <input wire:model.live.debounce.350ms="q" type="text"
                class="w-full rounded-2xl border border-zinc-300/70 bg-white/80 px-4 py-3 text-sm text-zinc-900 outline-none
                       placeholder:text-zinc-400
                       focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                       dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:placeholder:text-zinc-500"
                placeholder="Cari nama / email..." />
        </div>

        <div>
            <select wire:model.live="role"
                class="w-full rounded-2xl border border-zinc-300/70 bg-white/80 px-4 py-3 text-sm text-zinc-900 outline-none
                       focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                       dark:border-white/15 dark:bg-white/5 dark:text-zinc-100">
                <option value="all">All</option>
                <option value="client">Client</option>
                <option value="lawyer">Lawyer</option>
                <option value="admin">Admin</option>
            </select>
        </div>
    </div>

    <div class="rounded-3xl border border-zinc-300/70 bg-white/80 p-4 dark:border-white/15 dark:bg-white/5">
        <div class="space-y-3">
            @foreach ($users as $u)
                @php $role = $u->roles->first()?->name ?? 'client'; @endphp

                <div
                    class="rounded-3xl border border-zinc-300/70 bg-white/70 p-4 dark:border-white/15 dark:bg-black/20">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-zinc-900 dark:text-white">
                                {{ $u->name }}
                            </div>
                            <div class="mt-1 text-xs text-zinc-600 dark:text-zinc-300">
                                {{ $u->email }}
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span
                                class="rounded-full border px-3 py-1 text-xs font-semibold
                                @if ($role === 'admin') border-[color:var(--brand-gold)]/35 bg-[color:var(--brand-gold)]/15 text-[color:var(--brand-gold)]
                                @elseif($role === 'lawyer') border-emerald-500/30 bg-emerald-500/15 text-emerald-700 dark:text-emerald-200
                                @else border-zinc-300/70 bg-white/60 text-zinc-700 dark:border-white/15 dark:bg-white/5 dark:text-zinc-200 @endif
                            ">
                                {{ strtoupper($role) }}
                            </span>

                            <button type="button" wire:click="openRoleModal({{ $u->id }})"
                                class="rounded-2xl border border-zinc-300/70 bg-white/70 px-4 py-2 text-sm font-semibold text-zinc-900 hover:bg-white transition
                                       dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
                                Edit Role
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 px-2">
            {{ $users->links() }}
        </div>
    </div>

    {{-- Modal --}}
    @if ($modal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40" wire:click="closeModal"></div>

            <div
                class="relative w-full max-w-md rounded-3xl border border-zinc-300/70 bg-white/95 p-6 shadow-xl
                        dark:border-white/15 dark:bg-[#0A0A0B]">
                <div class="text-lg font-semibold text-zinc-900 dark:text-white">Ubah Role</div>
                <div class="mt-2 text-sm text-zinc-700 dark:text-zinc-200/80">
                    Pilih role baru untuk user.
                </div>

                <div class="mt-4">
                    <select wire:model="selectedRole"
                        class="w-full rounded-2xl border border-zinc-300/70 bg-white/80 px-4 py-3 text-sm text-zinc-900 outline-none
                               focus:ring-2 focus:ring-[color:var(--brand-gold)]/40 focus:border-[color:var(--brand-gold)]/40
                               dark:border-white/15 dark:bg-white/5 dark:text-zinc-100">
                        <option value="client">Client</option>
                        <option value="lawyer">Lawyer</option>
                        <option value="admin">Admin</option>
                    </select>
                    @error('selectedRole')
                        <div class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" wire:click="closeModal"
                        class="rounded-2xl border border-zinc-300/70 bg-white/70 px-4 py-2 text-sm font-semibold text-zinc-900 hover:bg-white transition
                               dark:border-white/15 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10">
                        Batal
                    </button>

                    <button type="button" wire:click="saveRole" wire:loading.attr="disabled" wire:target="saveRole"
                        class="rounded-2xl px-4 py-2 text-sm font-semibold text-white shadow-md hover:shadow-lg transition
                               focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-gold)]/50 ring-1 ring-black/10 dark:ring-white/10 disabled:opacity-60"
                        style="background: linear-gradient(135deg, #8E0C12, #C39A3A);">
                        <span wire:loading.remove wire:target="saveRole">Simpan</span>
                        <span wire:loading wire:target="saveRole">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
