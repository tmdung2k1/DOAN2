<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MauHopDong extends Model
{
    use HasFactory;
    protected $table = 'mau_hop_dong';
    protected $primaryKey = 'Ma_MauHopDong';
    protected $guarded = [];
}
