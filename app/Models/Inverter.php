<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inverter extends Model
{
    protected $fillable = [
        'name',
        'description',
        'brand',
        'power',
        'weight',
        'dimensions',
        'datasheet',
        'price',
    ];

    public function selectLabel()
    {
        return "{$this->name} - {$this->power} W - R$ {$this->price}";
    }
}
