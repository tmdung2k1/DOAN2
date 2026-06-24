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

// API lấy danh sách phòng trống để hiển thị ở trang chủ
Route::get('/phong-trong', [TrangChuController::class, 'danhSachPhong']);

Route::post('/dang-nhap', [XacThucController::class, 'dangNhap']);
//khu vực bảo mật cho admin
Route::middleware('auth:sanctum')
    ->group(function () {
        //Lấy thông tin admin đang đăng nhập
        Route::get('/admin/thong-tin', function (Request $request) {
            return $request->user();
        });

        //Quản lý phòng
        Route::get('/admin/phong', [PhongController::class, 'index']);
        Route::get('/admin/loai-phong', [PhongController::class, 'getLoaiPhong']);
        Route::post('/admin/phong', [PhongController::class, 'store']);
        Route::put('/admin/phong/{id}', [PhongController::class, 'update']);
        Route::delete('/admin/phong/{id}', [PhongController::class, 'destroy']);

        // API Quản lý Loại phòng 
        Route::get('/admin/loai-phong', [LoaiPhongController::class, 'index']);
        Route::post('/admin/loai-phong', [LoaiPhongController::class, 'store']);
        Route::put('/admin/loai-phong/{id}', [LoaiPhongController::class, 'update']);
        Route::delete('/admin/loai-phong/{id}', [LoaiPhongController::class, 'destroy']);

        // Quản lý yêu cầu đặt phòng
        Route::get('/admin/dat-phong', [DatPhongController::class, 'index']);
        Route::put('/admin/dat-phong/{id}/duyet', [DatPhongController::class, 'duyet']);
        Route::put('/admin/dat-phong/{id}/tu-choi', [DatPhongController::class, 'tuChoi']);

        // Quản lý cài đặt hệ thống
        Route::get('/admin/cai-dat', [CaiDatController::class, 'layCaiDat']);
        Route::post('/admin/cai-dat', [CaiDatController::class, 'luuCaiDat']);

        // Quản lý Hợp Đồng
        Route::get('/admin/hop-dong', [HopDongController::class, 'index']);
        Route::post('/admin/hop-dong', [HopDongController::class, 'store']);
        Route::put('/admin/hop-dong/{id}/huy', [HopDongController::class, 'huyHopDong']);
    });
