<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-6">
            {{ __('Horarios') }}
        </h2>
    </x-slot>

    <div class="pb-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-8">
                <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 overflow-x-auto">

                    <div class="mb-4">
                        <a href="{{ route('horariosC.create') }}"
                            class='inline-flex items-center px-4 py-2 bg-gray-800 border border-white rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-300 disabled:opacity-25 transition ease-in-out duration-150'>
                            Registrar Horario</a>
                    </div>

                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">ID</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Curso</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Aula</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Días</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Fecha Inicio</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Fecha Fin</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Hora Inicio</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Hora Fin</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($horarios as $horario)
                            <tr>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{
                                    $horario->id_horario}}</td>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{
                                   Crypt::decryptString($horario->clase->nombre_clase)}}</td>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{
                                    $horario->aula->aula}}</td>

                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{ $horario->dias
                                    }}</td>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{
                                    $horario->fecha_inicio }}</td>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{
                                    $horario->fecha_fin }}</td>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{
                                    $horario->hora_inicio }}</td>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{
                                    $horario->hora_fin }}</td>

                                <td class="border px-4 py-2 text-center">
                                    <div class="flex justify-center">
                                        <a href="{{ route('horarioEdit', $horario->id_horario) }}"
                                            class="bg-cyan-900 dark:bg-cyan-900 hover:bg-indigo-600 dark:hover:bg-cyan-700 text-white font-bold py-2 px-4 rounded mr-2">Editar</a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center justify-end mb-2 mt-8">
                    <button
                        class='inline-flex items-center px-4 py-2 bg-gray-700 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 active:bg-gray-900 focus:outline-none focus:bg-gray-600 focus:ring focus:ring-gray-400 focus:ring-opacity-50 disabled:opacity-25 transition ease-in-out duration-150'>
                        <a href="/imprimir-horarios" class="text-white no-underline">
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
        alertify.confirm("¿Desea Inhabilitar el Horario?",
        function(){
            let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/horariosC/' + id;
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
<script>
    @if ($errors->any())
        alertify.alert("Atención", "Por favor, corrija los siguientes errores:<br><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>");
    @endif

    @if (session('error'))
        alertify.error("{{ session('error') }}");
    @endif
</script>