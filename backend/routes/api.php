<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TrangChuController;
use App\Http\Controllers\Api\XacThucController;
use App\Http\Controllers\Api\PhongController;
use App\Http\Controllers\Api\LoaiPhongController;
use App\Http\Controllers\Api\DatPhongController;
use App\Http\Controllers\Api\CaiDatController;
use App\Http\Controllers\Api\HopDongController;
use App\Http\Controllers\Api\ChiSoDienNuocController;
use App\Http\Controllers\Api\HoaDonController;

Route::get('/phong-trong', [TrangChuController::class, 'danhSachPhong']);

Route::post('/dang-nhap', [XacThucController::class, 'dangNhap']);

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::get('/admin/thong-tin', function (Request $request) {
            return $request->user();
        });

        Route::get('/admin/phong', [PhongController::class, 'index']);
        Route::post('/admin/phong', [PhongController::class, 'store']);
        Route::put('/admin/phong/{id}', [PhongController::class, 'update']);
        Route::delete('/admin/phong/{id}', [PhongController::class, 'destroy']);

        Route::get('/admin/loai-phong', [LoaiPhongController::class, 'index']);
        Route::post('/admin/loai-phong', [LoaiPhongController::class, 'store']);
        Route::put('/admin/loai-phong/{id}', [LoaiPhongController::class, 'update']);
        Route::delete('/admin/loai-phong/{id}', [LoaiPhongController::class, 'destroy']);

        Route::get('/admin/dat-phong', [DatPhongController::class, 'index']);
        Route::put('/admin/dat-phong/{id}/duyet', [DatPhongController::class, 'duyet']);
        Route::put('/admin/dat-phong/{id}/tu-choi', [DatPhongController::class, 'tuChoi']);

        Route::get('/admin/cai-dat', [CaiDatController::class, 'layCaiDat']);
        Route::post('/admin/cai-dat', [CaiDatController::class, 'luuCaiDat']);

        Route::get('/admin/hop-dong/du-lieu-form', [HopDongController::class, 'layDuLieuTaoHopDong']);
        Route::get('/admin/hop-dong', [HopDongController::class, 'index']);
        Route::post('/admin/hop-dong', [HopDongController::class, 'store']);
        Route::put('/admin/hop-dong/{id}/huy', [HopDongController::class, 'huyHopDong']);
        Route::get('/admin/hop-dong/{id}/pdf', [HopDongController::class, 'xuatPDF']);

        Route::get('/admin/dien-nuoc', [ChiSoDienNuocController::class, 'index']);
        Route::post('/admin/dien-nuoc', [ChiSoDienNuocController::class, 'store']);
        Route::delete('/admin/dien-nuoc/{id}', [ChiSoDienNuocController::class, 'destroy']);

        Route::get('/admin/hoa-don/du-lieu-form', [HoaDonController::class, 'layDuLieuForm']);
        Route::get('/admin/hoa-don', [HoaDonController::class, 'index']);
        Route::post('/admin/hoa-don', [HoaDonController::class, 'store']);
        Route::get('/admin/hoa-don/{id}/chi-tiet', [HoaDonController::class, 'layChiTiet']);
        Route::put('/admin/hoa-don/{id}/thanh-toan', [HoaDonController::class, 'xacNhanThanhToan']);
    });
