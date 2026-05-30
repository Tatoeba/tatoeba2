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

/**
 * Display latest contributions.
 *
 * @category Contributions
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

$this->set('title_for_layout', $this->Pages->formatTitle(__("Latest contributions")));
?>

<div id="annexe_content">
    <?php $this->CommonModules->createFilterByLangMod(); ?>

    <?php
    echo $this->element(
        'currently_active_members',
        array(
            'currentContributors' => $currentContributors
        )
    );
    ?>
</div>

<div id="main_content">
    <section>
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2 flex><?= __('Latest contributions'); ?></h2>
            </div>
        </md-toolbar>
        
        <md-list id="logs" class="md-whiteframe-1dp">
        <?php
        $this->Logs->obsoletize($contributions);
        foreach ($contributions as $contribution) {
            echo $this->element('logs/log_entry', array(
                'log' => $contribution,
                'type' => 'sentence'
            ));
        }
        ?>
        </md-list>
    </section>
</div>
