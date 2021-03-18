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
                'required' => true,
            ]);
        return $parser;
    }

    protected function editOwner($ids, $newOwner, $newOwnerId) {
        foreach ($ids as $id) {
            try {
                $sentence = $this->Sentences->setOwner($id, $newOwnerId, CurrentUser::get('role'));
                $result = $sentence && $sentence->user_id === $newOwnerId;
            } catch (RecordNotFoundException $e) {
                $result = false;
            }

            if ($result) {
                $this->log[] = "id $id - Owner set to $newOwner";
            } else {
                $this->log[] = "id $id - Record not found or could not save changes";
            }
            $this->total++;
        }
    }

    public function execute(Arguments $args, ConsoleIo $io) {
        $this->becomeUser($args, $io);

        $newOwner = $args->getArgument('owner');
        $newOwnerId = $this->Users->getIdFromUsername($newOwner);
        if (!$newOwnerId) {
            $io->error("User '$newOwner' does not exist!");
            $this->abort();
        }

        $ids = $this->readIdsFromFile($args, $io);

        $this->editOwner($ids, $newOwner, $newOwnerId);

        $this->printLog($io);
    }
}
