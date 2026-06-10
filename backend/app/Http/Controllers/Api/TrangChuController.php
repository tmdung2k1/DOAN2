<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrangChuController extends Controller
{
    // API lấy danh sách phòng trống để hiển thị ở trang chủ
    public function danhSachPhong()
    {
        // Join các bảng lại để lấy đầy đủ thông tin: Phòng + Loại phòng
        $danhSach = DB::table('phong')
            ->join('loai_phong', 'phong.loai_phong_id', '=', 'loai_phong.id')
            ->select('phong.id', 'phong.so_phong', 'phong.dien_tich', 'phong.gia_thue', 'phong.trang_thai', 'loai_phong.ten_loai')
            ->where('phong.trang_thai', 'trong')
            ->get();

        // Trả về định dạng JSON cho Vue.js đọc
        return response()->json([
            'status' => 'success',
            'data' => $danhSach
        ]);
    }
}
