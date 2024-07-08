<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum AlgorithmMode: string
{
    use EnumTrait;

    case DETERMINISTIC = 'Deterministic';

    case RANDOM = 'Random';
}
