<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KasirController extends Controller
{
    // GET /admin/api/kasir
    public function index()
    {
        $kasir = User::with('cabang')
            ->where('role', 'kasir')
            ->orderBy('name')
            ->get();

        return response()->json($kasir);
    }

    // POST /admin/kasir
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8',
            'cabang_id' => 'required|exists:cabang,id',
            'role'      => 'in:kasir,admin',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'kasir',
            'cabang_id' => $request->cabang_id,
        ]);

        return response()->json([
            'success' => true,
            'user'    => $user->load('cabang'),
        ]);
    }

    // DELETE /admin/kasir/{id}
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'Tidak bisa menghapus akun sendiri'], 422);
        }

        if ($user->role === 'admin') {
            return response()->json(['error' => 'Tidak bisa menghapus akun admin'], 422);
        }

        $user->delete();
        return response()->json(['success' => true]);
    }
}