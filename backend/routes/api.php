<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TrangChuController;

// API lấy danh sách phòng trống để hiển thị ở trang chủ
Route::get('/phong-trong', [TrangChuController::class, 'danhSachPhong']);   



