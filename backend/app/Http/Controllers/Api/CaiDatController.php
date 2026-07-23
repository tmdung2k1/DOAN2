<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaiDat;

class CaiDatController extends Controller
{
    public function layCaiDat()
    {
        $caiDat = CaiDat::first();

        if (!$caiDat) {
            $caiDat = CaiDat::create(['gia_dien' => 3500, 'gia_nuoc' => 15000]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $caiDat
        ]);
    }

    public function luuCaiDat(Request $request)
    {
        $caiDat = CaiDat::first();

        if (!$caiDat) {
            CaiDat::create($request->except('Ma_CaiDat'));
        } else {
            $caiDat->update($request->except('Ma_CaiDat'));
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lưu cài đặt hệ thống thành công!'
        ]);
    }
}