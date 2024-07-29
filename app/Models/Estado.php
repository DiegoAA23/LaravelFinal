<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;

    protected $table = 'estados';

    protected $primaryKey = 'id_estado';


    protected $fillable = [
        'id_estado', 'descripcion'
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado');
    }
}

