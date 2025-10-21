<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));

        $users = User::query()
            ->when($q, fn($s) =>
                $s->where('name','like',"%{$q}%")
                  ->orWhere('email','like',"%{$q}%")
                  ->orWhere('role','like',"%{$q}%")
            )
            ->orderBy('created_at','desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users','q'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $password = $data['password'] ?? null;
        if (!$password) {
            // password default random mudah diingat lalu bisa diganti
            $password = 'password123';
        }

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'role'     => $data['role'],
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        return redirect()->route('admin.users.index')->with('ok', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        // Tidak boleh menurunkan/ubah role dirinya sendiri ke non-admin (opsional safety)
        if ($user->id === $request->user()->id && $request->input('role') !== 'admin') {
            return back()->withErrors(['role' => 'Tidak dapat mengubah role akun Anda sendiri.'])->withInput();
        }

        $data = $request->validated();

        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->role  = $data['role'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('ok', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['general' => 'Tidak dapat menghapus akun Anda sendiri.']);
        }
        $user->delete();

        return redirect()->route('admin.users.index')->with('ok', 'User dihapus.');
    }

    public function resetPassword(User $user)
    {
        $new = Str::password(10); // random aman
        $user->password = Hash::make($new);
        $user->save();

        // Untuk demo: tampilkan password baru di flash (di produksi kirim via email/WA internal)
        return back()->with('ok', "Password baru untuk {$user->name}: {$new}");
    }
}
