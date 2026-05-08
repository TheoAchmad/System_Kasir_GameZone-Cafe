<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PsUnit;
use Illuminate\Http\Request;

class PsController extends Controller
{
    public function index(Request $request)
    {
        $ps = PsUnit::where('cabang_id', $request->cabang)
            ->orderBy('nomor_ps')
            ->get();

        return response()->json($ps);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_ps'      => 'required|string|max:20',
            'tipe_ps'       => 'required|in:PS4,PS5',
            'harga_per_jam' => 'required|numeric|min:0',
            'status'        => 'required|in:kosong,maintenance',
            'cabang_id'     => 'required|exists:cabang,id',
        ]);

        $ps = PsUnit::create($request->only(
            'nomor_ps', 'tipe_ps', 'harga_per_jam', 'status', 'cabang_id'
        ));

        return response()->json(['success' => true, 'ps' => $ps]);
    }

    // ✅ PATCH /admin/ps/{id}
    public function update(Request $request, PsUnit $ps)
    {
        $request->validate([
            'nomor_ps'      => 'required|string|max:20',
            'tipe_ps'       => 'required|in:PS4,PS5',
            'harga_per_jam' => 'required|numeric|min:0',
            'status'        => 'required|in:kosong,maintenance',
        ]);

        $data = $request->only('nomor_ps', 'tipe_ps', 'harga_per_jam');

        // Jangan ubah status jika PS sedang dipakai
        if ($ps->status !== 'dipakai') {
            $data['status'] = $request->status;
        }

        $ps->update($data);

        return response()->json(['success' => true, 'ps' => $ps]);
    }

    public function destroy(PsUnit $ps)
    {
        if ($ps->status === 'dipakai') {
            return response()->json(['error' => 'PS sedang dipakai, tidak bisa dihapus'], 422);
        }

        $ps->delete();
        return response()->json(['success' => true]);
    }
}