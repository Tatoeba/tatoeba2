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
use Cake\I18n\I18n;

if (isset($lang)){
    $title = format(
        __('Sentences in {language} with audio'),
        array('language' => $this->Languages->codeToNameToFormat($lang))
    );
} else {
    $title = __('Sentences with audio');
}
$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<div id="annexe_content">
    <?= $this->element('audio_stats',
            [ 'stats' => $stats ],
            [ 'cache' => [
                'config' => 'stats',
                'key' => 'audio_stats_'.I18n::getLocale(),
            ]]
    ); ?>
</div>

<div id="main_content">

<section class="md-whiteframe-1dp">
<?php
if (isset($sentencesWithAudio)) {
    if (count($sentencesWithAudio) == 0) {
        echo $this->Html->tag('h2', format(
            __('There are no sentences with audio')
        ));
    } else {
        ?>        
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <?= $this->Pages->formatTitleWithResultCount($this->Paginator, $title, $total, true); ?>
            </div>
        </md-toolbar>

        <md-content layout-padding>
        <?php
        if ($total > $this->Paginator->param('count')) {
            ?>
            <div layout-padding>
            <?= format(
                __('Only sentences having the last {n} audios are displayed here.'),
                ['n' => $this->Number->format($totalLimit)]
            ); ?>
            </div>
            <?php
        }

        $this->Pagination->display();

        $type = 'mainSentence';
        $parentId = null;
        $withAudio = true;
        foreach ($sentencesWithAudio as $sentence) {
            $this->Sentences->displayGenericSentence(
                $sentence,
                $type,
                $withAudio,
                $parentId
            );
        }

        $this->Pagination->display();
        ?>
        </md-content>
        <?php
    }
} else {
}
?>
</section>
</div>
