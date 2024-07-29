<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    use HasFactory;

    protected $table = 'inscripciones';

    protected $primaryKey = 'id_inscripcion';

    public $incrementing = true;

    protected $fillable = [
        'id_inscripcion', 'id_estudiante', 'id_curso', 'año', 'estado_id'
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

}