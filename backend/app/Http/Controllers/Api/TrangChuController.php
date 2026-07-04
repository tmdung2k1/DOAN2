<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrangChuController extends Controller
{
    public function danhSachPhong()
    {
        $danhSach = DB::table('phong')
            ->join('loai_phong', 'phong.loai_phong_id', '=', 'loai_phong.id')
            ->select('phong.id', 'phong.so_phong', 'phong.dien_tich', 'phong.gia_thue', 'phong.trang_thai', 'loai_phong.ten_loai')
            ->where('phong.trang_thai', 'trong')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $danhSach
        ]);
    }
}
