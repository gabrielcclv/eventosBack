<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipo_local',
        'equipo_visitante',
        'fecha_hora',
        'grupo',
        'resultado_local',
        'resultado_visitante',
        'fase'
    ];

    protected $table = 'partidos';

    public function predicciones(){
        return $this->hasMany(Prediccion::class, 'partido_id');
    }
}
