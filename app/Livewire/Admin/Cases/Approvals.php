<?php

namespace App\Livewire\Admin\Cases;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CaseApplication;
use Illuminate\Support\Facades\DB;
use App\Support\CaseEventLogger;

class Approvals extends Component
{
    use WithPagination;

    public function approve(int $appId): void
    {
        DB::beginTransaction();

        try {
            $app = CaseApplication::query()->with(['legalCase', 'lawyer'])->lockForUpdate()->findOrFail($appId);
            if ($app->status !== 'pending') {
                session()->flash('toast', 'Application sudah diproses.');
                DB::rollBack();
                return;
            }

            $case = $app->legalCase;

            // assign case ke lawyer
            $oldStatus = $case->status;
            $case->assigned_lawyer_id = $app->lawyer_id;
            $case->status = 'assigned';
            $case->save();

            // approve app
            $app->status = 'approved';
            $app->save();

            // reject aplikasi lain utk case yg sama
            CaseApplication::query()
                ->where('legal_case_id', $case->id)
                ->where('id', '!=', $app->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            // events
            CaseEventLogger::log($case, 'lawyer_approved', 'Admin approve lawyer application.', $oldStatus, $case->status, [
                'lawyer_id' => $app->lawyer_id,
                'lawyer_name' => $app->lawyer?->name,
            ]);

            CaseEventLogger::log($case, 'lawyer_assigned', 'Admin assign lawyer: '.$app->lawyer?->name, $oldStatus, $case->status, [
                'lawyer_id' => $app->lawyer_id,
                'lawyer_name' => $app->lawyer?->name,
            ]);

            CaseEventLogger::log($case, 'status_changed', 'Status berubah karena approval lawyer.', $oldStatus, $case->status);

            DB::commit();

            session()->flash('toast', 'Approved & assigned.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            session()->flash('toast', 'Gagal approve.');
        }
    }

    public function reject(int $appId): void
    {
        DB::beginTransaction();

        try {
            $app = CaseApplication::query()->with(['legalCase', 'lawyer'])->lockForUpdate()->findOrFail($appId);
            if ($app->status !== 'pending') {
                session()->flash('toast', 'Application sudah diproses.');
                DB::rollBack();
                return;
            }

            $app->status = 'rejected';
            $app->save();

            CaseEventLogger::log(
                $app->legalCase,
                'lawyer_rejected',
                'Admin reject lawyer application.',
                $app->legalCase->status,
                $app->legalCase->status,
                ['lawyer_id' => $app->lawyer_id, 'lawyer_name' => $app->lawyer?->name]
            );

            DB::commit();

            session()->flash('toast', 'Rejected.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            session()->flash('toast', 'Gagal reject.');
        }
    }

    public function render()
    {
        $apps = CaseApplication::query()
            ->with(['legalCase.client:id,name,email', 'lawyer:id,name,email'])
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->paginate(12);

        return view('livewire.admin.cases.approvals', compact('apps'))
            ->layout('components.layouts.app');
    }
}
