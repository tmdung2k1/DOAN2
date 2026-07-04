<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HoaDon;
use App\Models\ChiTietHoaDon;
use Illuminate\Support\Facades\DB;

class HoaDonController extends Controller
{
    public function index()
    {
        HoaDon::where('trang_thai', 'chua_thanh_toan')
            ->where('han_chot', '<', now()->toDateString())
            ->update(['trang_thai' => 'qua_han']);

        $danhSach = DB::table('hoa_don')
            ->join('hop_dong', 'hoa_don.hop_dong_id', '=', 'hop_dong.id')
            ->join('phong', 'hop_dong.phong_id', '=', 'phong.id')
            ->join('tai_khoans', 'hop_dong.khach_id', '=', 'tai_khoans.id')
            ->select(
                'hoa_don.*',
                'phong.so_phong',
                'tai_khoans.ho_ten as ten_khach',
                'tai_khoans.so_dien_thoai'
            )
            ->orderBy('hoa_don.created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $danhSach
        ]);
    }

    public function layDuLieuForm()
    {
        $hopDongs = DB::table('hop_dong')
            ->join('phong', 'hop_dong.phong_id', '=', 'phong.id')
            ->join('tai_khoans', 'hop_dong.khach_id', '=', 'tai_khoans.id')
            ->where('hop_dong.trang_thai', 'hieu_luc')
            ->select('hop_dong.id', 'phong.so_phong', 'tai_khoans.ho_ten', 'hop_dong.gia_thue_hang_thang')
            ->get();

        return response()->json(['status' => 'success', 'hop_dongs' => $hopDongs]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'hop_dong_id' => 'required|integer',
            'thang_thanh_toan' => 'required|date',
            'han_chot' => 'required|date',
            'chi_tiet' => 'required|array'
        ]);

        $tongTien = 0;
        foreach ($request->chi_tiet as $item) {
            $tongTien += $item['thanh_tien'];
        }

        DB::beginTransaction();
        try {
            $hoaDon = HoaDon::create([
                'ma_hoa_don' => 'INV-' . time(),
                'hop_dong_id' => $request->hop_dong_id,
                'thang_thanh_toan' => $request->thang_thanh_toan,
                'tong_tien' => $tongTien,
                'han_chot' => $request->han_chot,
                'trang_thai' => 'chua_thanh_toan'
            ]);

            foreach ($request->chi_tiet as $item) {
                ChiTietHoaDon::create([
                    'hoa_don_id' => $hoaDon->id,
                    'loai_phi' => $item['loai_phi'],
                    'so_luong' => $item['so_luong'],
                    'don_gia' => $item['don_gia'],
                    'thanh_tien' => $item['thanh_tien']
                ]);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Đã xuất hóa đơn thành công!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Lỗi tạo hóa đơn: ' . $e->getMessage()], 500);
        }
    }

    public function layChiTiet($id)
    {
        $hoaDon = DB::table('hoa_don')
            ->join('hop_dong', 'hoa_don.hop_dong_id', '=', 'hop_dong.id')
            ->join('phong', 'hop_dong.phong_id', '=', 'phong.id')
            ->join('tai_khoans', 'hop_dong.khach_id', '=', 'tai_khoans.id')
            ->where('hoa_don.id', $id)
            ->select(
                'hoa_don.*',
                'phong.so_phong',
                'tai_khoans.ho_ten as ten_khach',
                'tai_khoans.so_dien_thoai'
            )
            ->first();

        if (!$hoaDon) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy hóa đơn!'], 404);
        }

        $chiTiet = DB::table('chi_tiet_hoa_don')
            ->where('hoa_don_id', $id)
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'hoa_don' => $hoaDon,
            'chi_tiet' => $chiTiet
        ]);
    }

    public function xacNhanThanhToan($id)
    {
        $hoaDon = HoaDon::find($id);
        if ($hoaDon) {
            $hoaDon->update([
                'trang_thai'      => 'da_thanh_toan',
                'ngay_thanh_toan' => now()->toDateString(),
            ]);
            return response()->json(['status' => 'success', 'message' => 'Đã xác nhận thu tiền!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Không tìm thấy hóa đơn!'], 404);
    }
}