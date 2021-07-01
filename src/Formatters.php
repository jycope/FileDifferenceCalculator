<?php

namespace Differ\Formatters;

use function Differ\Parsers\getDataFromFile;

function formattedDefault($pathFile1, $pathFile2, $format, $depth = 0): array
{
    $data1 = is_array($pathFile1) ? $pathFile1: getDataFromFile($pathFile1);
    $data2 = is_array($pathFile2) ? $pathFile2: getDataFromFile($pathFile2);

    if (empty($data1) && empty($data2)) {
        return "{\n}";
    }

    $result = [];
    $mergedFiles = array_merge($data1, $data2);

    ksort($mergedFiles);

    foreach ($mergedFiles as $key => $value) {
        $value = is_bool($value) ? ($value === true ? 'true' : 'false') : $value;

        $isKeyContainsTwoFiles = array_key_exists($key, $data1) && array_key_exists($key, $data2);
        $isKeyContainsOnlFirstFile = array_key_exists($key, $data1) && !array_key_exists($key, $data2);
        $isKeyContainsOnlySecondFile = !array_key_exists($key, $data1) && array_key_exists($key, $data2);

        $emptySecondFileValue = '- ' . $key;
        $emptyFirstFileValue = '+ ' . $key;
        $keyEmpty = '  ' . $key;

        if ($isKeyContainsTwoFiles) {
            $valueFirstFile = $data1[$key];
            $valueSecondFile = $data2[$key];
            
            if (is_array($value)) {
                $result[$keyEmpty] = formattedDefault($valueFirstFile, $valueSecondFile, $format, $depth + 1);
            } elseif ($valueFirstFile === $valueSecondFile) {
                $result[$keyEmpty] = $value;
            } elseif ($valueFirstFile !== $valueSecondFile) {
                $result[$emptySecondFileValue] = $valueFirstFile;
                $result[$emptyFirstFileValue] = $value;
            }

        } elseif ($isKeyContainsOnlySecondFile) {
            $result[$emptyFirstFileValue] = $value;
        } elseif ($isKeyContainsOnlFirstFile) {
            $result[$emptySecondFileValue] = $value;
        }
    }
    
    return $result;
}

function formattedPlain($pathFile1, $pathFile2, $format, $path = "")
{
    $data1 = is_array($pathFile1) ? $pathFile1: getDataFromFile($pathFile1);
    $data2 = is_array($pathFile2) ? $pathFile2: getDataFromFile($pathFile2);

    if (empty($data1) && empty($data2)) {
        return "{\n}";
    }

    $mergedFiles = array_merge($data1, $data2);
    $result = "";

    ksort($mergedFiles);

    foreach ($mergedFiles as $key => $value) {
        $value = is_bool($value) ? ($value === true ? 'true' : 'false') : $value;
        $currentPath = $path . "." . $key;

        $isKeyContainsTwoFiles = array_key_exists($key, $data1) && array_key_exists($key, $data2);
        $isKeyContainsOnlFirstFile = array_key_exists($key, $data1) && !array_key_exists($key, $data2);
        $isKeyContainsOnlySecondFile = !array_key_exists($key, $data1) && array_key_exists($key, $data2);

        $emptySecondFileValue = '- ' . $key;
        $emptyFirstFileValue = '+ ' . $key;
        $keyEmpty = '  ' . $key;

        if ($isKeyContainsTwoFiles) {
            $valueFirstFile = $data1[$key];
            $valueSecondFile = $data2[$key];
            
            if (is_array($value)) {
                $path .= formattedPlain($valueFirstFile, $valueSecondFile, $format, $key);
                // $path .= $key . formattedPlain($valueFirstFile, $valueSecondFile, $format);
            } elseif ($valueFirstFile === $valueSecondFile) {
            } elseif ($valueFirstFile !== $valueSecondFile) {
            }

        } elseif ($isKeyContainsOnlySecondFile) {
            print_r("Property '{$currentPath}' was added with value: $value");
            print_r("\n");
        } elseif ($isKeyContainsOnlFirstFile) {
        }
    }

    // print_r($result);

    // return $path;
}