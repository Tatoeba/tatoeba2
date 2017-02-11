<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  Allan SIMON <allan.simon@supinfo.com>
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
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Controller for favorite.
 *
 * @category TODEFINE
 * @package  Controllers
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

class FavoritesController extends AppController
{

    public $name = 'Favorites' ;
    public $paginate = array('limit' => 50);
    public $helpers = array('Navigation', 'Html', 'Menu', 'CommonModules', 'Pagination');
    public $uses = array('Favorite', 'User');

    /**
     * to know who can do what
     *
     * @return void
     */

    public function beforeFilter()
    {
        parent::beforeFilter();

        // setting actions that are available to everyone, even guests
        $this->Auth->allowedActions = array('of_user');
    }

    /**
     * view all favorites sentences of a given user
     *
     * @param string $username User to retrieve favorites
     */
    public function of_user($username)
    {
        $userId = $this->User->getIdFromUsername($username);
        $backLink = $this->referer(array('action'=>'index'), true);

        $this->set('backLink', $backLink);
        $this->set('username', $username);
        if (empty($userId)) {

            $this->set("userExists", false);
            return;
        }
        $this->paginate = $this->Favorite->getPaginatedFavoritesOfUser($userId);
        $favorites = $this->paginate('Favorite');
        $this->set('favorites', $favorites);
        $this->set("userExists", true);
    }

    /**
     * add a sentence to current user's ones
     *
     * @param int $sentenceId id of the sentence to favorite
     *
     * @return void
     */

    public function add_favorite($sentenceId, $withRemoveOrUndo = false)
    {
        $sentenceId = Sanitize::paranoid($sentenceId);

        $userId =$this->Auth->user('id');

        if ($userId != null) {
            $isSaved = $this->Favorite->addFavorite($sentenceId, $userId);
            $isLogged = true;
        }

        $this->_renderFavoriteButton($sentenceId, $isSaved, $isLogged, $withRemoveOrUndo);
    }

    /**
     * remove a favorite to current user's ones
     *
     * @param int $sentenceId id of the sentence to remove from favorites
     *
     * @return void
     */

    public function remove_favorite($sentenceId, $withRemoveOrUndo = false)
    {
        $sentenceId = Sanitize::paranoid($sentenceId);

        $userId =$this->Auth->user('id');

        if ($userId != null) {
            $isSaved = $this->Favorite->removeFavorite($sentenceId, $userId);
            $isLogged = true;
        }

        $this->_renderFavoriteButton($sentenceId, !$isSaved, $isLogged, $withRemoveOrUndo);
    }


    private function _renderFavoriteButton($sentenceId, $isFavorited, $isLogged, $withRemoveOrUndo = false)
    {
        $this->set('sentenceId', $sentenceId);
        $this->set('isFavorited', $isFavorited);
        $this->set('isLogged', $isLogged);
        $this->set('withRemoveOrUndo', $withRemoveOrUndo);

        $this->layout = null;
        $this->render('add_remove_favorite');

    }

}
