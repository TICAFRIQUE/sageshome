<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResidenceImage extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'residence_id',
        'image_path',
        'alt_text',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = IdGenerator::generate([
                'table' => 'residence_images',
                'length' => 10,
                'prefix' => 'RI'
            ]);
        });
    }

    // Relations
    public function residence()
    {
        return $this->belongsTo(Residence::class);
    }

    // Méthodes utilitaires
    /** URL d'une version redimensionnée (WebP, mise en cache) */
    public function thumb(int $width): string
    {
        return \App\Support\ImageVariants::url($this->image_path, $width) ?? $this->full_url;
    }

    public function srcset(array $widths = [480, 800]): ?string
    {
        return \App\Support\ImageVariants::srcset($this->image_path, $widths);
    }

    public function getFullUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}