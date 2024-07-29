<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NivelesUsuario extends Controller
{
    /*private $tipo;

    public function index()
    {
        $usuario = Auth::user(); // Obtiene el usuario autenticado
        //$idUsuario = Auth::id();
        if($usuario->id_estudiante !== null && $usuario->id_profesor !== null){
            $this->tipo=3;
        }else if($usuario->id_profesor !== null){
            $this->tipo=2;
        }else if($usuario->id_estudiante !== null){
            $this->tipo=1;
        }else{
            $this->tipo=0;
        }

        //session(['usuario' => $this->tipo]);
        return view('layouts/app', ['usuario' => $this->tipo]);
    }*/

    
}
