<?php

namespace Differ\Differ;

use Symfony\Component\Yaml\Yaml;

use function Differ\Formatters\formattedDefault;
use function Differ\Formatters\formattedPlain;
use function Differ\Formatters\formattedJson;

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
    $search = ['* ', '\'', 'NULL'];
    $replace =  ['  ', '', 'null'];
    
    $clearedData = str_replace($search, $replace, $json);

    return "{\n" . $json . "}";
}

function genDiff($pathFile1, $pathFile2, $format = "stylish")
{
    switch ($format) {
        case 'plain':
            return formattedPlain($pathFile1, $pathFile2, $format);
        case 'json':
            return iter(formattedJson($pathFile1, $pathFile2, $format));
        default:
            return iter(formattedDefault($pathFile1, $pathFile2, $format));
    }
}
