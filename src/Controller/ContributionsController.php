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
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\I18n\Time;

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
     * Display all contributions in specified language (or all languages).
     *
     * @param string $filter Language of the contributions.
     *
     * @return void
     */
    public function index($filter = 'und')
    {
        $this->helpers[] = 'Pagination';

        $conditions = [];
        if ($filter != 'und') {
            $conditions = array('sentence_lang' => $filter);
        }

        $this->paginate = [
            'conditions' => $conditions,
            'contain' => [
                'Users' => [
                    'fields' => ['username', 'image']
                ]
            ],
            'limit' => 200,
            'order' => ['id' => 'DESC'],
        ];
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
        $this->loadModel('LastContributions');
        $currentContributors = $this->LastContributions->getCurrentContributors();
        $total = $this->LastContributions->getTotal($currentContributors);

        $this->set('currentContributors', $currentContributors);
        $this->set('total', $total);
        $this->set(
            'contributions', $this->Contributions->getLastContributions(200, $filter)
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

        $now = Time::now();
        $redirect = false;
        if ($year == null || $year > $now->format('Y') || $year < 2007) {
            $year = $now->format('Y');
            $redirect = true;
        }
        if ($month == null || $month < 1 || $month > 12) {
            $month = $now->format('m');
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

            $this->loadModel('ContributionsStats');
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

        $this->loadModel('Users');
        $userId = $this->Users->getIdFromUsername($username);
        $this->set('username', $username);

        if (empty($userId)) {
            $this->set('userExists', false);
            return;
        }

        $this->paginate = [
            'conditions' => [
                'user_id' => $userId,
                'type !=' => 'license'
            ],
            'contain' => [
                'Users' => [
                    'fields' => ['username', 'image']
                ]
            ],
            'limit' => 200,
            'order' => ['id' => 'DESC'],
        ];
        $contributions = $this->paginate();
        $this->set('contributions', $contributions);
        $this->set('userExists', true);
    }
}
