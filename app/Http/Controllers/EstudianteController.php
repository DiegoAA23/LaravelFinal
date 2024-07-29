<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\RoleUser;
use App\Models\Profesore;
use App\Models\User;
use Barryvdh\DomPDF\Facade\PDF;

class EstudianteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $idest;
    protected $estudiante;
    protected $user;
    protected $role;
    public function __construct(Estudiante $estudiante, User $user, RoleUser $role)
    {
        $this->estudiante = $estudiante;
        $this->user = $user;
        $this->role = $role;
    }
    public function index()
    {
        $estudiantes = $this->estudiante->all();
        foreach ($estudiantes as $estudiante) {
            $estudiante->idDec = $estudiante->id_estudiante;
            $estudiante->id_estudiante = Crypt::decryptString($estudiante->id_estudiante);
            $estudiante->nombre = Crypt::decryptString($estudiante->nombre);
            $estudiante->apellido = Crypt::decryptString($estudiante->apellido);
            $estudiante->fecha_de_nacimiento = Crypt::decryptString($estudiante->fecha_de_nacimiento);
            $estudiante->direccion = Crypt::decryptString($estudiante->direccion);
            $estudiante->correo_electronico = Crypt::decryptString($estudiante->correo_electronico);
            $estudiante->telefono = Crypt::decryptString($estudiante->telefono);
        }
        $this->idest = $estudiantes;
        //nombre de la pantalla vista    la otra variable de arriba
        return view('estudiante.estudianteView', compact('estudiantes'));
    }
    public function imprimirEstudiantes()
    {
        $this->index();
        $estudiantes = $this->idest;

        $pdf = PDF::loadView('estudiante.estudianteReporte', compact('estudiantes'))->setPaper('a4', 'landscape');

        return $pdf->download('estudiantes.pdf');
    }


    public function create()
    {
        return view('estudiante.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_estudiante' => [
                'required',
                'unique:estudiantes,id_estudiante',
                'regex:/^[0-1][0-8](0[1-9]|1[0-9]|2[0-8])(19\d{2}|200\d|2010)\d{5}$/'
            ],
            'nombre' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[\pL\s]+$/u'],
            'apellido' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[\pL\s]+$/u'],
            'fecha_de_nacimiento' => 'required|date|before:today',
            'genero' => 'required',
            'direccion' => 'required|string|min:3|max:100',
            'telefono' => 'required|min:8|max:8|unique:estudiantes,telefono',
            'correo_electronico' => 'required|string|min:4|max:50|unique:estudiantes,correo_electronico',
        ]);

        $estudiantes = $this->estudiante::all();
        foreach ($estudiantes as $estudiante) {
            $estudiante->id_estudiante_decrypt = Crypt::decryptString($estudiante->id_estudiante);
            $estudiante->correo_electronico_decrypt = Crypt::decryptString($estudiante->correo_electronico);
            $estudiante->telefono_decrypt = Crypt::decryptString($estudiante->telefono);
        }

        foreach ($estudiantes as $estudiante) {
            if ($estudiante->id_estudiante_decrypt === $request->id_estudiante) {
                return redirect()->back()->withInput()->withErrors(['id_estudiante' => 'The professor ID is already in use.']);
            }
            if ($estudiante->correo_electronico_decrypt === $request->correo_electronico) {
                return redirect()->back()->withInput()->withErrors(['correo_electronico' => 'The email is already in use.']);
            }
            if ($estudiante->telefono_decrypt === $request->telefono) {
                return redirect()->back()->withInput()->withErrors(['telefono' => 'The phone number is already in use.']);
            }
        }


        // Crear registro en la base de datos
        try {
            $encryptedDNI = Crypt::encryptString($request->id_estudiante);
            $encryptedName = Crypt::encryptString($request->nombre);
            $encryptedApellido = Crypt::encryptString($request->apellido);
            $encryptedCorreo = Crypt::encryptString($request->correo_electronico);
            $encryptedTelefono = Crypt::encryptString($request->telefono);
            $encryptedFecha = Crypt::encryptString($request->fecha_de_nacimiento);
            $encryptedDireccion = Crypt::encryptString($request->direccion);
            $nombreConcatenado = $request->nombre . ' ' . $request->apellido;
            $encryptedNombreCompleto = Crypt::encryptString($nombreConcatenado);


            $this->estudiante->create([
                'id_estudiante' => $encryptedDNI,
                'nombre' => $encryptedName,
                'apellido' => $encryptedApellido,
                'fecha_de_nacimiento' => $encryptedFecha,
                'correo_electronico' => $encryptedCorreo,
                'genero' => $request->genero,
                'direccion' => $encryptedDireccion,
                'telefono' => $encryptedTelefono,
                'estado_id' => 1,
            ]);

            $users = $this->user->all();
            $userExists = false;
            foreach ($users as $user) {
                $id_estudiante_decrypt = $user->id_estudiante ? Crypt::decryptString($user->id_estudiante) : null;
                $id_profesor_decrypt = $user->id_profesor ? Crypt::decryptString($user->id_profesor) : null;
                if ($id_estudiante_decrypt === $request->id_estudiante || $id_profesor_decrypt === $request->id_estudiante) {
                    $userExists = true;
                    $user->update(['id_estudiante' => $encryptedDNI]);
                    break;
                }
            }

            if (!$userExists) {
                $this->user->create([
                    'name' => $encryptedNombreCompleto,
                    'email' => $encryptedCorreo,
                    'password' => bcrypt(strtolower($request->correo_electronico)),
                    'id_estudiante' => $encryptedDNI,
                ]);
                $nuevo = $this->user->latest()->first();

                $this->role->create([
                    'user_id' => $nuevo->id,
                    'role_id' => 2, // Estudiante
                ]);
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        return redirect()->route('estudianteView');
    }

    /**
     * Display the specified resource.
     */
    public function show(Estudiante $estudiante)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id_estudiante)
    {
        $estudiante = $this->estudiante->findOrFail($id_estudiante);

        $estudiante->nombre = Crypt::decryptString($estudiante->nombre);
        $estudiante->apellido = Crypt::decryptString($estudiante->apellido);
        $estudiante->correo_electronico = Crypt::decryptString($estudiante->correo_electronico);
        $estudiante->fecha_de_nacimiento = Crypt::decryptString($estudiante->fecha_de_nacimiento);
        $estudiante->direccion = Crypt::decryptString($estudiante->direccion);
        $estudiante->telefono = Crypt::decryptString($estudiante->telefono);

        return view('estudiante.edit', compact('estudiante'));
    }

    public function update(Request $request, $id_estudiante)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[\pL\s]+$/u'],
            'apellido' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[\pL\s]+$/u'],
            'fecha_de_nacimiento' => 'required|date|before:today',
            'genero' => 'required',
            'direccion' => 'required|string|min:3|max:100',
            'telefono' => 'required|min:8|max:8|unique:estudiantes,telefono,' . $id_estudiante . ',id_estudiante',
            'correo_electronico' => 'required|string|min:4|max:50|unique:estudiantes,correo_electronico,' . $id_estudiante . ',id_estudiante',
        ]);

        // Validación de unique
        $estudiantes = Estudiante::all();
        foreach ($estudiantes as $estudiante) {
            $estudiante->id_estudiante_decrypt = Crypt::decryptString($estudiante->id_estudiante);
            $estudiante->correo_electronico_decrypt = Crypt::decryptString($estudiante->correo_electronico);
            $estudiante->telefono_decrypt = Crypt::decryptString($estudiante->telefono);
        }

        foreach ($estudiantes as $estudiante) {
            if ($estudiante->correo_electronico_decrypt === $request->correo_electronico && $estudiante->id_estudiante != $id_estudiante) {
                return redirect()->back()->withInput()->withErrors(['correo_electronico' => 'The email is already in use.']);
            }
            if ($estudiante->telefono_decrypt === $request->telefono && $estudiante->id_estudiante != $id_estudiante) {
                return redirect()->back()->withInput()->withErrors(['telefono' => 'The phone number is already in use.']);
            }
        }

        try {
            $estudiante = $this->estudiante->findOrFail($id_estudiante);
            $currentEmailDecrypted=$estudiante->correo_electronico;
            $encryptedName = Crypt::encryptString($request->nombre);
            $encryptedApellido = Crypt::encryptString($request->apellido);
            $encryptedNacimiento = Crypt::encryptString($request->fecha_de_nacimiento);
            $encryptedDireccion = Crypt::encryptString($request->direccion);
            $encryptedTelefono = Crypt::encryptString($request->telefono);
            $encryptedCorreo = Crypt::encryptString($request->correo_electronico);

            $estudiante->update([
                'nombre' => $encryptedName,
                'apellido' => $encryptedApellido,
                'fecha_de_nacimiento' => $encryptedNacimiento,
                'genero' => $request->genero,
                'direccion' => $encryptedDireccion,
                'telefono' => $encryptedTelefono,
                'correo_electronico' => $encryptedCorreo,
                'estado_id' => $request->estado_id,
            ]);
            $usid = $this->userEstudiante($id_estudiante); //
            if ($request->estado_id == 1) {
                $this->role->create([
                    'user_id' => $usid,
                    'role_id' => 2, // Estudiante
                ]);
            }
            $users = User::all();
            foreach ($users as $user) {
                try {
                    $userEmailDecrypted = Crypt::decryptString($user->email);
                    if ($userEmailDecrypted === $currentEmailDecrypted) {
                        $user->update(['email' => $encryptedCorreo]);
                        break;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            // Actualizar correo en profesor
            $profesores = Profesore::all();
            foreach ($profesores as $profesor) {
                try {
                    $profesorEmailDecrypted = Crypt::decryptString($profesor->correo_electronico);
                    if ($profesorEmailDecrypted === $currentEmailDecrypted) {
                        $profesor->update(['correo_electronico' => $encryptedCorreo]);
                        break;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            return redirect()->route('estudianteView');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private $usuario;
    public function userEstudiante($id_estudiante)
    {
        $user = User::where('id_estudiante', $id_estudiante)->first();
        $this->usuario = $user->id;
        return $this->usuario;
    }

    public function destroy($id_estudiante)
    {
        try {
            $estudiante = Estudiante::findOrFail($id_estudiante);
            if ($estudiante->estado_id == 1) {
                $estudiante->update(['estado_id' => 2]);
                $us = $this->userEstudiante($id_estudiante);
                DB::statement('DELETE FROM role_user WHERE user_id = ? AND role_id = ?', [$us, 2]);
                return redirect()->route('estudianteView')->with('success', 'Estudiante desactivado exitosamente.');
            } else {
                return redirect()->route('estudianteView');
            }
        } catch (\Exception $e) {
            return redirect()->route('estudianteView')->with('error', 'Error al desactivar el estudiante: ' . $e->getMessage());
        }
    }


    public function matricularClases(Request $request)
    {
        $periodo = $request->query('periodo', 0);

        return view('matricularClases', compact('periodo'));
    }
}
