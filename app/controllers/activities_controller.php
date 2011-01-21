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

/**
 * Controller for activities (i.e. things that contributors can do in Tatoeba).
 *
 * @category Activities
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class ActivitiesController extends AppController
{   
    public $helpers = array('AttentionPlease');
    
    /**
     * Before filter.
     * 
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        
        // setting actions that are available to everyone, even guests
        $this->Auth->allowedActions = array("*");
    }
    
    
    /**
     * Add new sentences.
     *
     * @return void
     */
    public function add_sentences()
    {
    }
    
    
    /**
     * Adopt sentences.
     *
     * @return void
     */
    public function adopt_sentences($lang = null)
    {
        $this->helpers[] = 'CommonModules';
        $this->helpers[] = 'Pagination';
        
        $conditions = array('user_id' => null);
        if(!empty($lang)) {
            $conditions['lang'] = $lang;
        }
        
        $this->loadModel('Sentence');
        $this->paginate = array(
            'limit' => 10,
            'conditions' => $conditions,
            'contain' => array()
        );
        $results = $this->paginate('Sentence');
        $this->set('results', $results);
        $this->set('lang', $lang);
    }
    
    
    /**
     * Imptove sentences.
     */
    public function improve_sentences()
    {
    }
    
    
    /**
     * Link sentences.
     */
    public function link_sentences()
    {
    }
    
    
    /**
     * Translate sentences.
     */
    public function translate_sentences()
    {
    }
}
?>