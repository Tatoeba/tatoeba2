<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009-2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

/**
 * Controller for sentences.
 *
 * @category Sentences
 * @package  Controllers
 * @author   CK
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class SController extends AppController
{
    public $name = 'S';

    public $helpers = array(
        'Sentences',
        'Html',
        'Languages',
    );

    public $uses = array('Sentence');

    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        // setting actions that are available to everyone, even guests
        $this->Auth->allowedActions = array('s');

        return parent::beforeFilter($event);
    }


    /**
     * Show sentence of specified id (or a random one if no id specified).
     *
     * @param mixed $id Id of the sentence or language of the random sentence.
     *
     * @return void
     */
    public function s($id = null)
    {
        $id = Sanitize::paranoid($id);

        if (is_numeric($id)) {

            // And now we retrieve the sentence
            $sentence = $this->Sentence->getSentenceWithId($id);

            // If no sentence, we don't need to go further.
            // We just set some variable so we don't get warnings.
            if (!$sentence) {
                throw new NotFoundException(format(
                    __('There is no sentence with id {number}'),
                    array('number' => $id)
                ));
            }

            $this->set('sentence', $sentence);
        } else {
            $max = $this->Sentence->getMaxId();
            $randId = rand(1, $max);

            return $this->redirect(
                array(
                    "action"=>"s",
                    $randId
                )
            );
        }
    }
}
