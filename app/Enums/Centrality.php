<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum Centrality: string
{
    use EnumTrait;

    case DEGREE = 'Degree';

    case PAGERANK = 'PageRank';
}
