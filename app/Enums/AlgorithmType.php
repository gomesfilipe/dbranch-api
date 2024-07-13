<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum AlgorithmType: string
{
    use EnumTrait;

    case CONSTRUCTIVE = 'Constructive';

    case LOCAL_SEARCH = 'Local Search';

    case META_HEURISTIC = 'Meta Heuristic';

    case EXACT = 'Exact';
}
