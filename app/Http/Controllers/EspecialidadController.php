<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\Request;
use App\Models\Estado;
use Barryvdh\DomPDF\Facade\PDF;

class EspecialidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $idest;
    public function index()
    {
        $especialidades = Especialidad::all();
        $this->idest = $especialidades;
        return view('especialidad.especialidadView', compact('especialidades'));
    }
    public function imprimirEspecialidades()
    {
        $this->index();
        $especialidades = $this->idest;

        $pdf = PDF::loadView('especialidad.especialidadReporte', compact('especialidades'))->setPaper('a4', 'landscape');

        return $pdf->download('especialidades.pdf');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('especialidad.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'especialidad' => ['required', 'string', 'min:4', 'max:50', 'unique:especialidades,especialidad', 'regex:/^[\pL\s]+$/u'],
        ]);

        try {

            Especialidad::create([
                'especialidad' => $request->especialidad,
                'estado_id' => 1,
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        return redirect()->route('especialidadView');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $especialidad = Especialidad::findOrFail($id);
        return view('especialidad.edit', compact('especialidad'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'especialidad' => ['required', 'string', 'min:4', 'max:50', 'unique:especialidades,especialidad,' . $id . ',id_especialidad', 'regex:/^[\pL\s]+$/u'],
            'estado_id' => 'required|min:1|max:1'
        ]);

        try {
            $especialidad = Especialidad::findOrFail($id);
            $especialidad->update($request->all());

            return redirect()->route('especialidadView');
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $especialidad = Especialidad::findOrFail($id);

            if ($especialidad->estado_id == 1) {
                $especialidad->update(['estado_id' => 2]);
                return redirect()->route('especialidadView');
            } else {
                return redirect()->route('especialidadView');
            }
        } catch (\Exception $e) {
            return redirect()->route('especialidadView');
        }
    }
}
