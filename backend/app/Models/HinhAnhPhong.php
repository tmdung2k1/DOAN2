<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HinhAnhPhong extends Model
{
    use HasFactory;
    protected $table = 'hinh_anh_phong';
    protected $primaryKey = 'Ma_HinhAnhPhong';
    protected $guarded = [];
}
