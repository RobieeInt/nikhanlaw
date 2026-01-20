<?php

namespace App\Livewire\Lawyer;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public int $myCases = 0;
    public int $pendingApplications = 0;

    public function mount(): void
    {
        $uid = auth()->id();

        if (DB::getSchemaBuilder()->hasTable('legal_cases')) {
            $this->myCases = (int) DB::table('legal_cases')
                ->where('assigned_lawyer_id', $uid)
                ->whereIn('status', ['assigned','active','waiting_client','waiting_external'])
                ->count();
        }

        if (DB::getSchemaBuilder()->hasTable('case_applications')) {
            $this->pendingApplications = (int) DB::table('case_applications')
                ->where('lawyer_id', $uid)
                ->where('status', 'pending')
                ->count();
        }
    }

    public function render()
    {
        return view('livewire.lawyer.dashboard')
            ->layout('components.layouts.app');
    }
}
