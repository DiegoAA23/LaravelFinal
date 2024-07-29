<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripciones';

    protected $primaryKey = 'id_inscripcion';

    public $incrementing = true;


    protected $fillable = [
        'id_inscripcion', '	id_estudiante', 'id_curso', 'año'
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante');
    }

    public function clase()
    {
        return $this->belongsTo(Clase::class, 'id_curso');
    }
}

