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

    const SENTENCE_ID_PATTERN = '/([\p{Ps}：\s]|^)(#([1-9]\d*))/';
    const START_OF_URL = '|^https?://[[:alnum:]~_-]+\.[[:alnum:]~_-]+|';
    const END_OF_URL = '|^[\s\n"\'<>]|';
    const ENTITY = '|^&[^&;]+;|';
    const ENTITY_OR_ESCAPE_SEQUENCE = '/(&[^&;]+;|%[0-9a-zA-Z]{2}|.)/u';

    private function splitWithEntitiesAndEscapeSequences($string) 
    {
        return preg_split(
            $this::ENTITY_OR_ESCAPE_SEQUENCE,
            $string,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );
    }

    private function checkTail($url, $par, $tail='')
    {
        if (strlen($url) == 0) {
            return ['', $tail, $par];
        }
    
        $last = $url[strlen($url)-1];
        
        if ($last == ')' && $par < 0) {
            return $this->checkTail(substr($url, 0, -1), $par+1, ')'.$tail);
        }
        
        if (in_array($last, ['!', '.', ',', ';', ':'])) {
            return $this->checkTail(substr($url, 0, -1), $par, $last.$tail);
        }
        
        return array($url, $tail, $par);
    }

    private function shortenUrl($url)
    {
        $maxUrlLength = 50;
        $offset1 = ceil(0.65*$maxUrlLength) - 2;
        $offset2 = ceil(0.30*$maxUrlLength) - 1;
        
        $displayedChars = $this->splitWithEntitiesAndEscapeSequences($url);
        if (count($displayedChars) > $maxUrlLength) {
            array_splice($displayedChars, $offset1, -$offset2, array('.', '.', '.'));
            return implode($displayedChars);
        }
        
        return $url;
    }
    
    /**
     * Replace URLs by clickable URLs.
     *
     * @param array $text Text to process.
     *
     * @return string
     */
    public function clickableURL($text)
    {
        // get rid of \r
        $text = preg_replace('#\r#u', '', $text);
        
        // add guard at the end to make sure the parser will replace links 
        // at the end of $text 
        $text .= ' ';
    
        $ret = '';
        while (strlen($text) > 0) {
        
            if (!preg_match($this::START_OF_URL, $text, $result)) {
                $ret .= $text[0];
                $text = substr($text, 1);
                continue;
            }
            $text = substr($text, strlen($result[0]));
            
            // the parsed URL is stored in two pieces: $url and $tail
            // when parsing is complete, the symbols in $tail can be removed
            // while the symbols in $url are fixed. This prevents the trailing
            // ; from an entity to be removed, while being able to remove
            // other trailing ;
            
            $url = $result[0];
            $tail = '';
            $countOfOpenedParanthesis = 0;
            
            // This loop always terminates because in every loop there is either 
            // a break statement or at least one character from $text is removed
            while (strlen($text) > 0) {
                if (preg_match($this::ENTITY, $text, $result)) {
                    $url .= $tail.$result[0];
                    $tail = '';
                    $text = substr($text, strlen($result[0]));
                    continue;
                }

                if (!preg_match($this::END_OF_URL, $text)) {
                    if ($text[0] == '(') {
                        $countOfOpenedParanthesis++;
                    }
                    if ($text[0] == ')') {
                        $countOfOpenedParanthesis--;
                    }
                    $tail .= $text[0];
                    $text = substr($text, 1);
                    if ($countOfOpenedParanthesis >= 0) {
                      continue;
                    }
                    
                    // a closing paranthesis without corresponding opening
                    // paranthesis ends the url 
                }

                list($head, $tail, $countOfOpenedParanthesis) = $this->checkTail($tail, $countOfOpenedParanthesis);
                $url .= $head;
                if ($countOfOpenedParanthesis == 0) {
                    $ret .= '<a href="'.$url.'" target="_blank" rel="nofollow">'.$this->shortenURL($url).'</a>'.$tail;
                } else {
                    $ret .= $url.$tail;
                }
                break;
            }
        }
    
        // remove guard
        return substr($ret, 0, -1);
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
            $this::START_OF_URL,
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
