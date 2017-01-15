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
 * @link     http://tatoeba.org
 */
$this->set('title_for_layout', $this->Pages->formatTitle(__('Translate sentences')));

$currentLanguage = $this->Session->read('browse_sentences_in_lang');
$notTranslatedInto = $this->Session->read('not_translated_into_lang');
if (empty($currentLanguage)) {
    $currentLanguage = $this->Session->read('random_lang_selected');
}
if (empty($notTranslatedInto)) {
    $notTranslatedInto = 'none';
}
?>

<div id="annexe_content">
    <div class="section" md-whiteframe="1">
        <h2><?php echo __("How to add a translation"); ?></h2>
        <p>
            <?php
            echo format(
                __(
                    'Once the sentences are displayed, click on {translateButton} to add '.
                    'a translation.', true
                ),
                array('translateButton' => $this->Html->image('translate.svg', array('height' => 16)))
            );
            ?>
        </p>
    </div>
    
    <div class="section" md-whiteframe="1">
    <h2><?php echo __('About translations'); ?></h2>
    
    <h4><?php echo __("Good translations"); ?></h4>
    <p>
    <?php echo __("We know it's difficult, but do NOT translate word for word!"); ?>
    </p>
    
    
    <h4><?php echo __("Multiple translations"); ?></h4>
    <p>
    <?php
    __(
        "If you feel there are several possible translations, ".
        "you can add several translations in the same language. "
    );
    ?>
    </p>
    </div>
</div>

<div id="main_content">
    <h2><?php echo __('Translate sentences'); ?></h2>

    <div class="section" md-whiteframe="1">
        <h3><?php echo __('Search for untranslated sentences'); ?></h3>
        <div>
            <?php
            echo $this->Form->create(
                'Activity',
                array("action" => "translate_sentences", "type" => "get")
            );

            $langsFrom = $this->Languages->onlyLanguagesArray();
            $langsTo = $this->Languages->LanguagesArrayForNegativeLists();

            ?>
            <fieldset class="select">
                <label for="ActivityLangFrom">
                    <?php echo __('Sentences in:'); ?>
                </label>
                <?php
                echo $this->Form->select(
                    'langFrom',
                    $langsFrom,
                    $currentLanguage,
                    array(
                        'class' => 'language-selector',
                        "empty" => false
                    ),
                    false
                );
                ?>
            </fieldset>

            <fieldset class="select">
                <label for="ActivityLangTo">
                    <?php echo __('Not directly translated into:'); ?>
                </label>
                <?php
                echo $this->Form->select(
                    'langTo',
                    $langsTo,
                    $notTranslatedInto,
                    array(
                        'class' => 'language-selector',
                        "empty" => false
                    ),
                    false
                );
                ?>
            </fieldset>

            <fieldset class="submit">
                <md-button type="submit" class="md-raised md-primary">
                    <?php echo __('show sentences'); ?>
                </md-button>
            </fieldset>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>

    <div class="section" md-whiteframe="1">
        <h3><?php echo __('Display random sentences'); ?></h3>
        <?php
        $numberOfSentencesWanted = array (10 => 10 , 20 => 20 , 50 => 50, 100 => 100);
        $selectedLanguage = $this->Session->read('random_lang_selected');
        echo $this->Form->create(
            'Sentence',
            array("action" => "several_random_sentences", "type" => "post")
        );
        ?>

        <fieldset class="select">
        <label><?php echo __('Quantity'); ?></label>
        <?php
        echo $this->Form->select(
            'numberWanted',
            $numberOfSentencesWanted,
            5,
            array(
                'empty' => false
            )
        );
        ?>
        </fieldset>

        <fieldset class="select">
        <label><?php echo __('Language'); ?></label>
        <?php
        echo $this->Form->select(
            'into',
            $this->Languages->languagesArrayAlone(),
            $selectedLanguage,
            array(
                'class' => 'language-selector',
                "empty" => false
            ),
            false
        );
        ?>
        </fieldset>

        <fieldset class="submit">
            <md-button type="submit" class="md-raised md-primary">
                <?php echo __('show random sentences'); ?>
            </md-button>
        </fieldset>

        <?php echo $this->Form->end();?>
    </div>
</div>
