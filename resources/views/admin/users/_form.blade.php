@props(['user' => null])

<div class="space-y-4">
  <x-ui.form-row for="name" :error="$errors->first('name')">
    <x-slot name="label"><x-ui.label for="name" text="Nama" /></x-slot>
    <x-ui.input id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required :error="$errors->has('name') ? 'true' : null" />
  </x-ui.form-row>

  <x-ui.form-row for="email" :error="$errors->first('email')">
    <x-slot name="label"><x-ui.label for="email" text="Email" /></x-slot>
    <x-ui.input id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required :error="$errors->has('email') ? 'true' : null" />
  </x-ui.form-row>

  <x-ui.form-row for="role" :error="$errors->first('role')">
    <x-slot name="label"><x-ui.label for="role" text="Role" /></x-slot>
    <select id="role" name="role" class="input w-full border border-gray-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
      @php $roleVal = old('role', $user->role ?? 'user'); @endphp
      <option value="user"  {{ $roleVal==='user' ? 'selected' : '' }}>User</option>
      <option value="admin" {{ $roleVal==='admin'? 'selected' : '' }}>Admin</option>
    </select>
  </x-ui.form-row>

  <x-ui.form-row for="password" :error="$errors->first('password')" help="{{ $user ? 'Kosongkan jika tidak ingin mengubah.' : 'Opsional, kosongkan untuk password default: password123' }}">
    <x-slot name="label"><x-ui.label for="password" text="Password" /></x-slot>
    <x-ui.password-input id="password" name="password" placeholder="{{ $user ? '•••••••• (opsional)' : '•••••••• (opsional)'}}" />
  </x-ui.form-row>
</div>
