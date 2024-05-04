<?php

namespace App\Enums;

enum Algorithm: string
{
    case BEP = 'Branch Expanding Prim';

    case MORENO_ET_AL = 'Moreno Et Al';

    case EXACT = 'Exact';

    case BEP_ANDERSON = 'BEP Anderson';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
