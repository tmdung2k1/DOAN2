<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoaiPhong;

class LoaiPhongController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => LoaiPhong::orderBy('created_at', 'asc')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['ten_loai' => 'required|string|unique:loai_phong,ten_loai']);
        $loaiPhong = LoaiPhong::create($request->except('Ma_LoaiPhong'));

        return response()->json(['status' => 'success', 'message' => 'Thêm loại phòng thành công!', 'data' => $loaiPhong]);
    }

    public function update(Request $request, $id)
    {
        $loaiPhong = LoaiPhong::find($id);
        if (!$loaiPhong) return response()->json(['status' => 'error', 'message' => 'Không tìm thấy!'], 404);

        $request->validate(['ten_loai' => 'required|string']);
        $loaiPhong->update($request->except('Ma_LoaiPhong'));

        return response()->json(['status' => 'success', 'message' => 'Cập nhật thành công!', 'data' => $loaiPhong]);
    }

    public function destroy($id)
    {
        $loaiPhong = LoaiPhong::find($id);
        if (!$loaiPhong) return response()->json(['status' => 'error', 'message' => 'Không tìm thấy!'], 404);

        $loaiPhong->delete();
        return response()->json(['status' => 'success', 'message' => 'Đã xóa loại phòng!']);
    }
}
