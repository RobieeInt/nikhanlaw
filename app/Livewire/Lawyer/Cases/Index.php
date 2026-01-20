<?php

namespace App\Livewire\Lawyer\Cases;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LegalCase;
use App\Models\CaseApplication;

class Index extends Component
{
    use WithPagination;

    public int $pendingCount = 0;

    /** @var array<int, \App\Models\CaseApplication> */
    public $pendingApps;

    public function mount(): void
    {
        $lawyerId = auth()->id();

        $this->pendingApps = CaseApplication::query()
            ->with(['legalCase.client:id,name,email'])
            ->where('lawyer_id', $lawyerId)
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->get();

        $this->pendingCount = $this->pendingApps->count();
    }

    public function render()
    {
        $lawyerId = auth()->id();

        $cases = LegalCase::query()
            ->with(['client:id,name,email'])
            ->where('assigned_lawyer_id', $lawyerId)
            ->orderByDesc('id')
            ->paginate(12);

        return view('livewire.lawyer.cases.index', compact('cases'))
            ->layout('components.layouts.app');
    }
}
