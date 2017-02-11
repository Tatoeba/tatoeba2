<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

$title = __('Native speakers');
$this->set('title_for_layout', $this->Pages->formatTitle($title));
$membersIcons = array(
    'status1' => __('Admins'),
    'status2' => __('Corpus maintainers'),
    'status3' => __('Advanced contributors'),
    'status4' => __('Contributors')
);
?>
<div id="annexe_content">
    <div class="module usersLanguagesStats">
        <h2><?php echo __('Legend'); ?></h2>
        <?php
        foreach ($membersIcons as $iconClass => $tooltip) {
            $icon = $this->Images->svgIcon(
                'user',
                array(
                    'width' => 16,
                    'height' => 16,
                    'class' => $iconClass
                )
            );
            $legend = $this->Html->tag('span', $tooltip);
            echo $this->Html->tag('p', $icon . $legend, array('class' => 'key'));
        }
        ?>
    </div>
</div>

<div id="main_content">
    <div class="module">
        <h2><?php echo $title ?></h2>

        <table class="languages-stats">
            <tr>
                <th></th>
                <th></th>
                <th><?php echo __('Language'); ?></th>
                <?php
                foreach ($membersIcons as $iconClass => $tooltip) {
                    $icon = $this->Images->svgIcon(
                        'user',
                        array(
                            'width' => 16,
                            'height' => 16,
                            'class' => $iconClass
                        )
                    );
                    echo $this->Html->tag('th', $icon, array('title' => $tooltip));
                }
                ?>
                <th><?php echo __('Total'); ?></th>
            </tr>

            <?php
            $rank = 1;
            foreach ($stats as $stat) {
                $language = $stat['Language'];

                $langCode = $language['code'];

                $numAdmins = $language['group_1'];
                $numCorpusMaintainers = $language['group_2'];
                $numAdvancedContributors = $language['group_3'];
                $numContributors = $language['group_4'];
                $total = $stat[0]['total'];

                $languageIcon = $this->Languages->icon(
                    $langCode, array('width' => 30, 'height' => 20)
                );

                $languageName = $this->Languages->codeToNameAlone($langCode);
                if (empty($langCode)) {
                    $langCode = 'unknown';
                }

                $languageStatusIcon = null;
                if ($numAdmins + $numCorpusMaintainers < 0) { // TODO for Trang
                    $languageStatusIcon = $this->Images->svgIcon(
                        'warning-small',
                        array('width' => 16, 'height' => 16, 'class' => 'status-warning')
                    );
                }

                echo '<tr>';

                echo $this->Html->tag('td', $rank);
                echo $this->Html->tag('td', $languageIcon);
                echo $this->Html->tag('td', $languageName);
                echo $this->Html->tag('td', $numAdmins);
                echo $this->Html->tag('td', $numCorpusMaintainers);
                echo $this->Html->tag('td', $numAdvancedContributors);
                echo $this->Html->tag('td', $numContributors);
                echo $this->Html->tag('td', $total);

                echo '</tr>';

                $rank++;
            }
            ?>
        </table>
    </div>
</div>
