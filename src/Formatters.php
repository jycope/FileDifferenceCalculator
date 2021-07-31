<?php

namespace Differ\Formatters;

use function Differ\Parsers\getDataFromFile;
use function Differ\Differ\clearedData;

function addOperatorToKeys($array)
{
    $result = [];

    foreach ($array as $key => $value) {
        $result["* " . $key] = is_array($value) ? addOperatorToKeys($value) : $value;
    }

    return $result;
}

function formattedDefault(array $data1, array $data2): array
{
    if (empty($data1) && empty($data2)) {
        return [];
    }

    $result = [];

    $mergedFiles = array_merge($data1, $data2);

    ksort($mergedFiles);

    foreach ($mergedFiles as $key => $value) {
        $isKeyContainsTwoFiles = array_key_exists($key, $data1) && array_key_exists($key, $data2);
        $isKeyContainsOnlyFirstFile = array_key_exists($key, $data1) && !array_key_exists($key, $data2);
        $isKeyContainsOnlySecondFile = !array_key_exists($key, $data1) && array_key_exists($key, $data2);

        $emptySecondFileValue = str_replace("* ", "- ", $key);
        $emptyFirstFileValue = str_replace("* ", "+ ", $key);

        if ($isKeyContainsTwoFiles) {
            $valueFirstFile = $data1[$key];
            $valueSecondFile = $data2[$key];

            if (is_array($valueFirstFile) && is_array($valueSecondFile)) {
                $result[$key] = formattedDefault($valueFirstFile, $valueSecondFile);
            } elseif ($valueFirstFile === $valueSecondFile) {
                $result[$key] = $value;
            } elseif ($valueFirstFile !== $valueSecondFile) {
                $result[$emptySecondFileValue] = $valueFirstFile;
                $result[$emptyFirstFileValue] = $value;
            }
        } elseif ($isKeyContainsOnlySecondFile) {
            $result[$emptyFirstFileValue] = $value;
        } elseif ($isKeyContainsOnlyFirstFile) {
            $result[$emptySecondFileValue] = $value;
        }
    }

    return $result;
}

function formattedPlain(array $data1, array $data2, $path = "")
{
    if (empty($data1) && empty($data2)) {
        return [];
    }

    $mergedFiles = array_merge($data1, $data2);
    $result = '';

    ksort($mergedFiles);

    foreach ($mergedFiles as $key => $value) {
        $currentPath = $path . $key;

        $isKeyContainsTwoFiles = array_key_exists($key, $data1) && array_key_exists($key, $data2);
        $isKeyContainsOnlyFirstFile = array_key_exists($key, $data1) && !array_key_exists($key, $data2);
        $isKeyContainsOnlySecondFile = !array_key_exists($key, $data1) && array_key_exists($key, $data2);

        if ($isKeyContainsTwoFiles) {
            $valueFirstFile = $data1[$key];
            $valueSecondFile = $data2[$key];

            if (is_array($valueFirstFile) && is_array($valueSecondFile)) {
                $result .= formattedPlain($valueFirstFile, $valueSecondFile, $currentPath . ".");
            } elseif ($valueFirstFile !== $valueSecondFile) {
                $valueFirstFile =  is_array($valueFirstFile)  ? '[complex value]' : var_export($data1[$key], true);
                $valueSecondFile = is_array($valueSecondFile) ? '[complex value]' : var_export($data2[$key], true);

                $result .= "Property '{$currentPath}' was updated. From {$valueFirstFile} to {$valueSecondFile}\n";
            }
        } elseif ($isKeyContainsOnlyFirstFile) {
            $result .= "Property '{$currentPath}' was removed\n";
        } elseif ($isKeyContainsOnlySecondFile) {
            $value = is_array($value) ? '[complex value]' : var_export($value, true);
            $result .= "Property '{$currentPath}' was added with value: {$value}\n";
        }
    }

    return $result;
}

function formattedJson(array $data1, array $data2)
{
    if (empty($data1) && empty($data2)) {
        return [];
    }

    $result = [];
    $mergedFiles = array_merge($data1, $data2);

    ksort($mergedFiles);

    foreach ($mergedFiles as $key => $value) {
        $value = is_bool($value) ? ($value === true ? 'true' : 'false') : $value;

        $isKeyContainsTwoFiles = array_key_exists($key, $data1) && array_key_exists($key, $data2);
        $isKeyContainsOnlyFirstFile = array_key_exists($key, $data1) && !array_key_exists($key, $data2);
        $isKeyContainsOnlySecondFile = !array_key_exists($key, $data1) && array_key_exists($key, $data2);

        $emptySecondFileValue = '- ' . $key;
        $emptyFirstFileValue = '+ ' . $key;
        $keyEmpty = '* ' . $key;

        if (is_array($valueFirstFile) && is_array($valueSecondFile)) {
            $valueFirstFile = $data1[$key];
            $valueSecondFile = $data2[$key];

            if (is_array($value)) {
                $result[$keyEmpty] = formattedDefault($valueFirstFile, $valueSecondFile);
            } elseif ($valueFirstFile === $valueSecondFile) {
                $result[$keyEmpty] = $value;
            } elseif ($valueFirstFile !== $valueSecondFile) {
                $result[$emptySecondFileValue] = $valueFirstFile;
                $result[$emptyFirstFileValue] = $value;
            }
        } elseif ($isKeyContainsOnlySecondFile) {
            $result[$emptyFirstFileValue] = $value;
        } elseif ($isKeyContainsOnlyFirstFile) {
            $result[$emptySecondFileValue] = $value;
        }
    }

    return $result;
}
