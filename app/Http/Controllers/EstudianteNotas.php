<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Matricula;
use Illuminate\Support\Facades\Crypt;
use Barryvdh\DomPDF\Facade\PDF;

class EstudianteNotas extends Controller
{
    public function index()
    {
        $data = array(
            'list' => DB::table('clases_horarios')->get(),
        );

        $periodo = array(
            'periodos' => $this->comprobarPeriodo(), // Cambia el nombre de la clave a 'periodos'
        );

        $combinedData = array_merge($data, $periodo);

        return view('matricula', $combinedData);
    }

    public function comprobarPeriodo()
    {
        $estudianteActual = Auth::user();
        $estudId = $estudianteActual->id_estudiante;

        $periodosUnicos = DB::table('notas_periodo')
            ->where('id_estudiante', $estudId)
            ->distinct()
            ->pluck('periodo')
            ->toArray();

        return $periodosUnicos;
    }

    private $idest;
    private $nombre;
    private $apellido;
    public function obtenerEstudiante()
    {
        $estudianteActual = Auth::user();
        $tmpEstud = $estudianteActual->id_estudiante;
        $estudiante = DB::table('estudiantes')->where('id_estudiante', $tmpEstud)->first();

        if (!$estudiante) {
            return redirect()->back()->with('error', 'No se encontró ningún estudiante.');
        }

        $id = $estudiante->id_estudiante;
        $this->idest = $id;
        $this->nombre = $estudiante->nombre;
        $this->apellido = $estudiante->apellido;

        return redirect()->route('estudcalificaciones.id', ['id' => $id]);
    }

    public function notas($id)
    {
        $periodoReciente = DB::table('notas')
            ->where('id_estudiante', $id)
            ->orderBy('periodo', 'desc')
            ->value('periodo');

        $item = DB::table('notas')
            ->where('id_estudiante', $id)
            ->where('periodo', $periodoReciente)
            ->get();

        return view('estudcalificaciones', ['item' => $item]);
    }

    public function imprimirNotas()
    {
        $this->obtenerEstudiante();
        $id_estudiante = $this->idest;

        $periodoReciente = DB::table('notas')
            ->where('id_estudiante', $id_estudiante)
            ->orderBy('periodo', 'desc')
            ->value('periodo');

        $item = DB::table('notas')
            ->where('id_estudiante', $id_estudiante)
            ->where('periodo', $periodoReciente)
            ->get();

        $pdf = PDF::loadView('calificacionesReporte', ['item' => $item])->setPaper('a4');

        return $pdf->download('notas.pdf');
    }

    public function notas_periodo($id)
    {
        $item = DB::table('notas_periodo')->where('id_estudiante', $id)->get();

        return view('historial', ['item' => $item]);
    }

    public function imprimirHistorial()
    {
        $this->obtenerEstudiante();
        $id_estudiante = $this->idest;
        $nombre_estudiante = $this->nombre;
        $apellido_estudiante = $this->apellido;

        $item = DB::table('notas_periodo')->where('id_estudiante', $id_estudiante)->get();

        $pdf = PDF::loadView('historialReporte', ['item' => $item, 'nombre_estudiante' => $nombre_estudiante, 'apellido_estudiante' => $apellido_estudiante, 'id_estudiante' => $id_estudiante])->setPaper('a3', 'landscape');

        return $pdf->download('historial.pdf');
    }

    public function obtenerEstudiante2()
    {
        $estudianteActual = Auth::user();
        $tmpEstud = $estudianteActual->id_estudiante;
        $estudiante = DB::table('estudiantes')->where('id_estudiante', $tmpEstud)->first();

        if (!$estudiante) {
            return redirect()->back()->with('error', 'No se encontró ningún estudiante.');
        }

        $id = $estudiante->id_estudiante;

        return redirect()->route('historial.id', ['id' => $id]);
    }
    public function clases_profesores()
    {
        $data = array(
            'lista' => DB::table('cursos')->get(),
        );
        $data1 = array(
            'list' => DB::table('profesores')->get(),
        );
        return view('asignacionClasedocente', $data, $data1);
    }

    public function clases()
    {
        $data = array(
            'lista' => DB::table('cursos')->get(),
        );
        return view('horarios', $data);
    }

    public function profesores()
    {
        $data = array(
            'lista' => DB::table('profesores')->get(),
        );
        return view('clases', $data);
    }

    public function calificaciones()
    {
        $data = array(
            'lista' => DB::table('cursos')->get(),
        );
        $data1 = array(
            'list' => DB::table('estudiantes')->get(),
        );
        return view('registrarCalificaciones', $data, $data1);
    }
}
