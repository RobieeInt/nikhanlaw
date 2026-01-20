<?php

namespace App\Livewire\Client\Cases;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\LegalCase;
use App\Models\CaseFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Support\CaseFileUploader;
use App\Support\CaseEventLogger;

class Show extends Component
{
    use WithFileUploads;

    public LegalCase $legalCase;

    // ===== Edit form state =====
    public bool $editMode = false;

    public string $title = '';
    public string $category = '';
    public string $type = 'consultation';
    public string $summary = '';

    // ===== Files =====
    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $newFiles = [];

    // ===== Delete confirm =====
    public bool $confirmDelete = false;

    public function mount(LegalCase $legalCase): void
    {
        if ((int) $legalCase->client_id !== (int) auth()->id()) {
            throw new NotFoundHttpException();
        }

        $this->legalCase = $legalCase;
        $this->legalCase->load(['files', 'events.actor']);

        $this->syncFormFromModel();
    }

    private function syncFormFromModel(): void
    {
        $this->title = (string) $this->legalCase->title;
        $this->category = (string) ($this->legalCase->category ?? '');
        $this->type = (string) ($this->legalCase->type ?? 'consultation');
        $this->summary = (string) ($this->legalCase->summary ?? '');
    }

    private function canClientModify(): bool
    {
        return in_array($this->legalCase->status, ['submitted', 'need_info'], true);
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

    // ===== Edit actions =====
    public function toggleEdit(): void
    {
        if (!$this->canClientModify()) {
            session()->flash('toast', 'Case sudah diproses, tidak bisa diedit.');
            return;
        }

        $this->editMode = !$this->editMode;

        if ($this->editMode) {
            $this->legalCase->refresh()->load(['files', 'events.actor']);
            $this->syncFormFromModel();
        } else {
            $this->resetValidation();
        }
    }

    public function saveCase(): void
    {
        if (!$this->canClientModify()) {
            session()->flash('toast', 'Case sudah diproses, tidak bisa diedit.');
            return;
        }

        $validated = $this->validate([
            'title' => 'required|string|min:6|max:120',
            'category' => 'nullable|string|max:80',
            'type' => 'required|in:consultation,non_litigation,litigation',
            'summary' => 'required|string|min:30|max:5000',
        ]);

        try {
            $this->legalCase->update([
                'title' => $validated['title'],
                'category' => $validated['category'] ?: null,
                'type' => $validated['type'],
                'summary' => $validated['summary'],
            ]);

            CaseEventLogger::log(
                $this->legalCase,
                'case_updated',
                'Client memperbarui detail case (judul/kategori/jenis/kronologi).',
                $this->legalCase->status,
                $this->legalCase->status
            );

            $this->legalCase->refresh()->load(['files', 'events.actor']);
            $this->syncFormFromModel();

            $this->editMode = false;
            session()->flash('toast', 'Case berhasil diperbarui.');
        } catch (\Throwable $e) {
            report($e);
            session()->flash('toast', 'Gagal menyimpan perubahan.');
        }
    }

    // ===== File actions =====
    public function addFiles(): void
    {
        if (!$this->canClientModify()) {
            $this->addError('newFiles', 'Case sudah diproses, file tidak bisa diubah.');
            return;
        }

        // whitelist di layer validation
        $this->validate([
            'newFiles.*' => 'required|file|max:5120|mimes:pdf,jpg,jpeg,png',
        ]);

        DB::beginTransaction();

        try {
            $count = 0;

            foreach ($this->newFiles as $file) {
                // extra runtime mime check (anti spoof)
                $mime = $file->getMimeType();
                if (!in_array($mime, ['application/pdf', 'image/jpeg', 'image/png'], true)) {
                    throw new \Exception("File type tidak diizinkan: {$mime}");
                }

                $stored = CaseFileUploader::storeForCase($file, $this->legalCase->id);

                $this->legalCase->files()->create([
                    'disk' => 'private',
                    'path' => $stored['path'],
                    'thumb_path' => $stored['thumb_path'],
                    'original_name' => $stored['original_name'],
                    'mime_type' => $stored['mime'],
                    'is_image' => $stored['is_image'],
                    'size' => $stored['size'],
                ]);

                $count++;
            }

            DB::commit();

            $this->newFiles = [];

            CaseEventLogger::log(
                $this->legalCase,
                'files_added',
                "Client menambahkan {$count} dokumen.",
                $this->legalCase->status,
                $this->legalCase->status,
                ['count' => $count]
            );

            $this->legalCase->refresh()->load(['files', 'events.actor']);
            session()->flash('toast', 'Dokumen berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            $this->addError('newFiles', 'Gagal upload file. Coba lagi.');
        }
    }

public function deleteFile(int $fileId): void
{
    if (!$this->canClientModify()) {
        session()->flash('toast', 'Case sudah diproses, file tidak bisa dihapus.');
        return;
    }

    /** @var CaseFile|null $file */
    $file = CaseFile::query()
        ->where('id', $fileId)
        ->where('legal_case_id', $this->legalCase->id)
        ->first();

    if (!$file) {
        session()->flash('toast', 'File tidak ditemukan.');
        return;
    }

    $name = $file->original_name;
    $disk = $file->disk;
    $path = $file->path;
    $thumb = $file->thumb_path;

    // ✅ Hapus DB dulu biar pasti hilang dari UI (storage best-effort)
    DB::beginTransaction();

    try {
        $file->delete();
        DB::commit();
    } catch (\Throwable $e) {
        DB::rollBack();
        report($e);
        session()->flash('toast', 'Gagal hapus dokumen (DB).');
        return;
    }

    // Best-effort storage delete (jangan bikin rollback)
    try {
        Storage::disk($disk)->delete($path);
        if ($thumb) {
            Storage::disk($disk)->delete($thumb);
        }
    } catch (\Throwable $e) {
        report($e); // biar lo tau kalau disk/path bermasalah
        // tetap lanjut, DB sudah kehapus
    }

    // Log event (optional)
    try {
        CaseEventLogger::log(
            $this->legalCase,
            'file_deleted',
            "Client menghapus dokumen: {$name}",
            $this->legalCase->status,
            $this->legalCase->status,
            ['file_id' => $fileId, 'name' => $name]
        );
    } catch (\Throwable $e) {
        report($e);
    }

    $this->legalCase->refresh()->load(['files', 'events.actor']);
    session()->flash('toast', 'Dokumen berhasil dihapus.');
}

    // ===== Delete case =====
    public function deleteCase(): void
    {
        if (!$this->canClientModify()) {
            session()->flash('toast', 'Case sudah diproses, tidak bisa dihapus.');
            $this->confirmDelete = false;
            return;
        }

        DB::beginTransaction();

        try {
            $this->legalCase->load('files');

            // log dulu (event bakal ikut hilang saat delete cascade, ini oke untuk MVP)
            CaseEventLogger::log(
                $this->legalCase,
                'case_deleted',
                'Client menghapus case.',
                $this->legalCase->status,
                null
            );

            foreach ($this->legalCase->files as $file) {
                Storage::disk($file->disk)->delete($file->path);
                if ($file->thumb_path) {
                    Storage::disk($file->disk)->delete($file->thumb_path);
                }
            }

            $this->legalCase->delete();

            DB::commit();

            $this->redirectRoute('client.cases.index');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            session()->flash('toast', 'Gagal hapus case.');
        } finally {
            $this->confirmDelete = false;
        }
    }

    public function render()
    {
        return view('livewire.client.cases.show')
            ->layout('components.layouts.app');
    }
}
