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
        $isKeyContainsOnlyFirstFile = array_key_exists($key, $data1) && !array_key_exists($key, $data2);
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
        } elseif ($isKeyContainsOnlyFirstFile) {
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
        $isKeyContainsOnlyFirstFile = array_key_exists($key, $data1) && !array_key_exists($key, $data2);
        $isKeyContainsOnlySecondFile = !array_key_exists($key, $data1) && array_key_exists($key, $data2);

        if ($isKeyContainsTwoFiles) {
            $valueFirstFile = $data1[$key];
            $valueSecondFile = $data2[$key];

            if (is_array($value)) {
                $result .= formattedPlain($valueFirstFile, $valueSecondFile, $format, $currentPath);
            } elseif ($valueFirstFile !== $valueSecondFile) {
                $result .= "{$currentPath} was updated. From {$valueFirstFile} to {$valueSecondFile}\n";
            }

        } elseif ($isKeyContainsOnlyFirstFile) {
            $result .= "{$currentPath} was removed\n";
        } elseif ($isKeyContainsOnlySecondFile) {
            $value = is_array($value) ? '[complex value]': $value;
            $result .= "{$currentPath} was added with value: {$value}\n";
        }
    }

    return $result;
}

// function getPath($pathFile1, $pathFile2, $format, $path = "")
// {
//     $data1 = is_array($pathFile1) ? $pathFile1: getDataFromFile($pathFile1);
//     $data2 = is_array($pathFile2) ? $pathFile2: getDataFromFile($pathFile2);

//     $mergedFiles = array_merge($data1, $data2);
//     $result = "";

//     ksort($mergedFiles);

//     foreach ($mergedFiles as $key => $value) {
//         $isKeyContainsTwoFiles = array_key_exists($key, $data1) && array_key_exists($key, $data2);

//         if ($isKeyContainsTwoFiles) {
//             $valueFirstFile = $data1[$key];
//             $valueSecondFile = $data2[$key];
            
//             $path .= getPath($valueFirstFile, $valueSecondFile, $format, $key);
//         }
//     }
        
//     return $path;
// }