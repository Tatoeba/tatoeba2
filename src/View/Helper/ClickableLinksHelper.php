<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  SIMON Allan <allan.simon@supinfo.com>
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
namespace App\View\Helper;

use App\View\Helper\AppHelper;
use Cake\ORM\TableRegistry;


/**
 * Helper for links
 *
 * @category Utilities
 * @package  Helpers
 * @author   SIMON Allan <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

class ClickableLinksHelper extends AppHelper
{
    public $helpers = array('Html', 'Pages');

    const URL_PATTERN = '/((ht|f)tps?:\/\/([\w\.]+\.)?[\w-]+(\.[a-zA-Z]{2,4})?[^\s\r\n"\'<]+)/siu';
    const SENTENCE_ID_PATTERN = '/([\p{Ps}：\s]|^)(#([1-9]\d*))/';

    private function splitWithEntities($string) {
        return preg_split(
            '/(&[^;]+;|.)/u',
            $string,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );
    }

    /**
     * Replace URLs by clickable URLs.
     * Inspired from :
     * http://prajapatinilesh.wordpress.com/2007/08/08/php-make-urls-clickable-and-short-down/
     *
     * @param array $text Text to process.
     *
     * @return string
     */
    public function clickableURL($text)
    {
        // get rid of \r
        $text = preg_replace('#\r#u', '', $text);
        $match = preg_match_all($this::URL_PATTERN, $text, $urls);

        if ($match) {
            $maxUrlLength = 50;
            $offset1 = ceil(0.65*$maxUrlLength) - 2;
            $offset2 = ceil(0.30*$maxUrlLength) - 1;


            foreach (array_unique($urls[1]) as $url) {
                $displayedChars = $this->splitWithEntities($url);
                if (count($displayedChars) > $maxUrlLength) {
                    array_splice($displayedChars, $offset1, -$offset2, array('.', '.', '.'));
                    $urlText = implode($displayedChars);
                } else {
                    $urlText = $url;
                }

                // Checking last character and taking it out if it's punctuation
                $unwantedLastCharacters = array('!', '.', ',', ';', ':');
                $lastCharacter = end($displayedChars);
                if (in_array($lastCharacter, $unwantedLastCharacters)) {
                    $url = mb_substr($url, 0, -1);
                    $urlText = mb_substr($urlText, 0, -1);
                }

                // There was a problem when one URL is included in another one.
                // For instance, https://tatoeba.org is included in
                // https://tatoeba.org/wall.
                // Because of the presence of https://tatoeba.org, the other URLS
                // beginning with https://tatoeba.org would be messed up.
                // That's why we need to replace only if there's a stop character.
                $escapedUrl = quotemeta($url);  // meta characters
                $escapedUrl = str_replace('/', '\/', $escapedUrl); // identifier
                $escapedUrl = str_replace('|', '\|', $escapedUrl); // pipe
                $stopChars = quotemeta(implode($unwantedLastCharacters));
                $pattern2 = '/'.$escapedUrl.'(['.$stopChars.'< \n])|'.$escapedUrl.'$/u';
                $text = preg_replace(
                    $pattern2,
                    "<a href=\"$url\" target=\"_blank\" rel=\"nofollow\">$urlText</a>$1",
                    $text
                );
            }
        }

        return $text;
    }


    /**
     * Converts sentence ids (ex: #123) into link.
     * 
     * @param  String $text Text of the comment
     * 
     * @return String       Text of the comment with sentences id converted to links.
     */
    public function clickableSentence($text)
    {
        $self = $this;
        $model = TableRegistry::getTableLocator()->get('Sentences');
        $content = preg_replace_callback(
            $this::SENTENCE_ID_PATTERN, 
            function ($m) use ($self, $model) {
                return $m[1] . $self->Html->link($m[2],
                    $self->request->scheme().'://'.$self->request->host().'/sentences/show/'.$m[3],
                    array('title' => $model->getSentenceTextForId($m[3]))
                );
            }, $text);

        $content = str_replace('\\#', '#', $content);

        return $content;
    }


    /**
     * Tells if a text has a string that can be converted into a clickable link.
     * 
     * @param  String  $text The text to check.
     * 
     * @return boolean
     */
    public function hasClickableLink($text)
    {
        $patterns = array(
            $this::URL_PATTERN,
            $this::SENTENCE_ID_PATTERN
        );

        foreach($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }


    /**
     * Build #n links where n is a sentence ID.
     *
     * @param int $sentenceId the ID of the sentence linked
     * @param string $sentenceText the text of the sentence linked
     *
     * @return string an HTML link whose title attribute is the text of the sentence
     */
    public function buildSentenceLink($sentenceId, $sentenceText = null)
    {
        $tooltipTag = $this->Html->tag(
            'md-tooltip',
            $this->_View->safeForAngular(h($sentenceText)),
            ['ng-cloak']
        );
        $linkText = $this->Pages->formatSentenceIdWithSharp($sentenceId);
        return $this->Html->link(
            h($linkText).$tooltipTag,
            array(
                'controller' => 'sentences',
                'action' => 'show',
                $sentenceId
            ),
            ['escape' => false]
        );
    }
}
?>
