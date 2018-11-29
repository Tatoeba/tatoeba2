<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2015  Gilles Bedel
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
namespace App\View\Helper;

use App\View\Helper\AppHelper;


class TranscriptionsHelper extends AppHelper
{
    public $helpers = array(
        'Html',
        'Images',
        'Languages',
        'Pinyin',
        'Search',
    );

    /**
     * Transforms "[kanji|reading]" to HTML <ruby> tags
     */
    private function _rubify($formatted) {
        return preg_replace_callback(
            '/\[([^|]*)\|([^\]]*)\]/',
            function ($matches) {
               $kanjis = preg_split('//u', $matches[1], null, PREG_SPLIT_NO_EMPTY);
               $readings = explode('|',$matches[2]);
               for ($i = 0; $i < count($readings); $i++) {
                   if ($i > 0 && empty($readings[$i])) {
                       array_splice($kanjis, $i-1, 2, $kanjis[$i-1].$kanjis[$i]);
                       array_splice($readings, $i, 1);
                       $i--;
                   }
               }
               while (count($kanjis) > count($readings)) {
                   $last = array_pop($kanjis);
                   array_push($kanjis, array_pop($kanjis).$last);
               }
               $ruby = '';
               for ($i = 0; $i < count($kanjis); $i++) {
                   $kanji = h($kanjis[$i]);
                   $reading = h($readings[$i]);
                   $ruby .= "<ruby>$kanji<rp>（</rp><rt>$reading</rt><rp>）</rp></ruby>";
               }
               return $ruby;
            },
            $formatted);
    }

    /**
     * Transforms "[kanji|reading]" into kanji｛reading｝
     * and "[kanjikanji|reading|reading]" into kanjikanji｛reading｜reading｝
     */
    private function _bracketify($formatted) {
        $formatted = preg_replace(
            '/\[([^|]*)\|([^\]]*)\]/',
            '$1｛$2｝',
            $formatted);
        return str_replace('|', '｜', $formatted);
    }

    /**
     * Display transcriptions.
     *
     * @param array  $transcriptions List of transcriptions.
     * @param string $lang           Language of the transcripted sentence.
     * @param string $sentenceOwnerId Id of the owner of the sentence
     *                                transcriptions comes from.
     *
     * @return void
     */
    public function displayTranscriptions(
        $transcriptions,
        $lang,
        $sentenceOwnerId
    ) {
        foreach ($transcriptions as $script => $transcr) {
            $this->displayTranscription(
                $transcr,
                $lang,
                $sentenceOwnerId
            );
        }
    }

    public function displayTranscription(
        $transcr,
        $lang,
        $sentenceOwnerId
    ) {
        $this->Html->script('jquery.jeditable.js', ['block' => true]);
        $this->Html->script('transcriptions.js', ['block' => true]);

        $canEdit = CurrentUser::canEditTranscription(
            $transcr['user_id'], $sentenceOwnerId
        );
        $isEditable = $canEdit && !$transcr['readonly'];
        $needsReview = $transcr['needsReview'];

        $toggleButton = $this->toggleButton($transcr);

        $buttonsDiv = $this->Html->tag('div',
            $this->Html->tag(
                'ul',
                $this->editButton($canEdit, $transcr)
                . $this->scriptIcon($transcr)
                . $this->warningReviewButton($isEditable, $needsReview, $transcr['type']),
                array('class' => 'menu')
            ),
            array('class' => 'column')
        );

        $class = 'column transcription';
        if ($isEditable)
            $class .= ' editable';
        if ($transcr['type'] == 'altscript')
            $class .= ' altscript';

        $html = $this->transcriptionAsHTML($lang, $transcr);
        $log = '';
        if (isset($transcr['User']['username'])) {
            $log = format(
                /* @translators: refers to a transcription */
                __('Last edited by {author} the {date}'),
                array(
                    'author' => $transcr['User']['username'],
                    'date' => $transcr['modified'],
                )
            );
        } else {
            if ($transcr['type'] == 'altscript') {
                $log = __('This alternative script was generated by '.
                          'a piece of software', true);
            } else {
                $log = __('This transcription was generated by '.
                          'a piece of software', true);
            }
        }
        $transcriptionDiv = $this->Languages->tagWithLang(
            'div', $lang, $html,
            array(
                'data-script' => $transcr['script'],
                'data-submit' => __('OK'),
                'data-cancel' => __('Cancel'),
                'data-reset' => __('Reset'),
                'title' => $log,
                'class' => $class,
                'escape' => false,
            ),
            $transcr['script']
        );

        $class = 'transcriptionContainer';
        if ($needsReview) {
            $class .= ' needsReview';
        } elseif ($lang == 'jpn') {
            $class .= ' blend';
        }
        $hide = $needsReview && !CurrentUser::get('settings.show_transcriptions');
        if ($hide) {
            $class .= ' hidden';
        }
        echo $this->Html->tag('div',
            $toggleButton.$buttonsDiv.$transcriptionDiv,
            array(
                'escape' => false,
                'class' => $class,
            )
        );
    }

    private function toggleButton($transcr) {
        if ($transcr['type'] == 'altscript') {
            $title = __('Show alternative script');
        } else {
            $title = __('Show transcription');
        }
        $icon = $this->scriptSvg($transcr, $title);
        return $this->Html->tag('li', "<a>$icon</a>", array(
            'class' => 'transcribe option',
            'style' => 'display:none',
            'escape' => false,
        ));
    }

    private function scriptIcon($transcr) {
        return $this->Html->tag('li', $this->scriptSvg($transcr), array(
            'class' => 'option script'
        ));
    }

    private function scriptSvg($transcr, $title = null) {
        $class = 'script-icon';
        if ($transcr['type'] == 'altscript') {
            $class .= ' altscript';
        }
        $script = $transcr['script'];
        return $this->Images->svgIcon(
            'scripts/' . $script,
            array(
                'class' => $class,
                'title' => $title,
            ),
            $script
        );
    }

    private function editButton($canEdit, $transcr) {
        if ($transcr['readonly'] || !CurrentUser::isMember()) {
            return $this->Html->tag('li', '', array('class' => 'option'));
        }

        $editImage = $this->Images->svgIcon('edit', array(
            'width'  => 16,
            'height' => 16,
        ));
        if ($transcr['type'] == 'altscript') {
            $title = __('Edit alternative script');
        } else {
            $title = __('Edit transcription');
        }
        $content = $editImage;

        if (!$canEdit) {
            if ($transcr['type'] == 'altscript') {
                $title = __('You cannot edit this script.');
            } else {
                $title = __('You cannot edit this transcription.');
            }
            $content = $this->Html->tag(
                'a', $editImage, array('class' => 'disabled')
            );
        }

        return $this->Html->tag('li', $content,
            array(
                'class' => 'option edit_transcription',
                'title'=> $title,
            )
        );
    }

    private function warningReviewButton($isEditable, $needsReview, $type) {
        $content = '';
        if ($needsReview) {
            $content = $this->Images->svgIcon('warning-small');
            if ($type == 'altscript') {
                $title = __(
                    'This alternative script may contain errors because '.
                    'it has been generated by a piece of software that '.
                    'is not perfect.',
                    true
                );
            } else {
                $title = __(
                    'This transcription may contain errors because '.
                    'it has been generated by a piece of software that '.
                    'is not perfect.',
                    true
                );
            }
            if ($isEditable) {
                $content .= $this->Images->svgIcon('check', array(
                    'class' => 'check',
                ));
                $title .= "\n";
                if ($type == 'altscript') {
                    $title .= __x(
                        'alternative script',
                        'Click to mark it as reviewed.',
                        true
                    );
                } else {
                    $title .= __x(
                        'transcription',
                        'Click to mark it as reviewed.',
                        true
                    );
                }
            }
        };

        if ($content) {
            $content = $this->Html->tag('li', $content, array(
                'class' => 'option review',
                'title' => $title,
            ));
        }
        return $content;
    }

    /**
     * Format and escape a transcription
     * so that it may be displayed as HTML.
     */
    public function transcriptionAsHTML($lang, $transcr) {
        $text = h($transcr['text']);

        if (isset($transcr['highlight'])) {
            $text = $this->Search->highlightMatches($transcr['highlight'], $text);
        }
        if ($transcr['script'] == 'Hrkt') {
            $ruby = $this->_rubify($transcr['text']);
            $bracketed = $this->_bracketify($text);
            $text = $this->Html->tag('span', $bracketed, array(
                'style' => 'display:none',
                'class' => 'markup',
            ));
            $text .= $ruby;
        } elseif ($lang == 'cmn' && $transcr['script'] == 'Latn') {
            $pinyin = $this->Pinyin->numeric2diacritic($text);
            $text = $this->Html->tag('span', $text, array(
                'style' => 'display:none',
                'class' => 'markup',
            ));
            $text .= $pinyin;
        }
        return $text;
    }
}
?>
