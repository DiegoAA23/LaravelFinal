<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Actualizar Aula') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-transparent dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-black dark:text-black">
                    <form method="POST" action="{{ route('aulasC.update', $aula->id_aula) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <x-labelWhite for="aula" :value="'Aula:'"></x-labelWhite>
                                <x-inputWhite class="block mt-1 w-full" type="number" name="aula"
                                    value="{{ old('aula', $aula->aula) }}" required autofocus></x-inputWhite>
                            </div>

                            <div>
                                <x-labelWhite for="estado" :value="'Estado:'" />
                                <select name="estado_id" id="estado_id" 
                                class="rounded-md shadow-sm block mt-1 w-full rounded-lg border border-white-300 dark:border-white-600 focus:outline-none focus:border-white focus:ring-white focus:ring-opacity-50 dark:focus:border-gray-400">
                                    <option value="1" {{ $aula->estado_id == 1 ? 'selected' : '' }}>Activo</option>
                                    <option value="2" {{ $aula->estado_id == 2 ? 'selected' : '' }}>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        

                        <div class="flex items-center justify-end mt-4">
                            <x-buttonWhite type="submit">
                                Actualizar
                            </x-buttonWhite>
                            <pre> </pre>
                            <!-- Botón de cancelar -->
                            <x-buttonOscuro route="aulaView">
                                Cancelar
                            </x-buttonOscuro>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>

<script>
        @if ($errors->any())
            alertify.alert("Atención", "Por favor, corrija los siguientes errores:<br><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>");
        @endif

        @if (session('success'))
            alertify.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            alertify.error("{{ session('error') }}");
        @endif
    </script>