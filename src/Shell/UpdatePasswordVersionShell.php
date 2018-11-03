<?php

App::import('Security', 'Utility');

class UpdatePasswordVersionShell extends AppShell {

    public $uses = array('User');

    public function main() {
        echo "Updating password hashes";
        $proceeded = $this->batchOperation(
            'User',
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
            if (strlen($row[$model]['password']) == 32) {
                $newHash = '0 '.Security::hash($row[$model]['password'], 'blowfish');

                $result[] = array(
                    'id' => $row[$model]['id'],
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
