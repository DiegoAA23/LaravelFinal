<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;

    protected $table = 'aulas';

    protected $primaryKey = 'id_aula';

    public $incrementing = true;


    protected $fillable = [
        'id_aula', 'aula', 'estado_id'
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}

