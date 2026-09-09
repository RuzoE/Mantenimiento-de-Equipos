<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($agrupaciones as $titulo => $filas)
        <x-ui.card :title="$titulo" padding="false">
            @if (empty($filas))
                <p class="px-5 py-4 text-sm text-gray-400">Sin datos</p>
            @else
                <ul class="divide-y divide-gray-100 text-sm">
                    @foreach ($filas as $fila)
                        <li class="flex items-center justify-between gap-3 px-5 py-2">
                            <span class="truncate text-gray-600">{{ $fila['label'] }}</span>
                            <span class="font-semibold text-gray-900">{{ $fila['total'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>
    @endforeach
</div>
