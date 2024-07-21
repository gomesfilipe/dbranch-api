<?php

namespace App\Models;

use App\Enums\Algorithm;
use App\Enums\Centrality;
use App\Enums\InstanceGroup;
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
        'centrality' => Centrality::class,
        'instance_group' => InstanceGroup::class,
        'branch_vertices' => 'array',
        'hyperparameters' => 'array',
    ];
}
