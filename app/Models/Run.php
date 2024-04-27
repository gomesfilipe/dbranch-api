<?php

namespace App\Models;

use App\Enums\Algorithm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Run extends Model
{
    use HasFactory;

    protected $fillable = [
        'vertices',
        'edges',
        'instance',
        'value',
        'algorithm',
    ];

    protected $casts = [
        'algorithm' => Algorithm::class,
    ];
}
