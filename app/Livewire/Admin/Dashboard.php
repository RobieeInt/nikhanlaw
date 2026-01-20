<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public int $submitted = 0;
    public int $needInfo = 0;
    public int $qualified = 0;
    public int $assigned = 0;
    public int $active = 0;

    /** @var array<int, array<string, mixed>> */
    public array $recentCases = [];

    /** @var array<int, array<string, mixed>> */
    public array $recentEvents = [];

    public function mount(): void
    {
        // Guard kalau tabel belum ada (biar ga error pas migrate belum lengkap)
        if (!DB::getSchemaBuilder()->hasTable('legal_cases')) return;

        $this->submitted = (int) DB::table('legal_cases')->where('status', 'submitted')->count();
        $this->needInfo  = (int) DB::table('legal_cases')->where('status', 'need_info')->count();
        $this->qualified = (int) DB::table('legal_cases')->where('status', 'qualified')->count();
        $this->assigned  = (int) DB::table('legal_cases')->where('status', 'assigned')->count();
        $this->active    = (int) DB::table('legal_cases')->where('status', 'active')->count();

        $this->recentCases = DB::table('legal_cases')
            ->leftJoin('users as c', 'c.id', '=', 'legal_cases.client_id')
            ->leftJoin('users as l', 'l.id', '=', 'legal_cases.assigned_lawyer_id')
            ->select(
                'legal_cases.id',
                'legal_cases.case_no',
                'legal_cases.title',
                'legal_cases.status',
                'legal_cases.type',
                'legal_cases.category',
                'legal_cases.created_at',
                DB::raw('c.name as client_name'),
                DB::raw('l.name as lawyer_name'),
            )
            ->orderByDesc('legal_cases.id')
            ->limit(8)
            ->get()
            ->map(fn($r) => (array) $r)
            ->toArray();

        if (DB::getSchemaBuilder()->hasTable('case_events')) {
            $this->recentEvents = DB::table('case_events')
                ->leftJoin('legal_cases', 'legal_cases.id', '=', 'case_events.legal_case_id')
                ->leftJoin('users as a', 'a.id', '=', 'case_events.actor_id')
                ->select(
                    'case_events.id',
                    'case_events.event',
                    'case_events.note',
                    'case_events.status_from',
                    'case_events.status_to',
                    'case_events.created_at',
                    DB::raw('legal_cases.id as case_id'),
                    DB::raw('legal_cases.case_no as case_no'),
                    DB::raw('a.name as actor_name'),
                    'case_events.actor_role'
                )
                ->orderByDesc('case_events.id')
                ->limit(10)
                ->get()
                ->map(fn($r) => (array) $r)
                ->toArray();
        }
    }

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
        return view('livewire.admin.dashboard')
            ->layout('components.layouts.app');
    }
}
