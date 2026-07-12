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
use App\Http\Controllers\Api\TienIchController;
use App\Http\Controllers\Api\DichVuController;

Route::get('/phong-trong', [TrangChuController::class, 'danhSachPhong']);

Route::post('/dang-nhap', [XacThucController::class, 'dangNhap']);

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::get('/admin/thong-tin', function (Request $request) {
            return $request->user();
        });

        // Quản lý phòng
        Route::get('/admin/phong', [PhongController::class, 'index']);
        Route::post('/admin/phong', [PhongController::class, 'store']);
        Route::put('/admin/phong/{id}', [PhongController::class, 'update']);
        Route::delete('/admin/phong/{id}', [PhongController::class, 'destroy']);

        // Cập nhật tiện ích cho phòng
        Route::get('/admin/phong/{id}/tien-ich', [PhongController::class, 'layTienIch']);
        Route::post('/admin/phong/{id}/tien-ich', [PhongController::class, 'capNhatTienIch']);
        
        // Quản lý loại phòng
        Route::get('/admin/loai-phong', [LoaiPhongController::class, 'index']);
        Route::post('/admin/loai-phong', [LoaiPhongController::class, 'store']);
        Route::put('/admin/loai-phong/{id}', [LoaiPhongController::class, 'update']);
        Route::delete('/admin/loai-phong/{id}', [LoaiPhongController::class, 'destroy']);

        // Quản lý đặt phòng
        Route::get('/admin/dat-phong', [DatPhongController::class, 'index']);
        Route::put('/admin/dat-phong/{id}/duyet', [DatPhongController::class, 'duyet']);
        Route::put('/admin/dat-phong/{id}/tu-choi', [DatPhongController::class, 'tuChoi']);
        // Quản lý cài đặt
        Route::get('/admin/cai-dat', [CaiDatController::class, 'layCaiDat']);
        Route::post('/admin/cai-dat', [CaiDatController::class, 'luuCaiDat']);
        // Quản lý hợp đồng
        Route::get('/admin/hop-dong/du-lieu-form', [HopDongController::class, 'layDuLieuTaoHopDong']);
        Route::get('/admin/hop-dong', [HopDongController::class, 'index']);
        Route::post('/admin/hop-dong', [HopDongController::class, 'store']);
        Route::put('/admin/hop-dong/{id}/huy', [HopDongController::class, 'huyHopDong']);
        Route::get('/admin/hop-dong/{id}/pdf', [HopDongController::class, 'xuatPDF']);
        
        // Quản lý chỉ số điện nước
        Route::get('/admin/dien-nuoc', [ChiSoDienNuocController::class, 'index']);
        Route::post('/admin/dien-nuoc', [ChiSoDienNuocController::class, 'store']);
        Route::delete('/admin/dien-nuoc/{id}', [ChiSoDienNuocController::class, 'destroy']);

        // Quản lý hóa đơn
        Route::get('/admin/hoa-don/du-lieu-form', [HoaDonController::class, 'layDuLieuForm']);
        Route::get('/admin/hoa-don/chi-so-moi-nhat/{phong_id}', [HoaDonController::class, 'layChiSoMoiNhat']);
        Route::get('/admin/hoa-don', [HoaDonController::class, 'index']);
        Route::post('/admin/hoa-don', [HoaDonController::class, 'store']);
        Route::get('/admin/hoa-don/{id}/chi-tiet', [HoaDonController::class, 'layChiTiet']);
        Route::put('/admin/hoa-don/{id}/thanh-toan', [HoaDonController::class, 'xacNhanThanhToan']);
        
        // Quản lý Tiện Ích
        Route::get('/admin/tien-ich', [TienIchController::class, 'index']);
        Route::post('/admin/tien-ich', [TienIchController::class, 'store']);
        Route::delete('/admin/tien-ich/{id}', [TienIchController::class, 'destroy']);

        // Quản lý Dịch Vụ
        Route::get('/admin/dich-vu', [DichVuController::class, 'index']);
        Route::post('/admin/dich-vu', [DichVuController::class, 'store']);
        Route::put('/admin/dich-vu/{id}', [DichVuController::class, 'update']);
        Route::delete('/admin/dich-vu/{id}', [DichVuController::class, 'destroy']);
    });
