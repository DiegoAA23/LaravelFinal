<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl mt-6 mb-2 text-gray-800 dark:text-gray-200 leading-tight ml-6">
            {{ __('Matricula de Cursos') }}
        </h2>
    </x-slot>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php
            $cont = 0;
            $currentPeriodo = null;
            @endphp
            @foreach ($list as $item)
            @if ($currentPeriodo !== $item->periodo)
            @if ($currentPeriodo !== null)
            <!-- Cerrar div del período anterior -->
        </div> <!-- Cerrar div flex flex-wrap -->
        @endif
        <!-- Iniciar nuevo contenedor para el período -->
        <div class="bg-gray-800 rounded-lg shadow-md mx-4 border border-gray-100 mb-6 p-6">
            <h2 class="text-2xl font-bold mb-6 text-white text-center">Periodo {{ $item->periodo }}</h2>
            <div class="flex flex-wrap justify-evenly mb-4">
                @php
                $currentPeriodo = $item->periodo;
                @endphp
                @endif
                <!-- Tarjeta de clase -->
                <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/5 mb-4 p-2">
                    <a href="#"
                        class="block w-full bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 p-2">
                        <h5 class="mb-2 text-lg font-bold text-gray-900"><strong>{{
                                Crypt::decryptString($item->nombre_clase) }}</strong></h5>
                        <p class="font-normal text-gray-700"><strong>Horario:</strong> {{
                            Crypt::decryptString($item->hora_inicio) }}</p>
                        <p class="font-normal text-gray-700"><strong>Días:</strong> {{ Crypt::decryptString($item->dias)
                            }}</p>
                    </a>
                </div>
                @php
                $cont++;
                @endphp
                @if ($loop->last || $list[$loop->index + 1]->periodo != $currentPeriodo)
                <!-- Cerrar div del período actual -->
            </div> <!-- Cerrar div flex flex-wrap -->
            @foreach ($periodo as $per)
            @if (!($per == $item->periodo))
            <div class="flex justify-center">
                <x-buttonWhite class="mt-4" onclick="matricularClases({{ $item->periodo }})">
                    Matricular Clases
                    <svg class="ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 10" width="23" height="20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                </x-buttonWhite>
            </div>
            @endif
            @endforeach
            @endif
            @endforeach
        </div>
    </div>
    <script>
        function matricularClases(periodo) {
            window.location.href = "{{ route('matricularClases') }}" + "?periodo=" + periodo;
        }
    </script>
</x-app-layout>