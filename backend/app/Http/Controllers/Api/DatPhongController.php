<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DatPhong;
use App\Models\Phong;
use Illuminate\Support\Facades\DB;

class DatPhongController extends Controller
{
    /**
     * API công khai: Khách vãng lai gửi yêu cầu đặt phòng
     */
    public function store(Request $request)
    {
        $request->validate([
            'phong_id' => 'required|exists:phong,id',
            'ho_ten' => 'required|string|max:100',
            'so_dien_thoai' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'ngay_du_kien_den' => 'required|date|after_or_equal:today',
            'ghi_chu' => 'nullable|string|max:500',
        ], [
            'phong_id.required' => 'Vui lòng chọn phòng.',
            'phong_id.exists' => 'Phòng không tồn tại.',
            'ho_ten.required' => 'Vui lòng nhập họ tên.',
            'so_dien_thoai.required' => 'Vui lòng nhập số điện thoại.',
            'ngay_du_kien_den.required' => 'Vui lòng chọn ngày dự kiến đến.',
            'ngay_du_kien_den.after_or_equal' => 'Ngày dự kiến đến phải từ hôm nay trở đi.',
            'email.email' => 'Email không hợp lệ.',
        ]);

        // Kiểm tra phòng còn trống không
        $phong = Phong::find($request->phong_id);
        if ($phong->trang_thai !== 'trong') {
            return response()->json([
                'status' => 'error',
                'message' => 'Phòng này hiện không còn trống. Vui lòng chọn phòng khác!'
            ], 422);
        }

        // Tạo mã đặt phòng tự động
        $lastId = DatPhong::max('id') ?? 0;
        $maDatPhong = 'DP-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        // Lưu yêu cầu đặt phòng
        $datPhong = DatPhong::create([
            'ma_dat_phong' => $maDatPhong,
            'phong_id' => $request->phong_id,
            'khach_id' => null,
            'ho_ten' => $request->ho_ten,
            'so_dien_thoai' => $request->so_dien_thoai,
            'email' => $request->email,
            'ngay_du_kien_den' => $request->ngay_du_kien_den,
            'tien_coc' => 0,
            'ghi_chu' => $request->ghi_chu,
            'trang_thai' => 'cho_xac_nhan',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Gửi yêu cầu đặt phòng thành công! Chúng tôi sẽ liên hệ xác nhận sớm nhất.',
            'ma_dat_phong' => $maDatPhong,
        ]);
    }

    /**
     * Admin: Danh sách yêu cầu đặt phòng
     */
    public function index()
    {
        $danhSach = DB::table('dat_phong')
            ->join('phong', 'dat_phong.phong_id', '=', 'phong.id')
            ->select(
                'dat_phong.*',
                'phong.so_phong'
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
