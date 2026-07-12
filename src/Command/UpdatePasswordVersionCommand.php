<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Utility\Security;

class UpdatePasswordVersionCommand extends Command
{
    use BatchOperationTrait;

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $io->out("Updating password hashes");
        $proceeded = $this->batchOperation(
            'Users',
            '_updateHash',
            array(
                'fields' => array('id', 'password')
            ),
            $io
        );
        $io->out('');
        $io->out("$proceeded password hashes updated.");
    }

    private function updateHashesFor($data, $model) {
        $result = array();
        foreach ($data as $row) {
            if (strlen($row['password']) == 32) {
                $newHash = '0 '.password_hash($row['password'], PASSWORD_BCRYPT);
                $entity = $model->newEntity(
                    ['id' => $row['id']],
                    ['validate' => false]
                );
                $entity->set('password', $newHash, ['setter' => false]);
                $result[] = $entity;
            }
        }
        return $result;
    }

    protected function _updateHash($rows, $modelName, $io) {
        $proceeded = 0;
        $model = $this->fetchTable($modelName);
        $entities = $this->updateHashesFor($rows, $model);
        $options = array(
            'callbacks' => false,
            'atomic' => false,
        );
        if ($entities) {
            if ($model->saveMany($entities, $options)) {
                $proceeded += count($entities);
            }
            $io->out('.', 0);
        }
        return $proceeded;
    }
}
