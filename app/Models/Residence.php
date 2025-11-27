<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Residence extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'full_description',
        'residence_type_id',
        'capacity',
        'price_per_night',
        'amenities',
        'address',
        'latitude',
        'longitude',
        'is_available',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'amenities' => 'array',
        'price_per_night' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = IdGenerator::generate([
                'table' => 'residences',
                'length' => 10,
                'prefix' => 'R'
            ]);
        });
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    // Relations
    public function residenceType()
    {
        return $this->belongsTo(ResidenceType::class);
    }

    public function images()
    {
        return $this->hasMany(ResidenceImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ResidenceImage::class)->where('is_primary', true);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function availabilityCalendar()
    {
        return $this->hasMany(AvailabilityCalendar::class, 'residence_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByType($query, $typeId)
    {
        return $query->where('residence_type_id', $typeId);
    }

    public function scopeByCapacity($query, $capacity)
    {
        return $query->where('capacity', '>=', $capacity);
    }

    public function scopePriceRange($query, $min = null, $max = null)
    {
        if ($min !== null) {
            $query->where('price_per_night', '>=', $min);
        }
        if ($max !== null) {
            $query->where('price_per_night', '<=', $max);
        }
        return $query;
    }

    // Méthodes utilitaires
    public static function getAmenityLabels()
    {
        return [
            'wifi' => 'Wi-Fi',
            'canal_plus' => 'Abonnement Canal+',
            'climatiseur' => 'Climatiseur',
            'chauffe_eau' => 'Chauffe-eau',
            'cuisiniere' => 'Cuisinière',
            'four' => 'Four',
            'micro_onde' => 'Micro-ondes',
            'bouilloire' => 'Bouilloire électrique',
            'refrigerateur' => 'Réfrigérateur',
            'mixeur' => 'Mixeur',
            'ustensiles' => 'Ustensiles de cuisine',
            'couverts' => 'Couverts complets',
            'menage' => 'Service de ménage',
            'securite' => 'Sécurité 24H/24 et 7J/7',
            'piscine' => 'Piscine',
            'arrivee_autonome' => 'Arrivée autonome',
            'annulation_gratuite' => 'Annulation gratuite',
            'animaux_interdits' => 'Animaux de compagnie non autorisés'
        ];
    }

    public function getFormattedAmenities()
    {
        if (!$this->amenities || !is_array($this->amenities)) {
            return [];
        }
        
        $labels = self::getAmenityLabels();
        return array_map(function($amenity) use ($labels) {
            return $labels[$amenity] ?? ucfirst(str_replace('_', ' ', $amenity));
        }, $this->amenities);
    }

    public function getTypeDisplayAttribute()
    {
        return $this->residenceType ? $this->residenceType->name : 'Non défini';
    }

    public function isAvailableForDates($checkIn, $checkOut)
    {
        // Vérifier qu'il n'y a pas de réservations confirmées pour ces dates
        $overlappingBookings = $this->bookings()
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where('check_in', '<=', $checkOut)
                      ->where('check_out', '>=', $checkIn);
            })
            ->whereIn('status', ['confirmed', 'pending'])
            ->exists();

        if ($overlappingBookings) {
            return false;
        }

        // Vérifier le calendrier de disponibilité
        $unavailableDates = $this->availabilityCalendar()
            ->whereBetween('date', [$checkIn, $checkOut])
            ->where('is_available', false)
            ->exists();

        return !$unavailableDates;
    }

    public function calculateTotalPrice($checkIn, $checkOut)
    {
        $checkInDate = \Carbon\Carbon::parse($checkIn);
        $checkOutDate = \Carbon\Carbon::parse($checkOut);
        $nights = $checkInDate->diffInDays($checkOutDate);
        
        $totalPrice = 0;
        
        for ($date = $checkInDate->copy(); $date->lt($checkOutDate); $date->addDay()) {
            $calendar = $this->availabilityCalendar()
                ->where('date', $date->format('Y-m-d'))
                ->first();
            
            $price = $calendar && $calendar->price_override 
                ? $calendar->price_override 
                : $this->price_per_night;
            
            $totalPrice += $price;
        }
        
        return [
            'nights' => $nights,
            'price_per_night' => $this->price_per_night,
            'total_price' => $totalPrice,
        ];
    }
}