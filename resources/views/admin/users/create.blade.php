@extends('layouts.admin')
@section('title','User Baru | Kasirku')

@section('content')
  <div class="max-w-2xl anim-card-in">
    <div class="card p-6">
      <h1 class="text-2xl font-semibold mb-4">User Baru</h1>
      <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
        @csrf
        @include('admin.users._form')
        <div class="flex items-center gap-3">
          <x-ui.button type="submit" variant="primary" class="w-auto">Simpan</x-ui.button>
          <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:underline">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection
