<?php

namespace App\Livewire\Admin\Cases;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LegalCase;

class Index extends Component
{
    use WithPagination;

    public string $q = '';
    public string $status = 'submitted';
    public string $type = 'all';

    protected $queryString = [
        'q' => ['except' => ''],
        'status' => ['except' => 'submitted'],
        'type' => ['except' => 'all'],
        'page' => ['except' => 1],
    ];

    public function updatingQ(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }
    public function updatingType(): void { $this->resetPage(); }

    public function statusLabel(string $status): string
    {
        return match ($status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'need_info' => 'Need Info',
            'qualified' => 'Qualified',
            'assigned' => 'Assigned',
            'active' => 'Active',
            'waiting_client' => 'Waiting Client',
            'waiting_external' => 'Waiting External',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    public function badgeClass(string $status): string
    {
        return match ($status) {
            'submitted' => 'border-zinc-300/70 bg-white/70 text-zinc-800 dark:border-white/15 dark:bg-white/5 dark:text-zinc-100',
            'need_info', 'waiting_client' => 'border-[color:var(--brand-gold)]/35 bg-[color:var(--brand-gold)]/15 text-[color:var(--brand-gold)]',
            'qualified' => 'border-indigo-500/30 bg-indigo-500/15 text-indigo-700 dark:text-indigo-200',
            'assigned', 'active' => 'border-emerald-500/30 bg-emerald-500/15 text-emerald-700 dark:text-emerald-200',
            'rejected', 'cancelled' => 'border-red-500/30 bg-red-500/15 text-red-700 dark:text-red-200',
            'resolved', 'closed' => 'border-emerald-500/30 bg-emerald-500/15 text-emerald-700 dark:text-emerald-200',
            default => 'border-zinc-300/70 bg-white/70 text-zinc-800 dark:border-white/15 dark:bg-white/5 dark:text-zinc-100',
        };
    }

    public function render()
    {
        $query = LegalCase::query()
            ->with(['client:id,name,email', 'lawyer:id,name,email'])
            ->orderByDesc('id');

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if ($this->type !== 'all') {
            $query->where('type', $this->type);
        }

        if ($this->q !== '') {
            $q = '%'.$this->q.'%';
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', $q)
                  ->orWhere('case_no', 'like', $q)
                  ->orWhere('category', 'like', $q);
            });
        }

        $cases = $query->paginate(12);

        return view('livewire.admin.cases.index', compact('cases'))
            ->layout('components.layouts.app');
    }
}
