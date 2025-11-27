<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Spatie\Permission\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Booking[] $bookings
 * @property-read int|null $bookings_count
 * @method \Illuminate\Database\Eloquent\Relations\HasMany bookings()
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable  , HasPermissions, HasRoles , SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'phone',
        'email',
        'email_verified_at',
        'password',
        'avatar',
        'role',
        'address',
        'city',
        'country',
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            // Génération d'un ID entier unique avec mt_rand
            do {
                $id = mt_rand(1000000000, 9999999999); // ID de 10 chiffres
            } while (self::where('id', $id)->exists());
            $model->id = $id;
        });
    }








    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relations
    /**
     * Get the bookings for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the active bookings for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeBookings(): HasMany
    {
        return $this->hasMany(Booking::class)->whereIn('status', ['pending', 'confirmed']);
    }

    /**
     * Get the completed bookings for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function completedBookings(): HasMany
    {
        return $this->hasMany(Booking::class)->where('status', 'completed');
    }
}
