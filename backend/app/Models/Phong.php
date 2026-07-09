<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phong extends Model
{
    use HasFactory;
    protected $table = 'phong';
    protected $fillable = [
        'tang_id',
        'loai_phong_id',
        'so_phong',
        'dien_tich',
        'chieu_dai',
        'chieu_rong',
        'gia_thue',
        'gia_coc',
        'trang_thai',
        'link_3d',
        'mo_ta',
    ];

    public function loaiPhong()
    {
        return $this->belongsTo(LoaiPhong::class, 'loai_phong_id');
    }

    public function tang()
    {
        return $this->belongsTo(Tang::class, 'tang_id');
    }

    public function tienIch()
    {
        return $this->belongsToMany(TienIch::class, 'tien_ich_phong', 'phong_id', 'tien_ich_id');
    }
}
