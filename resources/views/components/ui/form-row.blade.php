@props(['error' => null, 'help' => null, 'for' => null])

<div {{ $attributes->merge(['class' => 'space-y-1.5']) }}>
    {{ $label ?? '' }}
    {{ $slot }}
    @if($help && !$error)
        <p class="text-xs text-gray-500">{{ $help }}</p>
    @endif
    @if($error)
        <p id="{{ $for ? $for.'-error' : '' }}" class="text-xs text-red-600">{{ $error }}</p>
    @endif
</div>
