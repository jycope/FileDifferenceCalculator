<?php

namespace Differ\differenceFiles;

use Symfony\Component\Yaml\Yaml;

use function Differ\Parsers\getDataFromFile;

function convertedToJson(array $array): string
{
    $result = '';

    foreach ($array as $key => $value) {
        $operatorChanged = explode(' ', $key)[1];

        $stateFile = $operatorChanged === ('-' || '+') ? $operatorChanged : '';

        $result .= "{$stateFile} {$key}: {$value}\n";
    }

    return "{\n" . $result . "}";
}

function genDiff($pathFile1, $pathFile2): string
{
    $data1 =  is_file($pathFile1) ? getDataFromFile($pathFile1): $pathFile1;
    $data2 =  is_file($pathFile2) ? getDataFromFile($pathFile2): $pathFile2;

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
        $isKeyHaveTwoFilesArray = function () use ($isKeyContainsTwoFiles, $data1, $data2) {
            if ($isKeyContainsTwoFiles) {
                if (is_array($data[$key1]) && is_array($data[$key2])) {
                    return true;
                }
            }
        };

        $emptySecondFileValue = ' - ' . $key;
        $emptyFirstFileValue = ' + ' . $key;
        $keyEmpty = '   ' . $key;

        // if ($isKeyHaveTwoFilesArray) {
        //     // $result[$key] = genDiff($data1[$key], $data2[$key]);
        //     print_r($data1);
        // }

        if ($isKeyContainsTwoFiles) {
            $valueFirstFile = $data1[$key];
            $valueSecondFile = $data2[$key];

            if ($valueFirstFile === $valueSecondFile) {
                $result[$keyEmpty] = $value;
            } elseif ($valueFirstFile !== $valueSecondFile) {
                $result[$emptySecondFileValue] = $valueFirstFile;
                $result[$emptyFirstFileValue] = $value;
            }

            if (is_array($valueFirstFile) && is_array($valueSecondFile)) {
                $result[$key] = genDiff($data1[$key], $data2[$key]);
            }


        } elseif ($isKeyContainsOnlySecondFile) {
            $result[$emptyFirstFileValue] = $value;
        } elseif ($isKeyContainsOnlFirstFile) {
            $result[$emptySecondFileValue] = $value;
        }
    }

    return convertedToJson($result);
}
