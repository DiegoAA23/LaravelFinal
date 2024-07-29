<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use Illuminate\Http\Request;
use App\Models\Profesore;
use App\Models\Estudiante;
use App\Models\Clase;
use App\Models\User;
use App\Models\Inscripcion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;



class CalificacionController extends Controller
{
    public function index()
    {
        $calificaciones = Clase::all();

        $userEmail = Auth::user()->email;

        $profesor = Profesore::where('correo_electronico', $userEmail)->first();

        $claseProfe = DB::table('clase_profe')
            ->where('id_profesor', $profesor->id_profesor)
            ->where('actualizado', 1)
            ->get();

        return view('calificacion.calificacionView', compact('claseProfe'));
    }


    public function create()
    {
        $userEmail = Auth::user()->email;
        $profesor = Profesore::where('correo_electronico', $userEmail)->first();

        $claseProfe = DB::table('clase_profe')
            ->where('id_profesor', $profesor->id_profesor)
            ->where('actualizado', 0)
            ->get();

        return view('calificacion.create', compact('claseProfe'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_inscripcion' => 'required|exists:inscripciones,id_inscripcion',
            'nota' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $userEmail = Auth::user()->email;
            $profesor = Profesore::where('correo_electronico', $userEmail)->first();

            if (!$profesor) {
                return redirect()->back()->with('error', 'Usuario no es un profesor.');
            }

            $calificacion = Calificacion::where('id_inscripcion', $request->id_inscripcion)->first();
            if (!$calificacion || $calificacion->actualizado != 0) {
                return redirect()->back()->with('error', 'Esta inscripción ya ha sido calificada o no existe.');
            }

            $calificacion->nota = $request->nota;
            $calificacion->actualizado = 1;
            $calificacion->save();

            return redirect()->route('calificacionView')->with('success', 'Nota actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar la nota: ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        $calificacion = Calificacion::findOrFail($id);
        $userEmail = Auth::user()->email;

        // Obtener el profesor utilizando el correo electrónico
        $profesor = Profesore::where('correo_electronico', $userEmail)->first();

        $claseProfe = DB::table('clase_profe')->where('id_profesor', $profesor->id_profesor)->get();
        $inscripciones = Inscripcion::all();

        return view('calificacion.edit', compact('calificacion', 'claseProfe', 'inscripciones'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'id_inscripcion' => 'required|exists:inscripciones,id_inscripcion',
            'nota' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $calificacion = Calificacion::findOrFail($id);
            $calificacion->update([
                'id_inscripcion' => $request->id_inscripcion,
                'nota' => $request->nota,
                'actualizado' => 1
            ]);

            return redirect()->route('calificacionView')->with('success', 'Calificación actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar la calificación: ' . $e->getMessage());
        }
    }


    public function destroy(Calificacion $calificacion)
    {
    }
}
