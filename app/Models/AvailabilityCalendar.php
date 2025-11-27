<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AvailabilityCalendar extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'availability_calendar';

    protected $fillable = [
        'residence_id',
        'date',
        'is_available',
        'price_override',
        'min_stay',
    ];

    protected $casts = [
        'date' => 'date',
        'is_available' => 'boolean',
        'price_override' => 'decimal:2',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = IdGenerator::generate([
                'table' => 'availability_calendar',
                'length' => 10,
                'prefix' => 'AC'
            ]);
        });
    }

    // Relations
    public function residence()
    {
        return $this->belongsTo(Residence::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeForDateRange($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }

    public function scopeForResidence($query, $residenceId)
    {
        return $query->where('residence_id', $residenceId);
    }
}