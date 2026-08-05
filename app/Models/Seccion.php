<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    protected $fillable = ['materia_id', 'profesor_id', 'periodo_academico_id', 'nombre_seccion'];

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function profesor()
    {
        return $this->belongsTo(\App\Models\User::class, 'profesor_id');
    }

    public function periodoAcademico()
    {
        return $this->belongsTo(PeriodoAcademico::class);
    }

    public function estudiantes()
    {
        return $this->belongsToMany(Estudiante::class, 'inscripcions');
    }

    public function sesionClases()
    {
        return $this->hasMany(SesionClase::class);
    }
}
