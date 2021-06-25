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

function genDiff(string $pathFile1, string $pathFile2): string
{
    $file1 = getDataFromFile($pathFile1);
    $file2 = getDataFromFile($pathFile2);

    if (empty($file1) && empty($file2)) {
        return "{\n}";
    }

    $result = [];
    $mergedFiles = array_merge($file1, $file2);

    ksort($mergedFiles);

    foreach ($mergedFiles as $key => $value) {
        $value = is_bool($value) ? ($value === true ? 'true' : 'false') : $value;

        $isKeyContainsTwoFiles = array_key_exists($key, $file1) && array_key_exists($key, $file2);
        $isKeyContainsOnlFirstFile = array_key_exists($key, $file1) && !array_key_exists($key, $file2);
        $isKeyContainsOnlySecondFile = !array_key_exists($key, $file1) && array_key_exists($key, $file2);

        $emptySecondFileValue = ' - ' . $key;
        $emptyFirstFileValue = ' + ' . $key;
        $keyEmpty = '   ' . $key;

        if ($isKeyContainsTwoFiles) {
            $valueFirstFile = $file1[$key];
            $valueSecondFile = $file2[$key];

            if ($valueFirstFile === $valueSecondFile) {
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

    return convertedToJson($result);
}
