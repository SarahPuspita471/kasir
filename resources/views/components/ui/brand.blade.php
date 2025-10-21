@props(['logoSrc' => null, 'appName' => 'Kasirku'])

<div class="flex items-center gap-2">
  @if($logoSrc)
    <img src="{{ $logoSrc }}" alt="{{ $appName }}" class="h-6 w-6 rounded-md" />
  @endif
  <span class="text-sm font-semibold text-gray-900">{{ $appName }}</span>
</div>
