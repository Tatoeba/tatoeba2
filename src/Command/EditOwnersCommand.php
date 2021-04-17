<?php
namespace App\Command;

use App\Command\EditCommand_;
use App\Model\CurrentUser;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\Exception\RecordNotFoundException;

class EditOwnersCommand extends EditCommand_
{
    protected function buildOptionParser(ConsoleOptionParser $parser) {
        $parser = parent::buildOptionParser($parser);
        $parser
            ->setDescription('Change sentence owners.')
            ->addArgument('owner', [
                'help' => 'username of the new owner.',
            ])
            ->addOption('orphan', [
                'help' => 'Unset owner (this orphans the sentence).',
                'short' => 'o',
                'boolean' => true,
            ]);
        return $parser;
    }

    protected function editOwner($ids, $newOwner, $newOwnerId) {
        foreach ($ids as $id) {
            try {
                if (!$newOwnerId) {
                    $result = $this->Sentences->unsetOwner($id, CurrentUser::get('id'));
                } else {
                    $sentence = $this->Sentences->setOwner($id, $newOwnerId, CurrentUser::get('role'));
                    $result = $sentence && $sentence->user_id === $newOwnerId;
                }
            } catch (RecordNotFoundException $e) {
                $result = false;
            }

            if ($result) {
                if (!$newOwnerId) {
                    $this->log[] = "id $id - Sentence orphaned";
                } else {
                    $this->log[] = "id $id - Owner set to $newOwner";
                }
            } else {
                $this->log[] = "id $id - Record not found or could not save changes";
            }
            $this->total++;
        }
    }

    public function execute(Arguments $args, ConsoleIo $io) {
        $this->becomeUser($args, $io);

        $orphan = $args->getOption('orphan');
        $newOwner = $args->getArgument('owner');
        if (!($orphan xor $newOwner)) {
            $io->error('You should either use the orphan option or '.
                       'specify a new owner as third argument.');
            $this->abort();
        }

        if ($newOwner) {
            $newOwnerId = $this->Users->getIdFromUsername($newOwner);
            if (!$newOwnerId) {
                $io->error("User '$newOwner' does not exist!");
                $this->abort();
            }
        } else {
            $newOwnerId = null;
        }

        $ids = $this->readIdsFromFile($args, $io);

        $this->editOwner($ids, $newOwner, $newOwnerId);

        $this->printLog($io);
    }
}
