<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrollQueue extends Model
{
    use HasFactory;

    protected $table = 'enroll_queue';
    protected $fillable = [
        'nisn',

    ];
}
