<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2016 Gilles Bedel
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

if (isset($lang)){
    $title = format(
        __('Sentences in {language} with audio', true),
        array('language' => $languages->codeToNameToFormat($lang))
    );
} else {
    $title = __('Sentences with audio', true);
}
?>

<div id="annexe_content">
    <?php echo $this->element('audio_stats', array(
        'stats' => $stats,
        'cache' => array(
            'time'=> '+6 hours',
            'key'=> Configure::read('Config.language')
        )
    )); ?>
</div>

<div id="main_content">
<div class="module">
<?php
if (isset($sentencesWithAudio)) {
    if (count($sentencesWithAudio) == 0) {
        echo $html->tag('h2', format(
            __('There are no sentences with audio', true)
        ));
    } else {
        $title = $paginator->counter(
            array(
                'format' => $title . ' ' . __("(total %count%)", true)
            )
        );
        echo $html->tag('h2', $title);

        $paginationUrl = array();
        $pagination->display($paginationUrl);

        $type = 'mainSentence';
        $parentId = null;
        $withAudio = true;
        foreach ($sentencesWithAudio as $sentence) {
            $sentences->displayGenericSentence(
                $sentence,
                $type,
                $withAudio,
                $parentId
            );
        }

        $pagination->display($paginationUrl);
    }
} else {
}
?>
</div>
</div>
