<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $fillable = ['carrera_id', 'nombre', 'codigo'];
    
    public function carrera()
    {
    return $this->belongsTo(Carrera::class);
    }

public function secciones()
    {
    return $this->hasMany(Seccion::class);
    }
}
