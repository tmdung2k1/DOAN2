<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YeuCauBaoTri extends Model
{
    use HasFactory;
    protected $table = 'yeu_cau_bao_tri';
    protected $primaryKey = 'Ma_YeuCauBaoTri';
    protected $guarded = [];
}
