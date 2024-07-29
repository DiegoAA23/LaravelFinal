<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'estudiantes';

    protected $primaryKey = 'id_estudiante';

    public $incrementing = false;

    protected $fillable = [
        'id_estudiante', 'nombre', 'apellido', 'fecha_de_nacimiento', 'genero', 'direccion', 'telefono', 'correo_electronico', 'estado_id'
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}
