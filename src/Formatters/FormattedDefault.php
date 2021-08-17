<?php

namespace Differ\Formatters\FormattedDefault;

function formattedDefault(array $data1, array $data2): array
{
    $mergedFiles = collect(array_merge($data1, $data2))->sortKeys();

    $result = $mergedFiles->reduce(function ($carry, $value, $key) use ($data1, $data2): object {
        $isKeyContainsTwoFiles = array_key_exists($key, $data1) && array_key_exists($key, $data2);
        $isKeyContainsOnlyFirstFile = array_key_exists($key, $data1) && !array_key_exists($key, $data2);
        $isKeyContainsOnlySecondFile = !array_key_exists($key, $data1) && array_key_exists($key, $data2);

        $emptySecondFileValue = str_replace("* ", "- ", $key);
        $emptyFirstFileValue = str_replace("* ", "+ ", $key);

        if ($isKeyContainsTwoFiles) {
            $valueFirstFile = $data1[$key];
            $valueSecondFile = $data2[$key];

            if (is_array($valueFirstFile) && is_array($valueSecondFile)) {
                $carry->put($key, formattedDefault($valueFirstFile, $valueSecondFile));
            } elseif ($valueFirstFile === $valueSecondFile) {
                $carry->put($key, $value);
            } elseif ($valueFirstFile !== $valueSecondFile) {
                $carry->put($emptySecondFileValue, $valueFirstFile);
                $carry->put($emptyFirstFileValue, $value);
            }
        } elseif ($isKeyContainsOnlySecondFile) {
            $carry->put($emptyFirstFileValue, $value);
        } elseif ($isKeyContainsOnlyFirstFile) {
            $carry->put($emptySecondFileValue, $value);
        }

        return $carry;
    }, collect([]))->all();

    return $result;
}
