<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'booking_id',
        'payment_reference',
        'payment_method',
        'amount',
        'status',
        'payment_details',
        'payment_data', // Pour les données JSON de l'API externe (Wave, PayPal, etc.)
        'transaction_id',
        'processed_at',
        'completed_at',
        'failure_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'payment_data' => 'array', // Pour les données JSON de l'API externe
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = IdGenerator::generate([
                'table' => 'payments',
                'length' => 10,
                'prefix' => 'P'
            ]);
            
            if (empty($model->payment_reference)) {
                $model->payment_reference = 'PAY' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relations
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Méthodes utilitaires
    public function getMethodDisplayAttribute()
    {
        $methods = [
            'wave' => 'Wave',
            'paypal' => 'PayPal',
            'cash' => 'Espèces',
            'bank_transfer' => 'Virement bancaire',
        ];
        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    public function getStatusDisplayAttribute()
    {
        $statuses = [
            'pending' => 'En attente',
            'processing' => 'En cours',
            'completed' => 'Terminé',
            'failed' => 'Échec',
            'cancelled' => 'Annulé',
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function markAsCompleted($transactionId = null)
    {
        $this->update([
            'status' => 'completed',
            'transaction_id' => $transactionId,
            'processed_at' => now(),
        ]);

        // Mettre à jour le statut de paiement de la réservation
        $this->booking->updatePaymentStatus();
    }

    public function markAsFailed($reason)
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);
    }
}