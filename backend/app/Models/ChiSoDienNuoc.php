<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChiSoDienNuoc extends Model
{
    use HasFactory;

    protected $table = 'chi_so_dien_nuoc';
    protected $primaryKey = 'Ma_ChiSoDienNuoc';
    protected $guarded = [];
}
