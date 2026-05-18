<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comunidad extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'code',
        'user_id'
    ];

    protected $table = 'comunidades';

    public function creador(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function miembros(){
        return $this->belongsToMany(User::class, 'comunidad_user', 'comunidad_id', 'user_id')
        ->withPivot('status')
        ->withTimestamps();
    }

}
