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

if (!empty($query)) {
    $title = format(__('Sentences with: {keywords}', true), array('keywords' => Sanitize::html($query)));
} else {
    if ($from != 'und' && $to != 'und') {
        $title = format(__('Sentences in {language} translated into {translationLanguage}', true),
                        array('language' => $languages->codeToNameToFormat($from),
                              'translationLanguage' => $languages->codeToNameToFormat($to)));
    } elseif ($from != 'und') {
        $title = format(__('Sentences in {language}', true),
                        array('language' => $languages->codeToNameToFormat($from)));
    } elseif ($to != 'und') {
        $title = format(__('Sentences translated into {language}', true),
                        array('language' => $languages->codeToNameToFormat($to)));
    } else {
        $title = format(__('All sentences', true));
    }
}
$this->set('title_for_layout', $pages->formatTitle($title));
?>

<div id="annexe_content">
    <?php
    echo $this->element('search_features');
    ?>
</div>


<div id="main_content">
    <div class="module">
    <?php
        echo $this->Form->create(
            'AdvancedSearch',
            array(
                'url' => array(
                    'controller' => 'sentences',
                    'action' => 'search',
                ),
                'type' => 'get',
            )
        );
        echo $this->Form->input('query', array(
            'label' => __('Query:', true),
            'value' => $query,
        ));

        echo $this->Search->selectLang('from', $from, array(
            'label' => __('Sentences in:', true),
        ));
        echo $this->Search->selectLang('to', $to, array(
            'label' => __('Show translations in:', true),
            'options' => $this->Languages->languagesArrayForPositiveLists(),
        ));

        $orphansNote = $this->Html->tag(
            'div',
            __('Oprhan sentences are likely to be incorrect.', true),
            array(
                'class' => 'note',
            )
        );
        echo $this->Form->input('orphans', array(
            'type' => 'checkbox',
            'hiddenField' => false,
            'label' => __('Show orphan sentences', true),
            'after' => $orphansNote,
            'value' => 'yes',
            'checked' => $orphans,
        ));

        echo $this->Form->input('user', array(
            'label' => __('Owner:', true),
            'placeholder' => __('Enter a username', true),
            'value' => $user,
        ));
        echo $this->Form->end(__('search', true));
    ?>
    </div>
<?php
if (!empty($results)) {
    
    ?>
    <div class="module">
        <?php 
        $keywords = $this->Languages->tagWithLang(
            'span', '', $query
        );
        if (!empty($query)) {
            $title = format(
                /* @translators: title on the top of a search result page */
                __('Search: {keywords}', true),
                compact('keywords')
            );
        }
        echo $this->Pages->formatTitleWithResultCount($paginator, $title, $real_total);
        ?>
        
        <?php
        $pagination->display();
        
        foreach ($results as $sentence) {
            $sentences->displaySentencesGroup(
                $sentence['Sentence'], 
                $sentence['Translations'], 
                $sentence['User'],
                $sentence['IndirectTranslations'],
                true,
                $to
            );
        }
        
        $pagination->display();
        ?>
    </div>
    <?php
    
} else {
    
    echo $this->element('search_with_no_result');
    
}
?>  
</div>
