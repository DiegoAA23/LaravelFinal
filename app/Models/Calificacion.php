<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    use HasFactory;

    protected $table = 'calificaciones';

    protected $primaryKey = 'id_calificacion';

    public $incrementing = true;

    protected $fillable = [
        'id_calificacion',
        'id_inscripcion',
        'nota',
        'profesor_id',
        'actualizado'
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'id_inscripcion');
    }

    public function profesor()
    {
        return $this->belongsTo(Profesore::class, 'profesor_id');
    }
}

