<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clase extends Model
{
    use HasFactory;

    protected $table = 'cursos';

    protected $primaryKey = 'id_curso';

    public $incrementing = true;

    protected $fillable = [
        'id_curso', 'nombre_clase', 'id_profesor', 'periodo', 'estado_id'
    ];

    public function profesor()
    {
        return $this->belongsTo(Profesore::class, 'id_profesor');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function clase()
    {
        return $this->belongsTo(Estado::class, 'id_curso');
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_curso');
    }
}
