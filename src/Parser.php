<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function getFileFullPath(string $path)
{
    return realpath($path);
}

function convertToObject(string $fileContent, string $extension): object
{
    $phpObject = match ($extension) {
        'json' => json_decode($fileContent),
        'yaml', 'yml' => Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP),
        default => throw new \Exception('Unsupported file')
    };

    return $phpObject;
}

function parse(string $filePath): object
{
    $fileFullPath = getFileFullPath($filePath);

    if (!$fileFullPath) {
        throw new \Exception('File not found');
    }

    $extension = pathinfo($fileFullPath, PATHINFO_EXTENSION);
    $content = file_get_contents($fileFullPath);

    if ($content === false || $content === '') {
        throw new \Exception('File is empty');
    }

    return convertToObject($content, $extension);
}
