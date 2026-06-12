<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TaiKhoan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Xóa dữ liệu cũ
        TaiKhoan::truncate();

        // Tạo tài khoản Quản trị hệ thống
        $admin = TaiKhoan::create([
            'ho_ten' => 'Trần Minh Dững',
            'email' => 'admin@chutro.com',
            'so_dien_thoai' => '0987654321',
            'mat_khau' => Hash::make('password123'),
            'trang_thai' => 1,
        ]);

        //Tao TT Tro 
        $nhaTroId = DB::table('nha_tro')->insertGetId([
            'ten_nha_tro' => 'Nhà Trọ Cao Cấp',
            'dia_chi' => 'Quận Ninh Kiều, TP Cần Thơ',
            'tong_so_tang' => 2,
            'mo_ta' => 'Nhà trọ an ninh, sạch sẽ, giờ giấc tự do.',
            'created_at' => $now,
        ]);
        //Tao Phong
        $loaiPhongId = DB::table('loai_phong')->insertGetId([
            'ten_loai' => 'Phòng Studio có gác',
            'mo_ta' => 'Phòng rộng rãi, phù hợp 2-3 người ở',
            'created_at' => $now,
        ]);

        //Tao mau phong de test
        DB::table('phong')->insert([
            'loai_phong_id' => $loaiPhongId,
            'so_phong' => '101',
            'dien_tich' => 25.5,
            'gia_thue' => 2500000,
            'gia_coc' => 2500000,
            'trang_thai' => 'trong',
            'mo_ta' => 'Phòng mới sửa, view cửa sổ thoáng mát.',
            'created_at' => $now,
        ]);
    }
}
