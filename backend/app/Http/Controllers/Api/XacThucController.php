<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaiKhoan;
use Illuminate\Support\Facades\Hash;

class XacThucController extends Controller
{
    public function dangNhap(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $request->validate([
            'email' => 'required|email',
            'mat_khau' => 'required'
        ]);

        // Tìm tài khoản theo email
        $admin = TaiKhoan::where('email', $request->email)->first();

        // Kiểm tra sự tồn tại và so khớp mật khẩu
        if (!$admin || !Hash::check($request->mat_khau, $admin->mat_khau)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email hoặc mật khẩu không chính xác!'
            ], 401);
        }

        // Cấp Token độc quyền truy cập trang quản trị
        $token = $admin->createToken('admin_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Đăng nhập thành công!',
            'thong_tin' => $admin,
            'token' => $token
        ]);
    }
}
