<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function getDataFromFile(string $filepath)
{
    $fileExtension = substr($filepath, -4);

    if ($fileExtension === 'json') {
        return json_decode(file_get_contents($filepath), true);
    }

    return Yaml::parseFile($filepath);
}
