<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ingresar Profesor') }}
        </h2>
    </x-slot>

    <div class="py-12">
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

                    <form method="POST" action="{{ route('profesoresC.store') }}">
                    @csrf

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <x-labelWhite for="id_profesor" :value="'ID:'"></x-labelWhite>
                                <x-inputWhite class="block mt-1 w-full" type="number" name="id_profesor" value="{{ old('id_profesor') }}"
                                              required autofocus></x-inputWhite>
                            </div>

                            <div>
                                <x-labelWhite for="nombre" :value="'Nombres:'"></x-labelWhite>
                                <x-inputWhite class="block mt-1 w-full" type="text" name="nombre" value="{{ old('nombre') }}"
                                              required maxlength="50" minlength="3" autofocus></x-inputWhite>
                            </div>

                            <div>
                                <x-labelWhite for="apellido" :value="'Apellidos:'"></x-labelWhite>
                                <x-inputWhite class="block mt-1 w-full" type="text" name="apellido" value="{{ old('apellido') }}"
                                              required maxlength="50" minlength="3" autofocus></x-inputWhite>
                            </div>

                            <div>
                            <x-labelWhite for="id_especialidad" :value="'Especialidad:'" />
                            <select id="id_especialidad" name="id_especialidad" required
                            class="rounded-md shadow-sm block mt-1 w-full rounded-lg border border-white-300 dark:border-white-600 focus:outline-none focus:border-white focus:ring-white focus:ring-opacity-50 dark:focus:border-gray-400">
                            @foreach($especialidades as $especialidad)
                                    <option value="{{ $especialidad->id_especialidad }}" {{ old('id_especialidad') == $especialidad->id_especialidad ? 'selected' : '' }}>
                                        {{ $especialidad->especialidad }}
                                    </option>
                                @endforeach
                            </select>
                            </div>

                            <div>
                                <x-labelWhite for="correo_electronico" :value="'Correo Electrónico:'"></x-labelWhite>
                                <x-inputWhite class="block mt-1 w-full" type="email" name="correo_electronico" value="{{ old('correo_electronico') }}"
                                              required maxlength="50" minlength="4" autofocus></x-inputWhite>
                            </div>

                            <div>
                                <x-labelWhite for="telefono" :value="'Teléfono:'"></x-labelWhite>
                                <x-inputWhite class="block mt-1 w-full" type="number" name="telefono" value="{{ old('telefono') }}"
                                              required autofocus></x-inputWhite>
                            </div>
                        </div>
                        

                        <div class="flex items-center justify-end mt-4">
                            <x-buttonWhite type="submit">
                                Registrar
                            </x-buttonWhite>
                            <pre> </pre>
                            <!-- Botón de cancelar -->
                            <x-buttonOscuro route="profesorView">
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

