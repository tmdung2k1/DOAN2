<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DichVu;

class DichVuController extends Controller
{
    public function index()
    {
        $dichVus = DichVu::orderBy('Ma_DichVu', 'asc')->get();
        return response()->json(['status' => 'success', 'data' => $dichVus]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_dich_vu' => 'required|string|max:100',
            'don_gia'     => 'required|numeric|min:0',
            'mo_ta'       => 'nullable|string|max:255',
        ]);

        $dichVu = DichVu::create([
            'ten_dich_vu' => $request->ten_dich_vu,
            'don_gia'     => $request->don_gia,
            'mo_ta'       => $request->mo_ta,
            'active'      => true,
        ]);

        return response()->json(['status' => 'success', 'data' => $dichVu], 201);
    }

    public function update(Request $request, $id)
    {
        $dichVu = DichVu::findOrFail($id);

        $request->validate([
            'ten_dich_vu' => 'sometimes|string|max:100',
            'don_gia'     => 'sometimes|numeric|min:0',
            'mo_ta'       => 'nullable|string|max:255',
            'active'      => 'sometimes|boolean',
        ]);

        $dichVu->update($request->only(['ten_dich_vu', 'don_gia', 'mo_ta', 'active']));

        return response()->json(['status' => 'success', 'data' => $dichVu]);
    }

    public function destroy($id)
    {
        $dichVu = DichVu::findOrFail($id);
        $dichVu->delete();
        return response()->json(['status' => 'success', 'message' => 'Đã xóa dịch vụ!']);
    }
}
