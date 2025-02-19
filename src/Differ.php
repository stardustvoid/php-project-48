<?php

namespace Differ\Differ;

use function Differ\Parser\parse;

function toString($value)
{
    return trim(var_export($value, true), "'");
}

function getKeys(array $originalData, array $updatedData): array
{
    $uniqueKeys = array_unique(array_merge(array_keys($originalData), array_keys($updatedData)));

    sort($uniqueKeys);

    return $uniqueKeys;
}

function buildDiff(array $originalData, array $updatedData): array
{
    $keys = getKeys($originalData, $updatedData);

    return array_map(function ($key) use ($originalData, $updatedData) {
        $originalValue = $originalData[$key] ?? null;
        $updatedValue = $updatedData[$key] ?? null;
        $strOriginalValue = toString($originalValue);
        $strUpdatedValue = toString($updatedValue);

        if (!array_key_exists($key, $originalData)) {
            return [
                'type' => 'added',
                'key' => $key,
                'value' => $strUpdatedValue
            ];
        } elseif (!array_key_exists($key, $updatedData)) {
            return [
                'type' => 'removed',
                'key' => $key,
                'value' => $strOriginalValue
            ];
        } elseif ($originalValue !== $updatedValue) {
            return [
                'type' => 'updated',
                'key' => $key,
                'originalValue' => $strOriginalValue,
                'updatedValue' => $strUpdatedValue
            ];
        }

        return [
            'type' => 'unchanged',
            'key' => $key,
            'value' => $originalValue
        ];
    }, $keys);
}

function genDiffString(array $diff): string
{
    $indent = '  ';

    $lines = array_reduce($diff, function ($acc, $item) use ($indent) {
        $type = $item['type'];
        $key = $item['key'];

        $line = match ($type) {
            'unchanged' => "{$indent}  {$key}: {$item['value']}",
            'added' => "{$indent}+ {$key}: {$item['value']}",
            'removed' => "{$indent}- {$key}: {$item['value']}",
            'updated' => "{$indent}- {$key}: {$item['originalValue']}\n{$indent}+ {$key}: {$item['updatedValue']}"
        };

        return [...$acc, $line];
    }, []);

    $result = ['{', ...$lines, '}'];

    return implode("\n", $result);
}

function genDiff(string $pathToFile1, string $pathToFile2): string
{
    $originalData = (array) parse($pathToFile1);
    $updatedData = (array) parse($pathToFile2);

    $diff = buildDiff($originalData, $updatedData);

    return genDiffString($diff);
}
