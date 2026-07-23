<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HoaDon;
use App\Models\ChiTietHoaDon;
use App\Models\DichVu;
use Illuminate\Support\Facades\DB;

class HoaDonController extends Controller
{
    public function index()
    {
        HoaDon::where('trang_thai', 'chua_thanh_toan')
            ->where('han_chot', '<', now()->toDateString())
            ->update(['trang_thai' => 'qua_han']);

        $danhSach = DB::table('hoa_don')
            ->join('hop_dong', 'hoa_don.Ma_HopDong', '=', 'hop_dong.Ma_HopDong')
            ->join('phong', 'hop_dong.Ma_Phong', '=', 'phong.Ma_Phong')
            ->join('tai_khoans', 'hop_dong.Ma_TaiKhoan', '=', 'tai_khoans.Ma_TaiKhoan')
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
            ->join('phong', 'hop_dong.Ma_Phong', '=', 'phong.Ma_Phong')
            ->join('tai_khoans', 'hop_dong.Ma_TaiKhoan', '=', 'tai_khoans.Ma_TaiKhoan')
            ->where('hop_dong.trang_thai', 'hieu_luc')
            ->select(
                'hop_dong.Ma_HopDong',
                'hop_dong.Ma_Phong',
                'phong.so_phong',
                'tai_khoans.ho_ten',
                'hop_dong.gia_thue_hang_thang'
            )
            ->get();

        $caiDat = \App\Models\CaiDat::first();
        if (!$caiDat) {
            $caiDat = \App\Models\CaiDat::create(['gia_dien' => 3500, 'gia_nuoc' => 15000]);
        }

        return response()->json([
            'status'    => 'success',
            'hop_dongs' => $hopDongs,
            'cai_dat'   => $caiDat,
            'dich_vus'  => DichVu::where('active', true)->orderBy('Ma_DichVu', 'asc')->get(),
        ]);
    }

    public function layChiSoMoiNhat($phong_id)
    {
        $dien = DB::table('chi_so_dien_nuoc')
            ->where('Ma_Phong', $phong_id)
            ->where('loai_chi_so', 'dien')
            ->orderBy('thang_ghi_nhan', 'desc')
            ->first();

        $nuoc = DB::table('chi_so_dien_nuoc')
            ->where('Ma_Phong', $phong_id)
            ->where('loai_chi_so', 'nuoc')
            ->orderBy('thang_ghi_nhan', 'desc')
            ->first();

        return response()->json([
            'status' => 'success',
            'dien'   => $dien ? [
                'tieu_thu' => $dien->chi_so_moi - $dien->chi_so_cu,
                'don_gia'  => $dien->don_gia,
                'thang'    => $dien->thang_ghi_nhan,
            ] : null,
            'nuoc'   => $nuoc ? [
                'tieu_thu' => $nuoc->chi_so_moi - $nuoc->chi_so_cu,
                'don_gia'  => $nuoc->don_gia,
                'thang'    => $nuoc->thang_ghi_nhan,
            ] : null,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'Ma_HopDong' => 'required|integer',
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
                'Ma_HopDong' => $request->Ma_HopDong,
                'thang_thanh_toan' => $request->thang_thanh_toan,
                'tong_tien' => $tongTien,
                'han_chot' => $request->han_chot,
                'trang_thai' => 'chua_thanh_toan'
            ]);

            foreach ($request->chi_tiet as $item) {
                ChiTietHoaDon::create([
                    'Ma_HoaDon' => $hoaDon->Ma_HoaDon,
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
            ->join('hop_dong', 'hoa_don.Ma_HopDong', '=', 'hop_dong.Ma_HopDong')
            ->join('phong', 'hop_dong.Ma_Phong', '=', 'phong.Ma_Phong')
            ->join('tai_khoans', 'hop_dong.Ma_TaiKhoan', '=', 'tai_khoans.Ma_TaiKhoan')
            ->where('hoa_don.Ma_HoaDon', $id)
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
            ->where('Ma_HoaDon', $id)
            ->orderBy('Ma_ChiTietHoaDon', 'asc')
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