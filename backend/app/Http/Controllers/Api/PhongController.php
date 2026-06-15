<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Phong;
use App\Models\LoaiPhong;
use Illuminate\Http\Request;

class PhongController extends Controller
{
    // 0. Lấy danh sách loại phòng
    public function getLoaiPhong()
    {
        $loaiPhongs = LoaiPhong::all();
        return response()->json([
            'status' => 'success',
            'data' => $loaiPhongs
        ]);
    }

    // 1. Lấy toàn bộ danh sách phòng
    public function index()
    {
        $danhSachPhong = Phong::with('loaiPhong')->orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $danhSachPhong
        ]);
    }
    // 2. Thêm một phòng mới
    public function store(Request $request)
    {
        $request->validate([
            'so_phong' => 'required|unique:phong,so_phong',
            'gia_thue' => 'required|numeric',
            'gia_coc' => 'required|numeric',
            'dien_tich' => 'required|numeric',
            'loai_phong_id' => 'required|exists:loai_phong,id',
            'tang_id' => 'nullable|exists:tang,id',
        ]);

        $phong = Phong::create($request->except('id'));

        return response()->json([
            'status' => 'success',
            'message' => 'Thêm phòng mới thành công!',
            'data' => $phong
        ]);
    }
    // 3. Cập nhật thông tin phòng
    public function update(Request $request, $id)
    {
        $phong = Phong::find($id);
        if (!$phong) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy phòng!'], 404);
        }

        $phong->update($request->except('id'));

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật thông tin thành công!',
            'data' => $phong
        ]);
    }
    // 4. Xóa phòng
    public function destroy($id)
    {
        $phong = Phong::find($id);
        if (!$phong) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy phòng!'], 404);
        }

        $phong->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã xóa phòng thành công!'
        ]);
    }
}
