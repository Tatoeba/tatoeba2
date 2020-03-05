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
 * @link     https://tatoeba.org
 */
use Cake\Core\Configure;

$this->set('title_for_layout', __('Tatoeba: Collection of sentences and translations'));

$registerUrl = $this->Url->build(
    array(
        "controller" => "users",
        "action" => "register"
    )
);
?>

<div layout="row" layout-align="center center" ng-cloak>
<div layout="column" flex="80">

<?php if(!isset($searchProblem)) { ?>
<div layout-margin>
<div layout="column">
    <md-toolbar class="md-hue-2">
        <div class="md-toolbar-tools">
            <h2><?= __('Random sentence'); ?></h2>
        </div>
    </md-toolbar>

    <section ng-cloak>
    <?php
    $sentence = $random;
    $translations = $random->translations;
    $sentenceOwner = $random->user;

    echo $this->element(
        'sentences/sentence_and_translations',
        array(
            'sentence' => $sentence,
            'translations' => $translations,
            'user' => $sentenceOwner
        )
    );
    ?>
    </section>
</div>
</div>
<?php } ?>

<div layout="row" layout-margin>
    <div class="join-us md-whiteframe-1dp" layout="column" flex>
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2><?= __('Want to help?') ?></h2>
            </div>
        </md-toolbar>
        <p>
        <?= __(
            'We are collecting sentences and their translations. '.
            'You can help us by translating or adding new sentences.', true
        ); ?>
        </p>
        <div layout="row" layout-align="center center">
            <md-button class="md-primary" href="<?= $registerUrl; ?>">
                <?php echo __('Join the community'); ?>
                <md-icon>keyboard_arrow_right</md-icon>
            </md-button>
        </div>
    </div>
    
    <div class="stats annexe-menu md-whiteframe-1dp" layout="column" flex>
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2><?= __('Stats')?></h2>
            </div>
        </md-toolbar>
        <?= $this->element('stats/homepage_stats',
                [ 'contribToday' => $contribToday,
                  'numberOfLanguages' => $numberOfLanguages,
                  'numSentences' => $numSentences,
                ],
                [ 'cache' => [ 'key' => 'homepage_stats_'.Configure::read('Config.language') ]]
        ); ?>
    </div>
</div>

</div>
</div>
