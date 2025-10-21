@props(['variant' => 'error', 'message' => null, 'shake' => false])

@php
$styles = [
  'error' => 'border border-red-200 bg-red-50 text-red-700'
];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-lg px-3 py-2 text-sm '.$styles[$variant].' '.($shake ? 'anim-micro-shake' : '')]) }}>
  {{ $message ?? $slot }}
</div>
