<?php

namespace App\Shell;

use Cake\Console\Shell;
use App\Security\Utility;

class UpdatePasswordVersionShell extends Shell {

    use BatchOperationTrait;

    public function main() {
        echo "Updating password hashes";
        $proceeded = $this->batchOperation(
            'Users',
            '_updateHash',
            array(
                'fields' => array('id', 'password')
            )
        );
        echo "\n$proceeded password hashes updated.\n";
    }

    private function updateHashesFor($data, $model) {
        $result = array();
        foreach ($data as $row) {
            if (strlen($row['password']) == 32) {
                $newHash = '0 '.Security::hash($row['password'], 'blowfish');

                $result[] = array(
                    'id' => $row['id'],
                    'password' => $newHash,
                );
            }
        }
        return $result;
    }

    protected function _updateHash($rows, $model) {
        $proceeded = 0;
        $data = $this->updateHashesFor($rows, $model);
        $options = array(
            'validate' => false,
            'callbacks' => false,
        );
        if ($data && $this->{$model}->saveAll($data, $options))
            $proceeded += count($data);
        $this->out('.', 0);
        return $proceeded;
    }
}
