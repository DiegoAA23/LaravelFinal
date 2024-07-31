<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-6">
            {{ __('Ingresar Cursos') }}
        </h2>
    </x-slot>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-transparent dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-black dark:text-black">

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

                    <form method="POST" action="{{ route('clasesC.store') }}">
                        @csrf
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <x-labelWhite for="nombre_clase" :value="'Clase:'" />
                                <x-inputWhite class="block mt-1 w-full" type="text" name="nombre_clase" value="{{ old('nombre_clase') }}" required maxlength="50" minlength="4" autofocus />
                            </div>

                            <div>
                                <x-labelWhite for="id_profesor" :value="'Profesor:'" />
                                <select id="id_profesor" name="id_profesor" required
                                    class="shadow-sm block mt-1 w-full rounded-lg border border-white-300 dark:border-white-600 focus:outline-none focus:border-white focus:ring-white focus:ring-opacity-50 dark:focus:border-gray-400">
                                    @foreach($profesores as $profesor)
                                        <option value="{{ $profesor->idDec }}" {{ old('id_profesor') == $profesor->id_profesor ? 'selected' : '' }}>
                                            {{ $profesor->nombre }} {{ $profesor->apellido }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-labelWhite for="periodo" :value="'Periodo:'" />
                                <x-inputWhite class="block mt-1 w-full" min=1 type="number" name="periodo" value="{{ old('periodo') }}" required autofocus />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-buttonWhite type="submit">
                                Registrar
                            </x-buttonWhite>
                            <pre> </pre>
                            <!-- Botón de cancelar -->
                            <x-buttonOscuro route="claseView">
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