<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2011  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
use App\Model\CurrentUser;

if (empty($lang)){
    $title = __('Orphan sentences');
} else {
    $title = format(
        __('Orphan sentences in {language}'), 
        array('language' => $this->Languages->codeToNameToFormat($lang))
    );
}
$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>
<div id="annexe_content">
    
    <?php $this->CommonModules->createFilterByLangMod(); ?>
    
    <div class="section md-whiteframe-1dp">
        <h2><?php echo __('About adoption'); ?></h2>
        <p>
        <?php
        echo __(
            'Adopting is a way to vote "this sentence is correct". It is also an '.
            'occasion to check the sentence and correct it if there is a mistake.'
        );
        ?>
        </p>
        
        <p>
        <?php
        echo format(
            __(
                'So if you want to help us check and correct sentences, then adopt '.
                '({adoptButton}) any "orphan" sentence you see in your <strong>native '.
                'language</strong>, and correct it if necessary. '.
                'Read <a href="{url}">this</a> for further explanation.', true
            ),
            array(
                'adoptButton' => $this->Html->image('unadopted.svg', array('height' => 16)),
                'url' => 'http://blog.tatoeba.org/2010/04/reliability-of-sentences-how-will-we.html'
            )
        )
        ?>
        </p>
    </div>
    
    <div class="section md-whiteframe-1dp">
        <?php /* @translators: title of the help text on the Adopt sentence page */ ?>
        <h2><?php echo __('Tips'); ?></h2>
        <p>
        <?php 
        echo __(
            'If you see another username appear after adopting a sentence, it '.
            'means that someone else adopted the sentence very shortly before '.
            'you did. In such cases, you can try adopting sentences that are '.
            'several pages away from your current page to reduce the chances of '.
            'that happening again.'
        );
        ?>
        </p>
    </div>
</div>

<div id="main_content">

<section class="md-whiteframe-1dp">
    <md-toolbar class="md-hue-2">
        <div class="md-toolbar-tools">
            <h2><?= $this->Pages->formatTitleWithResultCount($this->Paginator, $title) ?></h2>
        </div>
    </md-toolbar>

    <md-content>
    <?php 

    $this->Pagination->display();
    
    if (!CurrentUser::isMember() || CurrentUser::getSetting('use_new_design')) {
        foreach ($results as $sentence) {
            echo $this->element(
                'sentences/sentence_and_translations',
                array(
                    'sentence' => $sentence,
                    'translations' => [0 =>[], 1 => []],
                    'user' => $sentence->user,
                    'menuExpanded' => true
                )
            );
        }
    } else {
        foreach ($results as $sentence) {
            $sentence->translations = [];
            $this->Sentences->displaySentencesGroup($sentence);
        }
    }
        
    $this->Pagination->display();
    ?>
    </md-content>
</section>

</div>
