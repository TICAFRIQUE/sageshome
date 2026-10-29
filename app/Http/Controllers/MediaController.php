<?php

namespace App\Http\Controllers;

use App\Support\ImageVariants;

class MediaController extends Controller
{
    /** Génère (une fois) et sert une variante WebP d'une photo, avec cache navigateur longue durée. */
    public function variant(int $width, string $path)
    {
        $file = ImageVariants::generate($path, $width);

        if (! $file) {
            // Repli : l'original, pour ne jamais afficher une image cassée
            $original = storage_path('app/public/' . $path);
            abort_unless(preg_match('#^[A-Za-z0-9_\-./]+$#', $path) && ! str_contains($path, '..') && is_file($original), 404);

            return response()->file($original, ['Cache-Control' => 'public, max-age=86400']);
        }

        return response()->file($file, [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
