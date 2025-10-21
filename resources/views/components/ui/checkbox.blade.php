@props(['id' => null, 'name' => null, 'checked' => false, 'label' => null])

<div class="flex items-center gap-2">
  <input
    type="checkbox"
    @if($id) id="{{ $id }}" @endif
    @if($name) name="{{ $name }}" @endif
    @if($checked) checked @endif
    class="checkbox h-5 w-5 border-gray-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500"
  />
  <label @if($id) for="{{ $id }}" @endif class="select-none text-sm text-gray-700">{{ $label }}</label>
</div>
