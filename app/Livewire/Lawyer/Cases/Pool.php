<?php

namespace App\Livewire\Lawyer\Cases;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LegalCase;
use App\Models\CaseApplication;
use Illuminate\Support\Facades\DB;
use App\Support\CaseEventLogger;

class Pool extends Component
{
    use WithPagination;

    public string $q = '';

    protected $queryString = [
        'q' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function updatingQ(): void
    {
        $this->resetPage();
    }

    public function apply(int $caseId): void
    {
        $lawyerId = auth()->id();

        $case = LegalCase::query()->findOrFail($caseId);

        // case harus eligible
        if ($case->status !== 'qualified' || $case->assigned_lawyer_id) {
            session()->flash('toast', 'Case ini tidak tersedia untuk diambil.');
            return;
        }

        try {
            $app = CaseApplication::query()->firstOrCreate(
                ['legal_case_id' => $case->id, 'lawyer_id' => $lawyerId],
                ['status' => 'pending']
            );

            // kalau ternyata sudah ada tapi statusnya rejected, balikin jadi pending (opsional)
            if ($app->wasRecentlyCreated === false && $app->status === 'rejected') {
                $app->status = 'pending';
                $app->save();
            }

            // event log
            CaseEventLogger::log(
                $case,
                'lawyer_applied',
                'Lawyer mengajukan untuk menangani case.',
                $case->status,
                $case->status,
                ['lawyer_id' => $lawyerId]
            );

            session()->flash('toast', 'Apply berhasil. Menunggu approval admin.');

            // biar langsung hilang dari list pool setelah apply
            $this->resetPage();
        } catch (\Throwable $e) {
            report($e);
            session()->flash('toast', 'Gagal apply.');
        }
    }

    public function render()
    {
        $lawyerId = auth()->id();

        // ✅ Ambil daftar case yang sudah pernah di-apply oleh lawyer ini (pending/approved)
        $appliedIds = DB::table('case_applications')
            ->where('lawyer_id', $lawyerId)
            ->whereIn('status', ['pending', 'approved'])
            ->pluck('legal_case_id')
            ->toArray();

        // ✅ Pool = qualified + belum assigned + belum pernah di-apply oleh lawyer ini
        $query = LegalCase::query()
            ->with(['client:id,name,email'])
            ->where('status', 'qualified')
            ->whereNull('assigned_lawyer_id')
            ->when(count($appliedIds) > 0, fn ($q) => $q->whereNotIn('id', $appliedIds))
            ->orderByDesc('id');

        if ($this->q !== '') {
            $q = '%'.$this->q.'%';
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', $q)
                  ->orWhere('case_no', 'like', $q)
                  ->orWhere('category', 'like', $q);
            });
        }

        $cases = $query->paginate(10);

        return view('livewire.lawyer.cases.pool', compact('cases'))
            ->layout('components.layouts.app');
    }
}
