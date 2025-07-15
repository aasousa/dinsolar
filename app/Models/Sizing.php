<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sizing extends Model
{
    protected $fillable = [
        'residence_id',
        'name',
        'days',
        'hours',
        'kw',
        'kwh',
    ];

    public function residence()
    {
        return $this->belongsTo(Residence::class);
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            $model->kwh = $model->days * $model->hours * $model->kw;
        });
    }
}
