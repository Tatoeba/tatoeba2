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

$session = $this->request->session();
$currentLanguage = $session->read('browse_sentences_in_lang');
$notTranslatedInto = $session->read('not_translated_into_lang');
if (empty($currentLanguage)) {
    $currentLanguage = $session->read('random_lang_selected');
}
if (empty($notTranslatedInto)) {
    $notTranslatedInto = 'none';
}
$langsFrom = $this->Languages->profileLanguagesArray(false, false);
$langsTo = $this->Languages->profileLanguagesArray(false, false, true, true);
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
    echo __(
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
        <? if (CurrentUser::isMember() && count($langsFrom) < 2) { ?>
        <div class="warning">
            <?= format(
                __(
                    'Translating sentences implies that you know at least two languages. '.
                    'Please complete <a href="{}">your profile</a> to indicate at least two languages.'
                ),
                $this->Url->build(
                    array(
                        'controller' => 'user', 
                        'action' => 'profile',
                        CurrentUser::get('username')
                    )
                )
            ); ?>
        </div>
        <? } ?>
        <h3><?php echo __('Search for untranslated sentences'); ?></h3>
        <div>
            <?php
            echo $this->Form->create(
                'Activity',
                array(
                    "url" => array("action" => "translate_sentences"),
                    "type" => "get"
                )
            );
            ?>
            <fieldset class="select">
                <label for="ActivityLangFrom">
                    <?php echo __('Sentences in:'); ?>
                </label>
                <?php
                echo $this->Form->select(
                    'langFrom',
                    $langsFrom,
                    array(
                        'value' => $currentLanguage,
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
                    array(
                        'value' => $notTranslatedInto,
                        'class' => 'language-selector',
                        "empty" => false
                    ),
                    false
                );
                ?>
                <p class="hint">
                    <?= __('Best practice: translate only into your native language.') ?>
                </p>
            </fieldset>

            <fieldset class="select">
                <input type="radio" name="sort" value="{{sort}}" checked hidden
                       ng-init="sort = 'created'"/>
                <label>
                    <?= __('Order:'); ?>
                </label>
                <md-radio-group ng-model='sort'>
                    <md-radio-button value='created' class='md-primary'>
                        <?= __('Last created first') ?>
                    </md-radio-button>
                    <md-radio-button value='modified' class='md-primary'>
                        <?= __('Last modified first') ?>
                    </md-radio-button>
                    <md-radio-button value='words' class='md-primary'>
                        <?= __('Fewest words first') ?>
                    </md-radio-button>
                    <md-radio-button value='random' class='md-primary'>
                        <?= __('Random') ?>
                    </md-radio-button>
                </md-radio-group>
            </fieldset>
            

            <fieldset class="submit">
                <md-button type="submit" class="md-raised md-primary">
                    <?php echo __('show sentences'); ?>
                </md-button>
            </fieldset>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>

</div>
