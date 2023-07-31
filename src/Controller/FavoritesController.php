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
 * @link     https://tatoeba.org
 */
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

/**
 * Controller for favorite.
 *
 * @category TODEFINE
 * @package  Controllers
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

class FavoritesController extends AppController
{

    public $name = 'Favorites' ;
    public $paginate = array('limit' => 50);
    public $helpers = array('Html', 'Menu', 'CommonModules', 'Pagination');
    public $uses = array('Favorite', 'User');

    /**
     * view all favorites sentences of a given user
     *
     * @param string $username User to retrieve favorites
     */
    public function of_user($username)
    {
        $this->loadModel('Users');
        $userId = $this->Users->getIdFromUsername($username);

        $this->set('username', $username);
        if (empty($userId)) {

            $this->set("userExists", false);
            return;
        }

        $filter = $this->request->getQuery('filter');
        $favorites = $this->paginate($this->Favorites->getPaginatedFavoritesOfUser($userId, $filter));
        $this->set('filter', $filter);
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
        $userId =$this->Auth->user('id');

        if ($userId != null) {
            $isSaved = $this->Favorites->addFavorite($sentenceId, $userId);
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
        $userId =$this->Auth->user('id');

        if ($userId != null) {
            $isSaved = $this->Favorites->removeFavorite($sentenceId, $userId);
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

        $this->viewBuilder()->setLayout('ajax');
        $this->render('add_remove_favorite');

    }

}
