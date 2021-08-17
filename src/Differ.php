<?php

namespace Differ\Differ;

use function Differ\Formatters\FormattedDefault\formattedDefault;
use function Differ\Formatters\FormattedPlain\formattedPlain;
use function Differ\Parsers\getDataFromFile;

const FORMAT_FLAGS =
JSON_NUMERIC_CHECK |
    JSON_FORCE_OBJECT |
    JSON_PRESERVE_ZERO_FRACTION |
    JSON_UNESCAPED_SLASHES |
    JSON_UNESCAPED_UNICODE |
    JSON_PRETTY_PRINT;


function addOperatorToKeys(array $data): array
{
    $result = collect($data)->reduce(function ($result, $value, $key): object {
        $tmp = is_array($value) ? addOperatorToKeys($value) : $value;
        $result->put('* ' . $key, $tmp);

        return $result;
    }, collect([]));

    return $result->all();
}

function convertingArrayToJson(array $data, string $replacer = " ", int $count = 2): string
{
    $result = collect($data)->reduce(function ($result, $value, $key) use ($replacer, $count) {
        $indent = str_repeat($replacer, $count);

        if (!is_array($value)) {
            $property = "{$indent}{$key}: " . var_export($value, true) . "\n";
            $result->push($property);

            return $result;
        }

        $firstSymbols = explode(" ", $key)[0];
        $isSymboldChanged = $firstSymbols === "-" || $firstSymbols === "+" || $firstSymbols === "*";
        $indentForBracket = $isSymboldChanged ? str_repeat($replacer, $count + 2) : $indent;

        $valueElemForJson = convertingArrayToJson($value, $replacer, $count + 4) . $indentForBracket;
        $properties = "{$indent}{$key}: {\n" . $valueElemForJson . "}\n";

        $result->push($properties);

        return $result;
    }, collect([]));

    return collect($result)->join("");
}

function clearedData(string $data, string $format = 'stylish'): string
{
    $search =  ['* ', '\'', 'NULL'];
    $replace = ['  ', '', 'null'];

    switch ($format) {
        case 'plain':
            return str_replace('NULL', 'null', $data);
    }

    return str_replace($search, $replace, $data);
}

function formattedDataToJsonStr(array $data): string
{
    $json = convertingArrayToJson($data);

    return "{\n" . clearedData($json) . "}";
}

function genDiff(string $pathFile1, string $pathFile2, string $format = "stylish"): string
{
    $data1 = getDataFromFile($pathFile1);
    $data2 = getDataFromFile($pathFile2);

    switch ($format) {
        case 'plain':
            $dataFormatted = collect(formattedPlain($data1, $data2))->flatten()->join("\n");
            return clearedData($dataFormatted, $format);
        case 'json':
            $jsonNotCleared = (string) json_encode(
                formattedDefault(addOperatorToKeys($data1), addOperatorToKeys($data2)),
                FORMAT_FLAGS
            );

            return clearedData($jsonNotCleared);
    }

    return formattedDataToJsonStr(formattedDefault(addOperatorToKeys($data1), addOperatorToKeys($data2)));
}
