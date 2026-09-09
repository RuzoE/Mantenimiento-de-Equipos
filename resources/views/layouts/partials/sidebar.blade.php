<aside
    class="fixed inset-y-0 left-0 z-40 flex w-64 transform flex-col border-r border-gray-200 bg-white transition-transform duration-200 ease-in-out lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    {{-- Encabezado --}}
    <div class="flex h-16 shrink-0 items-center gap-3 border-b border-gray-200 px-5">
        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-brand-600">
            <x-application-logo class="h-5 w-5 fill-current text-white" />
        </span>
        <span class="text-sm font-semibold leading-tight text-gray-900">
            Mantenimiento<br><span class="font-normal text-gray-500">de Equipos</span>
        </span>
        <button type="button" @click="sidebarOpen = false"
                class="ml-auto rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 lg:hidden">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Navegación --}}
    <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-4">
        <div class="space-y-1">
            <p class="px-3 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-400">General</p>
            <x-ui.nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
                Panel principal
            </x-ui.nav-link>
        </div>

        <div class="space-y-1">
            <p class="px-3 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-400">Gestión</p>
            @can('equipos.ver')
                <x-ui.nav-link :href="route('equipos.index')" :active="request()->routeIs('equipos.*')" icon="desktop">
                    Equipos
                </x-ui.nav-link>
            @endcan
            @can('mantenimientos.ver')
                <x-ui.nav-link :href="route('mantenimientos.index')" :active="request()->routeIs('mantenimientos.*')" icon="wrench">
                    Mantenimientos
                </x-ui.nav-link>
            @endcan
            @can('programaciones.ver')
                <x-ui.nav-link :href="route('programaciones.index')" :active="request()->routeIs('programaciones.*')" icon="calendar">
                    Programación
                </x-ui.nav-link>
            @endcan
            @can('traslados.ver')
                <x-ui.nav-link :href="route('traslados.index')" :active="request()->routeIs('traslados.*')" icon="map-pin">
                    Traslados
                </x-ui.nav-link>
            @endcan
        </div>

        @can('catalogos.ver')
            <div class="space-y-1">
                <p class="px-3 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-400">Catálogos</p>
                <x-ui.nav-link :href="route('catalogos.tipos-equipo.index')" :active="request()->routeIs('catalogos.tipos-equipo.*')" icon="desktop">
                    Tipos de equipo
                </x-ui.nav-link>
                <x-ui.nav-link :href="route('catalogos.marcas.index')" :active="request()->routeIs('catalogos.marcas.*')" icon="clipboard">
                    Marcas
                </x-ui.nav-link>
                <x-ui.nav-link :href="route('catalogos.ubicaciones.index')" :active="request()->routeIs('catalogos.ubicaciones.*')" icon="map-pin">
                    Ubicaciones
                </x-ui.nav-link>
                <x-ui.nav-link :href="route('catalogos.responsables.index')" :active="request()->routeIs('catalogos.responsables.*')" icon="users">
                    Responsables
                </x-ui.nav-link>
            </div>
        @endcan

        @canany(['usuarios.ver', 'reportes.ver', 'auditoria.ver'])
            <div class="space-y-1">
                <p class="px-3 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-400">Administración</p>
                @can('usuarios.ver')
                    <x-ui.nav-link :href="route('usuarios.index')" :active="request()->routeIs('usuarios.*')" icon="users">
                        Usuarios
                    </x-ui.nav-link>
                @endcan
                @can('reportes.ver')
                    <x-ui.nav-link :href="route('reportes.index')" :active="request()->routeIs('reportes.*')" icon="chart-bar">
                        Reportes
                    </x-ui.nav-link>
                @endcan
                @can('auditoria.ver')
                    <x-ui.nav-link :href="route('auditoria.index')" :active="request()->routeIs('auditoria.*')" icon="clipboard">
                        Auditoría
                    </x-ui.nav-link>
                @endcan
            </div>
        @endcanany
    </nav>

    {{-- Pie de la barra lateral --}}
    <div class="shrink-0 border-t border-gray-200 p-4">
        <a href="{{ route('profile.edit') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-brand-100 text-xs font-semibold text-brand-700">
                {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->take(2)->map(fn ($p) => \Illuminate\Support\Str::substr($p, 0, 1))->implode('') }}
            </span>
            <span class="min-w-0">
                <span class="block truncate font-medium text-gray-900">{{ auth()->user()->name }}</span>
                <span class="block truncate text-xs text-gray-500">{{ auth()->user()->email }}</span>
            </span>
        </a>
    </div>
</aside>
