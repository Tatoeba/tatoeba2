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
 * @link     https://tatoeba.org
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
 * @link     https://tatoeba.org
 */
class SController extends AppController
{
    public $name = 'S';

    public $helpers = array(
        'Sentences',
        'Html',
        'Languages',
    );

    /**
     * Show sentence of specified id
     *
     * @param mixed $id Id of the sentence
     *
     * @return void
     */
    public function s($id)
    {
        $this->loadModel('Sentences');

        $sentence = $this->Sentences->getSentenceWith($id, ['translations' => true]);

        if (!$sentence) {
            throw new \Cake\Http\Exception\NotFoundException(format(
                __('There is no sentence with id {number}'),
                array('number' => $id)
            ));
        } else {
            $this->set('sentence', $sentence);
        }
    }
}
