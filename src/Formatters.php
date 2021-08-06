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

function formattedJson(array $data1, array $data2): array
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

        if ($isKeyContainsTwoFiles) {
            $valueFirstFile = $data1[$key];
            $valueSecondFile = $data2[$key];

            if (is_array($valueFirstFile) && is_array($valueSecondFile)) {
                $result[$key] = formattedJson($valueFirstFile, $valueSecondFile);
            } elseif ($valueFirstFile === $valueSecondFile) {
                $result[$key] = $value;
            } elseif ($valueFirstFile !== $valueSecondFile) {
                $result[$key] = $valueFirstFile;
                $result[$key] = $value;
            }
        } elseif ($isKeyContainsOnlySecondFile) {
            $result[$key] = $value;
        } elseif ($isKeyContainsOnlyFirstFile) {
            $result[$key] = $value;
        }
    }

    return $result;
}

function getProperties($property)
{
    return $property['properties'];
}

function getСomparison(array $result, $value, $key, array $data1, array $data2): array
{
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

    return $result;
}

function formattedDefault(array $data1, array $data2)
{
    $mergedFiles = collect(array_merge($data1, $data2))->sortKeys();

    $comparisonData = $mergedFiles->reduce(function ($carry, $item, $key) use ($data1, $data2) {
        return getСomparison($carry, $item, $key, $data1, $data2);
    }, []);

    return collect($comparisonData)->all();
}

function formattedPlain(array $data1, array $data2, $path = "")
{
    if (empty($data1) && empty($data2)) {
        return [];
    }

    $mergedFiles = array_merge($data1, $data2);
    $result = [];

    ksort($mergedFiles);

    foreach ($mergedFiles as $key => $value) {
        $currPath = $path . $key;

        $isKeyContainsTwoFiles = array_key_exists($key, $data1) && array_key_exists($key, $data2);
        $isKeyContainsOnlyFirstFile = array_key_exists($key, $data1) && !array_key_exists($key, $data2);
        $isKeyContainsOnlySecondFile = !array_key_exists($key, $data1) && array_key_exists($key, $data2);

        if ($isKeyContainsTwoFiles) {
            $valueFirstFile = $data1[$key];
            $valueSecondFile = $data2[$key];

            if (is_array($valueFirstFile) && is_array($valueSecondFile)) {
                $result[] = formattedPlain($valueFirstFile, $valueSecondFile, $currPath . ".");
            } elseif ($valueFirstFile !== $valueSecondFile) {
                $valueFirstFile =  is_array($valueFirstFile)  ? '[complex value]' : var_export($data1[$key], true);
                $valueSecondFile = is_array($valueSecondFile) ? '[complex value]' : var_export($data2[$key], true);

                $result[] = "Property '{$currPath}' was updated. From {$valueFirstFile} to {$valueSecondFile}";
            }
        } elseif ($isKeyContainsOnlyFirstFile) {
            $result[] = "Property '{$currPath}' was removed";
        } elseif ($isKeyContainsOnlySecondFile) {
            $value = is_array($value) ? '[complex value]' : var_export($value, true);
            $result[] = "Property '{$currPath}' was added with value: {$value}";
        }
    }

    return $result;
}
