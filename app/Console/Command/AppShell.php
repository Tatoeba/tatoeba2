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
        if (!isset($options['order'])) {
            $options['order'] = $this->{$model}->alias.'.'.$this->{$model}->primaryKey;
        }
        if (is_string($options['order'])) {
            $options['order'] = array($options['order']);
        }
        $order = $options['order'][0];
        if (isset($options['fields'])) {
            $options['fields'][] = $order;
        }

        $oparts = explode('.', $order);
        $proceeded = 0;
        $options = array_merge(
            array(
                'contain' => array(),
                'limit' => $this->batchOperationSize,
            ),
            $options
        );

        if (!isset($options['conditions'])) {
            $options['conditions'] = array();
        }
        $options['conditions'][] = array();
        end($options['conditions']);
        $conditionKey = key($options['conditions']);
        reset($options['conditions']);

        $data = array();
        do {
            $data = $this->{$model}->find('all', $options);
            $args = func_get_args();
            array_splice($args, 0, 3, array($data, $model));
            $proceeded += call_user_func_array(array($this, $operation), $args);
            $lastRow = end($data);
            if ($lastRow) {
                $lastId = $lastRow[ $oparts[0] ][ $oparts[1] ];
                $options['conditions'][$conditionKey] = array("$pKey >" => $lastId);
            }
            echo ".";
        } while ($data);
        return $proceeded;
    }
}
