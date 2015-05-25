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
        $chained = array();
        foreach ($transcriptions as $script => $transcr) {
            if (isset($transcr['parent_id'])) {
                $chained[ $transcr['parent_id'] ] = $transcriptions[$script];
                unset($transcriptions[$script]);
            }
        }

        foreach ($transcriptions as $script => $transcr) {
            if (isset($transcr['id']) && isset($chained[$transcr['id']])) {
                $subTranscr = $chained[$transcr['id']];
            } else {
                $subTranscr = null;
            }
            $this->displayTranscription(
                $transcr,
                $lang,
                $subTranscr,
                $sentenceOwnerId
            );
        }
    }

    private function displayTranscription(
        $transcr,
        $lang,
        $subTranscr,
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

        $class = 'transcription';
        if ($isEditable)
            $class .= ' editable';
        $html = $this->transcriptionAsHTML($lang, $transcr);
        $transcriptionDiv = $this->Languages->tagWithLang(
            'div', $lang, $html,
            array(
                'data-script' => $transcr['script'],
                'data-tooltip' => __('Click to edit this transcription', true),
                'data-submit' => __('OK', true),
                'data-cancel' => __('Cancel', true),
                'class' => $class,
                'escape' => false,
            ),
            $transcr['script']
        );

        $infoDiv = '';
        if ($needsReview) {
            if(!CurrentUser::isMember()) {
                $loginUrl = $this->url(array(
                    'controller' => 'users',
                    'action' => 'login',
                    '?' => array(
                        'redirectTo' => Router::reverse($this->params)
                    ),
                ));
                $registerUrl = $this->url(array(
                    'controller' => 'users',
                    'action' => 'register',
                ));
                $warningMessage = __(format(
                    'The following transcription has been automatically '.
                    'generated and <strong>may contain errors</strong>. '.
                    'If you wish to review it, please <a href="{loginUrl}">'.
                    'log in</a> or <a href="{registerUrl}">register</a> first.',
                    compact('loginUrl', 'registerUrl')),
                    true
                );
            } elseif ($isEditable) {
                $warningMessage = __(
                    'The following transcription has been automatically '.
                    'generated and <strong>may contain errors</strong>. '.
                    'If you can, you are welcome to review by clicking it.',
                    true
                );
            } else {
                $warningMessage = __(
                    'The following transcription has been automatically '.
                    'generated and <strong>may contain errors</strong>. '.
                    'You may not edit it for technical reasons.',
                    true
                );
            }
            $infoDiv = $this->Html->tag('div', $warningMessage, array(
                'class' => 'transcriptionWarning',
            ));
        }

        $class = 'transcription subTranscription';
        $subTranscrDiv = '';
        if ($subTranscr) {
            $subTranscrDiv = $this->Languages->tagWithLang(
                'div', $lang, $subTranscr['text'],
                array('class' => $class),
                $subTranscr['script']
            );
        }

        $class = 'transcriptionContainer';
        if ($needsReview) {
            $class .= ' needsReview';
        }
        echo $this->Html->tag('div', $infoDiv.$transcriptionDiv.$subTranscrDiv, array(
            'escape' => false,
            'class' => $class,
        ));
    }

    /**
     * Format and escape a transcription
     * so that it may be displayed as HTML.
     */
    public function transcriptionAsHTML($lang, $transcr) {
        $text = Sanitize::html($transcr['text']);
        if ($transcr['script'] == 'Hrkt')
            $text = $this->_rubify($text);
        elseif ($lang == 'cmn' && $transcr['script'] == 'Latn') {
            $pinyin = Sanitize::html($this->Pinyin->numeric2diacritic($text));
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
