<?php

namespace Differ\Formatters\FormattedJson;

function formattedJson(array $data1, array $data2): array
{
    $mergedFiles = collect(array_merge($data1, $data2))->sortKeys();

    $result = $mergedFiles->reduce(function ($carry, $value, $key) use ($data1, $data2): object {
        $isKeyContainsTwoFiles = array_key_exists($key, $data1) && array_key_exists($key, $data2);
        $isKeyContainsOnlyFirstFile = array_key_exists($key, $data1) && !array_key_exists($key, $data2);
        $isKeyContainsOnlySecondFile = !array_key_exists($key, $data1) && array_key_exists($key, $data2);

        if ($isKeyContainsTwoFiles) {
            $valueFirstFile = $data1[$key];
            $valueSecondFile = $data2[$key];

            if (is_array($valueFirstFile) && is_array($valueSecondFile)) {
                $carry->put($key, formattedJson($valueFirstFile, $valueSecondFile));
            } elseif ($valueFirstFile === $valueSecondFile) {
                $carry->put($key, $value);
            } elseif ($valueFirstFile !== $valueSecondFile) {
                $carry->put($key, $valueFirstFile);
                $carry->put($key, $value);
            }
        } elseif ($isKeyContainsOnlySecondFile) {
            $carry->put($key, $value);
        } elseif ($isKeyContainsOnlyFirstFile) {
            $carry->put($key, $value);
        }

        return $carry;
    }, collect([]))->all();

    return $result;
}
