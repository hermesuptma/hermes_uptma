<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trayecto extends Model
{
    protected $fillable = ['nombre'];

    public function secciones()
    {
        return $this->hasMany(Seccion::class);
    }
}