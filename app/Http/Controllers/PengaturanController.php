<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PengaturanController extends Controller
{
    public function pemilikIndex()
    {
        $user = auth()->user();
        return view('Pengaturan.pemilik', compact('user'));
    }

    public function pemilikUpdate(Request $request)
    {
        $user = auth()->user();

        // Normalisasi email dulu agar validasi unique cek gmail dot-insensitive
        $request->merge(['email' => strtolower(trim($request->email))]);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => [
                'required','string','lowercase','email:rfc','max:255',
                'regex:/^[a-z0-9._%+\-]+@gmail\.com$/i',
                'unique:users,email,'.$user->id,
                function($attr,$val,$fail) use ($user) {
                    $norm = function($e){ [$l,$d]=explode('@',strtolower($e),2); if($d==='gmail.com') $l=str_replace('.','',$l); $l=explode('+',$l)[0]; return $l.'@'.$d; };
                    $n = $norm($val);
                    $exists = \App\Models\User::whereRaw('LOWER(email) != ?', [strtolower($user->email)])
                        ->get()->first(function($u) use ($n,$norm){ return $norm($u->email) === $n; });
                    if ($exists) $fail('Email gmail ini sudah terpakai (titik di gmail diabaikan).');
                },
            ],
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'email.regex'         => 'Email harus format gmail yang valid, contoh: nama@gmail.com',
            'email.email'         => 'Format email tidak valid.',
            'email.unique'        => 'Email ini sudah digunakan oleh akun lain.',
            'password.confirmed'  => 'Konfirmasi password tidak cocok.',
            'password.min'        => 'Password minimal harus 8 karakter.',
            'password.string'     => 'Password harus berupa teks.',
        ]);

        $user->name  = $request->name;
        $user->email = strtolower(trim($request->email));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil pemilik berhasil diperbarui!');
    }

    public function kasir()
    {
        $kasir = User::where('role', 'kasir')->get();
        return view('Pengaturan.kasir', compact('kasir'));
    }

    public function kasirStore(Request $request)
    {
        $request->merge(['email' => strtolower(trim($request->email))]);
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => [
                'required','string','lowercase','email:rfc','max:255',
                'regex:/^[a-z0-9._%+\-]+@gmail\.com$/i','unique:users,email',
                function($attr,$val,$fail){
                    $norm=function($e){ [$l,$d]=explode('@',strtolower($e),2); if($d==='gmail.com') $l=str_replace('.','',$l); $l=explode('+',$l)[0]; return $l.'@'.$d; };
                    $n=$norm($val);
                    if(\App\Models\User::get()->first(fn($u)=>$norm($u->email)===$n)) $fail('Email gmail ini sudah terpakai (titik di gmail diabaikan).');
                },
            ],
            'password' => 'required|string|min:8|confirmed',
        ],[
            'email.regex'=>'Email harus gmail valid: nama@gmail.com',
            'password.confirmed'=>'Konfirmasi password tidak cocok.',
        ]);
        User::create([
            'name'     => $request->name,
            'email'    => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            'role'     => 'kasir',
        ]);
        return back()->with('success', 'Akun kasir berhasil ditambahkan!');
    }

        public function kasirEdit($id)
    {
        $kasir = User::findOrFail($id);
        return view('Pengaturan.kasir-edit', compact('kasir'));
    }

    public function kasirUpdate(Request $request, $id)
    {
        $kasir = User::findOrFail($id);
        $request->merge(['email' => strtolower(trim($request->email))]);
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => [
                'required','string','lowercase','email:rfc','max:255',
                'regex:/^[a-z0-9._%+\-]+@gmail\.com$/i','unique:users,email,'.$id,
                function($attr,$val,$fail) use ($kasir){
                    $norm=function($e){ [$l,$d]=explode('@',strtolower($e),2); if($d==='gmail.com') $l=str_replace('.','',$l); $l=explode('+',$l)[0]; return $l.'@'.$d; };
                    $n=$norm($val);
                    if(\App\Models\User::where('id','!=',$kasir->id)->get()->first(fn($u)=>$norm($u->email)===$n)) $fail('Email gmail ini sudah terpakai (titik di gmail diabaikan).');
                },
            ],
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
            'email.unique'       => 'Email sudah digunakan.',
            'email.regex'        => 'Email harus gmail valid: nama@gmail.com',
        ]);
        $kasir->name  = $request->name;
        $kasir->email = strtolower(trim($request->email));
        if ($request->filled('password')) $kasir->password = Hash::make($request->password);
        $kasir->save();
        return redirect()->route('pengaturan.kasir')->with('success', 'Akun kasir berhasil diperbarui!');
    }

    public function kasirDestroy($id)
    {
        $kasir = User::where('role', 'kasir')->findOrFail($id);
        $kasir->delete();

        return back()->with('success', 'Akun kasir berhasil dihapus!');
    }
}