<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhatKyHoatDong extends Model
{
    use HasFactory;
    protected $table = 'nhat_ky_hoat_dong';
    protected $primaryKey = 'Ma_NhatKyHoatDong';
    protected $guarded = [];
}
