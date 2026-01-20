<?php

namespace App\Livewire\Admin\Cases;

use Livewire\Component;
use App\Models\LegalCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Support\CaseEventLogger;

class Show extends Component
{
    public LegalCase $legalCase;

    public ?int $assignLawyerId = null;

    public string $status = '';
    public string $adminNote = '';

    public function mount(LegalCase $legalCase): void
    {
        $this->legalCase = $legalCase->load(['client', 'lawyer', 'files', 'events.actor']);
        $this->assignLawyerId = $this->legalCase->assigned_lawyer_id;
        $this->status = (string) $this->legalCase->status;
    }

    public function assignLawyer(): void
    {
        $this->validate([
            'assignLawyerId' => 'required|integer|exists:users,id',
        ]);

        $lawyer = User::query()->findOrFail($this->assignLawyerId);

        // Pastikan role lawyer (biar ga assign admin jadi lawyer)
        if (!method_exists($lawyer, 'hasRole') || !$lawyer->hasRole('lawyer')) {
            $this->addError('assignLawyerId', 'User ini bukan lawyer.');
            return;
        }

        DB::beginTransaction();

        try {
            $oldStatus = $this->legalCase->status;
            $oldLawyerId = $this->legalCase->assigned_lawyer_id;

            $this->legalCase->assigned_lawyer_id = $lawyer->id;

            // auto status update: kalau masih early stage, naik jadi assigned
            if (in_array($oldStatus, ['submitted', 'need_info', 'qualified'], true)) {
                $this->legalCase->status = 'assigned';
                $this->status = 'assigned';
            }

            $this->legalCase->save();

            // event: lawyer assigned
            CaseEventLogger::log(
                $this->legalCase,
                'lawyer_assigned',
                'Admin assign lawyer: '.$lawyer->name,
                $oldStatus,
                $this->legalCase->status,
                ['lawyer_id' => $lawyer->id, 'lawyer_name' => $lawyer->name, 'prev_lawyer_id' => $oldLawyerId]
            );

            // event: status changed (kalau berubah)
            if ($oldStatus !== $this->legalCase->status) {
                CaseEventLogger::log(
                    $this->legalCase,
                    'status_changed',
                    'Status diubah oleh admin saat assign lawyer.',
                    $oldStatus,
                    $this->legalCase->status
                );
            }

            DB::commit();

            $this->legalCase->refresh()->load(['client', 'lawyer', 'files', 'events.actor']);
            session()->flash('toast', 'Lawyer berhasil di-assign.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            session()->flash('toast', 'Gagal assign lawyer.');
        }
    }

    public function updateStatus(): void
    {
        $this->validate([
            'status' => 'required|string|in:submitted,need_info,qualified,assigned,active,waiting_client,waiting_external,resolved,closed,rejected,cancelled',
            'adminNote' => 'nullable|string|max:2000',
        ]);

        DB::beginTransaction();

        try {
            $oldStatus = $this->legalCase->status;

            $this->legalCase->status = $this->status;
            $this->legalCase->save();

            CaseEventLogger::log(
                $this->legalCase,
                'status_changed',
                $this->adminNote ? 'Admin note: '.$this->adminNote : 'Status diubah oleh admin.',
                $oldStatus,
                $this->status,
                $this->adminNote ? ['note' => $this->adminNote] : []
            );

            DB::commit();

            $this->adminNote = '';
            $this->legalCase->refresh()->load(['client', 'lawyer', 'files', 'events.actor']);

            session()->flash('toast', 'Status berhasil diupdate.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            session()->flash('toast', 'Gagal update status.');
        }
    }

    public function render()
    {
        $lawyers = User::role('lawyer')->select('id','name','email')->orderBy('name')->get();

        return view('livewire.admin.cases.show', compact('lawyers'))
            ->layout('components.layouts.app');
    }
}
