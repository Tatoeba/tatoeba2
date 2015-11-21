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

class TranscriptionsHelper extends AppHelper
{
    public $helpers = array(
        'Html',
        'Images',
        'Javascript',
        'Languages',
        'Pinyin',
    );

    /**
     * Transforms "[kanji|reading]" to HTML <ruby> tags
     */
    private function _rubify($formatted) {
        return preg_replace(
            '/\[([^|]*)\|([^\]]*)\]/',
            '<ruby><rp>[</rp>$1<rp>|</rp><rt>$2</rt><rp>]</rp></ruby>',
            $formatted);
    }

    /**
     * Transforms "[kanji|reading]" into kanji｛reading｝
     */
    private function _bracketify($formatted) {
        return preg_replace(
            '/\[([^|]*)\|([^\]]*)\]/',
            '$1｛$2｝',
            $formatted);
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

    private function displayTranscription(
        $transcr,
        $lang,
        $sentenceOwnerId
    ) {
        $this->Javascript->link('jquery.jeditable.js', false);
        $this->Javascript->link('transcriptions.edit_in_place.js', false);

        $canEdit = CurrentUser::canEditTranscription(
            $transcr['user_id'], $sentenceOwnerId
        );
        $isEditable = $canEdit && !$transcr['readonly'];
        $isReviewed = isset($transcr['user_id']);
        $needsReview = $transcr['needsReview'] && !$isReviewed;
        $warn = CurrentUser::get('settings.transcriptions_warning');

        $toggleButton = $this->toggleButton($transcr);

        $buttonsDiv = $this->Html->tag('div',
            $this->Html->tag(
                'ul',
                $this->editButton($canEdit, $transcr)
                . $this->scriptIcon($transcr['script']),
                array('class' => 'menu')
            ),
            array('class' => 'column')
        );

        $class = 'column transcription';
        if ($needsReview) {
            $class .= ' rightWarningIcon';
        }
        if ($isEditable)
            $class .= ' editable';
        $html = $this->transcriptionAsHTML($lang, $transcr);
        $log = '';
        if (isset($transcr['User']['username'])) {
            $log = format(
                /* @translators: refers to a transcription */
                __('Last edited by {author} on {date}.', true),
                array(
                    'author' => $transcr['User']['username'],
                    'date' => $transcr['modified'],
                )
            );
            $log .= "\n";
        }
        $transcriptionDiv = $this->Languages->tagWithLang(
            'div', $lang, $html,
            array(
                'data-script' => $transcr['script'],
                'data-submit' => __('OK', true),
                'data-cancel' => __('Cancel', true),
                'data-reset' => __('Reset', true),
                'title' => $log,
                'class' => $class,
                'escape' => false,
            ),
            $transcr['script']
        );

        $infoDiv = '';
        if ($needsReview && $warn) {
            $warningIcon = $this->Images->svgIcon('warning-small', array(
                'class' => 'inlined-icon',
            ));
            if ($transcr['type'] == 'altscript') {
                $warningMessage = __(format(
                    'The following alternative script is marked with the '.
                    '{warningIcon} icon, which means it has been automatically '.
                    'generated and <strong>may contain errors</strong>.',
                    compact('warningIcon')),
                    true
                );
            } else {
                $warningMessage = __(format(
                    'The following transcription is marked with the '.
                    '{warningIcon} icon, which means it has been automatically '.
                    'generated and <strong>may contain errors</strong>.',
                    compact('warningIcon')),
                    true
                );
            }
            $closeButton = $this->Html->div(
                'close',
                $this->Images->svgIcon('close', array(
                    'title' => __("Don't show this message again", true)
                ))
            );
            $infoDiv = $this->Html->div(
                'transcriptionInfo',
                $closeButton.$warningMessage
            );
        }

        $class = 'transcriptionContainer';
        if ($needsReview) {
            $class .= ' needsReview';
        }
        $hide = $transcr['type'] == 'transcription';
        echo $this->Html->tag('div',
            $toggleButton.$infoDiv.$buttonsDiv.$transcriptionDiv,
            array(
                'escape' => false,
                'class' => $class,
                'style' => $hide ? 'display:none' : null,
            )
        );
    }

    private function toggleButton($transcr) {
        if ($transcr['type'] == 'altscript') {
            $title = __('Show alternative script', true);
        } else {
            $title = __('Show transcription', true);
        }
        $icon = $this->scriptSvg($transcr['script'], $title);
        return $this->Html->tag('li', "<a>$icon</a>", array(
            'class' => 'transcribe option',
            'style' => 'display:none',
            'escape' => false,
        ));
    }

    private function scriptIcon($script) {
        return $this->Html->tag('li', $this->scriptSvg($script), array(
            'class' => 'option script'
        ));
    }

    private function scriptSvg($script, $title = null) {
        return $this->Images->svgIcon(
            'scripts/' . $script,
            array(
                'class' => 'script-icon',
                'title' => $title,
            ),
            $script
        );
    }

    private function editButton($canEdit, $transcr) {
        if ($transcr['readonly']) {
            return $this->Html->tag('li', '', array('class' => 'option'));
        }

        $editImage = $this->Images->svgIcon('edit', array(
            'width'  => 16,
            'height' => 16,
        ));
        if ($transcr['type'] == 'altscript') {
            $title = __('Edit transcription', true);
        } else {
            $title = __('Edit alternative script', true);
        }
        $content = $editImage;

        if(!CurrentUser::isMember()) {
            $loginUrl = $this->url(array(
                'controller' => 'users',
                'action' => 'login',
                '?' => array(
                    'redirectTo' => Router::reverse($this->params)
                ),
            ));
            $content = $this->Html->tag(
                'a', $editImage, array('href' => $loginUrl)
            );
        } elseif (!$canEdit) {
            if ($transcr['type'] == 'altscript') {
                $title = __('You cannot edit this transcription.', true);
            } else {
                $title = __('You cannot edit this script.', true);
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

    /**
     * Format and escape a transcription
     * so that it may be displayed as HTML.
     */
    public function transcriptionAsHTML($lang, $transcr) {
        $text = Sanitize::html($transcr['text']);

        if ($transcr['script'] == 'Hrkt') {
            $ruby = $this->_rubify($text);
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
