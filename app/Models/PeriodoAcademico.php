<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodoAcademico extends Model
{
    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin'];
    
    public function secciones()
    {
    return $this->hasMany(Seccion::class);
    }
}
