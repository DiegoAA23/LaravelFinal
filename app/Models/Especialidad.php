<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    use HasFactory;

    protected $table = 'especialidades';

    protected $primaryKey = 'id_especialidad';

    public $incrementing = true;


    protected $fillable = [
        'id_especialidad', 'especialidad', 'estado_id'
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}
