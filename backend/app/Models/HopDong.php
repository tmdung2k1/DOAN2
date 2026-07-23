<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HopDong extends Model
{
    use HasFactory;

    protected $table = 'hop_dong';
    protected $primaryKey = 'Ma_HopDong';
    
    protected $guarded = [];

    public function phong()
    {
        return $this->belongsTo(Phong::class, 'Ma_Phong', 'Ma_Phong');
    }

    public function khachHangs()
    {
        return $this->hasMany(KhachHang::class, 'Ma_HopDong', 'Ma_HopDong');
    }
}
