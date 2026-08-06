<?php

namespace App\Imports;

use App\Models\Estudiante;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class EstudiantesImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Estudiante([
            'matricula' => $row['matricula'],
            'nombre'    => $row['nombre'],
            'correo'    => $row['correo'],
            'codigo_qr' => (string) Str::uuid(), // se genera automático, no viene del Excel
        ]);
    }

    public function rules(): array
    {
        return [
            'matricula' => 'required|string|unique:estudiantes,matricula',
            'nombre'    => 'required|string|max:255',
            'correo'    => 'required|email|unique:estudiantes,correo',
        ];
    }
}