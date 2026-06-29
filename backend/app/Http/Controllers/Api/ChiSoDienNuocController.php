<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChiSoDienNuoc;
use App\Models\CaiDat;
use Illuminate\Support\Facades\DB;

class ChiSoDienNuocController extends Controller
{
    public function index()
    {
        $danhSach = DB::table('chi_so_dien_nuoc')
            ->join('phong', 'chi_so_dien_nuoc.phong_id', '=', 'phong.id')
            ->select('chi_so_dien_nuoc.*', 'phong.so_phong')
            ->orderBy('chi_so_dien_nuoc.thang_ghi_nhan', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $danhSach
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'phong_id'       => 'required|integer|exists:phong,id',
            'thang_ghi_nhan' => 'required|date',
            'loai_chi_so'    => 'required|in:dien,nuoc',
            'chi_so_cu'      => 'required|numeric|min:0',
            'chi_so_moi'     => 'required|numeric|gte:chi_so_cu',
        ], [
            'phong_id.required'       => 'Vui lòng chọn phòng.',
            'phong_id.exists'         => 'Phòng không tồn tại.',
            'thang_ghi_nhan.required' => 'Vui lòng chọn ngày ghi nhận.',
            'thang_ghi_nhan.date'     => 'Ngày ghi nhận không hợp lệ.',
            'loai_chi_so.required'    => 'Vui lòng chọn loại dịch vụ.',
            'loai_chi_so.in'          => 'Loại dịch vụ chỉ được là dien hoặc nuoc.',
            'chi_so_cu.required'      => 'Vui lòng nhập chỉ số cũ.',
            'chi_so_cu.numeric'       => 'Chỉ số cũ phải là số.',
            'chi_so_moi.required'     => 'Vui lòng nhập chỉ số mới.',
            'chi_so_moi.numeric'      => 'Chỉ số mới phải là số.',
            'chi_so_moi.gte'          => 'Chỉ số mới phải lớn hơn hoặc bằng chỉ số cũ.',
        ]);

        // Kiểm tra cài đặt đơn giá
        $caiDat = CaiDat::first();
        if (!$caiDat) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Chưa cấu hình đơn giá điện/nước. Vui lòng vào Cài Đặt để thiết lập trước!'
            ], 422);
        }

        $donGia = $request->loai_chi_so === 'dien' ? $caiDat->gia_dien : $caiDat->gia_nuoc;

        $chiSo = ChiSoDienNuoc::create([
            'phong_id'       => $validated['phong_id'],
            'thang_ghi_nhan' => $validated['thang_ghi_nhan'],
            'loai_chi_so'    => $validated['loai_chi_so'],
            'chi_so_cu'      => $validated['chi_so_cu'],
            'chi_so_moi'     => $validated['chi_so_moi'],
            'don_gia'        => $donGia,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã ghi nhận chỉ số ' . ($request->loai_chi_so === 'dien' ? 'điện' : 'nước') . ' thành công!',
            'data'    => $chiSo
        ]);
    }

    public function destroy($id)
    {
        $chiSo = ChiSoDienNuoc::find($id);
        if (!$chiSo) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy dữ liệu!'], 404);
        }

        $chiSo->delete();
        return response()->json(['status' => 'success', 'message' => 'Đã xóa chỉ số thành công!']);
    }
}
