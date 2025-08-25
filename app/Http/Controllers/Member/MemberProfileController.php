<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MemberProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('member.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        // Jika ada foto yang diupload
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto && Storage::exists('public/photos/' . $user->foto)) {
                Storage::delete('public/photos/' . $user->foto);
            }

            // Simpan foto baru
            $filename = time() . '.' . $request->file('foto')->extension();
            $request->file('foto')->storeAs('public/photos', $filename);

            $user->foto = $filename;
        }

        $user->save();

        return redirect()->route('member.profile.edit')->with('success', 'Profile updated successfully.');
    }
}
