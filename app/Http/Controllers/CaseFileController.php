<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CaseFileController extends Controller
{
    private function authorizeAccess(CaseFile $caseFile): void
    {
        $userId = auth()->id();

        // // client cuma boleh akses file case miliknya
        // if ((int) $caseFile->legalCase->client_id !== (int) $userId) {
        //     abort(404);
        // }
        if (auth()->user()->hasRole('admin')) return;
        if ((int) $caseFile->legalCase->client_id === (int) auth()->id()) return;
        // (nanti lawyer: cek assigned_lawyer_id)
        abort(404);
    }

    public function thumb(Request $request, CaseFile $caseFile)
{
    $caseFile->load('legalCase');
    $this->authorizeAccess($caseFile);

    if (!$caseFile->thumb_path) abort(404);

    $stream = Storage::disk($caseFile->disk)->readStream($caseFile->thumb_path);
    if (!$stream) abort(404);

    return response()->stream(function () use ($stream) {
        fpassthru($stream);
    }, 200, [
        'Content-Type' => 'image/jpeg',
        'Content-Disposition' => 'inline; filename="thumb.jpg"',
        'X-Content-Type-Options' => 'nosniff',
    ]);
}

    public function view(Request $request, CaseFile $caseFile)
    {
        $caseFile->load('legalCase');
        $this->authorizeAccess($caseFile);

        // inline preview (image/pdf)
        $mime = $caseFile->mime_type ?: 'application/octet-stream';

        // streaming response
        $stream = Storage::disk($caseFile->disk)->readStream($caseFile->path);
        if (!$stream) abort(404);

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$caseFile->original_name.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function download(Request $request, CaseFile $caseFile)
    {
        $caseFile->load('legalCase');
        $this->authorizeAccess($caseFile);

        return Storage::disk($caseFile->disk)->download(
            $caseFile->path,
            $caseFile->original_name,
            [
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }
}
