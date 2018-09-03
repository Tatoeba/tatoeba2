<?php
/**
 * AppShell file
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Shell', 'Console');

/**
 * Application Shell
 *
 * Add your application-wide methods in the class below, your shells
 * will inherit them.
 *
 * @package       app.Console.Command
 */
class AppShell extends Shell {
    public $batchOperationSize = 1000;

    protected function batchOperation($model, $operation, $options) {
        $pKey = $this->{$model}->alias.'.'.$this->{$model}->primaryKey;
        $pKeyShort = $this->{$model}->primaryKey;
        if (isset($options['fields'])) {
            assert(in_array($pKey, $options['fields']) || in_array($pKeyShort, $options['fields']));
        }

        $proceeded = 0;
        $options = array_merge(
            array(
                'contain' => array(),
                'limit' => $this->batchOperationSize,
                'order' => "$pKey ASC",
            ),
            $options
        );

        if (!isset($options['conditions'])) {
            $options['conditions'] = array();
        }
        $options['conditions'] = array_merge(
            array("$pKey >" => 0),
            $options['conditions']
        );

        $data = array();
        do {
            $data = $this->{$model}->find('all', $options);
            $args = func_get_args();
            array_splice($args, 0, 3, array($data, $model));
            $proceeded += call_user_func_array(array($this, $operation), $args);
            $lastRow = end($data);
            if ($lastRow) {
                $lastId = isset($lastRow[$model][$pKey]) ? $lastRow[$model][$pKey] : $lastRow[$model][$pKeyShort];
                $options['conditions']["$pKey >"] = $lastId;
            }
            echo ".";
        } while ($data);
        return $proceeded;
    }
}
