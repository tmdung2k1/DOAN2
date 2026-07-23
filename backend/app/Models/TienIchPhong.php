<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TienIchPhong extends Model
{
    use HasFactory;
    protected $table = 'tien_ich_phong';
    protected $primaryKey = 'Ma_TienIchPhong';
    protected $guarded = [];
}
