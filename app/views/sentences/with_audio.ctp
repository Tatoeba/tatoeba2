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
if (empty($lang)){
    $title = __('Sentences with audio', true);
} else {
    $title = format(
        __('Sentences in {language} with audio', true), 
        array('language' => $languages->codeToNameToFormat($lang))
    );
}

$this->set('title_for_layout', $pages->formatTitle($title));
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
    if (!empty($results)) {
        ?>
        
        <h2>
        <?php 
        $resultsCount = $paginator->counter(array('format' => '%count%'));
        printf(__n('%1$s (one result)', '%1$s (%2$s&nbsp;results)', $resultsCount, true), $title, $resultsCount);
        ?>
        </h2>
        
        
        <?php
        $pagination->display(array($lang));
        
        foreach ($results as $sentence) {
            $sentences->displayGenericSentence(
                $sentence['Sentence'], 
                null,
                'mainSentence'
            );
        }
        
        $pagination->display(array($lang));
    } 
    ?>
    </div>
</div>
