<?php

namespace Differ\Cli;

use function Differ\Differ\genDiff;

function differCli(string $doc): void
{
    $args = \Docopt::handle($doc, array('version' => 'Differ 1.0'));

    $fileOnePath = $args['<firstFile>'];
    $fileTwoPath = $args['<secondFile>'];

    $format = $args['--format'];

    $diff = genDiff($fileOnePath, $fileTwoPath, $format);

    print_r($diff);
}
