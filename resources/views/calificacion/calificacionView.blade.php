<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-6 mb-2">
            {{ __('Calificaciones') }}
        </h2>
    </x-slot>

    <div class="pb-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-8">
                <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
                    @if ($errors->any())
                    <div class="bg-red-500 text-white p-4 rounded mb-4">
                        <strong>Atención:</strong> Por favor, corrija los siguientes errores:
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('success'))
                    <div class="bg-green-500 text-white p-4 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-500 text-white p-4 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="mb-4">
                        <a href="{{ route('calificacionesC.create') }}" 
                        class='inline-flex items-center px-4 py-2 bg-gray-800 border border-white rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-300 disabled:opacity-25 transition ease-in-out duration-150'>
                        Calificar</a>
                    </div>

                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">#</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Curso</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Alumno</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Nota</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $contador = 1; @endphp
                                @foreach($claseProfe as $claseP)
                                <tr>
                                    <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{ $contador }}</td>
                                    <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{ Crypt::decryptString($claseP->nombre_clase) }}</td>
                                    <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{ Crypt::decryptString($claseP->nombre) }} {{ Crypt::decryptString($claseP->apellido) }}</td>
                                    <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{ $claseP->nota }}</td>

                                    <td class="border px-4 py-2 text-center">
                                        <div class="flex justify-center">
                                            <a href="{{ route('calificacionEdit', $claseP->id_calificacion) }}" class="bg-cyan-900 dark:bg-cyan-900 hover:bg-indigo-600 dark:hover:bg-cyan-700 text-white font-bold py-2 px-4 rounded mr-2">Editar</a>
                                        </div> 
                                    </td>
                                </tr>
                                @php $contador++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>