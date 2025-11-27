<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'booking_number',
        'user_id',
        'residence_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'country',
        'check_in',
        'check_out',
        'check_in_date',
        'check_out_date',
        'guests',
        'guests_count',
        'nights',
        'price_per_night',
        'total_price',
        'subtotal_amount',
        'tax_amount',
        'final_amount',
        'total_amount',
        'status',
        'payment_status',
        'special_requests',
        'notes',
        'confirmed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'price_per_night' => 'decimal:2',
        'total_price' => 'decimal:2',
        'subtotal_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = IdGenerator::generate([
                'table' => 'bookings',
                'length' => 10,
                'prefix' => 'B'
            ]);
            
            if (empty($model->booking_number)) {
                $model->booking_number = 'SH' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function residence()
    {
        return $this->belongsTo(Residence::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class)->latest();
    }

    public function successfulPayments()
    {
        return $this->hasMany(Payment::class)->where('status', 'completed');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed']);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForResidence($query, $residenceId)
    {
        return $query->where('residence_id', $residenceId);
    }

    public function scopeDateRange($query, $start, $end)
    {
        return $query->where(function ($q) use ($start, $end) {
            $q->where('check_in', '<=', $end)
              ->where('check_out', '>=', $start);
        });
    }

    // Méthodes utilitaires
    public function getStatusDisplayAttribute()
    {
        $statuses = [
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'cancelled' => 'Annulée',
            'completed' => 'Terminée',
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function getPaymentStatusDisplayAttribute()
    {
        $statuses = [
            'pending' => 'En attente',
            'paid' => 'Payée',
            'partial' => 'Partiellement payée',
            'refunded' => 'Remboursée',
        ];
        return $statuses[$this->payment_status] ?? $this->payment_status;
    }

    public function getTotalPaidAmount()
    {
        return $this->successfulPayments()->sum('amount');
    }

    public function getRemainingAmount()
    {
        return $this->final_amount - $this->getTotalPaidAmount();
    }

    public function isFullyPaid()
    {
        return $this->getTotalPaidAmount() >= $this->final_amount;
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']) && 
               $this->check_in->gt(now()->addDays(1)); // au moins 24h avant
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'notes' => $this->notes ? $this->notes . "\n\nAnnulée: " . $reason : "Annulée: " . $reason,
        ]);
    }

    public function confirm()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function updatePaymentStatus()
    {
        $totalPaid = $this->getTotalPaidAmount();
        
        if ($totalPaid >= $this->final_amount) {
            $this->update(['payment_status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $this->update(['payment_status' => 'partial']);
        } else {
            $this->update(['payment_status' => 'pending']);
        }
    }
}