<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $fillable = ['cedula', 'nacionalidad', 'nombre', 'correo', 'telefono', 'codigo_qr'];

    public function secciones()
    {
        return $this->belongsToMany(Seccion::class, 'inscripcions');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    public static function buscarPorCodigoEscaneado(string $textoEscaneado): ?self
    {
        if (str_contains($textoEscaneado, 'verificacion.iutm.edu.ve')) {
            $partes = parse_url($textoEscaneado);
            parse_str($partes['query'] ?? '', $query);
            $idConLetra = $query['id'] ?? null;

            if ($idConLetra) {
                $cedula = preg_replace('/^[A-Za-z]+/', '', $idConLetra);
                return self::where('cedula', $cedula)->first();
            }

            return null;
        }

        return self::where('codigo_qr', $textoEscaneado)->first();
    }
}