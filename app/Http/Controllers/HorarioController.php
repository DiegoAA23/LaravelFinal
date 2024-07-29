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
        $aulas = $this->aula::all();
        $cursos = $this->clase::all();
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
            'dias' => ['required', 'string', 'min:1', 'max:6', 'regex:/^[LMMJVS]+$/'],
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|after:hora_inicio',
        ]);

        // Verificar aula
        $horarios = $this->horarioVal::where('aula_id', $request->aula_id)->get();

        $conflictingAulas = $horarios->filter(function ($horario) use ($request) {
            $fecha_inicio = Crypt::decryptString($horario->fecha_inicio);
            $fecha_fin = Crypt::decryptString($horario->fecha_fin);
            $hora_inicio = Crypt::decryptString($horario->hora_inicio);
            $hora_fin = Crypt::decryptString($horario->hora_fin);

            return (
                ($request->fecha_inicio <= $fecha_fin && $request->fecha_fin >= $fecha_inicio) &&
                ($request->hora_inicio <= $hora_fin && $request->hora_fin >= $hora_inicio)
            );
        });

        $conflictingAulas = $conflictingAulas->values()->all();

        foreach ($conflictingAulas as $conflict) {
            if ($this->hasDayConflict($conflict->dias, $request->dias)) {
                throw ValidationException::withMessages(['aula_id' => 'The classroom is already occupied at the indicated time and dates.']);
            }
        }


        $curso = $this->clase::findOrFail($request->id_curso);

        $horarios = HorarioVal::where('id_profesor', Crypt::decryptString($curso->id_profesor))->get();

        $conflictingProfesor = $horarios->filter(function ($horario) use ($request) {
            $fecha_inicio = Crypt::decryptString($horario->fecha_inicio);
            $fecha_fin = Crypt::decryptString($horario->fecha_fin);
            $hora_inicio = Crypt::decryptString($horario->hora_inicio);
            $hora_fin = Crypt::decryptString($horario->hora_fin);

            return (
                ($request->fecha_inicio <= $fecha_fin && $request->fecha_fin >= $fecha_inicio) &&
                ($request->hora_inicio <= $hora_fin && $request->hora_fin >= $hora_inicio)
            );
        });

        $conflictingProfesor = $conflictingProfesor->values()->all();

        foreach ($conflictingProfesor as $conflict) {
            if ($this->hasDayConflict($conflict->dias, $request->dias)) {
                throw ValidationException::withMessages(['id_curso' => 'The teacher is already occupied at the indicated time and dates.']);
            }
        }

        $encryptedDias = Crypt::encryptString($request->dias);
        $encryptedFechaInicio = Crypt::encryptString($request->fecha_inicio);
        $encryptedFechaFin = Crypt::encryptString($request->fecha_fin);
        $encryptedHoraInicio = Crypt::encryptString($request->hora_inicio);
        $encryptedHoraFin = Crypt::encryptString($request->hora_fin);

        try {
            $this->horario::create([
                'id_curso' => $request->id_curso,
                'aula_id' => $request->aula_id,
                'dias' =>  $encryptedDias,
                'fecha_inicio' => $encryptedFechaInicio,
                'fecha_fin' => $encryptedFechaFin,
                'hora_inicio' => $encryptedHoraInicio,
                'hora_fin' => $encryptedHoraFin,
                'estado_id' => 1
            ]);

            return redirect()->route('horarioView');
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
        $aulas = $this->aula::all();
        $clases = $this->clase::all();
        foreach ($clases as $clase) {
            $clase->nombre_clase = Crypt::decryptString($clase->nombre_clase);
        }
        return view('horario.edit', compact('horario', 'aulas', 'clases'));
    }

    public function update(Request $request, $id)
    {
        $desencriptar = function ($valor) {
            return Crypt::decryptString($valor);
        };

        // Verificar aula
        $conflictingAulas = HorarioVal::where('aula_id', $request->aula_id)
            ->get();

        foreach ($conflictingAulas as $conflict) {
            $conflictFechaInicio = $desencriptar($conflict->fecha_inicio);
            $conflictFechaFin = $desencriptar($conflict->fecha_fin);
            $conflictHoraInicio = $desencriptar($conflict->hora_inicio);
            $conflictHoraFin = $desencriptar($conflict->hora_fin);
            $conflictDias = $conflict->dias; // Asumimos que los días no están encriptados

            if (
                ($request->fecha_inicio <= $conflictFechaFin && $request->fecha_fin >= $conflictFechaInicio) &&
                ($request->hora_inicio <= $conflictHoraFin && $request->hora_fin >= $conflictHoraInicio) &&
                $this->hasDayConflict($conflictDias, $request->dias)
            ) {
                throw ValidationException::withMessages(['aula_id' => 'The classroom is already occupied at the indicated time and dates.']);
            }
        }

        // Verificar profesor
        $curso = Clase::findOrFail($request->id_curso);
        $conflictingProfesor = HorarioVal::where('id_profesor', $curso->id_profesor)
            ->get();

        foreach ($conflictingProfesor as $conflict) {
            $conflictFechaInicio = $desencriptar($conflict->fecha_inicio);
            $conflictFechaFin = $desencriptar($conflict->fecha_fin);
            $conflictHoraInicio = $desencriptar($conflict->hora_inicio);
            $conflictHoraFin = $desencriptar($conflict->hora_fin);
            $conflictDias = $conflict->dias; // Asumimos que los días no están encriptados

            if (
                ($request->fecha_inicio <= $conflictFechaFin && $request->fecha_fin >= $conflictFechaInicio) &&
                ($request->hora_inicio <= $conflictHoraFin && $request->hora_fin >= $conflictHoraInicio) &&
                $this->hasDayConflict($conflictDias, $request->dias)
            ) {
                throw ValidationException::withMessages(['id_curso' => 'The teacher is already occupied at the indicated time and dates.']);
            }
        }

        $encryptedDias = Crypt::encryptString($request->dias);
        $encryptedFechaInicio = Crypt::encryptString($request->fecha_inicio);
        $encryptedFechaFin = Crypt::encryptString($request->fecha_fin);
        $encryptedHoraInicio = Crypt::encryptString($request->hora_inicio);
        $encryptedHoraFin = Crypt::encryptString($request->hora_fin);

        try {
            $horario = $this->horario::findOrFail($id);
            $horario->update([
                'id_curso' => $request->id_curso,
                'aula_id' => $request->aula_id,
                'dias' =>  $encryptedDias,
                'fecha_inicio' => $encryptedFechaInicio,
                'fecha_fin' => $encryptedFechaFin,
                'hora_inicio' => $encryptedHoraInicio,
                'hora_fin' => $encryptedHoraFin,
                'estado_id' => 1,
            ]);

            return redirect()->route('horarioView');
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
