@props(['for' => null, 'text' => null])

<label @if($for) for="{{ $for }}" @endif class="block text-sm font-medium text-gray-700">
    {{ $text ?? $slot }}
</label>
