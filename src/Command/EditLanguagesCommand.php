<?php
namespace App\Command;

use App\Lib\LanguagesLib;
use App\Model\CurrentUser;
use Cake\Collection\Collection;
use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class EditLanguagesCommand extends Command
{
    protected $log;

    public function initialize() {
        parent::initialize();
        $this->loadModel('Sentences');
        $this->loadModel('Users');
    }

    protected function buildOptionParser(ConsoleOptionParser $parser) {
        $parser
            ->setDescription('Change sentence languages.')
            ->addArgument('username', [
                'help' => 'Do all the work as given user who needs to be either ' .
                          'corpus maintainer or admin.',
                'required' => true,
            ])
            ->addArgument('language', [
                'help' => 'ISO code of the new language.',
                'required' => true,
            ])
            ->addArgument('file', [
                'help' => 'Name of the file that contains the sentence ids whose ' .
                          'language should be changed. ("stdin" will read from ' .
                          'standard input.)',
                'required' => true,
            ]);
        return $parser;
    }

    protected function editLanguage($ids, $lang) {
        foreach ($ids as $id) {
            $result = $this->Sentences->editSentence(compact('id', 'lang'));
            if ($result) {
                $this->log[] = "id $id - Language set to {$result->lang}";
            } else {
                $this->log[] = "id $id - Record not found or could not save changes";
            }
        }
    }

    public function execute(Arguments $args, ConsoleIo $io) {
        $username = $args->getArgument('username');
        $userId = $this->Users->getIdFromUsername($username);
        if (!$userId) {
            $io->error("User '$username' does not exist!");
            $this->abort();
        } else {
            CurrentUser::store($this->Users->get($userId));
            if (!CurrentUser::isModerator()) {
                $io->error('User must be corpus maintainer or admin!');
                $this->abort();
            }
        }

        $input = $args->getArgument('file');
        if ($input === 'stdin') {
            $input = 'php://stdin';
        } elseif ($input && !file_exists($input)) {
            $io->error("File '$input' does not exist!");
            $this->abort();
        }

        $newLanguage = $args->getArgument('language');
        if (!LanguagesLib::languageExists($newLanguage)) {
            $io->error("Language '$newLanguage' does not exist!");
            $this->abort();
        }

        $this->log = [];
        $ids = collection(file($input, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
               ->filter(function ($v) { return preg_match('/^\d+$/', $v); });
        $total = $ids->count();

        $this->editLanguage($ids, $newLanguage);

        if (empty($this->log)) {
            $io->out('There was nothing to do.');
        } else {
            $io->out("$total rows proceeded:");
            foreach ($this->log as $logEntry) {
                $io->out($logEntry);
            }
        }
    }
}
