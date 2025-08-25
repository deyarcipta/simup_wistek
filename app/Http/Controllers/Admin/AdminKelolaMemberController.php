<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminKelolaMemberController extends Controller
{
    public function index()
    {
        $members = User::where('role', 'member')
                ->with('member')
                ->latest()
                ->paginate(10);
        return view('admin.kelola_member.index', compact('members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
        ]);

        // Simpan ke tabel users
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make('member123'),
            'role'     => 'member', // set role sebagai member
        ]);

        // Simpan ke tabel members (jika ada field tambahan)
        Member::create([
            'user_id' => $user->id,
            'nama' => $request->name,
            'no_hp' => $request->no_hp,
            // tambahkan field lain jika ada, contoh: 'alamat' => $request->alamat
        ]);

        return redirect()->back()->with('success', 'Member berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Member berhasil diupdate.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete(); // otomatis hapus member kalau pakai cascade

        return redirect()->back()->with('success', 'Member berhasil dihapus.');
    }
}
