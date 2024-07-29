<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl mt-6 mb-2 text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('BIENVENIDO') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("¡BIENVENIDO!") }}
                </div>
                <div id="clock" class="text-gray-900 dark:text-gray-100"></div>

                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <?php
                    $hoy = date('d/m/Y');?>
                    <p>Hoy Es:
                        <?php echo $hoy; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <footer class="py-16 text-center text-sm text-black dark:text-white/70">
        Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
        <p>Grupo #5</p>
    </footer>
</x-app-layout>