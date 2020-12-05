<?php declare(strict_types=1);

use Krasilnikovm\Compiler\FileReader;
use Krasilnikovm\Compiler\Scanner;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

require __DIR__ . '/../vendor/autoload.php';

$output = new ConsoleOutput();
$table = new Table($output);
$table->setStyle('box');
$table->setHeaders(['Lexem', 'Type', 'Message']);

$fileReader = new FileReader(__DIR__ . '/../doc/code.txt');
$scanner = new Scanner($fileReader);

foreach ($scanner->scan() as $lexem) {
    $table->addRow($lexem);
}

$table->render();
