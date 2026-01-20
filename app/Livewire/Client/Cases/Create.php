<?php

namespace App\Livewire\Client\Cases;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\LegalCase;
use App\Support\CaseNumber;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver; // (default GD)
use App\Support\CaseFileUploader;
use App\Support\CaseEventLogger;

class Create extends Component
{
    use WithFileUploads;

    public string $title = '';
    public string $category = '';
    public string $type = 'consultation';
    public string $summary = '';

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $files = [];

    public function save()
    {
        $this->validate([
            'title' => 'required|string|min:6|max:120',
            'category' => 'nullable|string|max:80',
            'type' => 'required|in:consultation,non_litigation,litigation',
            'summary' => 'required|string|min:30|max:5000',
            'files.*' => 'nullable|file|max:5120', // 5MB
        ]);

        DB::beginTransaction();

        try {
            $case = LegalCase::create([
                'client_id' => auth()->id(),
                'case_no' => CaseNumber::make(),
                'title' => $this->title,
                'category' => $this->category ?: null,
                'type' => $this->type,
                'status' => 'submitted',
                'summary' => $this->summary,
                'submitted_at' => now(),
            ]);

            CaseEventLogger::log(
                $case,
                'case_created',
                'Client membuat case dan mengirim kronologi awal.',
                null,
                $case->status,
                ['type' => $case->type, 'category' => $case->category]
            );

            $uploaded = 0;
            foreach ($this->files as $file) {
                // whitelist server-side
                $mime = $file->getMimeType();
                if (!in_array($mime, ['application/pdf', 'image/jpeg', 'image/png'], true)) {
                    throw new \Exception("File type tidak diizinkan: {$mime}");
                }

                $stored = CaseFileUploader::storeForCase($file, $case->id);

                $case->files()->create([
                    'disk' => 'private',
                    'path' => $stored['path'],
                    'thumb_path' => $stored['thumb_path'],
                    'original_name' => $stored['original_name'],
                    'mime_type' => $stored['mime'],
                    'is_image' => $stored['is_image'],
                    'size' => $stored['size'],
                ]);

                $uploaded++;
            }

            if ($uploaded > 0) {
                CaseEventLogger::log(
                    $case,
                    'files_added',
                    "Client menambahkan {$uploaded} dokumen.",
                    $case->status,
                    $case->status,
                    ['count' => $uploaded]
                );
            }

            DB::commit();
            return redirect()->route('client.cases.show', $case->id);

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            $this->addError('title', 'Gagal menyimpan case. Coba lagi.');
            return;
        }
    }

    public function render()
    {
        return view('livewire.client.cases.create');
    }
}
