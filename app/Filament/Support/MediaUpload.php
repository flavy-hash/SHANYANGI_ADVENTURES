<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;

/**
 * Image upload field for website content.
 *
 * Files go to the private "media" disk (storage/app/private/media/images), so
 * the website serves them through protected, signed URLs (ProtectedMedia).
 * The stored value is a path like "uploads/packages/abc.jpg".
 */
class MediaUpload
{
    public static function make(string $name, string $directory): FileUpload
    {
        return FileUpload::make($name)
            ->image()
            ->disk('media')
            ->visibility('private')
            ->directory('uploads/'.$directory)
            ->imageEditor()
            ->maxSize(8192) // KB
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']);
    }
}
