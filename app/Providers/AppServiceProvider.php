<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckRole;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Compartir la variable 'usuario' con todas las vistas
        View::composer('*', function ($view) {
            $usuario = Auth::user();
            $tipo = 0;

            if ($usuario) {
                if ($usuario->id_estudiante !== null && $usuario->id_profesor !== null) {
                    $tipo = 3;
                } else if ($usuario->id_profesor !== null) {
                    $tipo = 2;
                } else if ($usuario->id_estudiante !== null) {
                    $tipo = 1;
                }
            }

            $view->with('usuario', $tipo);
            $view->with('idest',);
        });

        View::composer('*', function ($view) {
            if (!isset($view->periodo)) {
                $view->with('periodo', 0);
            }
        });

        View::composer('*', function ($view) {
            $lista = DB::table('clases_horarios')->get();

            $view->with('lista', $lista);
        });

        View::composer('*', function ($view) {
            if ($estudianteActual = Auth::user()) {
                $tmpEstud = $estudianteActual->id_estudiante;
                if ($tmpEstud) {
                    $estudiante = DB::table('estudiantes')->where('id_estudiante', $tmpEstud)->first();
                    $idest = $estudiante->id_estudiante;
                    $view->with('idest', $idest);
                } else {
                    //h
                }
            }

            View::composer('*', function ($view) {
                if ($profesorActual = Auth::user()) {
                    $tmpProfe = $profesorActual->id_profesor;
                    if ($tmpProfe) {
                        $profesor = DB::table('profesores')->where('id_profesor', $tmpProfe)->first();
                        $idprof = $profesor->id_profesor;
                        $view->with('idprof', $idprof);
                    } else {
                        //h
                    }
                }
            });
        });
    }
}
