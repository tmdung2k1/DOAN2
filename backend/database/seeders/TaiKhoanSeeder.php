<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TaiKhoan;
use Illuminate\Support\Facades\Hash;

class TaiKhoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ
        TaiKhoan::truncate();

        // Tạo tài khoản admin
        TaiKhoan::create([
            'ho_ten' => 'Quản Trị Viên',
            'email' => 'admin@chutro.com',
            'so_dien_thoai' => '0123456789',
            'mat_khau' => Hash::make('password123'),
            'trang_thai' => 1,
        ]);
    }
}
