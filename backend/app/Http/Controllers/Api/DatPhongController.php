<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DatPhong;
use App\Models\Phong;
use Illuminate\Support\Facades\DB;

class DatPhongController extends Controller
{
    // Lấy danh sách yêu cầu đặt phòng 
    public function index()
    {
        // Sử dụng Join để lấy luôn số phòng từ bảng phong sang hiển thị
        $danhSach = DB::table('dat_phong')
            ->join('phong', 'dat_phong.phong_id', '=', 'phong.id')
            ->select('dat_phong.*', 'phong.so_phong')
            ->orderBy('dat_phong.created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $danhSach
        ]);
    }

    // Xử lý Duyệt đặt phòng
    public function duyet($id)
    {
        $yeuCau = DatPhong::find($id);
        if (!$yeuCau) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy yêu cầu!'], 404);
        }

       
        $yeuCau->update(['trang_thai' => 'da_duyet']);

        
        Phong::where('id', $yeuCau->phong_id)->update(['trang_thai' => 'da_thue']);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã duyệt yêu cầu đặt phòng và cập nhật trạng thái phòng!'
        ]);
    }

    // Xử lý Từ chối đặt phòng
    public function tuChoi($id)
    {
        $yeuCau = DatPhong::find($id);
        if (!$yeuCau) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy yêu cầu!'], 404);
        }

        $yeuCau->update(['trang_thai' => 'tu_choi']);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã từ chối yêu cầu đặt phòng này.'
        ]);
    }
}