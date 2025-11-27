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
    public function getFullUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}