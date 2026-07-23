<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TienIch extends Model
{
    use HasFactory;
    protected $table = 'tien_ich';
    protected $primaryKey = 'Ma_TienIch';
    protected $guarded = [];
}
