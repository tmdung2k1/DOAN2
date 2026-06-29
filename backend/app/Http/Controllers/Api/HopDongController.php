<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HopDong;
use App\Models\Phong;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


class HopDongController extends Controller
{
    // Lấy danh sách hợp đồng 
    public function index()
    {
        $danhSach = DB::table('hop_dong')
            ->join('phong', 'hop_dong.phong_id', '=', 'phong.id')
            ->join('tai_khoans', 'hop_dong.khach_id', '=', 'tai_khoans.id')
            ->select(
                'hop_dong.*', 
                'phong.so_phong',
                'tai_khoans.ho_ten as ten_khach_hang',
                'tai_khoans.so_dien_thoai'
            )
            ->orderBy('hop_dong.created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $danhSach
        ]);
    }

    // Tạo hợp đồng mới
    public function store(Request $request)
    {
        $request->validate([
            'phong_id' => 'required|integer',
            'khach_id' => 'required|integer',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'gia_thue_hang_thang' => 'required|numeric',
            'tien_coc' => 'required|numeric',
            'ngay_thu_tien_hang_thang' => 'required|integer|min:1|max:31'
        ]);

        // Tự động sinh mã hợp đồng 
        $maHopDong = 'HD-' . time();

        $hopDong = HopDong::create(array_merge($request->except('id'), [
            'ma_hop_dong' => $maHopDong,
            'trang_thai' => 'hieu_luc'
        ]));

        Phong::where('id', $request->phong_id)->update(['trang_thai' => 'da_thue']);

        return response()->json([
            'status' => 'success',
            'message' => 'Tạo hợp đồng thành công!',
            'data' => $hopDong
        ]);
    }

    // Hủy / Chấm dứt hợp đồng
    public function huyHopDong($id)
    {
        $hopDong = HopDong::find($id);
        if (!$hopDong) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy hợp đồng!'], 404);
        }
        $hopDong->update(['trang_thai' => 'da_huy']);

        Phong::where('id', $hopDong->phong_id)->update(['trang_thai' => 'trong']);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã chấm dứt hợp đồng và giải phóng phòng!'
        ]);
    }
    // Xuất file PDF hợp đồng
    public function xuatPDF($id)
    {
        $hopDong = DB::table('hop_dong')
            ->join('phong', 'hop_dong.phong_id', '=', 'phong.id')
            ->join('tai_khoans', 'hop_dong.khach_id', '=', 'tai_khoans.id')
            ->select('hop_dong.*', 'phong.so_phong', 'tai_khoans.ho_ten as ten_khach_hang', 'tai_khoans.so_dien_thoai')
            ->where('hop_dong.id', $id)
            ->first();

        if (!$hopDong) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy hợp đồng!'], 404);
        }

        // Tạo PDF từ view và truyền dữ liệu hợp đồng sang
        $pdf = Pdf::loadView('hop_dong', ['hopDong' => $hopDong]);
        
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('HopDong_' . $hopDong->ma_hop_dong . '.pdf');
    }
    // Hàm lấy dữ liệu cho Form tạo Hợp đồng
    public function layDuLieuTaoHopDong()
    {
        // lấy những phòng có trạng thái 'trong'
        $phongs = \App\Models\Phong::where('trang_thai', 'trong')
                    ->select('id', 'so_phong', 'gia_thue')
                    ->get();
        
        // Lấy danh sách khách hàng
        $khachs = DB::table('tai_khoans')->select('id', 'ho_ten', 'so_dien_thoai')->get();

        return response()->json([
            'status' => 'success',
            'phongs' => $phongs,
            'khachs' => $khachs
        ]);
    }
}
