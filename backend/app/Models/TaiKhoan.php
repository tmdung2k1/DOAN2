<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class TaiKhoan extends Model
{
    use HasApiTokens;

    protected $table = 'tai_khoans';
    protected $primaryKey = 'Ma_TaiKhoan';

    protected $fillable = [
        'ho_ten',
        'email',
        'so_dien_thoai',
        'mat_khau',
        'trang_thai',
    ];

    protected $hidden = [
        'mat_khau',
    ];
}
