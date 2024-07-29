<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calificaciones extends Model
{
    use HasFactory;

    protected $table = 'calificaciones';

    protected $primaryKey = 'id_calificacion';

    public $incrementing = true;

    protected $fillable = [
        'id_calificacion', 'id_inscripcion', 'nota', 'actualizado'
    ];
}