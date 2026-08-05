<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $fillable = ['sesion_clase_id', 'estudiante_id', 'hora_entrada', 'hora_salida', 'estado'];

    public function sesionClase()
    {
        return $this->belongsTo(SesionClase::class);
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }
}
