@php
    $flash = collect([
        'success' => session('success'),
        'error'   => session('error'),
        'warning' => session('warning'),
        'info'    => session('status') ?? session('info'),
    ])->filter();
@endphp

@if ($flash->isNotEmpty())
    <div class="mb-6 space-y-3">
        @foreach ($flash as $type => $message)
            <x-ui.alert :type="$type">{{ $message }}</x-ui.alert>
        @endforeach
    </div>
@endif
