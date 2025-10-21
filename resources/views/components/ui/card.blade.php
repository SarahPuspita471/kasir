@props(['title' => null, 'subtitle' => null])

<section {{ $attributes->merge(['class' => 'card p-6 sm:p-7 lg:p-8']) }}>
    @if($title || $subtitle)
        <header class="mb-5">
            @if($title)
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900">{{ $title }}</h1>
            @endif
            @if($subtitle)
                <p class="mt-1 text-sm text-gray-600">{{ $subtitle }}</p>
            @endif
        </header>
    @endif
    <div>
        {{ $slot }}
    </div>
</section>
