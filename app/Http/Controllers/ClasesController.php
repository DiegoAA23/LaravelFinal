<?php

namespace App\Http\Controllers;

use App\Models\Clase;
use Illuminate\Http\Request;
use App\Models\Profesore;
use Illuminate\Support\Facades\Validator;
use App\Models\Horario;
use Illuminate\Support\Facades\Crypt;
use Barryvdh\DomPDF\Facade\PDF;
use Carbon\Carbon;


class ClasesController extends Controller
{
    private $idest;
    protected $profesore;
    private $profe;
    private $clase;
    public function __construct(Profesore $profesore, Clase $clase)
    {
        $this->profesore = $profesore;
        $this->clase = $clase;
    }
    public function index()
    {
        /* $clases = $this->clase::all();
        foreach ($clases as $clase) {
            $clase->nombre_clase = Crypt::decryptString($clase->nombre_clase);
        }*/
        $clases = Clase::all();
        foreach ($clases as $clase) {
            $profesor = $this->profesore::find($clase->id_profesor);
            $clase->nombre_clase = Crypt::decryptString($clase->nombre_clase);
            if ($profesor) {
                if ($profesor->estado_id == 2) {
                    $clase->profesor_nombre = "Sin Asignar";
                } else {
                    $clase->profesor_nombre = Crypt::decryptString($profesor->nombre) . ' ' . Crypt::decryptString($profesor->apellido);
                }
            } else {
                $clase->profesor_nombre = "Desconocido";
            }
        }

        $this->idest = $clases;
        return view('clase.claseView', compact('clases'));
    }

    public function create()
    {
        $profesores = $this->profesore::where('estado_id', '!=', 2)->get();
        foreach ($profesores as $profesore) {
            $profesore->idDec = $profesore->id_profesor;
            $profesore->id_profesor = Crypt::decryptString($profesore->id_profesor);
            $profesore->nombre = Crypt::decryptString($profesore->nombre);
            $profesore->apellido = Crypt::decryptString($profesore->apellido);
        }
        return view('clase.create', compact('profesores'));
    }

    public function imprimirClases()
    {
        $this->index();
        $clases = $this->idest;

        $pdf = PDF::loadView('clase.claseReporte', compact('clases'))->setPaper('a4', 'landscape');

        return $pdf->download('clases.pdf');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_clase' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'unique:cursos,nombre_clase',
                'regex:/^[\pL\s]+$/u'
            ],
            'id_profesor' => 'required',
            'periodo' => 'required|min:1|max:2'
        ]);

        $validator->after(function ($validator) use ($request) {
            $periodoCount = Clase::where('periodo', $request->periodo)->count();
            if ($periodoCount >= 5) {
                $validator->errors()->add('periodo', '5 records with this period already exist.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $clases = Clase::all();
        foreach ($clases as $clase) {
            $clase->id_nombre_clase_decrypt = Crypt::decryptString($clase->nombre_clase);
        }

        foreach ($clases as $clase) {
            if ($this->removeAccents(strtolower($clase->id_nombre_clase_decrypt)) === $this->removeAccents(strtolower($request->nombre_clase))) {
                return redirect()->back()->withInput()->withErrors(['nombre_clase' => 'The class name is already in use.']);
            }
        }
        try {
            $encryptedNombre = Crypt::encryptString($request->nombre_clase);
            $encryptedProfesor = $this->profe;

            $this->clase->create([
                'nombre_clase' => $encryptedNombre,
                'id_profesor' => $request->id_profesor,
                'periodo' => $request->periodo,
                'estado_id' => 1,
            ]);

            return redirect()->route('claseView')->with('success', 'Clase Agregada Exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear la clase.')->withInput();
        }
    }

    function removeAccents($string)
    {
        $search = [
            'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Þ', 'ß',
            'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'þ', 'ÿ'
        ];
        $replace = [
            'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'TH', 'ss',
            'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'd', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'th', 'y'
        ];
        return str_replace($search, $replace, $string);
    }
    public function edit($id)
    {
        $clase = $this->clase::findOrFail($id);
        $clase->nombre_clase = Crypt::decryptString($clase->nombre_clase);
        $profesores = $this->profesore::where('estado_id', '!=', 2)->get();
        foreach ($profesores as $profesore) {
            $profesore->nombre = Crypt::decryptString($profesore->nombre);
            $profesore->apellido = Crypt::decryptString($profesore->apellido);
        }
        return view('clase.edit', compact('clase', 'profesores'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre_clase' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[\pL\s]+$/u'
            ],
            'id_profesor' => 'required',
            'periodo' => 'required|min:1|max:2',
            'estado_id' => 'required|min:1|max:1'
        ], [
            'nombre_clase.regex' => 'El campo nombre de la clase solo puede contener letras y espacios.',
        ]);

        $nombreClaseRequest = $this->removeAccents(strtolower($request->nombre_clase));

        // Obtén las clases y normaliza los nombres para comparación
        $clases = Clase::where('id_curso', '!=', $id)->get();

        foreach ($clases as $clase) {
            // Desencripta el nombre de la clase y normalízalo
            $nombreClaseDb = Crypt::decryptString($clase->nombre_clase);
            $nombreClaseDb = $this->removeAccents(strtolower($nombreClaseDb));

            // Compara los nombres normalizados
            if ($nombreClaseDb === $nombreClaseRequest) {
                return redirect()->back()->withInput()->withErrors(['nombre_clase' => 'El nombre de la clase ya está en uso.']);
            }
        }

        $validator->after(function ($validator) use ($request, $id) {
            $periodoCount = $this->clase::where('periodo', $request->periodo)
                ->where('id_curso', '!=', $id)
                ->count();
            if ($periodoCount >= 5) {
                $validator->errors()->add('periodo', 'Ya existen 5 registros con este período.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {
            $clase = Clase::findOrFail($id);
            if ($request->estado_id == 2) {
                $fechaActual = Carbon::now()->format('Y-m-d');
                $horarioPendiente = Horario::where('id_curso', $id)
                    ->where('fecha_fin', '>=', $fechaActual)
                    ->exists();
                if ($horarioPendiente) {
                    return redirect()->back()->withInput()->withErrors(['estado_id' => 'La clase no puede deshabilitarse, ya que tiene horarios activos.']);
                }
            }
            $encryptedNombre = Crypt::encryptString($request->nombre_clase);

            $clase->update([
                'nombre_clase' => $encryptedNombre,
                'id_profesor' => $request->id_profesor,
                'periodo' => $request->periodo,
                'estado_id' => $request->estado_id,
            ]);
            return redirect()->route('claseView')->with('success', 'Clase Actualizada Exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar la clase.')->withInput();
        }
    }


    public function destroy($id)
    {
        /*try {
            $clase = $this->clase->findOrFail($id);

            if ($clase->estado_id == 1) {
                $clase->update(['estado_id' => 2]);
                return redirect()->route('claseView');
            } else {
                return redirect()->route('claseView');
            }
        } catch (\Exception $e) {
            return redirect()->route('claseView');
        }*/

        try {
            $clase = Clase::findOrFail($id);

            $fechaActual = Carbon::now()->format('Y-m-d');
            $horarioPendiente = Horario::where('id_curso', $id)
                ->where('fecha_fin', '>=', $fechaActual)
                ->exists();

            if ($horarioPendiente) {
                return redirect()->back()->withInput()->withErrors(['estado_id' => 'The class cannot be disabled because it has pending schedules.']);
            }

            if ($clase->estado_id == 1) {
                $clase->update(['estado_id' => 2]);
                return redirect()->route('claseView')->with('success', 'Clase Deshabilitada Exitosamente');
            } else {
                return redirect()->route('claseView')->with('info', 'La Clase Ya Está Deshabilitada');
            }
        } catch (\Exception $e) {
            return redirect()->route('claseView')->withErrors(['error' => 'Error al inhabilitar la clase.']);
        }
    }
}
