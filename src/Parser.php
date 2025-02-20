<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function getFileFullPath(string $path)
{
    return realpath($path);
}

function convertToPhp(string $fileContent, string $extension): object
{
    $phpObject = match ($extension) {
        'json' => json_decode($fileContent),
        'yaml', 'yml' => Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP)
    };

    return $phpObject;
}

function parse(string $filePath): object
{
    $fileFullPath = getFileFullPath($filePath);
    $extension = pathinfo($fileFullPath, PATHINFO_EXTENSION);
    $content = file_get_contents($fileFullPath);

    return convertToPhp($content, $extension);
}
