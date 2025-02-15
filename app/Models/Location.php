<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'labren_id',
        'lon',
        'lat',
        'name',
        'state',
        'annual_irradiation',
    ];

    public function residences()
    {
        return $this->hasMany(Residence::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->name} - {$this->state}";
    }
}
