<?php

namespace App\Enums;

enum Algorithm: string
{
    case BEP = 'Branch Expanding Prim';

    case MORENO_ET_AL = 'Moreno Et Al';

    case EXACT = 'Exact';

    case BEP_ANDERSON = 'BEP Anderson';

    case R_BEP_ANDERSON = 'R-BEP Anderson';

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

    public static function hasBranchVerticesSaved(): array
    {
        return [
            self::BEP,
            self::PR_BEP,
            self::R_BEP,
            self::R_PR_BEP,
        ];
    }

    public static function algorithmsBasedOnDegreeCentrality(): array
    {
        return [
            self::BEP,
            self::MORENO_ET_AL,
            self::R_BEP_ANDERSON,
            self::R_BEP,
            self::BEP_ANDERSON,
            self::EXACT,
        ];
    }

    public static function algorithmsBasedOnPageRankCentrality(): array
    {
        return [
            self::PR_BEP,
            self::R_PR_BEP,
        ];
    }

    public function centrality(): ?Centrality
    {
        return match (true) {
            in_array($this, self::algorithmsBasedOnDegreeCentrality()) => Centrality::DEGREE,
            in_array($this, self::algorithmsBasedOnPageRankCentrality()) => Centrality::PAGERANK,
            default => null,
        };
    }

    public static function disregardRuns(): array
    {
        return [
            self::R_BEP_ANDERSON,
            self::R_BEP,
            self::R_PR_BEP,
            self::EXACT,
        ];
    }
}
