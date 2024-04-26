<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Optimal extends Model
{
    use HasFactory;

    protected $fillable = [
        'vertices',
        'edges',
        'algorithm',
        'min',
        'max',
    ];
}
