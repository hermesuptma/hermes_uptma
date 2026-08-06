<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $fillable = ['cedula', 'nombre', 'correo', 'telefono', 'codigo_qr'];

    public function secciones()
    {
        return $this->belongsToMany(Seccion::class, 'inscripcions');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }
}
