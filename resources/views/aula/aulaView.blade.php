<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-6">
            {{ __('Aulas') }}
        </h2>
    </x-slot>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                        <a href="{{ route('aulasC.create') }}"
                            class='inline-flex items-center px-4 py-2 bg-gray-800 border border-white rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-300 disabled:opacity-25 transition ease-in-out duration-150'>
                            Registrar Aula</a>
                    </div>

                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">No.</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Aula</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Estado</th>
                                <th class="px-4 py-2 text-gray-900 dark:text-white text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($aulas as $aula)
                            <tr>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{ $aula->id_aula
                                    }}</td>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{ $aula->aula }}
                                </td>
                                <td class="border px-4 py-2 text-gray-900 dark:text-white text-center">{{
                                    $aula->estado->descripcion }}</td>

                                <td class="border px-4 py-2 text-center">
                                    <div class="flex justify-center">
                                        <a href="{{ route('aulaEdit', $aula->id_aula) }}"
                                            class="bg-cyan-900 dark:bg-cyan-900 hover:bg-indigo-600 dark:hover:bg-cyan-700 text-white font-bold py-2 px-4 rounded mr-2">Editar</a>
                                        <button type="button"
                                            class="bg-gray-700 dark:bg-gray-700 hover:bg-red-600 dark:hover:bg-red-900 text-white font-bold py-2 px-4 rounded"
                                            onclick="confirmDelete('{{ $aula->id_aula }}')">Inhabilitar</button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center justify-end mb-2 mt-8">
                    <button class='inline-flex items-center px-4 py-2 bg-gray-700 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 active:bg-gray-900 focus:outline-none focus:bg-gray-600 focus:ring focus:ring-gray-400 focus:ring-opacity-50 disabled:opacity-25 transition ease-in-out duration-150'>
                        <a href="/imprimir-aulas" class="text-white no-underline">
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
        alertify.confirm("¿Desea Inhabilitar el Aula?",
        function(){
            let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/aulasC/' + id;
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