<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TinNhanAI extends Model
{
    use HasFactory;
    protected $table = 'tin_nhan_ai';
    protected $primaryKey = 'Ma_TinNhanAI';
    protected $guarded = [];
}
