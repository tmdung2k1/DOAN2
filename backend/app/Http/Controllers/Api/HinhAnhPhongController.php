<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HinhAnhPhong;
use Illuminate\Support\Facades\Storage;

class HinhAnhPhongController extends Controller
{
    public function layAnhCuaPhong($phong_id)
    {
        $anh = HinhAnhPhong::where('Ma_Phong', $phong_id)->get();
        return response()->json(['status' => 'success', 'data' => $anh]);
    }

    public function uploadAnh(Request $request, $phong_id)
    {
        $request->validate([
            'hinh_anh' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        if ($request->hasFile('hinh_anh')) {
            $file = $request->file('hinh_anh');
            $tenFile = 'phong_' . $phong_id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $duongDan = $file->storeAs('phong_images', $tenFile, 'public');
            $url = asset('storage/' . $duongDan);

            $anhMoi = HinhAnhPhong::create([
                'Ma_Phong' => $phong_id,
                'url_anh' => $url,
                'la_anh_chinh' => false
            ]);

            return response()->json(['status' => 'success', 'message' => 'Tải ảnh lên thành công!', 'data' => $anhMoi]);
        }

        return response()->json(['status' => 'error', 'message' => 'Không tìm thấy file!'], 400);
    }

    public function xoaAnh($id)
    {
        $anh = HinhAnhPhong::find($id);
        if ($anh) {
            $duongDanTuongDoi = str_replace(asset('storage') . '/', '', $anh->url_anh);
            Storage::disk('public')->delete($duongDanTuongDoi);
            $anh->delete();
            return response()->json(['status' => 'success', 'message' => 'Đã xóa ảnh!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Lỗi không tìm thấy ảnh!'], 404);
    }
}
