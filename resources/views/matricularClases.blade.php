<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl mt-6 text-gray-800 dark:text-gray-200 leading-tight ml-6">
            {{ __('Confirmar Matricula') }}
        </h2>
    </x-slot>

    <div class="pb-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-transparent dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-black dark:text-black flex items-center justify-center w-full">
                    <form method="POST" action="{{ route('matriculaC.store') }}" class="w-full">
                        @csrf
                        <div class="grid grid-cols-3 gap-8 justify-center">
                            <div class="col-span-3">
                                <h5
                                    class="font-semibold text-xl mt-6 ml-3 text-gray-800 dark:text-gray-200 leading-tight text-center">
                                    ¿Está Seguro de Querer Matricular Estas Clases?
                                </h5>
                            </div>
                            @foreach ($lista as $item)
                            @if ($item->periodo == $periodo)
                            <div class="w-full mb-4 mt-6 p-2">
                                <a href="#"
                                    class="block w-full bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 p-6">
                                    <h5 class="mb-2 text-lg font-bold text-gray-900">
                                        <strong>{{Crypt::decryptString($item->nombre_clase)}}</strong></h5>
                                    <p class="font-normal text-gray-700"><strong>Horario:
                                            {{Crypt::decryptString($item->hora_inicio)}}</strong></p>
                                    <p class="font-normal text-gray-700"><strong>Días:
                                            {{Crypt::decryptString($item->dias)}}</strong></p>
                                    <p class="font-normal text-gray-700"><strong>Fecha de Inicio:
                                            {{Crypt::decryptString($item->fecha_inicio)}}</strong></p>
                                </a>
                            </div>
                            <input type="hidden" name="id_estudiante[]" id="id_estudiante" value="{{$idest}}">
                            <input type="hidden" name="id_curso[]" id="id_curso" value="{{$item->id_curso}}">
                            @endif
                            @endforeach
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <x-buttonWhite type="submit">
                                Registrar
                            </x-buttonWhite>
                            <pre> </pre>
                            <x-buttonOscuro route="matricula">
                                Cancelar
                            </x-buttonOscuro>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
</x-app-layout>