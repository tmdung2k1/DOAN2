<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HoaDon extends Model
{
    use HasFactory;
    protected $table = 'hoa_don';
    protected $primaryKey = 'Ma_HoaDon';
    protected $guarded = [];
}
