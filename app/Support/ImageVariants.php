<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Versions redimensionnées (WebP) des photos de résidences.
 *
 * Les originales font souvent 1700-2500 px : servir ces fichiers sur mobile
 * est la première cause de lenteur. Chaque variante est générée une seule fois
 * dans storage/app/public/_cache/{largeur}/ puis servie comme fichier statique
 * (avec cache navigateur d'un an).
 */
class ImageVariants
{
    public const WIDTHS = [200, 480, 800, 1280, 1600];

    /** URL d'une variante : statique si elle existe déjà, sinon via la route qui la génère. */
    public static function url(?string $path, int $width): ?string
    {
        if (! $path || ! self::isSafe($path)) {
            return null;
        }
        $width = self::nearestWidth($width);
        $cached = self::cachedPath($path, $width);

        if (is_file(storage_path('app/public/' . $cached))) {
            return asset('storage/' . $cached);
        }

        return route('media.variant', ['width' => $width, 'path' => $path]);
    }

    public static function srcset(?string $path, array $widths = [480, 800]): ?string
    {
        if (! $path) {
            return null;
        }

        return collect($widths)->map(fn ($w) => self::url($path, $w) . ' ' . self::nearestWidth($w) . 'w')->implode(', ');
    }

    /** Génère (si besoin) la variante et renvoie son chemin absolu, ou null si impossible. */
    public static function generate(string $path, int $width): ?string
    {
        if (! self::isSafe($path) || ! in_array($width, self::WIDTHS, true)) {
            return null;
        }

        $source = storage_path('app/public/' . $path);
        if (! is_file($source)) {
            return null;
        }

        $target = storage_path('app/public/' . self::cachedPath($path, $width));
        if (is_file($target)) {
            return $target;
        }

        if (! function_exists('imagewebp')) {
            return null;
        }

        $info = @getimagesize($source);
        if (! $info) {
            return null;
        }

        $image = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($source),
            IMAGETYPE_PNG => @imagecreatefrompng($source),
            IMAGETYPE_WEBP => @imagecreatefromwebp($source),
            default => null,
        };
        if (! $image) {
            return null;
        }

        // Orientation EXIF (photos de téléphone)
        if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($source);
            $rotate = match ($exif['Orientation'] ?? 1) {
                3 => 180, 6 => -90, 8 => 90, default => 0,
            };
            if ($rotate) {
                $image = imagerotate($image, $rotate, 0) ?: $image;
            }
        }

        $w = imagesx($image);
        $h = imagesy($image);
        $newW = min($width, $w); // jamais d'agrandissement
        $newH = (int) round($h * $newW / $w);

        $resized = imagecreatetruecolor($newW, $newH);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newW, $newH, $w, $h);

        @mkdir(dirname($target), 0775, true);
        $tmp = $target . '.' . uniqid('', true) . '.tmp';
        imagewebp($resized, $tmp, $width <= 200 ? 70 : 78);
        imagedestroy($image);
        imagedestroy($resized);
        rename($tmp, $target); // écriture atomique

        return $target;
    }

    private static function cachedPath(string $path, int $width): string
    {
        return '_cache/' . $width . '/' . str_replace('/', '_', pathinfo($path, PATHINFO_DIRNAME) . '/' . pathinfo($path, PATHINFO_FILENAME)) . '.webp';
    }

    private static function nearestWidth(int $width): int
    {
        foreach (self::WIDTHS as $w) {
            if ($w >= $width) {
                return $w;
            }
        }

        return end(self::WIDTHS);
    }

    private static function isSafe(string $path): bool
    {
        return (bool) preg_match('#^[A-Za-z0-9_\-./]+$#', $path) && ! str_contains($path, '..') && ! str_starts_with($path, '/');
    }
}
