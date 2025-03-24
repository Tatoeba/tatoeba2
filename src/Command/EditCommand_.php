<?php
namespace App\Command;

use App\Lib\LanguagesLib;
use App\Model\CurrentUser;
use Cake\Collection\Collection;
use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Helps creating edit commands that starts with arguments <username> and <file>
 * to edit sentences as <username> while reading sentence ids from <file>.
 *
 * An underscore is appended to the class name to avoid this file showing up
 * as an actual command when running `cake'.
 */
class EditCommand_ extends Command
{
    protected $log = [];
    protected $total = 0;

    public function initialize() {
        parent::initialize();
        $this->loadModel('Sentences');
        $this->loadModel('Users');
    }

    protected function buildOptionParser(ConsoleOptionParser $parser) {
        $parser
            ->addArgument('username', [
                'help' => 'Do all the work as given user.',
                'required' => true,
            ])
            ->addArgument('file', [
                'help' => 'Name of the file that contains the ids of sentences ' .
                          'to perform the operation on. ("stdin" will read from ' .
                          'standard input.)',
                'required' => true,
            ]);
        return $parser;
    }

    protected function readIdsFromFile(Arguments $args, ConsoleIo $io) {
        $input = $args->getArgument('file');
        if ($input === 'stdin') {
            $input = 'php://stdin';
        } elseif ($input && !file_exists($input)) {
            $io->error("File '$input' does not exist!");
            $this->abort();
        }

        return Collection(file($input, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
               ->filter(function ($v) { return preg_match('/^\d+$/', $v); })
               ->toList();
    }

    protected function becomeUser(Arguments $args, ConsoleIo $io) {
        $username = $args->getArgument('username');
        $userId = $this->Users->getIdFromUsername($username);
        if (!$userId) {
            $io->error("User '$username' does not exist!");
            $this->abort();
        } else {
            CurrentUser::store($this->Users->get($userId));
        }
    }

    protected function printLog(ConsoleIo $io) {
        if (empty($this->log)) {
            $io->out('There was nothing to do.');
        } else {
            $io->out("{$this->total} row(s) proceeded:");
            array_walk($this->log, [$this, 'logLinePrinter'], $io);
        }
    }

    protected function logLinePrinter($value, $index, ConsoleIo $io) {
        $io->out($value);
    }
}
