<?php

namespace Differ\Differ;

use Symfony\Component\Yaml\Yaml;

use function Differ\Formatters\formattedDefault;
use function Differ\Formatters\formattedPlain;
use function Differ\Formatters\formattedJson;
use function Differ\Formatters\addOperatorToKeys;
use function Differ\Parsers\getDataFromFile;

function iterAst($data, $replacer = " ", $count = 2): string
{
    $result = "";
    $indent = str_repeat($replacer, $count);

    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $firstSymbols = explode(" ", $key)[0];
            $isSymboldChanged = $firstSymbols === "-" || $firstSymbols === "+" || $firstSymbols === "*";
            $indentForBracket = $isSymboldChanged  ? str_repeat($replacer, $count + 2): $indent;

            $result .= $indent . $key . ": {\n" . iterAst($value, $replacer, $count + 4) . $indentForBracket . "}\n";
        } elseif (!is_array($value)) {
            $value = var_export($value, true);

            $result .= $indent . $key . ": " . $value . "\n";
        }
    }

    return $result;
}

function iter(array $data)
{
    $json = iterAst($data);
    $search  =  ['* ', '\'', 'NULL'];
    $replace =  ['  ', '', 'null'];
    
    $clearedData = str_replace($search, $replace, $json);

    return "{\n" . $clearedData . "}";
}

function genDiff($pathFile1, $pathFile2, $format = "stylish")
{
    $data1 = getDataFromFile($pathFile1);
    $data2 = getDataFromFile($pathFile2);

    switch ($format) {
        case 'plain':
            return formattedPlain($data1, $data2, $format);
        case 'json':
            return iter(formattedJson(addOperatorToKeys($data1), addOperatorToKeys($data2), $format));
        default:
            return iter(formattedDefault(addOperatorToKeys($data1), addOperatorToKeys($data2), $format));
    }
}
