<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HopDong;
use App\Models\Phong;
use App\Models\DatPhong;
use App\Models\KhachHang;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class HopDongController extends Controller
{
    public function index()
    {
        $danhSach = DB::table('hop_dong')
            ->join('phong', 'hop_dong.Ma_Phong', '=', 'phong.Ma_Phong')
            ->select(
                'hop_dong.*',
                'phong.so_phong'
            )
            ->orderBy('hop_dong.created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $danhSach
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'Ma_Phong' => 'required|integer|exists:phong,Ma_Phong',
            'ten_khach_hang' => 'required|string|max:100',
            'so_dien_thoai' => 'required|string|max:20',
            'cccd' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'gia_thue_hang_thang' => 'required|numeric|min:0',
            'tien_coc' => 'required|numeric|min:0',
            'ngay_thu_tien_hang_thang' => 'required|integer|min:1|max:31'
        ], [
            'ten_khach_hang.required' => 'Vui lòng nhập tên khách hàng.',
            'so_dien_thoai.required' => 'Vui lòng nhập số điện thoại khách hàng.',
            'Ma_Phong.required' => 'Vui lòng chọn phòng.',
            'ngay_bat_dau.required' => 'Vui lòng chọn ngày bắt đầu.',
            'ngay_ket_thuc.required' => 'Vui lòng chọn ngày kết thúc.',
            'ngay_ket_thuc.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
        ]);

        $maHopDong = 'HD-' . time();

        $hopDong = HopDong::create(array_merge($request->except('Ma_HopDong'), [
            'ma_hop_dong' => $maHopDong,
            'trang_thai' => 'hieu_luc'
        ]));

        // Đổi trạng thái phòng thành ĐÃ THUÊ
        Phong::where('Ma_Phong', $request->Ma_Phong)->update(['trang_thai' => 'da_thue']);

        // Đổi trạng thái Đặt Phòng thành ĐÃ NHẬN PHÒNG (nếu có đơn đặt cọc trước)
        DatPhong::where('Ma_Phong', $request->Ma_Phong)
            ->where('trang_thai', 'da_coc')
            ->update(['trang_thai' => 'da_nhan_phong']);

        // Tự động lưu thông tin khách hàng vào bảng khach_hang (dành cho người ở cùng / ở chính)
        KhachHang::create([
            'Ma_HopDong' => $hopDong->Ma_HopDong,
            'ho_ten' => $request->ten_khach_hang,
            'so_dien_thoai' => $request->so_dien_thoai,
            'cccd' => $request->cccd ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tạo hợp đồng thành công!',
            'data' => $hopDong
        ]);
    }

    public function huyHopDong($id)
    {
        $hopDong = HopDong::find($id);
        if (!$hopDong) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy hợp đồng!'], 404);
        }
        $hopDong->update(['trang_thai' => 'da_huy']);

        // Giải phóng phòng về trạng thái TRỐNG
        Phong::where('Ma_Phong', $hopDong->Ma_Phong)->update(['trang_thai' => 'trong']);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã chấm dứt hợp đồng và giải phóng phòng!'
        ]);
    }

    public function xuatPDF($id)
    {
        $hopDong = DB::table('hop_dong')
            ->join('phong', 'hop_dong.Ma_Phong', '=', 'phong.Ma_Phong')
            ->select('hop_dong.*', 'phong.so_phong')
            ->where('hop_dong.Ma_HopDong', $id)
            ->first();

        if (!$hopDong) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy hợp đồng!'], 404);
        }

        $pdf = Pdf::loadView('hop_dong', ['hopDong' => $hopDong]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('HopDong_' . $hopDong->ma_hop_dong . '.pdf');
    }

    public function layDuLieuTaoHopDong()
    {
        // Ưu tiên đề xuất phòng ở trạng thái 'dat_truoc' (đã đặt cọc) và phòng 'trong'
        $phongs = Phong::whereIn('trang_thai', ['dat_truoc', 'trong'])
            ->select('Ma_Phong', 'so_phong', 'gia_thue', 'gia_coc', 'trang_thai')
            ->orderByRaw("FIELD(trang_thai, 'dat_truoc', 'trong')")
            ->orderBy('so_phong', 'asc')
            ->get();

        // Kèm theo thông tin đặt cọc của các phòng dat_truoc
        $danhSachDaCoc = DatPhong::where('trang_thai', 'da_coc')
            ->select('Ma_DatPhong', 'ma_dat_phong', 'Ma_Phong', 'ho_ten', 'so_dien_thoai', 'email', 'ngay_du_kien_den', 'tien_coc', 'ghi_chu')
            ->get()
            ->keyBy('Ma_Phong');

        $phongsFormatted = $phongs->map(function ($p) use ($danhSachDaCoc) {
            $thongTinCoc = $danhSachDaCoc->get($p->Ma_Phong);
            return [
                'Ma_Phong' => $p->Ma_Phong,
                'so_phong' => $p->so_phong,
                'gia_thue' => $p->gia_thue,
                'gia_coc'  => $p->gia_coc,
                'trang_thai' => $p->trang_thai,
                'dat_coc_info' => $thongTinCoc ? [
                    'ho_ten' => $thongTinCoc->ho_ten,
                    'so_dien_thoai' => $thongTinCoc->so_dien_thoai,
                    'email' => $thongTinCoc->email,
                    'ngay_du_kien_den' => $thongTinCoc->ngay_du_kien_den,
                    'tien_coc' => $thongTinCoc->tien_coc,
                    'ghi_chu' => $thongTinCoc->ghi_chu,
                ] : null
            ];
        });

        return response()->json([
            'status' => 'success',
            'phongs' => $phongsFormatted
        ]);
    }
}
