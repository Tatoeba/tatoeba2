<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;

class SentenceDerivationCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $runner = new SentenceDerivation\Runner($io);
        $proceeded = $runner->main();
        $io->out("\n$proceeded sentences proceeded.");
    }
}
