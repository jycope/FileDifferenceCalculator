<?php

namespace Differ\differenceFiles;

use Symfony\Component\Yaml\Yaml;

use function Differ\Parsers\getDataFromFile;

function convertedToJson(array $array, $depth = 0): string
{
    $result = '';
    $leftIndentForValue   = str_repeat('  ', $depth);
    $leftIndentForBracket = str_repeat('  ', $depth - 1);

    foreach ($array as $key => $value) {
        // $operatorChanged = explode(' ', $key)[1];
        $value = is_array($value) ? convertedToJson($value, $depth + 2): $value;
        // print_r(json_decode($value));
        // print_r($value);
        // $value = is_array($value) ? json_encode($value): $value;

        // print_r("\n");
        // print_r($key);
        // print_r($depth);
        // $stateFile = $operatorChanged === ('-' || '+') ? $operatorChanged : '';

        $result .= "{$leftIndentForValue}{$key}: {$value}\n";
    }

    // $dataFormattedToJson = json_encode(
    //     $array,
    //     JSON_NUMERIC_CHECK|
    //     JSON_FORCE_OBJECT|
    //     JSON_PRESERVE_ZERO_FRACTION|
    //     JSON_UNESCAPED_SLASHES|
    //     JSON_UNESCAPED_UNICODE|
    //     JSON_PRETTY_PRINT
    // );

    // $formattedJson = str_replace(["\"", ','], '', $dataFormattedToJson);

    // return $formattedJson;

    return "{\n" . $result . "{$leftIndentForBracket}}";
}

function genDiff($pathFile1, $pathFile2, $depth = 0)
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

        // $leftIndent = str_repeat('    ', $depth);
        $emptySecondFileValue = '- ' . $key;
        $emptyFirstFileValue = '+ ' . $key;
        $keyEmpty = '  ' . $key;

        // if ($isKeyHaveTwoFilesArray) {
        //     // $result[$key] = genDiff($data1[$key], $data2[$key]);
        //     print_r($data1);
        // }

        if ($isKeyContainsTwoFiles) {
            $valueFirstFile = $data1[$key];
            $valueSecondFile = $data2[$key];
            
            if (is_array($value)) {
                $result[$keyEmpty] = genDiff($valueFirstFile, $valueSecondFile, $depth + 1);
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

    // print_r($result);

    return $result;
}
