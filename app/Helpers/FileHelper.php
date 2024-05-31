<?php

if (! function_exists('path_join')) {
    function path_join(...$paths)
    {

        if (count($paths) === 1) {
            return $paths[0];
        }

        $first = array_shift($paths) ?? '';
        $last = array_pop($paths) ?? '';

        if (count($paths) === 0) {
            return rtrim($first, '/') . '/' . ltrim($last, '/');
        }

        $fullPath = rtrim($first, '/');
        foreach ($paths as $path) {
            $fullPath .= '/' . trim($path, '/') . '/';
        }
        $fullPath .= ltrim($last, '/');

        return $fullPath;
    }
}
