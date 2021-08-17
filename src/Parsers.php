<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function getDataFromFile(string $filepath): array
{
    $fileExtension = explode(".", $filepath)[1];

    switch ($fileExtension) {
        case 'json':
            return json_decode((string) file_get_contents($filepath), true);
        case 'yaml':
        case 'yml':
            return Yaml::parseFile($filepath) ?? [];
        default:
            throw new \Exception("Unknown format: \"{$fileExtension}\"");
    }
}
