<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Console\Command;
use Cake\Collection\Collection;
use Cake\Datasource\Exception\RecordNotFoundException;
use App\Model\CurrentUser;
use App\Lib\Licenses;

class EditLicensesCommand extends Command
{
    protected $log;
    const DEFAULT_USER = 'admin';

    public function initialize() {
        parent::initialize();
        $this->loadModel('Sentences');
        $this->loadModel('Users');
    }

    protected function buildOptionParser(ConsoleOptionParser $parser) {
        $parser
            ->setDescription('Change sentence licenses.')
            ->addArgument('file', [
                'help' => 'Name of the file that contains the sentence ids whose ' .
                          'license should be changed. ("stdin" will read from ' .
                          'standard input.)',
                'required' => true,
            ])
            ->addArgument('license', [
                'help' => 'New license.',
                'choices' => array_keys(Licenses::getSentenceLicenses())
            ])
            ->addOption('dry-run', [
                'help' => 'Do everything except saving any changes.',
                'short' => 'n',
                'boolean' => true
            ])
            ->addOption('user', [
                'help' => 'Do all the work as given user.',
                'short' => 'u',
                'default' => self::DEFAULT_USER
            ]);
        return $parser;
    }

    protected function editLicense($ids, $options) {
        extract($options);
        foreach ($ids as $id) {
            $this->Sentences->getConnection()->transactional(
                function ($conn) use ($id, $newLicense, $dryRun) {
                    try {
                        $sentence = $this->Sentences->get($id);
                    } catch (RecordNotFoundException $e) {
                        $this->log[] = ["$id ignored: Record not found"];
                        return false;
                    }
                    $oldLicense = $sentence->license;
                    if ($oldLicense != $newLicense) {
                        $newSentence = $this->Sentences->patchEntity(
                            $sentence,
                            ['license' => $newLicense]
                        );
                        if ($newSentence->hasErrors()) {
                            $this->log[] = ["$id ignored: Cannot change license"];
                            return false;
                        } else {
                            $this->log[] = [$id, $oldLicense, $newLicense];
                            if (!$dryRun) {
                                return $this->Sentences->save($newSentence);
                            }
                        }
                    } else {
                        $this->log[] = ["$id ignored: License is already $oldLicense"];
                        return true;
                    }
                }
            );
        }
    }

    public function execute(Arguments $args, ConsoleIo $io) {
        $input = $args->getArgument('file');
        if ($input === 'stdin') {
            $input = 'php://stdin';
        } elseif ($input && !file_exists($input)) {
            $io->error(format('{path} does not exist!', ['path' => $input]));
            $this->abort();
        }

        $newLicense = $args->getArgument('license') ?? '';
        $dryRun = $args->getOption('dry-run');
        $username = $args->getOption('user');
        $userId = $this->Users->getIdFromUsername($username);
        if (!$userId) {
            $io->error(format('User "{user}" does not exist!', ['user' => $username]));
            $this->abort();
        } else {
            CurrentUser::store($this->Users->get($userId));
        }

        $this->log = [];

        $ids = collection(file($input, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
               ->filter(function ($v) { return preg_match('/^\d+$/', $v); });
        $total = $ids->count();

        $this->editLicense($ids, compact('newLicense', 'dryRun', 'io'));

        if ($dryRun) {
            $io->out('<info>This is a dry run! No changes to the database were committed!</info>');
        }

        if (empty($this->log)) {
            $io->out('There was nothing to do.');
        } else {
            $io->out("$total rows proceeded:");
            array_walk($this->log, function ($value, $index) use ($io) {
                if (count($value) == 1) {
                    $io->out($value);
                } else {
                    $io->out("id $value[0] - license changed from $value[1] to $value[2]");
                }
            });
        }
    }
}
