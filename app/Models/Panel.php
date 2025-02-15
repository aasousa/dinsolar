<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panel extends Model
{
    protected $fillable = [
        'name',
        'description',
        'brand',
        'power',
        'weight',
        'dimensions',
        'datasheet',
    ];

    public function sizings()
    {
        return $this->hasMany(Sizing::class);
    }
}
