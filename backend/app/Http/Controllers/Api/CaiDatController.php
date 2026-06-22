<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaiDat;

class CaiDatController extends Controller
{
    public function layCaiDat()
    {
        // Lấy dòng đầu tiên trong bảng
        $caiDat = CaiDat::first();
        
        // Nếu bảng trống trơn, tự động tạo một dòng mặc định
        if (!$caiDat) {
            $caiDat = CaiDat::create(['gia_dien' => 3500, 'gia_nuoc' => 15000]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $caiDat
        ]);
    }

    // Lưu thông tin cài đặt
    public function luuCaiDat(Request $request)
    {
        // Lấy dòng đầu tiên ra để cập nhật
        $caiDat = CaiDat::first();
        
        // Nếu bằng một cách nào đó bảng bị mất dữ liệu, thì tạo mới luôn
        if (!$caiDat) {
            CaiDat::create($request->except('id'));
        } else {
            // Có dữ liệu thì tiến hành cập nhật
            $caiDat->update($request->except('id'));
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lưu cài đặt hệ thống thành công!'
        ]);
    }
}