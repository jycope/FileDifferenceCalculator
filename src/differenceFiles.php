<?php

namespace Differ\DifferenceFiles;

use Symfony\Component\Yaml\Yaml;

use function Differ\Formatters\formattedDefault;
use function Differ\Formatters\formattedPlain;

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

function genDiff($pathFile1, $pathFile2, $format, $depth = 0)
{   
    if ($format === 'plain') {
        return formattedPlain($pathFile1, $pathFile2, $format);
    }

    return formattedDefault($pathFile1, $pathFile2, $format, $depth);
}
