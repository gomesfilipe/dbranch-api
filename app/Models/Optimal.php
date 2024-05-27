<?php

namespace App\Models;

use App\Enums\Algorithm;
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

    public static function optimalResults(): array
    {
        return [
            // Medium Instances Exact Algorithm
            self::generateOptimalAttributes(20, 41.8, 0.8, 0.8, Algorithm::EXACT),
            self::generateOptimalAttributes(40, 70.8, 2.8, 2.8, Algorithm::EXACT),
            self::generateOptimalAttributes(60, 95.0, 6.3, 6.3, Algorithm::EXACT),
            self::generateOptimalAttributes(80, 119.8, 9.2, 9.2, Algorithm::EXACT),
            self::generateOptimalAttributes(100, 144.0, 13.3, 13.3, Algorithm::EXACT),
            self::generateOptimalAttributes(120, 168.8, 17.5, 17.5, Algorithm::EXACT),
            self::generateOptimalAttributes(140, 193.0, 20.9, 20.9, Algorithm::EXACT),
            self::generateOptimalAttributes(160, 217.8, 25.0, 25.0, Algorithm::EXACT),
            self::generateOptimalAttributes(180, 242.0, 29.1, 29.1, Algorithm::EXACT),
            self::generateOptimalAttributes(200, 266.8, 32.6, 32.6, Algorithm::EXACT),
            self::generateOptimalAttributes(250, 321.0, 44.6, 44.6, Algorithm::EXACT),
            self::generateOptimalAttributes(300, 380.0, 57.4, 57.4, Algorithm::EXACT),
            self::generateOptimalAttributes(350, 434.8, 68.6, 68.6, Algorithm::EXACT),
            self::generateOptimalAttributes(400, 489.0, 81.8, 81.8, Algorithm::EXACT),
            self::generateOptimalAttributes(450, 548.0, 93.4, 93.4, Algorithm::EXACT),
            self::generateOptimalAttributes(500, 602.8, 106.7, 106.7, Algorithm::EXACT),

            // Medium Instances Moreno Et Al algorithm
            self::generateOptimalAttributes(20, 41.8, 1.04, 1.04, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(40, 70.8, 3.76, 3.76, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(60, 95.0, 8.16, 8.16, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(80, 119.8, 11.68, 11.68, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(100, 144.0, 16.24, 16.24, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(120, 168.8, 20.88, 20.88, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(140, 193.0, 24.52, 24.52, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(160, 217.8, 29.84, 29.84, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(180, 242.0, 33.44, 33.44, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(200, 266.8, 37.56, 37.56, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(250, 321.0, 50.72, 50.72, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(300, 380.0, 63.16, 63.16, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(350, 434.8, 76.12, 76.12, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(400, 489.0, 90.84, 90.84, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(450, 548.0, 102.04, 102.04, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(500, 602.8, 116.64, 116.64, Algorithm::MORENO_ET_AL),

            // Medium Instances R-BEP Anderson Algorithm
            self::generateOptimalAttributes(20, 41.8, 0.84, 1.50, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(40, 70.8, 3.28, 4.71, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(60, 95.0, 7.12, 8.70, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(80, 119.8, 10.24, 12.01, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(100, 144.0, 14.76, 16.59, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(120, 168.8, 19.00, 20.98, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(140, 193.0, 22.68, 24.92, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(160, 217.8, 27.24, 29.36, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(180, 242.0, 31.28, 33.79, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(200, 266.8, 35.20, 37.64, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(250, 321.0, 47.76, 50.17, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(300, 380.0, 60.40, 63.02, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(350, 434.8, 72.36, 75.02, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(400, 489.0, 85.88, 88.76, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(450, 548.0, 97.68, 100.52, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(500, 602.8, 111.00, 114.05, Algorithm::R_BEP_ANDERSON),

            // Large Instances Exact algorithm
            self::generateOptimalAttributes(600, 637, 183.8, 183.8, Algorithm::EXACT),
            self::generateOptimalAttributes(600, 674, 167.2, 167.2, Algorithm::EXACT),
            self::generateOptimalAttributes(600, 712, 150.6, 150.6, Algorithm::EXACT),
            self::generateOptimalAttributes(600, 749, 138.8, 138.8, Algorithm::EXACT),
            self::generateOptimalAttributes(600, 787, 125.8, 125.8, Algorithm::EXACT),
            self::generateOptimalAttributes(700, 740, 214.4, 214.4, Algorithm::EXACT),
            self::generateOptimalAttributes(700, 780, 198.0, 198.0, Algorithm::EXACT),
            self::generateOptimalAttributes(700, 821, 180.0, 180.0, Algorithm::EXACT),
            self::generateOptimalAttributes(700, 861, 164.0, 164.0, Algorithm::EXACT),
            self::generateOptimalAttributes(700, 902, 154.2, 154.2, Algorithm::EXACT),
            self::generateOptimalAttributes(800, 843, 245.6, 245.6, Algorithm::EXACT),
            self::generateOptimalAttributes(800, 886, 227.6, 227.6, Algorithm::EXACT),
            self::generateOptimalAttributes(800, 930, 208.4, 208.4, Algorithm::EXACT),
            self::generateOptimalAttributes(800, 973, 194.2, 194.2, Algorithm::EXACT),
            self::generateOptimalAttributes(800, 1017, 176.2, 176.2, Algorithm::EXACT),
            self::generateOptimalAttributes(900, 944, 279.6, 279.6, Algorithm::EXACT),
            self::generateOptimalAttributes(900, 989, 259.2, 259.2, Algorithm::EXACT),
            self::generateOptimalAttributes(900, 1034, 240.6, 240.6, Algorithm::EXACT),
            self::generateOptimalAttributes(900, 1079, 223.2, 223.2, Algorithm::EXACT),
            self::generateOptimalAttributes(900, 1124, 206.0, 206.0, Algorithm::EXACT),
            self::generateOptimalAttributes(1000, 1047, 312.0, 312.0, Algorithm::EXACT),
            self::generateOptimalAttributes(1000, 1095, 290.0, 290.0, Algorithm::EXACT),
            self::generateOptimalAttributes(1000, 1143, 271.2, 271.2, Algorithm::EXACT),
            self::generateOptimalAttributes(1000, 1191, 251.0, 251.0, Algorithm::EXACT),
            self::generateOptimalAttributes(1000, 1239, 235.2, 235.2, Algorithm::EXACT),

            // Large Instances Moreno Et Al algorithm
            self::generateOptimalAttributes(600, 637, 187.6, 187.6, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(600, 674, 173.4, 173.4, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(600, 712, 159.4, 159.4, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(600, 749, 145.4, 145.4, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(600, 787, 135.4, 135.4, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(700, 740, 218.8, 218.8, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(700, 780, 204.4, 204.4, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(700, 821, 189.0, 189.0, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(700, 861, 174.2, 174.2, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(700, 902, 165.2, 165.2, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(800, 843, 250.8, 250.8, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(800, 886, 235.0, 235.0, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(800, 930, 216.2, 216.2, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(800, 973, 205.0, 205.0, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(800, 1017, 189.0, 189.0, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(900, 944, 285.2, 285.2, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(900, 989, 268.8, 268.8, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(900, 1034, 250.6, 250.6, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(900, 1079, 235.8, 235.8, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(900, 1124, 219.0, 219.0, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(1000, 1047, 317.4, 317.4, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(1000, 1095, 299.6, 299.6, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(1000, 1143, 283.0, 283.0, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(1000, 1191, 264.0, 264.0, Algorithm::MORENO_ET_AL),
            self::generateOptimalAttributes(1000, 1239, 249.6, 249.6, Algorithm::MORENO_ET_AL),

            // Medium Instances R-BEP Anderson Algorithm
            self::generateOptimalAttributes(600, 637, 184.6, 186.1, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(600, 674, 170.2, 171.2, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(600, 712, 154.0, 156.3, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(600, 749, 142.6, 145.2, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(600, 787, 131.6, 134.0, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(700, 740, 216.2, 217.3, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(700, 780, 201.10, 202.5, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(700, 821, 184.0, 185.8, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(700, 861, 168.8, 171.2, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(700, 902, 159.0, 162.3, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(800, 843, 247.8, 248.5, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(800, 886, 230.6, 231.9, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(800, 930, 212.6, 214.9, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(800, 973, 128.8, 201.4, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(800, 1017, 182.6, 185.4, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(900, 944, 282.0, 282.7, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(900, 989, 263.4, 265.0, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(900, 1034, 245.0, 247.0, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(900, 1079, 229.6, 232.0, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(900, 1124, 213.0, 215.3, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(1000, 1047, 314.6, 315.9, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(1000, 1095, 294.4, 295.9, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(1000, 1143, 276.2, 278.0, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(1000, 1191, 257.2, 260.0, Algorithm::R_BEP_ANDERSON),
            self::generateOptimalAttributes(1000, 1239, 242.2, 244.7, Algorithm::R_BEP_ANDERSON),
        ];
    }
    private static function generateOptimalAttributes(int $vertices, float $edges, float $min, float $mean, Algorithm $algorithm): array
    {
        return [
            'vertices' => $vertices,
            'edges' => $edges,
            'min' => $min,
            'mean' => $mean,
            'algorithm' => $algorithm,
        ];
    }
}
