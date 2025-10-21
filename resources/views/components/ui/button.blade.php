@props(['type' => 'button', 'variant' => 'primary', 'loading' => false, 'disabled' => false])

@php
$base = 'btn inline-flex items-center justify-center w-full px-4 text-sm font-medium transition';
$variants = [
  'primary' => 'bg-indigo-600 text-white hover:bg-indigo-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500',
];
@endphp

<button
  type="{{ $type }}"
  @if($loading || $disabled) disabled @endif
  {{ $attributes->merge(['class' => $base.' '.$variants[$variant]]) }}
>
  @if($loading)
    <span class="spinner mr-2"></span>
  @endif
  <span>{{ $loading ? 'Memproses…' : $slot }}</span>
</button>
