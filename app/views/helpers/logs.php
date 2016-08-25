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
 * Helper for contribution logs.
 *
 * @category Contributions
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class LogsHelper extends AppHelper
{

    public $helpers = array('Date', 'Html', 'Languages');

    private function _findLatestContributionDates($contributions) {
        $latestContribs = array();
        foreach ($contributions as $contribution) {
            $sentenceId = $contribution['Contribution']['sentence_id'];
            $contribTime = strtotime($contribution['Contribution']['datetime']);
            if (!isset($latestContribs[$sentenceId]) ||
                (isset($latestContribs[$sentenceId]) && $latestContribs[$sentenceId] < $contribTime)) {
                $latestContribs[$sentenceId] = $contribTime;
            }
        }
        return $latestContribs;
    }

    /**
     * Mark contributions that are not the latest contributions of a given set
     * as obsolete, and the others as non obsolete.
     *
     * @param array $contributions Set of contributions which is
     *                             to be displayed together.
     * @return void
     */
    public function obsoletize(&$contributions) {
        $latestContribs = $this->_findLatestContributionDates($contributions);
        foreach ($contributions as &$contribution) {
            $sentenceId = $contribution['Contribution']['sentence_id'];
            $contribTime = strtotime($contribution['Contribution']['datetime']);
            $contribution['Contribution']['obsolete'] = ($contribTime < $latestContribs[$sentenceId]);
        }
    }

    /**
     * Display a contribution.
     *
     * @param array $contribution Contribution to display.
     * @param array $user         User who contributed.
     *
     * @return void
     */
    public function entry($contribution, $user = null)
    {
        $sentenceId = $contribution['sentence_id'];
        $sentenceLang = $contribution['sentence_lang'];

        if (isset($user)) {
            $username = $user['username'];
        }

        $action = $contribution['action'];
        $datetime = $contribution['datetime'];

        $isObsolete = false;
        if (isset($contribution['obsolete']) && $contribution['obsolete']) {
            $isObsolete = true;
        }

        $type = 'sentence';
        if (isset($contribution['type'])) {
            $type = $contribution['type'];
        }

        if ($type == 'sentence') {
            $sentenceText = $contribution['text'];
            
            $this->_sentenceEntry(
                $sentenceId, 
                $sentenceText, 
                $sentenceLang, 
                $contribution['script'],
                $username, 
                $datetime, 
                $action, 
                $isObsolete
            );
        } else if ($type == 'link') {
            $translationId = $contribution['translation_id'];

            $this->_linkEntry(
                $sentenceId, 
                $translationId, 
                $username, 
                $datetime, 
                $action
            );
        }
    }

    /**
     * Display a contribution in annexe module.
     *
     * @param array $contribution Contribution to display.
     * @param array $user         User who contributed.
     *
     * @return void
     */
    public function annexeEntry($contribution, $user = null)
    {
        $sentenceId = null;
        if (isset($contribution['sentence_id'])) {
            $sentenceId = $contribution['sentence_id'];
        }
        $sentenceLang = $contribution['sentence_lang'];

        $username = null;
        if (isset($user)) {
            $username = $user['username'];
        }

        $action = $contribution['action'];
        $datetime = $contribution['datetime'];

        $isObsolete = false;
        if (isset($contribution['obsolete']) && $contribution['obsolete']) {
            $isObsolete = true;
        }

        $type = 'sentence';
        if (!empty($contribution['translation_id'])) {
            $type = 'link';
        }

        if ($type == 'sentence') {
            $sentenceText = $contribution['text'];
            
            $this->_annexeSentenceEntry(
                $sentenceId,
                $sentenceText, 
                $sentenceLang, 
                $contribution['script'],
                $username, 
                $datetime, 
                $action, 
                $isObsolete
            );
        } else if ($type == 'link') {
            $translationId = $contribution['translation_id'];

            $this->_annexeLinkEntry(
                $sentenceId,
                $translationId, 
                $username, 
                $datetime, 
                $action
            );
        }
    }


    private function _displayInfosInAnnexe($sentenceId, $username, $datetime)
    {
        echo '<ul class="info">';
            if (!empty($sentenceId)) {
                echo '<li class="sentenceId">';
                echo $this->Html->link(
                    '#'.$sentenceId,
                    array(
                        'controller' => 'sentences',
                        'action' => 'show',
                        $sentenceId
                    ),
                    array(
                        'class' => 'sentenceId'
                    )
                );
                echo '</li>';
            }

            if (!empty($username)) {
                // contributor
                echo '<li class="user">';
                echo $this->_linkToUserProfile($username);
                echo '</li>';
            }

            // date of contribution
            echo '<li class="date">';
            echo ' ';
            echo $this->Date->ago($datetime);
            echo '</li>';

        echo '</ul>';
    }


    private function _annexeSentenceEntry(
        $sentenceId,
        $sentenceText, 
        $sentenceLang, 
        $sentenceScript,
        $username, 
        $datetime, 
        $action,
        $isObsolete
    ) {
        $type = 'sentence';
        $css = $this->_getLogCss($type, $action, $isObsolete);

        echo '<div class="'.$css.'">';
        $this->_displayInfosInAnnexe($sentenceId, $username, $datetime);
        $this->_displaySentenceInAnnexe($sentenceLang, $sentenceScript, $sentenceText);
        echo '</div>';
    }


    private function _annexeLinkEntry(
        $sentenceId,
        $translationId, 
        $username, 
        $datetime, 
        $action
    ) {
        $type = 'link';
        $css = $this->_getLogCss($type, $action);
        $attributes = array(
            'data-translation-id' => $translationId
        );
        echo $this->Html->div($css, null, $attributes);
        $this->_displayInfosInAnnexe($sentenceId, $username, $datetime);
        $this->_displayLink($action, $sentenceId, $translationId);
        echo '</div>';
    }


    private function _displaySentenceInAnnexe($sentenceLang, $sentenceScript, $sentenceText)
    {
        echo '<div class="contribution"><div class="content">';
        // sentence text
        echo $this->Languages->tagWithLang(
            'div', $sentenceLang, $sentenceText,
            array(), $sentenceScript
        );
        echo '</div></div>';
    }


    /**
     * Create the html link to the profile of a given user
     *
     * @param string $userName The user name
     *
     * @return string The html link.
     */
    private function _linkToUserProfile($username)
    {
        return $this->Html->link(
            $username,
            array(
                "controller" => "user",
                "action" => "profile",
                $username
            )
        );
    }


    /**
     * Display log entry of 'sentence' type.
     *
     * @param int    $sentenceId   Id of the sentence.
     * @param string $sentenceText Text of the sentence.
     * @param string $sentenceLang Language of the sentence.
     * @param string $sentenceScript Script of the sentence.
     * @param string $username     Username of the contributor.
     * @param string $datetime     Datetime, format YYYY-MM-DD HH:mm:ss.
     * @param string $action       { 'insert', 'update', 'delete' }
     * @param bool   $isObsolete   Entry is obsolete if sentence has been modified
     *                             or deleted later on.
     *
     * @return void
     */
    private function _sentenceEntry(
        $sentenceId, 
        $sentenceText, 
        $sentenceLang, 
        $sentenceScript,
        $username, 
        $datetime, 
        $action,
        $isObsolete
    ) {
        $type = 'sentence';
        $css = $this->_getLogCss($type, $action, $isObsolete);

        ?><md-list class="<?= $css ?>"><?
        $this->_displayInfos($type, $action, $sentenceId, $username, $datetime);
        $this->_displaySentence($sentenceLang, $sentenceScript, $sentenceText);
        ?></md-list><?
    }


    /**
     * Display log entry of 'link' type.
     *
     * @param int    $sentenceId    Id of the sentence.
     * @param int    $translationId Id of the translation.
     * @param string $username      Username of the contributor.
     * @param string $datetime      Datetime.
     * @param string $action        { 'insert', 'delete' }
     *
     * @return void
     */
    private function _linkEntry(
        $sentenceId, $translationId, $username, $datetime, $action
    ) {
        $type = 'link';
        $css = $this->_getLogCss($type, $action);

        echo '<div class="'.$css.'">';
        $this->_displayInfos($type, $action, $sentenceId, $username, $datetime);
        $this->_displayLink($action, $sentenceId, $translationId);
        echo '</div>';
    }


    /**
     * Display log entry infos.
     *
     * @param  string   $type       [description]
     * @param  string   $action     [description]
     * @param  int      $sentenceId [description]
     * @param  string   $username   [description]
     * @param  datetime $datetime   [description]
     * 
     * @return
     */
    private function _displayInfos(
        $type,
        $action,
        $sentenceId, 
        $username, 
        $datetime
    ) {
        echo '<ul class="info">';

            // sentence id
            echo '<li class="sentenceId">';
            $sentenceLink = $this->Html->link(
                $sentenceId,
                array(
                    'controller' => 'sentences',
                    'action' => 'show',
                    $sentenceId
                )
            );
            echo format(__('Sentence #{number}', true), array('number' => $sentenceLink));
            echo '</li>';

            // contributor
            echo '<li class="user">';
            echo $this->_getActionLabel($type, $action, $username);
            echo '</li>';

    
            // date of contribution
            echo '<li class="date">';
            echo $this->Html->link(
                $this->Date->ago($datetime),
                array(
                    'controller' => 'sentences',
                    'action' => 'show',
                    $sentenceId
                ),
                array('escape' => false)
            );
            echo '</li>';

        echo '</ul>';
    }


    /**
     * [_displayLogEntrySentence description]
     * 
     * @param  [type] $sentenceLang [description]
     * @param  [type] $sentenceText [description]
     * 
     * @return
     */
    private function _displaySentence($sentenceLang, $sentenceScript, $sentenceText)
    {
        echo '<div class="contribution"><div class="content">';
            // language flag
            echo '<span class="lang">';
            echo $this->Languages->icon(
                $sentenceLang,
                array(
                    "class" => "flag",
                    "width" => 30,
                    "height" => 20
                )
            );
            echo '</span>';

            // sentence text
            echo $this->Languages->tagWithLang(
                'div', $sentenceLang, $sentenceText,
                array('class' => 'sentence-text'), $sentenceScript
            );
        echo '</div></div>';
    }


    private function _displayLink($action, $sentenceId, $translationId)
    {
        $linkToTranslation = $this->Html->link(
            $translationId,
            array(
                "controller" => "sentences",
                "action" => "show",
                $translationId
            )
        );
        ?>
        <div class="contribution"><div class="content">
            <?php
            if ($action == 'insert') {
                echo format(
                    __('linked to #{sentenceNumber}', true),
                    array('sentenceNumber' => $linkToTranslation)
                );
            } else {
                echo format(
                    __('unlinked from #{sentenceNumber}', true),
                    array('sentenceNumber' => $linkToTranslation)
                );
            }
            ?>
            </div></div>
        <?php
    }


    /**
     * Returns the CSS class for a log entry, given its type and action.
     *
     * @param string $type       { 'link', 'sentence' }
     * @param string $action     { 'insert', 'update', 'delete' }
     * @param bool   $isObsolete Entry is obsolete if sentence has been modified
     *                           or deleted later on.
     *
     * @return string
     */
    private function _getLogCss($type, $action, $isObsolete = false)
    {
        $obsolete = null;
        if ($isObsolete) {
            $obsolete = 'obsolete';
        }

        switch ($action) {
            case 'insert' :
                $status = 'added';
                break;
            case 'update' :
                $status = 'edited';
                break;
            case 'delete' :
                $status = 'deleted';
                break;
            default:
                $status = null;
                break;
        }

        $css = join(' ', array($type.'Log', $status, $obsolete));

        return $css;
    }


    private function _getActionLabel($type, $action, $username) {
        $userProfileLink = $this->_linkToUserProfile($username);

        switch ($action) {
            case 'insert' :
                if ($type == 'sentence') {
                    $label = format(__('added by {user}', true), array('user' => $userProfileLink));
                } else if ($type == 'link') {
                    $label = format(__('linked by {user}', true), array('user' => $userProfileLink));
                }
                break;
            case 'update' :
                $label = format(__('edited by {user}', true), array('user' => $userProfileLink));
                break;
            case 'delete' :
                if ($type == 'sentence') {
                    $label = format(__('deleted by {user}', true), array('user' => $userProfileLink));
                } else if ($type == 'link') {
                    $label = format(__('unlinked by {user}', true), array('user' => $userProfileLink));
                }
                
                break;
            default:
                $status = null;
                break;
        }

        return $label;
    }


    public function getInfoLabel($type, $action, $username, $date) {
        $userProfileLink = $this->_linkToUserProfile($username);
        $dateLabel = $this->Date->ago($date);

        switch ($action) {
            case 'insert' :
                if ($type == 'sentence') {
                    $label = __('added by {user}', true);
                } else if ($type == 'link') {
                    $label = __('linked by {user}', true);
                }
                break;
            case 'update' :
                $label = __('edited by {user}', true);
                break;
            case 'delete' :
                if ($type == 'sentence') {
                    $label = __('deleted by {user}', true);
                } else if ($type == 'link') {
                    $label = __('unlinked by {user}', true);
                }
                break;
            default:
                $status = null;
                break;
        }

        $formatLabel = format($label, array('user' => $userProfileLink));

        return format(__('{info}, {date}', true), array(
            'info' => $formatLabel,
            'date' => $dateLabel
        ));
    }
}
?>
