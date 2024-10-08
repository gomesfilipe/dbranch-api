<?php

namespace App\Utils;

use App\Enums\Algorithm;
use App\Enums\InstanceGroup;

class RunResultsParser
{
    /**
     * @throws \Exception
     */
    public static function parseAndersonResults(string $filename, Algorithm $algorithm): array
    {
        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);

        if ($fileExtension !== 'txt') {
            throw new \Exception('The file extension must be txt.');
        }

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
                'instance_group' => InstanceGroup::SPD_RF2,
                'instance' => basename($instance, '.txt'),
                'value' => $value,
                'algorithm' => $algorithm,
                'time' =>  str_replace(',', '.', $time),
            ];
        })->toArray();
    }

    /**
     * @throws \Exception
     */
    public static function parseJsonResults(string $filename): array
    {
        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);

        if ($fileExtension !== 'json') {
            throw new \Exception('The file extension must be json.');
        }

        $file = fopen($filename, 'r');
        $size = filesize($filename);

        $content = (array) json_decode(
            trim(fread($file, $size))
        );

        $hyperparameters = json_encode($content['hyperparameters']);

        return collect($content['runs'])
            ->map(function (\stdClass $item) use ($hyperparameters)
            {
                $item = (array) $item;
                $item['hyperparameters'] = $hyperparameters;

                return $item;
            })
            ->toArray();
    }
}
