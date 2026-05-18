<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediccion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'partido_id',
        'resultado_local',
        'resultado_visitante',
        'puntos'
    ];

    protected $table = 'predicciones';

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function partido(){
        return $this->belongsTo(Partido::class, 'partido_id');
    }
}
