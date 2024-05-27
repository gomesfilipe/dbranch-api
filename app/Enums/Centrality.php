<?php

namespace App\Enums;

enum Centrality: string
{
    case DEGREE = 'Degree';

    case PAGERANK = 'PageRank';


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
