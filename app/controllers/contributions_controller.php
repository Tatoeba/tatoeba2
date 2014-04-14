<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

App::import('Core', 'Sanitize');

/**
 * Controller for contributions.
 *
 * @category Contributions
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class ContributionsController extends AppController
{
    public $persistentModel = true;
    public $name = 'Contributions';
    public $helpers = array(
        'Html',
        'Form',
        'Sentences',
        'Logs',
        'Navigation',
        'Date',
        'languages',
        'CommonModules'
    );
    public $components = array('Permissions');

    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->Auth->allowedActions = array('*');
    }

    /**
     * Display all contributions in specified language (or all languages).
     *
     * @param string $filter Language of the contributions.
     *
     * @return void
     */
    public function index($filter = 'und')
    {

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if(strpos($user_agent, "Baidu") !== false) {
            $this->redirect($redirectPage, 404);
        }

        $this->helpers[] = 'Pagination';

        $conditions = array();
        if ($filter != 'und') {
            $conditions = array('sentence_lang' => $filter);
        }

        $this->paginate = array(
            'Contribution' => array(
                'conditions' => $conditions,
                'limit' => 200,
                'order' => 'id DESC',
                'contain' => array()
            )
        );
        $contributions = $this->paginate();

        $usersIds = array();
        foreach($contributions as $contribution) {
            $userId = $contribution['Contribution']['user_id'];
            if (!in_array($userId, $usersIds)) {
                $usersIds[] = $userId;
            }
        }
        $users = $this->Contribution->User->getUsernamesFromIds($usersIds);

        $this->set('contributions', $contributions);
        $this->set('users', $users);
    }


    /**
     * Display 200 last contributions in specified language (or all languages).
     *
     * @param string $filter Language of the contributions.
     *
     * @return void
     */
    public function latest($filter = 'und')
    {
        $this->set(
            'contributions', $this->Contribution->getLastContributions(200, $filter)
        );
    }


    /**
     * Display number of contributions for each member.
     *
     * @return void
     */
    public function statistics()
    {
        $this->helpers[] = 'Cache';
        $this->cacheAction = '1 day';
        $this->set('stats', $this->Contribution->getUsersStatistics());
    }

    /**
     * Display number of contributions for each day.
     *
     * @param string $month Example: '2010-02' (for February 2010).
     *
     * @return void
     */
    public function activity_timeline($year = null, $month = null)
    {
        $redirect = false;
        if ($year == null || $year > date('Y') || $year < 2007) {
            $year = date('Y');
            $redirect = true;
        }
        if ($month == null || $month < 1 || $month > 12) {
            $month = date('m');
            $redirect = true;
        }

        if ($redirect) {

            $this->redirect(
                array(
                    'action' => 'activity_timeline',
                    $year,
                    $month
                )
            );

        } else {

            $stats = $this->Contribution->getActivityTimelineStatistics($year, $month);
            $this->set('year', $year);
            $this->set('month', $month);
            $this->set('stats', $stats);

        }
    }


    /**
     * Display logs of contributions of specified user.
     *
     * @param string $username Username.
     *
     * @return void
     */
    public function of_user($username)
    {
        $this->helpers[] = 'Pagination';

        $userId = $this->Contribution->User->getIdFromUsername($username);
        $this->set('username', $username);

        if (empty($userId)) {
            $this->set('userExists', false);
            return;
        }

        $this->paginate = array(
            'Contribution' => array(
                'conditions' => array(
                    'user_id' => $userId
                ),
                'limit' => 200,
                'order' => 'id DESC',
                'contain' => array()
            )
        );
        $contributions = $this->paginate();
        $this->set('contributions', $contributions);
        $this->set('userExists', true);
    }
}
?>
