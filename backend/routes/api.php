<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TrangChuController;
use App\Http\Controllers\Api\XacThucController;

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
    });
