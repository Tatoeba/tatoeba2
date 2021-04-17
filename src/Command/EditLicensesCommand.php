<?php
namespace App\Command;

use App\Command\EditCommand_;
use App\Lib\Licenses;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\Exception\RecordNotFoundException;

class EditLicensesCommand extends EditCommand_
{
    public function initialize() {
        parent::initialize();
        $this->licenses = Licenses::nameToKeys(Licenses::getSentenceLicenses());
    }

    protected function buildOptionParser(ConsoleOptionParser $parser) {
        $parser = parent::buildOptionParser($parser);
        $parser
            ->setDescription('Change sentence licenses.')
            ->addArgument('license', [
                'help' => 'New license.',
                'choices' => array_keys($this->licenses),
                'required' => true,
            ])
            ->addOption('dry-run', [
                'help' => 'Do everything except saving any changes.',
                'short' => 'n',
                'boolean' => true
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
            $this->total++;
        }
    }

    public function execute(Arguments $args, ConsoleIo $io) {
        $this->becomeUser($args, $io);

        $newLicense = $this->licenses[$args->getArgument('license')];
        $dryRun = $args->getOption('dry-run');
        $ids = $this->readIdsFromFile($args, $io);

        $this->editLicense($ids, compact('newLicense', 'dryRun', 'io'));

        if ($dryRun) {
            $io->out('<info>This is a dry run! No changes to the database were committed!</info>');
        }

        $this->printLog($io);
    }

    protected function logLinePrinter($value, $index, ConsoleIo $io) {
        $displayNames = array_flip($this->licenses);
        if (count($value) == 1) {
            $io->out($value[0]);
        } else {
            $io->out(
                format(
                    "id {id} - license changed from {old} to {new}",
                    [
                        'id' => $value[0],
                        'old' => $displayNames[$value[1]],
                        'new' => $displayNames[$value[2]],
                    ]
                )
            );
        }
    }
}
