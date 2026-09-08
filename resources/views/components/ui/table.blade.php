@props(['head' => null])

<div {{ $attributes->merge(['class' => 'overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-200']) }}>
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        @isset($head)
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    {{ $head }}
                </tr>
            </thead>
        @endisset
        <tbody class="divide-y divide-gray-100 text-gray-700">
            {{ $slot }}
        </tbody>
    </table>
</div>
