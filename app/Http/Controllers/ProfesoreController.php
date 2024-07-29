<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profesore;
use App\Models\Estudiante;
use App\Models\Estado;
use App\Models\RoleUser;
use App\Models\Especialidad;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Clase;
use Barryvdh\DomPDF\Facade\PDF;

class ProfesoreController extends Controller
{
    protected $profesore;
    protected $especialidad;
    protected $user;
    protected $role;
    private $idest;
    public function __construct(Profesore $profesore, Especialidad $especialidad, User $user, RoleUser $role)
    {
        $this->profesore = $profesore;
        $this->especialidad = $especialidad;
        $this->user = $user;
        $this->role = $role;
    }

    public function index()
    {
        $profesores = $this->profesore
            ->orderBy('estado_id', 'asc')
            ->orderBy('nombre', 'asc')
            ->get();

        foreach ($profesores as $profesore) {
            $profesore->idDec = $profesore->id_profesor;
            $profesore->id_profesor = Crypt::decryptString($profesore->id_profesor);
            $profesore->nombre = Crypt::decryptString($profesore->nombre);
            $profesore->apellido = Crypt::decryptString($profesore->apellido);
            $profesore->correo_electronico = Crypt::decryptString($profesore->correo_electronico);
            $profesore->telefono = Crypt::decryptString($profesore->telefono);

            $especialidad = $this->especialidad->find($profesore->id_especialidad);
        }

        $this->idest = $profesores;
        return view('profesor.profesorView', compact('profesores'));
    }

    public function imprimirProfesores()
    {
        $this->index();
        $profesores = $this->idest;

        $pdf = PDF::loadView('profesor.profesorReporte', compact('profesores'))->setPaper('a4', 'landscape');

        return $pdf->download('profesores.pdf');
    }

    public function create()
    {
        $especialidades = $this->especialidad->where('estado_id', '!=', 2)->get();
        return view('profesor.create', compact('especialidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_profesor' =>  [
                'required',
                'unique:profesores,id_profesor',
                'regex:/^[0-1][0-8](0[1-9]|1[0-9]|2[0-8])(19\d{2}|200\d|2010)\d{5}$/'
            ],
            'nombre' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[\pL\s]+$/u'],
            'apellido' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[\pL\s]+$/u'],
            'id_especialidad' => 'required',
            'correo_electronico' => 'required|string|min:4|max:50|unique:profesores,correo_electronico',
            'telefono' => 'required|min:8|max:8|unique:profesores,telefono',
        ]);


        //validacion de unique
        $profesores = Profesore::all();
        foreach ($profesores as $profesore) {
            $profesore->id_profesor_decrypt = Crypt::decryptString($profesore->id_profesor);
            $profesore->correo_electronico_decrypt = Crypt::decryptString($profesore->correo_electronico);
            $profesore->telefono_decrypt = Crypt::decryptString($profesore->telefono);
        }

        foreach ($profesores as $profesore) {
            if ($profesore->id_profesor_decrypt === $request->id_profesor) {
                return redirect()->back()->withInput()->withErrors(['id_profesor' => 'The professor ID is already in use.']);
            }
            if ($profesore->correo_electronico_decrypt === $request->correo_electronico) {
                return redirect()->back()->withInput()->withErrors(['correo_electronico' => 'The email is already in use.']);
            }
            if ($profesore->telefono_decrypt === $request->telefono) {
                return redirect()->back()->withInput()->withErrors(['telefono' => 'The phone number is already in use.']);
            }
        }


        $estudiantes = Estudiante::all();
        foreach ($estudiantes as $estudiante) {
            $estudiante->id_estudiante_decrypt = Crypt::decryptString($estudiante->id_estudiante);
            $estudiante->correo_electronico_decrypt = Crypt::decryptString($estudiante->correo_electronico);
        }


        try {
            $encryptedDNI = Crypt::encryptString($request->id_profesor);
            $encryptedName = Crypt::encryptString($request->nombre);
            $encryptedApellido = Crypt::encryptString($request->apellido);
            $encryptedCorreo = Crypt::encryptString($request->correo_electronico);
            $encryptedTelefono = Crypt::encryptString($request->telefono);
            $nombreConcatenado = $request->nombre . ' ' . $request->apellido;
            $encryptedNombreCompleto = Crypt::encryptString($nombreConcatenado);

            $this->profesore->create([
                'id_profesor' => $encryptedDNI,
                'nombre' => $encryptedName,
                'apellido' => $encryptedApellido,
                'id_especialidad' => $request->id_especialidad,
                'correo_electronico' => $encryptedCorreo,
                'telefono' => $encryptedTelefono,
                'estado_id' => 1,
            ]);

            $users = $this->user->all();
            $userExists = false;
            foreach ($users as $user) {
                $id_estudiante_decrypt = $user->id_estudiante ? Crypt::decryptString($user->id_estudiante) : null;
                $id_profesor_decrypt = $user->id_profesor ? Crypt::decryptString($user->id_profesor) : null;
                if ($id_estudiante_decrypt === $request->id_profesor || $id_profesor_decrypt === $request->id_profesor) {
                    $userExists = true;
                    $user->update(['id_profesor' => $encryptedDNI]);
                    break;
                }
            }

            $users = $this->user->all();
            $userExists = false;
            foreach ($users as $user) {
                $id_estudiante_decrypt = $user->id_estudiante ? Crypt::decryptString($user->id_estudiante) : null;
                $id_profesor_decrypt = $user->id_profesor ? Crypt::decryptString($user->id_profesor) : null;
                if ($id_estudiante_decrypt === $request->id_profesor || $id_profesor_decrypt === $request->id_profesor) {
                    $userExists = true;
                    $user->update(['id_profesor' => $encryptedDNI]);
                    break;
                }
            }

            if (!$userExists) {
                $this->user->create([
                    'name' => $encryptedNombreCompleto,
                    'email' => $encryptedCorreo,
                    'password' => bcrypt(strtolower($request->correo_electronico)),
                    'id_profesor' => $encryptedDNI,
                ]);
                $nuevo = $this->user->latest()->first();

                $this->role->create([
                    'user_id' => $nuevo->id,
                    'role_id' => 3, // Profesor
                ]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('profesorView')->with('success', 'Profesor creado exitosamente');
    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $profesore = $this->profesore->findOrFail($id);

        $profesore->nombre = Crypt::decryptString($profesore->nombre);
        $profesore->apellido = Crypt::decryptString($profesore->apellido);
        $profesore->correo_electronico = Crypt::decryptString($profesore->correo_electronico);
        $profesore->telefono = Crypt::decryptString($profesore->telefono);

        $especialidades = $this->especialidad->where('estado_id', '!=', 2)->get();

        return view('profesor.edit', compact('profesore', 'especialidades'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[\pL\s]+$/u'],
            'apellido' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[\pL\s]+$/u'],
            'id_especialidad' => 'required',
            'correo_electronico' => 'required|string|min:4|max:50|unique:profesores,correo_electronico,' . $id . ',id_profesor',
            'telefono' => 'required|min:8|max:8|unique:profesores,telefono,' . $id . ',id_profesor',
            'estado_id' => 'required|min:1|max:1'
        ]);

        $profesores = Profesore::all();
        foreach ($profesores as $profesore) {
            $profesore->correo_electronico_decrypt = Crypt::decryptString($profesore->correo_electronico);
            $profesore->telefono_decrypt = Crypt::decryptString($profesore->telefono);
        }

        foreach ($profesores as $profesore) {
            if ($profesore->correo_electronico_decrypt === $request->correo_electronico && $profesore->id_profesor != $id) {
                return redirect()->back()->withInput()->withErrors(['correo_electronico' => 'The email is already in use.']);
            }
            if ($profesore->telefono_decrypt === $request->telefono && $profesore->id_profesor != $id) {
                return redirect()->back()->withInput()->withErrors(['telefono' => 'The phone number is already in use.']);
            }
        }

        try {
            $profesore = $this->profesore->findOrFail($id);
            $currentEmailDecrypted = Crypt::decryptString($profesore->correo_electronico);
            $encryptedName = Crypt::encryptString($request->nombre);
            $encryptedApellido = Crypt::encryptString($request->apellido);
            $encryptedCorreo = Crypt::encryptString($request->correo_electronico);
            $encryptedTelefono = Crypt::encryptString($request->telefono);

            $profesore->update([
                'nombre' => $encryptedName,
                'apellido' => $encryptedApellido,
                'id_especialidad' => $request->id_especialidad,
                'correo_electronico' => $encryptedCorreo,
                'telefono' => $encryptedTelefono,
                'estado_id' => $request->estado_id,
            ]);
            $usid = $this->userProfesor($id);
            if ($request->estado_id == 1) {
                $this->role->create([
                    'user_id' => $usid,
                    'role_id' => 3, // Estudiante
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

            $estudiantes = Estudiante::all();
            foreach ($estudiantes as $estudiante) {
                try {
                    $estudianteEmailDecrypted = Crypt::decryptString($estudiante->correo_electronico);
                    if ($estudianteEmailDecrypted === $currentEmailDecrypted) {
                        $estudiante->update(['correo_electronico' => $encryptedCorreo]);
                        break;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            if ($profesore->estado_id == 2) {
                $clases = Clase::where('id_profesor', $profesore->id_profesor)->get();
                $nuevoProfesor = Profesore::whereDoesntHave('clases')->first();

                foreach ($clases as $clase) {
                    if ($nuevoProfesor) {
                        $clase->update(['id_profesor' => $nuevoProfesor->id_profesor]);
                    } else {
                        // Si no hay profesores sin clases asignadas, lo coloca nulo
                        $clase->update(['id_profesor' => null]);
                    }
                }
            }


            return redirect()->route('profesorView')->with('success', 'Profesor actualizado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    private $usuario;
    public function userProfesor($id_profesor)
    {
        $user = User::where('id_profesor', $id_profesor)->first();
        $this->usuario = $user->id;
        return $this->usuario;
    }
    public function destroy(string $id)
    {
        try {
            $profesore = $this->profesore->findOrFail($id);

            if ($profesore->estado_id == 1) {
                $profesore->update(['estado_id' => 2]);
                $us = $this->userProfesor($id);
                DB::statement('DELETE FROM role_user WHERE user_id = ? AND role_id = ?', [$us, 3]);
                $clases = Clase::where('id_profesor', $profesore->id_profesor)->get();
                $nuevoProfesor = Profesore::whereDoesntHave('clases')->first();
                foreach ($clases as $clase) {
                    if ($nuevoProfesor) {
                        $clase->update(['id_profesor' => $nuevoProfesor->id_profesor]);
                    } else {
                        // Si no hay profesores sin clases asignadas, lo coloca nulo
                        $clase->update(['id_profesor' => null]);
                    }
                }
                return redirect()->route('profesorView')->with('success', 'Profesor desactivado exitosamente');
            } else {
                return redirect()->route('profesorView')->with('info', 'El profesor ya está desactivado');
            }
        } catch (\Exception $e) {
            return redirect()->route('profesorView')->withErrors(['error' => $e->getMessage()]);
        }
    }
}
