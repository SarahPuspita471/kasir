@extends('layouts.admin')
@section('title','Edit User | Kasirku')

@section('content')
  <div class="max-w-2xl anim-card-in">
    <div class="card p-6">
      <h1 class="text-2xl font-semibold mb-4">Edit User</h1>
      <form method="POST" action="{{ route('admin.users.update',$user) }}" class="space-y-4">
        @csrf @method('PUT')
        @include('admin.users._form', ['user' => $user])
        <div class="flex items-center gap-3">
          <x-ui.button type="submit" variant="primary" class="w-auto">Update</x-ui.button>
          <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:underline">Kembali</a>
        </div>
      </form>
    </div>
  </div>
@endsection
