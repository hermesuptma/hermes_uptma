<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesionClase extends Model
{
    protected $fillable = ['seccion_id', 'fecha', 'hora_inicio', 'modo_actual'];

    public function seccion()
    {
        return $this->belongsTo(Seccion::class);
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }
}
