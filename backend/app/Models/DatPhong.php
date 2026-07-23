<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DatPhong extends Model
{
    use HasFactory;
    protected $table = 'dat_phong';
    protected $primaryKey = 'Ma_DatPhong';
    protected $guarded = [];

    public function phong()
    {
        return $this->belongsTo(Phong::class, 'Ma_Phong', 'Ma_Phong');
    }
}
