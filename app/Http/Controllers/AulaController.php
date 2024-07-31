<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use Illuminate\Http\Request;
use App\Models\Estado;
use Barryvdh\DomPDF\Facade\PDF;

class AulaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $idest;
    public function index()
    {
        $aulas = Aula::all();
        $this->idest = $aulas;
        return view('aula.aulaView', compact('aulas'));
    }
    public function imprimirAulas()
    {
        $this->index();
        $aulas = $this->idest;

        $pdf = PDF::loadView('aula.aulaReporte', compact('aulas'))->setPaper('a4', 'landscape');

        return $pdf->download('aulas.pdf');
    }
    public function create()
    {
        return view('aula.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'aula' => 'required|min:3|max:4|unique:aulas,aula',
        ], [
            'aula.unique' => 'El aula ya está ingresada.',
        ]);

        try {

            Aula::create([
                'aula' => $request->aula,
                'estado_id' => 1,
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        return redirect()->route('aulaView')->with('success', 'Aula Ingresada Exitosamente');
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
        $aula = Aula::findOrFail($id);
        return view('aula.edit', compact('aula'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'aula' => 'required|min:3|max:4|unique:aulas,aula,' . $id . ',id_aula',
            'estado_id' => 'required|min:1|max:1'
        ]);

        try {
            $aula = Aula::findOrFail($id);
            $aula->update($request->all());

            return redirect()->route('aulaView')->with('success', 'Aula Editada Exitosamente');;
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
            $aula = Aula::findOrFail($id);

            if ($aula->estado_id == 1) {
                $aula->update(['estado_id' => 2]);
                return redirect()->route('aulaView')->with('success', 'Aula Deshabilitada Exitosamente');
            } else {
                return redirect()->route('aulaView')->with('error', 'El Aula Ya Esta Desahabilitada');
            }
        } catch (\Exception $e) {
            return redirect()->route('aulaView');;
        }
    }
}
