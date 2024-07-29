<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl mt-6 text-gray-800 dark:text-gray-200 leading-tight ml-6">
            {{ __('Historial Academico') }}
        </h2>
    </x-slot>

    @php
    $tmpPer = 0;
    $cambio = 0;
    @endphp
    <div class="pb-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 items-center justify-center">
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg items-center justify-center p-8">
                <div>
                    @if (!$item->isEmpty())
                    <x-buttonWhite class="ml-4 mb-6 mt-6">
                        <a href="/imprimir-historial">DESCARGAR HISTORIAL</a>
                    </x-buttonWhite>
                    @endif
                    @if($item->isEmpty())
                    <p class="text-white text-center">No ha cursado ningun periodo.</p>
                    <p class="text-white text-center">Inscribase en la pestaña de Matrícula.</p>
                    @endif
                    @foreach ($item as $it)
                    @if ($it->periodo !== $tmpPer)
                    @php
                    $tmpPer = $it->periodo;
                    echo '
                </div>';
                @endphp
                <div class="w-full max-w-2xl mx-auto bg-gray-800 p-4 mb-6 rounded-lg shadow-md border border-gray-400">
                    <h2 class="text-xl font-bold mb-6 mt-2 text-white text-center">Periodo {{$tmpPer}}</h2>

                    @endif
                    <div class="space-y-4">
                        <a href="#"
                            class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 mb-6">
                            <h5 class="mb-4 text-lg font-bold tracking-tight text-gray-900">
                                <strong>{{Crypt::decryptString($it->nombre_clase)}}</strong></h5>
                            <p class="font-normal text-gray-700"><strong>@if ($it->nota == 0.0)
                                    AUN NO APROBADA
                                    @elseif ($it->nota < 70) REPROBADA @else APROBADA @endif</strong></p>
                            <p class="font-normal text-gray-700">Promedio: @if ($it->nota == 0.0)
                                N.A.
                                @else{{ $it->nota }}@endif</p>
                        </a>
                    </div>
                    @php
                    $tmpPer = $it->periodo;
                    @endphp
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
</x-app-layout>