<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\HorarioVal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use App\Models\Clase;
use App\Models\Aula;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Validation\ValidationException;

class HorarioController extends Controller
{
    private $idest;
    private $horario;
    private $aula;
    private $clase;
    private $horarioVal;

    public function __construct(Horario $horario, Aula $aula, Clase $clase, HorarioVal $horarioVal)
    {
        $this->horario = $horario;
        $this->aula = $aula;
        $this->clase = $clase;
        $this->horarioVal = $horarioVal;
    }
    public function index()
    {
        $horarios = $this->horario::all();
        foreach ($horarios as $horario) {
            $horario->dias = Crypt::decryptString($horario->dias);
            $horario->fecha_inicio = Crypt::decryptString($horario->fecha_inicio);
            $horario->fecha_fin = Crypt::decryptString($horario->fecha_fin);
            $horario->hora_inicio = Crypt::decryptString($horario->hora_inicio);
            $horario->hora_fin = Crypt::decryptString($horario->hora_fin);
        }
        $this->idest = $horarios;
        return view('horario.horarioView', compact('horarios'));
    }

    public function create()
    {
        $aulas = Aula::where('estado_id', '!=', 2)->get();
        $cursos = Clase::where('estado_id', '!=', 2)->get();
        $horarios = $this->horarioVal::all();

        $clases = [];
        $horarioCursosIds = $horarios->pluck('id_curso')->toArray();
        foreach ($cursos as $curso) {
            if (!in_array($curso->id_curso, $horarioCursosIds)) {
                $clases[] = $curso;
            }
        }
        foreach ($clases as $clase) {
            $clase->nombre_clase = Crypt::decryptString($clase->nombre_clase);
        }

        return view('horario.create', compact('aulas', 'clases', 'horarios'));
    }

    public function imprimirHorarios()
    {
        $this->index();
        $horarios = $this->idest;

        $pdf = PDF::loadView('horario.horarioReporte', compact('horarios'))->setPaper('a4', 'landscape');

        return $pdf->download('horarios.pdf');
    }
    public function store(Request $request)
    {
        $request->validate([
            'id_curso' => 'required',
            'aula_id' => 'required',
            'dias' => [
                'required',
                'string',
                'min:1',
                'max:6',
                'regex:/^[LMMJVS]+$/',
                function ($attribute, $value, $fail) {
                    $daysArray = str_split($value);
                    $daysCount = array_count_values($daysArray);

                    foreach ($daysCount as $day => $count) {
                        if ($day == 'M' && $count > 2) {
                            $fail('The day M can appear at most twice.');
                        } elseif ($day != 'M' && $count > 1) {
                            $fail('The day ' . $day . ' can appear only once.');
                        }
                    }
                }
            ],
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|after:hora_inicio',
        ]);

        $encryptedDias = Crypt::encryptString($request->dias);
        $encryptedFechaInicio = Crypt::encryptString($request->fecha_inicio);
        $encryptedFechaFin = Crypt::encryptString($request->fecha_fin);
        $encryptedHoraInicio = Crypt::encryptString($request->hora_inicio);
        $encryptedHoraFin = Crypt::encryptString($request->hora_fin);

        // Verificar aula
        $conflictingAulas = HorarioVal::where('aula_id', $request->aula_id)
            ->get()
            ->filter(function ($conflict) use ($request) {
                return (
                    ($this->hasDateConflict(Crypt::decryptString($conflict->fecha_inicio), Crypt::decryptString($conflict->fecha_fin), $request->fecha_inicio, $request->fecha_fin)) &&
                    ($this->hasTimeConflict(Crypt::decryptString($conflict->hora_inicio), Crypt::decryptString($conflict->hora_fin), $request->hora_inicio, $request->hora_fin)) &&
                    ($this->hasDayConflict(Crypt::decryptString($conflict->dias), $request->dias))
                );
            });

        if ($conflictingAulas->isNotEmpty()) {
            throw ValidationException::withMessages(['aula_id' => 'The classroom is already occupied at the indicated time and dates.']);
        }

        // Verificar profesor
        $curso = Clase::findOrFail($request->id_curso);
        $conflictingProfesor = HorarioVal::where('id_profesor', $curso->id_profesor)
            ->get()
            ->filter(function ($conflict) use ($request) {
                return (
                    ($this->hasDateConflict(Crypt::decryptString($conflict->fecha_inicio), Crypt::decryptString($conflict->fecha_fin), $request->fecha_inicio, $request->fecha_fin)) &&
                    ($this->hasTimeConflict(Crypt::decryptString($conflict->hora_inicio), Crypt::decryptString($conflict->hora_fin), $request->hora_inicio, $request->hora_fin)) &&
                    ($this->hasDayConflict(Crypt::decryptString($conflict->dias), $request->dias))
                );
            });

        if ($conflictingProfesor->isNotEmpty()) {
            throw ValidationException::withMessages(['id_curso' => 'The teacher is already occupied at the indicated time and dates.']);
        }

        try {
            $this->horario::create([
                'id_curso' => $request->id_curso,
                'aula_id' => $request->aula_id,
                'dias' => $encryptedDias,
                'fecha_inicio' => $encryptedFechaInicio,
                'fecha_fin' => $encryptedFechaFin,
                'hora_inicio' => $encryptedHoraInicio,
                'hora_fin' => $encryptedHoraFin,
                'estado_id' => 1
            ]);

            return redirect()->route('horarioView')->with('success', 'Horario Agregado Exitosamente');
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
    public function edit($id)
    {
        $horario = $this->horario::findOrFail($id);
        $horario->dias = Crypt::decryptString($horario->dias);
        $horario->fecha_inicio = Crypt::decryptString($horario->fecha_inicio);
        $horario->fecha_fin = Crypt::decryptString($horario->fecha_fin);
        $horario->hora_inicio = Crypt::decryptString($horario->hora_inicio);
        $horario->hora_fin = Crypt::decryptString($horario->hora_fin);
        $aulas = Aula::where('estado_id', '!=', 2)->get();
        $clases = Clase::where('estado_id', '!=', 2)->get();
        foreach ($clases as $clase) {
            $clase->nombre_clase = Crypt::decryptString($clase->nombre_clase);
        }
        return view('horario.edit', compact('horario', 'aulas', 'clases'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_curso' => 'required',
            'aula_id' => 'required',
            'dias' => [
                'required',
                'string',
                'min:1',
                'max:6',
                'regex:/^[LMMJVS]+$/',
                function ($attribute, $value, $fail) {
                    $daysArray = str_split($value);
                    $daysCount = array_count_values($daysArray);

                    foreach ($daysCount as $day => $count) {
                        if ($day == 'M' && $count > 2) {
                            $fail('The day M can appear at most twice.');
                        } elseif ($day != 'M' && $count > 1) {
                            $fail('The day ' . $day . ' can appear only once.');
                        }
                    }
                }
            ],
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|after:hora_inicio',
        ]);

        $encryptedDias = Crypt::encryptString($request->dias);
        $encryptedFechaInicio = Crypt::encryptString($request->fecha_inicio);
        $encryptedFechaFin = Crypt::encryptString($request->fecha_fin);
        $encryptedHoraInicio = Crypt::encryptString($request->hora_inicio);
        $encryptedHoraFin = Crypt::encryptString($request->hora_fin);

        $currentHorario = $this->horario::findOrFail($id);

        // Verificar aula
        $conflictingAulas = HorarioVal::where('aula_id', $request->aula_id)
            ->where(function ($query) use ($currentHorario) {
                $query->where('id_curso', '!=', $currentHorario->id_curso)
                    ->orWhere('fecha_inicio', '!=', $currentHorario->fecha_inicio)
                    ->orWhere('fecha_fin', '!=', $currentHorario->fecha_fin)
                    ->orWhere('hora_inicio', '!=', $currentHorario->hora_inicio)
                    ->orWhere('hora_fin', '!=', $currentHorario->hora_fin)
                    ->orWhere('dias', '!=', $currentHorario->dias);
            })
            ->get()
            ->filter(function ($conflict) use ($request) {
                return (
                    $this->hasDateConflict(Crypt::decryptString($conflict->fecha_inicio), Crypt::decryptString($conflict->fecha_fin), $request->fecha_inicio, $request->fecha_fin) &&
                    $this->hasTimeConflict(Crypt::decryptString($conflict->hora_inicio), Crypt::decryptString($conflict->hora_fin), $request->hora_inicio, $request->hora_fin) &&
                    $this->hasDayConflict(Crypt::decryptString($conflict->dias), $request->dias)
                );
            });

        if ($conflictingAulas->isNotEmpty()) {
            throw ValidationException::withMessages(['aula_id' => 'The classroom is already occupied at the indicated time and dates.']);
        }

        // Verificar profesor
        $curso = Clase::findOrFail($request->id_curso);
        $conflictingProfesor = HorarioVal::where('id_profesor', $curso->id_profesor)
            ->where(function ($query) use ($currentHorario) {
                $query->where('id_curso', '!=', $currentHorario->id_curso)
                    ->orWhere('fecha_inicio', '!=', $currentHorario->fecha_inicio)
                    ->orWhere('fecha_fin', '!=', $currentHorario->fecha_fin)
                    ->orWhere('hora_inicio', '!=', $currentHorario->hora_inicio)
                    ->orWhere('hora_fin', '!=', $currentHorario->hora_fin)
                    ->orWhere('dias', '!=', $currentHorario->dias);
            })
            ->get()
            ->filter(function ($conflict) use ($request) {
                return (
                    $this->hasDateConflict(Crypt::decryptString($conflict->fecha_inicio), Crypt::decryptString($conflict->fecha_fin), $request->fecha_inicio, $request->fecha_fin) &&
                    $this->hasTimeConflict(Crypt::decryptString($conflict->hora_inicio), Crypt::decryptString($conflict->hora_fin), $request->hora_inicio, $request->hora_fin) &&
                    $this->hasDayConflict(Crypt::decryptString($conflict->dias), $request->dias)
                );
            });

        if ($conflictingProfesor->isNotEmpty()) {
            throw ValidationException::withMessages(['id_curso' => 'The teacher is already occupied at the indicated time and dates.']);
        }

        try {
            $currentHorario->update([
                'id_curso' => $request->id_curso,
                'aula_id' => $request->aula_id,
                'dias' => $encryptedDias,
                'fecha_inicio' => $encryptedFechaInicio,
                'fecha_fin' => $encryptedFechaFin,
                'hora_inicio' => $encryptedHoraInicio,
                'hora_fin' => $encryptedHoraFin,
                'estado_id' => 1,
            ]);

            return redirect()->route('horarioView')->with('success', 'Horario Actualizado Exitosamente');
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $horario = $this->horario::findOrFail($id);

            if ($horario->estado_id == 1) {
                $horario->update(['estado_id' => 2]);
                return redirect()->route('horarioView');
            } else {
                return redirect()->route('horarioView');
            }
        } catch (\Exception $e) {
            return redirect()->route('horarioView');
        }
    }


    private function hasDateConflict($start1, $end1, $start2, $end2)
    {
        return (
            ($start1 <= $end2 && $end1 >= $start2) ||
            ($start2 <= $end1 && $end2 >= $start1)
        );
    }

    private function hasTimeConflict($start1, $end1, $start2, $end2)
    {
        return (
            ($start1 <= $end2 && $end1 >= $start2) ||
            ($start2 <= $end1 && $end2 >= $start1)
        );
    }

    private function hasDayConflict($days1, $days2)
    {
        $daysArray1 = str_split($days1);
        $daysArray2 = str_split($days2);

        foreach ($daysArray1 as $day) {
            if (in_array($day, $daysArray2)) {
                return true;
            }
        }
        return false;
    }
}
