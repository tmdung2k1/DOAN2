<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Phong;
use Illuminate\Http\Request;

class PhongController extends Controller
{
    public function index()
    {
        $danhSachPhong = Phong::with(['loaiPhong', 'tienIch'])->orderBy('so_phong', 'asc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $danhSachPhong
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'so_phong' => 'required|unique:phong,so_phong',
            'gia_thue' => 'required|numeric|min:0',
            'gia_coc' => 'required|numeric|min:0',
            'dien_tich' => 'required|numeric|min:0',
            'Ma_LoaiPhong' => 'required|exists:loai_phong,Ma_LoaiPhong',
            'Ma_Tang' => 'nullable|exists:tang,Ma_Tang',
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
            'Ma_LoaiPhong.required' => 'Vui lòng chọn loại phòng.',
            'Ma_LoaiPhong.exists' => 'Loại phòng không hợp lệ.',
            'trang_thai.in' => 'Trạng thái phòng không hợp lệ.',
        ]);

        $phong = Phong::create($request->except('Ma_Phong'));

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

        $request->validate([
            'so_phong' => 'required|unique:phong,so_phong,' . $id . ',Ma_Phong',
            'gia_thue' => 'required|numeric|min:0',
            'gia_coc' => 'required|numeric|min:0',
            'dien_tich' => 'required|numeric|min:0',
            'Ma_LoaiPhong' => 'required|exists:loai_phong,Ma_LoaiPhong',
            'Ma_Tang' => 'nullable|exists:tang,Ma_Tang',
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
            'Ma_LoaiPhong.required' => 'Vui lòng chọn loại phòng.',
            'Ma_LoaiPhong.exists' => 'Loại phòng không hợp lệ.',
            'trang_thai.in' => 'Trạng thái phòng không hợp lệ.',
        ]);

        $phong->update($request->except('Ma_Phong'));

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
    public function layTienIch($id)
    {
        $tienIchIds = \Illuminate\Support\Facades\DB::table('tien_ich_phong')
            ->where('Ma_Phong', $id)
            ->pluck('Ma_TienIch');
            
        return response()->json(['status' => 'success', 'data' => $tienIchIds]);
    }

    public function capNhatTienIch(Request $request, $id)
    {
        $request->validate([
            'tien_ich_ids' => 'array'
        ]);

        \Illuminate\Support\Facades\DB::table('tien_ich_phong')->where('Ma_Phong', $id)->delete();

        $duLieuMoi = [];
        foreach ($request->tien_ich_ids as $ti_id) {
            $duLieuMoi[] = [
                'Ma_Phong' => $id,
                'Ma_TienIch' => $ti_id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        if (count($duLieuMoi) > 0) {
            \Illuminate\Support\Facades\DB::table('tien_ich_phong')->insert($duLieuMoi);
        }

        return response()->json(['status' => 'success', 'message' => 'Đã cập nhật tiện ích cho phòng!']);
    }
}
