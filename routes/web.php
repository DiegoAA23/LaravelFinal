<?php

use App\Http\Controllers\Clases;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\EstudianteNotas;
use App\Http\Controllers\ProfesoreController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\ClasesController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\NivelesUsuario;
use App\Http\Controllers\PeriodoController;
use App\Http\Controllers\CalificacionController;

Route::get('/', function () {
    return view('/auth/login');
});

/*Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');*/
Route::middleware(['auth', 'checkRole:admin,maestro,estudiante'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

//RUTAS DE IMPRESION PDFs
Route::get('/imprimir-notas', [EstudianteNotas::class, 'imprimirNotas']);
Route::get('/imprimir-historial', [EstudianteNotas::class, 'imprimirHistorial']);
Route::get('/imprimir-profesores', [ProfesoreController::class, 'imprimirProfesores']);
Route::get('/imprimir-estudiantes', [EstudianteController::class, 'imprimirEstudiantes']);
Route::get('/imprimir-clases', [ClasesController::class, 'imprimirClases']);
Route::get('/imprimir-horarios', [HorarioController::class, 'imprimirHorarios']);
Route::get('/imprimir-aulas', [AulaController::class, 'imprimirAulas']);
Route::get('/imprimir-especialidades', [EspecialidadController::class, 'imprimirEspecialidades']);

Route::middleware('auth')->group(function () {
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', CheckRole::class.':admin'])->group(function () {
    Route::get('/clase/claseView', [ClasesController::class, 'index'])->name('claseView');
    Route::get('/clase/create', [ClasesController::class, 'create'])->name('claseCreate');
    Route::get('/clase/edit/{id}', [ClasesController::class, 'edit'])->name('claseEdit');
    Route::resource('clasesC', ClasesController::class);

    Route::get('/horario/horarioView', [HorarioController::class, 'index'])->name('horarioView');
    Route::get('/horario/create', [HorarioController::class, 'create'])->name('horarioCreate');
    Route::get('/horario/edit/{id}', [HorarioController::class, 'edit'])->name('horarioEdit');
    Route::resource('horariosC', HorarioController::class);

    Route::get('/aula/aulaView', [AulaController::class, 'index'])->name('aulaView');
    Route::get('/aula/create', [AulaController::class, 'create'])->name('aulaCreate');
    Route::get('/aula/edit/{id}', [AulaController::class, 'edit'])->name('aulaEdit');
    Route::resource('aulasC', AulaController::class);

    Route::get('/profesor/profesorView', [ProfesoreController::class, 'index'])->name('profesorView');
    Route::get('/profesor/create', [ProfesoreController::class, 'create'])->name('profesorCreate');
    Route::get('/profesor/edit/{id}', [ProfesoreController::class, 'edit'])->name('profesorEdit');
    Route::resource('profesoresC', ProfesoreController::class);

    Route::get('/estudiante/estudianteView', [EstudianteController::class, 'index'])->name('estudianteView');
    Route::get('/estudiante/create', [EstudianteController::class, 'create'])->name('estudianteCreate');
    Route::get('/estudiante/edit/{id}', [EstudianteController::class, 'edit'])->name('estudianteEdit');
    Route::resource('estudiantesC', EstudianteController::class);

    Route::get('/especialidad/aulaView', [EspecialidadController::class, 'index'])->name('especialidadView');
    Route::get('/especialidad/create', [EspecialidadController::class, 'create'])->name('especialidadCreate');
    Route::get('/especialidad/edit/{id}', [EspecialidadController::class, 'edit'])->name('especialidadEdit');
    Route::resource('especialidadesC', EspecialidadController::class);

    Route::get('asignacionClasedocente', [EstudianteNotas::class, 'clases_profesores'])->name('asignacionClasedocente');
});

Route::middleware(['auth', CheckRole::class.':maestro'])->group(function () {
    Route::get('/calificacion/calificacionView', [CalificacionController::class, 'index'])->name('calificacionView');
    Route::get('/calificacion/create', [CalificacionController::class, 'create'])->name('calificacionCreate');
    Route::get('/calificacion/edit/{id}', [CalificacionController::class, 'edit'])->name('calificacionEdit');
    Route::resource('calificacionesC', CalificacionController::class);
});

Route::middleware(['auth', CheckRole::class.':estudiante'])->group(function () {
    Route::get('historial/{id}', [EstudianteNotas::class, 'notas_periodo'])->name('historial.id');
    Route::get('obtenerEstudiante', [EstudianteNotas::class, 'obtenerEstudiante'])->name('obtenerEstudiante');
    Route::get('obtenerEstudiante2', [EstudianteNotas::class, 'obtenerEstudiante2'])->name('obtenerEstudiante2');
    Route::get('estudcalificaciones/{id}', [EstudianteNotas::class, 'notas'])->name('estudcalificaciones.id');
    Route::get('matricula', [EstudianteNotas::class, 'index'])->name('matricula');
    Route::resource('matriculaC', PeriodoController::class);
    Route::get('matricularClases', [EstudianteController::class, 'matricularClases'])->name('matricularClases');
    Route::post('/matricula', [EstudianteController::class, 'registrarMatricula'])->name('matricula');
});

});

require __DIR__ . '/auth.php';
