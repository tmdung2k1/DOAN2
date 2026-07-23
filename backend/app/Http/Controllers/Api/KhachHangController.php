<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KhachHang;

class KhachHangController extends Controller
{
    public function layTheoHopDong($hop_dong_id)
    {
        $danhSach = KhachHang::where('Ma_HopDong', $hop_dong_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['status' => 'success', 'data' => $danhSach]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'Ma_HopDong' => 'required|integer|exists:hop_dong,Ma_HopDong',
            'ho_ten' => 'required|string|max:100',
            'cccd' => 'nullable|string|max:20',
            'so_dien_thoai' => 'nullable|string|max:20'
        ]);

        $khach = KhachHang::create($request->all());

        return response()->json(['status' => 'success', 'message' => 'Đã thêm khách ở ghép!', 'data' => $khach]);
    }

    public function destroy($id)
    {
        $khach = KhachHang::find($id);
        if ($khach) {
            $khach->delete();
            return response()->json(['status' => 'success', 'message' => 'Đã xóa người ở ghép!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Không tìm thấy dữ liệu!'], 404);
    }
}
