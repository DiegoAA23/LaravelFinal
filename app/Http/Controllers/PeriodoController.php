<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use App\Models\Calificaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodoController extends Controller
{
    private $periodo;

    private $calificacion;

    public function __construct(Periodo $periodo, Calificaciones $calificacion)
    {
        $this->periodo = $periodo;
        $this->calificacion = $calificacion;
    }

    public function store(Request $request)
    {
        try {
            foreach ($request->id_curso as $key => $curso) {
                $this->periodo::create([
                    'id_estudiante' => $request->id_estudiante[$key],
                    'id_curso' => $curso,
                    'año' => 2024,
                    'estado_id' => 1,
                ]);
            }
            $this->storeN();
            return redirect()->route('matricula')->with('success', 'Inscripciones creadas exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear las inscripciones.')->withInput();
        }
    }

    public function storeN()
    {
        try {

            $inscripciones = DB::table('nueva_insc')->get();

            foreach ($inscripciones as $inscripcion) {
                $this->calificacion::create([
                    'id_inscripcion' => $inscripcion->id_inscripcion,
                    'nota' => 0.0,
                    'actualizado' => 0,
                ]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear las inscripciones.')->withInput();
        }
    }
}
