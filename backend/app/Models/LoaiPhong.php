<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoaiPhong extends Model
{
    use HasFactory;
    protected $table = 'loai_phong';
    protected $primaryKey = 'Ma_LoaiPhong';
    protected $guarded = []; 
}
