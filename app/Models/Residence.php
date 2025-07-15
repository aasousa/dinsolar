<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Residence extends Model
{
    //
    protected $fillable = [
        'label',
        'user_id',
        'location_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function consuptions()
    {
        return $this->hasMany(Consuption::class);
    }

    public function averageKwh()
    {
        return $this->consuptions()->avg('kwh');
    }

    public function sizings()
    {
        return $this->hasMany(Sizing::class);
    }

    public function potenciaPico()
    {
        $irradiacao_solar_local = $this->location->annual_irradiation / 1000;
        $fator_de_rendimento = 0.75;
        $consumo_diario = $this->averageKwh() / 30;
        $energia_ajustada = $consumo_diario / $fator_de_rendimento;
        $potencia_de_pico = $energia_ajustada / $irradiacao_solar_local;
        return round($potencia_de_pico, 2);
    }
}
