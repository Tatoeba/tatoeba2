<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 SIMON   Allan   <allan.simon@supinfo.com>
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
 */
namespace App\Model\Table;

use Cake\ORM\Table;
use \Cake\Database\Expression\QueryExpression;

class LanguagesTable extends Table
{
    /**
     * Return stats for number of sentences per language.
     *
     * @param int $limit Specifying a limit will only take the top languages
     *                   with the most sentences.
     *
     * @return array
     */
    public function getSentencesStatistics($limit = null)
    {
        $results = $this->find()
            ->select([
                'code',
                'sentences',
                'audio',
            ])
            ->order(['sentences' => 'DESC'])
            ->limit($limit)
            ->toList();

        return $results ;
    }

    /**
     * Return number of sentences per language grouped by milestones.
     *
     * @param array of int $milestones Milestones in decreasing order
     *
     * @return array of array Number of sentences grouped by milestones
     */
    public function getMilestonedStatistics($milestones)
    {
        $mapper = function ($stat, $key, $mapReduce) use ($milestones) {
            for ($i = 0; $i < count($milestones); $i++) {
                if ($stat->sentences >= $milestones[$i]) {
                    break;
                }
            }
            $mapReduce->emitIntermediate($stat, $milestones[$i]);
        };
        $reducer = function ($languages, $milestone, $mapReduce) {
            $mapReduce->emit($languages, $milestone);
        };
        $results = $this->find()
            ->select([
                'code',
                'sentences',
            ])
            ->order(['sentences' => 'DESC'])
            ->mapReduce($mapper, $reducer)
            ->toArray();

        return $results;
    }

    /**
     * Return stats for number of audio per language.
     *
     * @return array
     */
    public function getAudioStats()
    {
        $results = $this->find()
            ->where(['audio >' => 0])
            ->select([
                'lang' => 'code', 
                'total' => 'audio']
            )
            ->order(['audio' => 'DESC'])
            ->toList();

        return $results;
    }


    /**
     * Return stats for number of members who speak the language in each group of
     * users.
     *
     * @return array
     */
    public function getUsersLanguagesStatistics()
    {
        $results = $this->find()
            ->where(['code IS NOT' => null])
            ->select([
                'code',
                'level_5',
                'level_4',
                'level_3',
                'level_2',
                'level_1',
                'level_0',
                'level_unknown',
                'total' => '(level_5 + level_4 + level_3 + level_2 + level_1 + level_0 + level_unknown)'
            ])
            ->order(['total' => 'DESC'])
            ->toList();

        return $results;
    }


    /**
     *
     */
    public function getNativeSpeakersStatistics()
    {
        $results = $this->find()
            ->where(['code IS NOT' => null])
            ->select([
                'code',
                'group_1',
                'group_2',
                'group_3',
                'group_4',
                'total' => '(group_1 + group_2 + group_3 + group_4)'
            ])
            ->order(['total' => 'DESC'])
            ->toList();
            
        return $results;
    }


    /**
     * Increment stats for specified language.
     *
     * @param $langCode Code of the language which should be incremented.
     *
     * @return void
     */
    public function incrementCountForLanguage($langCode)
    {
        return $this->updateAll(
            ['sentences' => new QueryExpression('sentences + 1')],
            ['code' => $langCode]
        );
    }


    /**
     * Decrement stats for specified language.
     *
     * @param $langCode Code of the language which should be incremented.
     *
     * @return void
     */
    public function decrementCountForLanguage($langCode)
    {
        return $this->updateAll(
            ['sentences' => new QueryExpression('sentences - 1')],
            ['code' => $langCode]
        );
    }


    public function incrementCountForGroup($langCode, $groupId)
    {
        $groupColumn = $this->_groupColumnName($groupId);

        $this->updateAll(
            array($groupColumn => "$groupColumn + 1"),
            array('code' => $langCode)
        );
    }


    public function decrementCountForGroup($langCode, $groupId)
    {
        $groupColumn = $this->_groupColumnName($groupId);

        $this->updateAll(
            array($groupColumn => "$groupColumn - 1"),
            array('code' => $langCode)
        );
    }


    private function _groupColumnName($groupId)
    {
        return 'Language.group_'.$groupId;
    }


    public function incrementCountForLevel($langCode, $level)
    {
        $levelColumn = $this->_levelColumnName($level);

        $this->updateAll(
            array($levelColumn => "$levelColumn + 1"),
            array('code' => $langCode)
        );
    }


    public function decrementCountForLevel($langCode, $level)
    {
        $levelColumn = $this->_levelColumnName($level);

        $this->updateAll(
            array($levelColumn => "$levelColumn - 1"),
            array('code' => $langCode)
        );
    }


    private function _levelColumnName($level)
    {
        if (isset($level)) {
            return 'Language.level_'.$level;
        } else {
            return 'Language.level_unknown';
        }

    }

    public function getTotalSentencesNumber()
    {
        $query = $this->find();
        $query->select(['count' => $query->func()->sum('sentences')]);
        return $query->first()->count;
    }
}
