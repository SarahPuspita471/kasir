@extends('layouts.admin')
@section('title','Kelola User | Kasirku')

@section('content')
  {{-- Flash --}}
  @if(session('ok'))
    <x-ui.alert class="mb-4" :message="session('ok')" />
  @endif
  @error('general')
    <x-ui.alert class="mb-4" :message="$message" />
  @enderror

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Kelola User</h1>
    <a href="{{ route('admin.users.create') }}" class="card px-4 py-2 hover:shadow-md transition">+ User Baru</a>
  </div>

  <form method="GET" class="mb-4">
    <div class="flex gap-2">
      <x-ui.input name="q" placeholder="Cari nama/email/role..." value="{{ $q }}" class="w-full" />
      <x-ui.button type="submit" variant="primary" class="w-auto">Cari</x-ui.button>
    </div>
  </form>

  <div class="card p-0 overflow-hidden anim-card-in">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-gray-600">
        <tr>
          <th class="text-left px-4 py-3">Nama</th>
          <th class="text-left px-4 py-3">Email</th>
          <th class="text-left px-4 py-3">Role</th>
          <th class="text-right px-4 py-3">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $u)
          <tr class="border-t">
            <td class="px-4 py-3 font-medium">{{ $u->name }}</td>
            <td class="px-4 py-3 text-gray-700">{{ $u->email }}</td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs {{ $u->role === 'admin' ? 'text-indigo-700' : 'text-gray-700' }}">
                {{ ucfirst($u->role) }}
              </span>
            </td>
            <td class="px-4 py-3">
              <div class="flex items-center justify-end gap-2">
                <a href="{{ route('admin.users.edit',$u) }}" class="text-indigo-600 hover:underline">Edit</a>

                <form method="POST" action="{{ route('admin.users.reset',$u) }}" onsubmit="return confirm('Reset password untuk {{ $u->name }}?')">
                  @csrf
                  <button class="text-indigo-600 hover:underline" type="submit">Reset Password</button>
                </form>

                <form method="POST" action="{{ route('admin.users.destroy',$u) }}" onsubmit="return confirm('Hapus user {{ $u->name }}?')">
                  @csrf @method('DELETE')
                  <button class="text-red-600 hover:underline" type="submit">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Tidak ada data.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $users->links() }}</div>
@endsection
