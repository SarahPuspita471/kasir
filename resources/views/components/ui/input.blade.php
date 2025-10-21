@props([
  'id' => null, 'name' => null, 'type' => 'text', 'value' => null, 'placeholder' => null,
  'autocomplete' => null, 'required' => false, 'error' => null
])

<input
  @if($id) id="{{ $id }}" @endif
  @if($name) name="{{ $name }}" @endif
  type="{{ $type }}"
  @if(!is_null($value)) value="{{ $value }}" @endif
  @if($placeholder) placeholder="{{ $placeholder }}" @endif
  @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
  @if($required) required @endif
  {{ $attributes->merge([
    'class' =>
      'input w-full border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 '.
      'placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 '.
      ($error ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : '')
  ]) }}
  @if($error && $id) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
/>
