<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioVal extends Model
{
    use HasFactory;

    protected $table = 'horarios_val';
    
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'hora_inicio', 'hora_fin', 'fecha_inicio', 'fecha_fin', 'aula_id', 'id_curso', 'id_profesor'
    ];
}
