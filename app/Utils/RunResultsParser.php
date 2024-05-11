<?php

namespace App\Utils;

use App\Enums\Algorithm;

class RunResultsParser
{
    public static function parseAndersonResults(string $filename, Algorithm $algorithm): array
    {
        $file = fopen($filename, 'r');
        $size = filesize($filename);

        $content = trim(fread($file, $size));
        $lines = explode("\n", $content);

        return collect($lines)->map(function (string $line) use ($algorithm)
        {
            list($instance, $value, $time, $_) = explode("\t", $line);

            return [
                'vertices' => explode('_', $instance)[2],
                'edges' => explode('_', $instance)[3],
                'instance' => basename($instance, '.txt'),
                'value' => $value,
                'algorithm' => $algorithm,
                'time' =>  str_replace(',', '.', $time),
            ];
        })->toArray();
    }
}
