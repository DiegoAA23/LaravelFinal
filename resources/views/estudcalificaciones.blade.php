<x-app-layout>
    <x-slot name="header" class="mt-6">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-6 ml-6">
            {{ __('Calificaciones') }}
        </h2>
    </x-slot>

    @php $cont = 0; @endphp
    <div class="pb-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-transparent dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-2 text-black dark:text-black items-center justify-center w-full">
                    @if (!$item->isEmpty())
                    <x-buttonWhite class="ml-3 mb-6 mt-6">
                        <a href="/imprimir-notas">DESCARGAR BOLETA</a>
                    </x-buttonWhite>
                    @endif
                    <div class="bg-gray-800 p-6 rounded-lg shadow-md mx-4 border border-gray-400 mb-6">
                        <div class="container mx-auto p-8">
                            <h2 class="text-xl font-bold mb-6 text-white text-center">Calificaciones del Estudiante</h2>
                            @if($item->isEmpty())
                            <p class="text-white text-center">No se encontraron calificaciones para este estudiante.</p>
                            @else
                            @foreach ($item as $it)
                            @if ($cont == 0)
                            <div class="bg-white rounded-lg shadow p-6 mb-6">
                                <h2 class="text-lg font-semibold mb-2">Información del Estudiante</h2>
                                <p><strong>Nombre: {{ Crypt::decryptString($it->nombre_estudiante) }} {{
                                        Crypt::decryptString($it->apellido_estudiante) }}</strong></p>
                                <p><strong>Cuenta: {{ Crypt::decryptString($it->id_estudiante) }}</strong></p>
                                <p><strong>Carrera: </strong>Ingeniería en Ciencias de la Computación</p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @endif
                                <div class="bg-white rounded-lg shadow p-6">
                                    <h2 class="text-lg font-semibold mb-2">{{ Crypt::decryptString($it->nombre_clase) }}
                                    </h2>
                                    @if (!$it->nombre_profesor)
                                    <p><strong>Profesor: Sin Asignar</strong></p>
                                    <p><strong>Nota: @if ($it->nota == 0.0)
                                            N.A.
                                            @else{{ $it->nota }}@endif</strong></p>
                                    @else
                                    <p><strong>Profesor: {{ Crypt::decryptString($it->nombre_profesor) }} {{
                                            Crypt::decryptString($it->apellido_profesor) }}</strong></p>
                                    <p><strong>Nota: @if ($it->nota == 0.0)
                                            N.A.
                                            @else{{ $it->nota }}@endif</strong></p>
                                    @endif

                                </div>
                                @php $cont++; @endphp
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>