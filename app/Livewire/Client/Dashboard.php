<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public int $totalCases = 0;
    public int $activeCases = 0;
    public int $needActionCases = 0;

    /** @var array<int, array<string, mixed>> */
    public array $recentCases = [];

    public function mount(): void
    {
        $userId = auth()->id();

        // Biar gak error kalau migration legal_cases belum dibuat
        $hasTable = DB::getSchemaBuilder()->hasTable('legal_cases');
        if (!$hasTable) return;

        $this->totalCases = (int) DB::table('legal_cases')
            ->where('client_id', $userId)
            ->count();

        $this->activeCases = (int) DB::table('legal_cases')
            ->where('client_id', $userId)
            ->whereIn('status', ['assigned', 'active', 'waiting_client', 'waiting_external'])
            ->count();

        $this->needActionCases = (int) DB::table('legal_cases')
            ->where('client_id', $userId)
            ->whereIn('status', ['need_info', 'waiting_client'])
            ->count();

        $this->recentCases = DB::table('legal_cases')
            ->select('id', 'case_no', 'title', 'status', 'created_at')
            ->where('client_id', $userId)
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(fn ($r) => (array) $r)
            ->toArray();
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

    public function statusClasses(string $status): string
    {
        // Tailwind: badge class by status
        return match ($status) {
            'active', 'assigned' =>
                'border-emerald-500/30 bg-emerald-500/15 text-emerald-700 dark:text-emerald-200',
            'need_info', 'waiting_client' =>
                'border-[color:var(--brand-gold)]/35 bg-[color:var(--brand-gold)]/15 text-[color:var(--brand-gold)]',
            'submitted', 'qualified' =>
                'border-zinc-300/70 bg-white/60 text-zinc-700 dark:border-white/15 dark:bg-white/5 dark:text-zinc-200',
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
        return view('livewire.client.dashboard')
            ->layout('components.layouts.app');
    }
}
