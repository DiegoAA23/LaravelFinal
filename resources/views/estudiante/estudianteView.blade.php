<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-6">
            {{ __('Estudiantes') }}
        </h2>
    </x-slot>

    <div class="pb-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-8">
                <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 overflow-x-auto">

                    <div class="mb-4">
                        <a href="{{ route('estudiantesC.create') }}"
                            class='inline-flex items-center px-4 py-2 bg-gray-800 border border-white rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-300 disabled:opacity-25 transition ease-in-out duration-150'>
                            Registrar Estudiante</a>
                    </div>

                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">ID</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Nombre</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Apellido</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Nacimiento</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Genero</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Dirección</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Teléfono</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Correo</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Estado</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($estudiantes as $estudiante)
                            <tr>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{ $estudiante->
                                    id_estudiante }}</td>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{
                                    $estudiante->nombre }}</td>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{
                                    $estudiante->apellido }}</td>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{
                                    $estudiante->fecha_de_nacimiento }}</td>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{
                                    $estudiante->genero }}</td>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{
                                    $estudiante->direccion }}</td>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{
                                    $estudiante->telefono }}</td>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{
                                    $estudiante->correo_electronico }}</td>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{
                                    $estudiante->estado->descripcion }}</td>

                                <td class="border px-4 py-2 text-center">
                                    <div class="flex justify-center">
                                        <a href="{{ route('estudianteEdit', $estudiante->idDec) }}"
                                            class="bg-cyan-900 dark:bg-cyan-900 hover:bg-indigo-600 dark:hover:bg-cyan-700 text-white font-bold py-2 px-4 rounded mr-2">Editar</a>
                                        <button type="button"
                                            class="bg-gray-700 dark:bg-gray-700 hover:bg-red-600 dark:hover:bg-red-900 text-white font-bold py-2 px-4 rounded"
                                            onclick="confirmDelete('{{ $estudiante->idDec }}')">Inhabilitar</button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center justify-end mb-2 mt-8">
                    <button class='inline-flex items-center px-4 py-2 bg-gray-700 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 active:bg-gray-900 focus:outline-none focus:bg-gray-600 focus:ring focus:ring-gray-400 focus:ring-opacity-50 disabled:opacity-25 transition ease-in-out duration-150'>
                        <a href="/imprimir-estudiantes" class="text-white no-underline">
                            DESCARGAR LISTADO
                        </a>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function confirmDelete(id) {
        alertify.confirm("¿Desea Inhabilitar al Estudiante?",
        function(){
            let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/estudiantesC/' + id;
                    form.innerHTML = '@csrf @method("DELETE")';
                    document.body.appendChild(form);
                    form.submit();
            alertify.success('Ok');
        },
        function(){
            alertify.error('Cancelado');
        });
    }
</script>