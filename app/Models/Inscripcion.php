<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    protected $fillable = ['estudiante_id', 'seccion_id'];
    
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

public function seccion()
    {
        return $this->belongsTo(Seccion::class);
    }
}
