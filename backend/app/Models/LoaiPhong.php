<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoaiPhong extends Model
{
    protected $table = 'loai_phong';
    protected $fillable = ['ten_loai', 'mo_ta'];

    public function phongs()
    {
        return $this->hasMany(Phong::class, 'loai_phong_id');
    }
}
