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
        $request->validate([
            'email' => 'required|email',
            'mat_khau' => 'required'
        ]);

        $admin = TaiKhoan::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->mat_khau, $admin->mat_khau)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email hoặc mật khẩu không chính xác!'
            ], 401);
        }

        $token = $admin->createToken('admin_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Đăng nhập thành công!',
            'thong_tin' => $admin,
            'token' => $token
        ]);
    }
}
