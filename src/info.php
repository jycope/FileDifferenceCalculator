<?php

namespace Differ\Info;

use Docopt;

use function Differ\DifferenceFiles\genDiff;
use function Differ\DifferenceFiles\convertedToJson;

function getInfo()
{
    $doc = <<<DOC
    Generate diff
    
    Usage:
        gendiff (-h|--help)
        gendiff (-v|--version)
        gendiff [--format <fmt>] <firstFile> <secondFile>
        
    Options:
        -h --help                     Show this screen
        -v --version                  Show version
        --format <fmt>                Report format [default: stylish]
    DOC;

    $args = Docopt::handle($doc, array('version' => 'Naval Fate 2.0'));

    $filePath1 = $args['<firstFile>'];
    $filePath2 = $args['<secondFile>'];
    $format    = $args['--format'];
    
    return genDiff($filePath1, $filePath2, $format);
}
