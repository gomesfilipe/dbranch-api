<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum Algorithm: string
{
    use EnumTrait;

    // Construtives
    case BEP = 'Branch Expanding Prim';

    case MORENO_ET_AL = 'Moreno Et Al';

    case EXACT = 'Exact';

    case BEP_ANDERSON = 'BEP Anderson';

    case R_BEP_ANDERSON = 'R-BEP Anderson';

    case PR_BEP = 'PageRank Branch Expanding Prim';

    case R_BEP = 'Randomized Branch Expanding Prim';

    case R_PR_BEP = 'Randomized PageRank Branch Expanding Prim';

    // Local Searchs
    case TVS = 'Treevial Search';

    case B_TVS = 'Boosted Treevial Search';

    // Meta Heuristics
    case GRASP_R_BEP_TVS = 'Grasp with R-BEP and Treevial Search';

    case GRASP_R_BEP_B_TVS = 'Grasp with R-BEP and Boosted Treevial Search';

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

    public static function deterministicAlgorithms(): array
    {
        return [
            self::EXACT,
            self::MORENO_ET_AL,
            self::BEP_ANDERSON,
            self::BEP,
            self::PR_BEP,
            self::TVS,
            self::B_TVS,
        ];
    }

    public static function randomizedAlgorithms(): array
    {
        return [
            self::R_BEP_ANDERSON,
            self::R_BEP,
            self::R_PR_BEP,
            self::GRASP_R_BEP_TVS,
            self::GRASP_R_BEP_B_TVS,
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

    public function mode(): ?AlgorithmMode
    {
        return match (true) {
            in_array($this, self::deterministicAlgorithms()) => AlgorithmMode::DETERMINISTIC,
            in_array($this, self::randomizedAlgorithms()) => AlgorithmMode::RANDOM,
            default => null,
        };
    }

    public static function disregardRuns(): array
    {
        return [
//            self::R_BEP_ANDERSON,
//            self::R_BEP,
//            self::R_PR_BEP,
//            self::EXACT,
        ];
    }
}
