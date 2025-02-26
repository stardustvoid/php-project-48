<?php

namespace Differ\Cli;

use function Differ\Differ\genDiff;

function differCli(string $doc): string
{
    $args = \Docopt::handle($doc, array('version' => 'Differ 1.0'));

    $fileOnePath = $args['<firstFile>'];
    $fileTwoPath = $args['<secondFile>'];

    $format = $args['--format'];

    return genDiff($fileOnePath, $fileTwoPath, $format);
}
