<?php

namespace App\Livewire\Client\Cases;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public string $q = '';
    public string $status = 'all';

    protected $queryString = [
        'q' => ['except' => ''],
        'status' => ['except' => 'all'],
        'page' => ['except' => 1],
    ];

    public function updatingQ(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }

    public function statusLabel(string $status): string
    {
        return match ($status) {
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

    public function statusClasses(string $status): string
    {
        return match ($status) {
            'active', 'assigned' =>
                'border-emerald-500/30 bg-emerald-500/15 text-emerald-700 dark:text-emerald-200',
            'need_info', 'waiting_client' =>
                'border-[color:var(--brand-gold)]/35 bg-[color:var(--brand-gold)]/15 text-[color:var(--brand-gold)]',
            'resolved', 'closed' =>
                'border-emerald-500/30 bg-emerald-500/15 text-emerald-700 dark:text-emerald-200',
            'rejected', 'cancelled' =>
                'border-red-500/30 bg-red-500/15 text-red-700 dark:text-red-200',
            default =>
                'border-zinc-300/70 bg-white/60 text-zinc-700 dark:border-white/15 dark:bg-white/5 dark:text-zinc-200',
        };
    }

    public function render()
    {
        $userId = auth()->id();

        $hasTable = DB::getSchemaBuilder()->hasTable('legal_cases');
        if (!$hasTable) {
            $cases = collect([])->paginate(10);
            return view('livewire.client.cases.index', compact('cases'))
                ->layout('components.layouts.app');
        }

        $query = DB::table('legal_cases')
            ->select('id','case_no','title','status','type','category','created_at')
            ->where('client_id', $userId);

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if ($this->q !== '') {
            $q = '%'.$this->q.'%';
            $query->where(function($w) use ($q) {
                $w->where('title', 'like', $q)
                  ->orWhere('case_no', 'like', $q)
                  ->orWhere('category', 'like', $q);
            });
        }

        $cases = $query->orderByDesc('id')->paginate(10);

        return view('livewire.client.cases.index', compact('cases'))
            ->layout('components.layouts.app');
    }
}
