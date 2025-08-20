<?php
// app/Services/Concerns/HandlesUploads.php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait UploadFiles
{
    /**
     * Store one image under given directory on chosen disk.
     * Returns ['path' => '...', 'original_name' => '...'].
     */
    protected function storeImage(UploadedFile $file, string $dir, string $disk = 'public'): array
    {
        // Generate stable unique filename preserving extension
        $ext  = $file->guessExtension() ?: $file->extension();
        $name = Str::uuid()->toString().'.'.$ext;

        // e.g., tasks/123/images/uuid.png
        $path = $file->storeAs($dir, $name, $disk);

        return [
            'path'          => $path,
            'original_name' => $file->getClientOriginalName(),
        ];
    }

}
