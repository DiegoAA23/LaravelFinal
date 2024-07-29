<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $table = 'horarios';

    protected $primaryKey = 'id_horario';

    public $incrementing = true;

    protected $fillable = [
        'id_horario', 'id_curso', 'aula_id', 'dias', 'fecha_inicio', 
        'fecha_fin', 'hora_inicio', 'hora_fin', 'estado_id'
    ];

    public function clase()
    {
        return $this->belongsTo(Clase::class, 'id_curso');
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class, 'aula_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}
