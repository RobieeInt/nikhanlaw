@props(['status'])

@php
    $map = [
        'submitted' => 'bg-yellow-100 text-yellow-800',
        'active' => 'bg-blue-100 text-blue-800',
        'resolved' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
    ];
@endphp

<span class="rounded-full px-3 py-1 text-xs font-medium {{ $map[$status] ?? 'bg-zinc-100' }}">
    {{ ucfirst($status) }}
</span>
