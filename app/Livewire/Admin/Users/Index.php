<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public string $q = '';
    public string $role = 'all';

    public bool $modal = false;

    public ?int $selectedUserId = null;
    public string $selectedRole = 'client';

    protected $queryString = [
        'q' => ['except' => ''],
        'role' => ['except' => 'all'],
        'page' => ['except' => 1],
    ];

    public function updatingQ(): void { $this->resetPage(); }
    public function updatingRole(): void { $this->resetPage(); }

    public function openRoleModal(int $userId): void
    {
        $user = User::findOrFail($userId);

        $this->selectedUserId = $user->id;

        // ambil role pertama (karena sistem lo 1 user = 1 role utama)
        $current = $user->getRoleNames()->first() ?? 'client';
        $this->selectedRole = $current;

        $this->modal = true;
    }

    public function closeModal(): void
    {
        $this->modal = false;
        $this->selectedUserId = null;
        $this->selectedRole = 'client';
        $this->resetValidation();
    }

    public function saveRole(): void
    {
        $this->validate([
            'selectedUserId' => 'required|integer|exists:users,id',
            'selectedRole' => 'required|in:client,lawyer,admin',
        ]);

        $user = User::findOrFail($this->selectedUserId);

        // safeguard: jangan copot admin terakhir
        if ($user->hasRole('admin') && $this->selectedRole !== 'admin') {
            $adminCount = DB::table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('roles.name', 'admin')
                ->where('model_has_roles.model_type', User::class)
                ->count();

            if ($adminCount <= 1) {
                $this->addError('selectedRole', 'Tidak bisa menghapus admin terakhir.');
                return;
            }
        }

        // enforce 1 role utama: syncRoles
        $user->syncRoles([$this->selectedRole]);

        session()->flash('toast', "Role user {$user->name} diubah jadi {$this->selectedRole}.");
        $this->closeModal();
    }

    public function roleLabel(User $u): string
    {
        return $u->getRoleNames()->first() ?? 'client';
    }

    public function render()
    {
        $query = User::query()->orderByDesc('id');

        if ($this->q !== '') {
            $q = '%'.$this->q.'%';
            $query->where(function($w) use ($q) {
                $w->where('name', 'like', $q)
                  ->orWhere('email', 'like', $q);
            });
        }

        if ($this->role !== 'all') {
            $query->whereHas('roles', fn($r) => $r->where('name', $this->role));
        }

        $users = $query->with('roles')->paginate(12);

        return view('livewire.admin.users.index', compact('users'))
            ->layout('components.layouts.app');
    }
}
