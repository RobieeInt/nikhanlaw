<button
    {{ $attributes->merge([
        'class' =>
            'inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition',
    ]) }}>
    {{ $slot }}
</button>
