<?php

namespace App\Enums;

enum Algorithm: string
{
    case BEP = 'Branch Expanding Prim';

    case MORENO_ET_AL = 'Moreno Et Al';

    case EXACT = 'Exact';

    case BEP_ANDERSON = 'BEP Anderson';

    case PR_BEP = 'PageRank Branch Expanding Prim';

    case R_BEP = 'Randomized Branch Expanding Prim';

    case R_PR_BEP = 'Randomized PageRank Branch Expanding Prim';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function referenceAlgorithmsValues(): array
    {
        return [
            self::EXACT->value,
            self::MORENO_ET_AL->value,
        ];
    }
}
