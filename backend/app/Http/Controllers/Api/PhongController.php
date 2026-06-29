<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Phong;
use App\Models\LoaiPhong;
use Illuminate\Http\Request;

class PhongController extends Controller
{
    public function getLoaiPhong()
    {
        $loaiPhongs = LoaiPhong::all();
        return response()->json([
            'status' => 'success',
            'data' => $loaiPhongs
        ]);
    }

    // Lấy toàn bộ danh sách phòng
    public function index()
    {
        $danhSachPhong = Phong::with('loaiPhong')->orderBy('so_phong', 'asc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $danhSachPhong
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'so_phong' => 'required|unique:phong,so_phong',
            'gia_thue' => 'required|numeric|min:0',
            'gia_coc' => 'required|numeric|min:0',
            'dien_tich' => 'required|numeric|min:0',
            'loai_phong_id' => 'required|exists:loai_phong,id',
            'tang_id' => 'nullable|exists:tang,id',
            'trang_thai' => 'nullable|in:trong,da_thue,dat_truoc,bao_tri',
        ], [
            'so_phong.required' => 'Vui lòng nhập số phòng.',
            'so_phong.unique' => 'Số phòng đã tồn tại trên hệ thống.',
            'gia_thue.required' => 'Vui lòng nhập giá thuê.',
            'gia_thue.numeric' => 'Giá thuê phải là số.',
            'gia_coc.required' => 'Vui lòng nhập tiền cọc.',
            'gia_coc.numeric' => 'Tiền cọc phải là số.',
            'dien_tich.required' => 'Vui lòng nhập diện tích.',
            'dien_tich.numeric' => 'Diện tích phải là số.',
            'loai_phong_id.required' => 'Vui lòng chọn loại phòng.',
            'loai_phong_id.exists' => 'Loại phòng không hợp lệ.',
            'trang_thai.in' => 'Trạng thái phòng không hợp lệ.',
        ]);

        $phong = Phong::create($request->except('id'));

        return response()->json([
            'status' => 'success',
            'message' => 'Thêm phòng mới thành công!',
            'data' => $phong
        ]);
    }

    public function update(Request $request, $id)
    {
        $phong = Phong::find($id);
        if (!$phong) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy phòng!'], 404);
        }

        $validated = $request->validate([
            'so_phong' => 'required|unique:phong,so_phong,' . $id,
            'gia_thue' => 'required|numeric|min:0',
            'gia_coc' => 'required|numeric|min:0',
            'dien_tich' => 'required|numeric|min:0',
            'loai_phong_id' => 'required|exists:loai_phong,id',
            'tang_id' => 'nullable|exists:tang,id',
            'trang_thai' => 'nullable|in:trong,da_thue,dat_truoc,bao_tri',
        ], [
            'so_phong.required' => 'Vui lòng nhập số phòng.',
            'so_phong.unique' => 'Số phòng đã tồn tại trên hệ thống.',
            'gia_thue.required' => 'Vui lòng nhập giá thuê.',
            'gia_thue.numeric' => 'Giá thuê phải là số.',
            'gia_coc.required' => 'Vui lòng nhập tiền cọc.',
            'gia_coc.numeric' => 'Tiền cọc phải là số.',
            'dien_tich.required' => 'Vui lòng nhập diện tích.',
            'dien_tich.numeric' => 'Diện tích phải là số.',
            'loai_phong_id.required' => 'Vui lòng chọn loại phòng.',
            'loai_phong_id.exists' => 'Loại phòng không hợp lệ.',
            'trang_thai.in' => 'Trạng thái phòng không hợp lệ.',
        ]);

        $phong->update($request->except('id'));

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật thông tin thành công!',
            'data' => $phong
        ]);
    }
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
