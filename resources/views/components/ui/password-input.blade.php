@props([
  'id' => null, 'name' => null, 'placeholder' => null,
  'autocomplete' => 'current-password', 'required' => false, 'error' => null
])

@php($uid = \Illuminate\Support\Str::uuid()->toString())
<div id="pwd-{{ $uid }}" class="relative">
  <input
    @if($id) id="{{ $id }}" @endif
    @if($name) name="{{ $name }}" @endif
    type="password"
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
    @if($required) required @endif
    {{ $attributes->merge([
      'class' =>
        'input w-full border border-gray-300 bg-white px-3 py-2.5 pr-10 text-sm text-gray-900 '.
        'placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 '.
        ($error ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : '')
    ]) }}
    @if($error && $id) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
  />

  <button
    type="button"
    class="absolute inset-y-0 right-2 my-auto inline-flex h-7 w-7 items-center justify-center rounded-md text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
    aria-label="Tampilkan/Sembunyikan kata sandi"
    data-toggle="show"
  >
    {{-- Ikon sederhana (text)—bisa diganti svg nanti --}}
    <span class="text-xs font-medium select-none">👁️</span>
  </button>
</div>

@push('scripts')
<script>
(function(){
  const root = document.getElementById('pwd-{{ $uid }}');
  if(!root) return;
  const input = root.querySelector('input');
  const btn = root.querySelector('[data-toggle="show"]');

  btn?.addEventListener('click', () => {
    const isPwd = input.type === 'password';
    input.type = isPwd ? 'text' : 'password';
    // Micro crossfade feel via opacity
    btn.style.opacity = '0.6';
    setTimeout(()=>{ btn.style.opacity = '1'; }, 120);
  });
})();
</script>
@endpush
