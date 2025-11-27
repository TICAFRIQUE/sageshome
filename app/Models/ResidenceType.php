<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class ResidenceType extends Model
{
    use Sluggable;
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'min_capacity',
        'max_capacity',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_capacity' => 'integer',
        'max_capacity' => 'integer',
        'sort_order' => 'integer',
    ];
    
    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
    
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Relations
    public function residences()
    {
        return $this->hasMany(Residence::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Accessors
    public function getDisplayNameAttribute()
    {
        return $this->name;
    }
    
    public function getFormattedCapacityAttribute()
    {
        if ($this->min_capacity === $this->max_capacity) {
            return $this->min_capacity . ' personne' . ($this->min_capacity > 1 ? 's' : '');
        }
        return $this->min_capacity . '-' . $this->max_capacity . ' personnes';
    }
}
