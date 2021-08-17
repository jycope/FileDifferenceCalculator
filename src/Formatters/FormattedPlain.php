<?php

namespace Differ\Formatters\FormattedPlain;

function formattedPlain(array $data1, array $data2, string $path = ""): array
{
    $mergedFiles = collect(array_merge($data1, $data2))->sortKeys();

    $result = $mergedFiles->reduce(function ($result, $value, $key) use ($path, $data1, $data2): object {
        $currPath = $path . $key;

        $isKeyContainsTwoFiles = array_key_exists($key, $data1) && array_key_exists($key, $data2);
        $isKeyContainsOnlyFirstFile = array_key_exists($key, $data1) && !array_key_exists($key, $data2);
        $isKeyContainsOnlySecondFile = !array_key_exists($key, $data1) && array_key_exists($key, $data2);

        if ($isKeyContainsTwoFiles) {
            $valueFirstFile = $data1[$key];
            $valueSecondFile = $data2[$key];

            if (is_array($valueFirstFile) && is_array($valueSecondFile)) {
                $result->push(formattedPlain($valueFirstFile, $valueSecondFile, $currPath . "."));
            } elseif ($valueFirstFile !== $valueSecondFile) {
                $valFirstFileCopy  =  is_array($valueFirstFile)  ? '[complex value]' : var_export($data1[$key], true);
                $valSecondFileCopy = is_array($valueSecondFile) ? '[complex value]' : var_export($data2[$key], true);

                $result->push("Property '{$currPath}' was updated. From {$valFirstFileCopy} to {$valSecondFileCopy}");
            }
        } elseif ($isKeyContainsOnlyFirstFile) {
            $result->push("Property '{$currPath}' was removed");
        } elseif ($isKeyContainsOnlySecondFile) {
            $valueTmp = is_array($value) ? '[complex value]' : var_export($value, true);
            $result->push("Property '{$currPath}' was added with value: {$valueTmp}");
        }

        return $result;
    }, collect([]))->all();

    return $result;
}
