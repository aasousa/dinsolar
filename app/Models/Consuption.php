<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consuption extends Model
{
    protected $fillable = [
        'residence_id',
        'date',
        'kwh',
        'te',
        'tusd',
        'ammount',
        'flag',
    ];

    public function residence()
    {
        return $this->belongsTo(Residence::class);
    }
}
