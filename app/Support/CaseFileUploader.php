<?php

namespace App\Support;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class CaseFileUploader
{
    /**
     * Store file (pdf/image) into private disk, optionally convert images.
     *
     * @return array{path:string, mime:string, size:int, original_name:string, is_image:bool, thumb_path:?string}
     */
    public static function storeForCase($uploadedFile, int $caseId, array $options = []): array
    {
        $options = array_merge([
            'max_px' => 1600,
            'img_quality' => 75,
            'thumb_width' => 400,
            'thumb_quality' => 70,
        ], $options);

        $originalName = $uploadedFile->getClientOriginalName();
        $mime = $uploadedFile->getMimeType() ?: 'application/octet-stream';

        $isImage = str_starts_with(strtolower($mime), 'image/');
        $thumbPath = null;

        // sanitize filename
        $safeName = preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $originalName);
        $baseName = pathinfo($safeName, PATHINFO_FILENAME);

        // ===== PDF: store as-is =====
        if (!$isImage) {
            $filename = now()->format('Ymd_His') . '_' . $safeName;
            $path = $uploadedFile->storeAs("cases/{$caseId}", $filename, 'private');

            return [
                'path' => $path,
                'mime' => $mime,
                'size' => (int) $uploadedFile->getSize(),
                'original_name' => $originalName,
                'is_image' => false,
                'thumb_path' => null,
            ];
        }

        // ===== IMAGE: convert + compress to JPG =====
        $manager = new ImageManager(new Driver());

        $img = $manager->read($uploadedFile->getRealPath());

        // resize down to max_px (keep aspect ratio)
        $img = $img->scaleDown(width: $options['max_px'], height: $options['max_px']);

        $filename = now()->format('Ymd_His') . '_' . $baseName . '.jpg';
        $path = "cases/{$caseId}/{$filename}";

        $jpgBinary = (string) $img->toJpeg($options['img_quality']);
        Storage::disk('private')->put($path, $jpgBinary);

        // thumbnail
        $thumb = $manager->read($jpgBinary)->scaleDown(width: $options['thumb_width']);
        $thumbPath = "cases/{$caseId}/thumbs/" . now()->format('Ymd_His') . '_' . $baseName . '_thumb.jpg';
        Storage::disk('private')->put($thumbPath, (string) $thumb->toJpeg($options['thumb_quality']));

        return [
            'path' => $path,
            'mime' => 'image/jpeg',
            'size' => strlen($jpgBinary),
            'original_name' => $originalName,
            'is_image' => true,
            'thumb_path' => $thumbPath,
        ];
    }
}
