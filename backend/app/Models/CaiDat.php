<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class CaiDat extends Model
{
    use HasFactory;
    protected $table = 'cai_dat';
    protected $primaryKey = 'Ma_CaiDat';
    protected $guarded = [];
}
