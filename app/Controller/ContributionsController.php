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
        'Languages',
        'CommonModules',
        'Members'
    );
    public $components = array('Permissions');
    public $uses = array('Contribution', 'ContributionsStats');

    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->Auth->allow();
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
        $filter = Sanitize::paranoid($filter);

        $this->helpers[] = 'Pagination';

        $conditions = array();
        if ($filter != 'und') {
            $conditions = array('sentence_lang' => $filter);
        }
        $conditions = $this->Contribution->getQueryConditionsWithExcludedUsers($conditions);

        $this->paginate = array(
            'Contribution' => array(
                'conditions' => $conditions,
                'contain' => array(
                    'User' => array(
                        'fields' => array('username', 'image')
                    )
                ),
                'limit' => 200,
                'order' => 'id DESC',
            )
        );
        $contributions = $this->paginate();

        $this->set('contributions', $contributions);
        $this->set('langFilter', $filter);
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
        $this->loadModel('LastContribution');
        $currentContributors = $this->LastContribution->getCurrentContributors();
        $total = $this->LastContribution->getTotal($currentContributors);

        $this->set('currentContributors', $currentContributors);
        $this->set('total', $total);
        $this->set(
            'contributions', $this->Contribution->getLastContributions(200, $filter)
        );
    }


    /**
     * Display number of contributions for each day.
     *
     * @param string $year  Year in 4 digits (ex: 2010).
     * @param string $month Month in 2 digits (ex: 02 for February).
     *
     */
    public function activity_timeline($year = null, $month = null)
    {
        $this->helpers[] = 'Date';

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

            $stats = $this->ContributionsStats->getActivityTimelineStatistics(
                $year, $month
            );
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
            $backLink = $this->referer(array('action'=>'index'), true);
            $this->set('backLink', $backLink);
            return;
        }

        $this->paginate = array(
            'Contribution' => array(
                'conditions' => array(
                    'user_id' => $userId
                ),
                'contain' => array(
                    'User' => array(
                        'fields' => array('username', 'image')
                    )
                ),
                'limit' => 200,
                'order' => 'id DESC',
            )
        );
        $contributions = $this->paginate();
        $this->set('contributions', $contributions);
        $this->set('userExists', true);
    }
}
