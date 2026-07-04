<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DatPhong;
use App\Models\Phong;
use Illuminate\Support\Facades\DB;

class DatPhongController extends Controller
{
    public function index()
    {
        $danhSach = DB::table('dat_phong')
            ->join('phong', 'dat_phong.phong_id', '=', 'phong.id')
            ->join('tai_khoans', 'dat_phong.khach_id', '=', 'tai_khoans.id')
            ->select(
                'dat_phong.*',
                'phong.so_phong',
                'tai_khoans.ho_ten',
                'tai_khoans.so_dien_thoai',
                'tai_khoans.email'
            )
            ->orderBy('dat_phong.created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $danhSach
        ]);
    }

    public function duyet($id)
    {
        $yeuCau = DatPhong::find($id);
        if (!$yeuCau) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy yêu cầu!'], 404);
        }
        $yeuCau->update(['trang_thai' => 'da_coc']);

        Phong::where('id', $yeuCau->phong_id)->update(['trang_thai' => 'dat_truoc']);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã duyệt yêu cầu đặt phòng (Trạng thái: Đã cọc)!'
        ]);
    }

    public function tuChoi($id)
    {
        $yeuCau = DatPhong::find($id);
        if (!$yeuCau) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy yêu cầu!'], 404);
        }

        $yeuCau->update(['trang_thai' => 'huy']);

        Phong::where('id', $yeuCau->phong_id)->update(['trang_thai' => 'trong']);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã từ chối (hủy) yêu cầu đặt phòng này.'
        ]);
    }
}
