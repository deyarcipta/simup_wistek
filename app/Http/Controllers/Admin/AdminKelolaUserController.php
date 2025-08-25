<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminKelolaUserController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['admin', 'operator'])
                        ->orderBy('name')
                        ->get();
        return view('admin.kelola_user.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|string'
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'foto'  => '', // Default empty photo
        ]);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request,  User $kelola_user)
    {
        // dd($kelola_user->id);
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $kelola_user->id,
            'role'  => 'required|string'
        ]);

        $data = $request->only(['name', 'email', 'role']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $kelola_user->update($data);
        return back()->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $kelola_user)
    {
        $kelola_user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }
}
