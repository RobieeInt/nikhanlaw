<?php

namespace App\Livewire\Lawyer\Cases;

use Livewire\Component;
use App\Models\LegalCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Show extends Component
{
    public LegalCase $legalCase;

    public function mount(LegalCase $legalCase): void
    {
        // ✅ security: lawyer cuma boleh lihat case assigned ke dia
        if ((int) $legalCase->assigned_lawyer_id !== (int) auth()->id()) {
            throw new NotFoundHttpException();
        }

        $this->legalCase = $legalCase->load(['client', 'files', 'events.actor']);
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

    public function render()
    {
        return view('livewire.lawyer.cases.show')
            ->layout('components.layouts.app');
    }
}
