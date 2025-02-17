<?php

namespace Differ\Parser;

function getFileFullPath(string $path)
{
    return realpath($path);
}

function toString($value)
{
    return trim(var_export($value, true), "'");
}

function parse(string $filePath)
{
    $fileFullPath = getFileFullPath($filePath);
    $content = file_get_contents($fileFullPath);

    return json_decode($content, associative: true);
}
