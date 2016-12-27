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

$title = __('Native speakers', true);
$this->set('title_for_layout', $pages->formatTitle($title));
$membersIcons = array(
    'status1' => __('Admins', true),
    'status2' => __('Corpus maintainers', true),
    'status3' => __('Advanced contributors', true),
    'status4' => __('Contributors', true)
);
?>
<div id="annexe_content">
    <div class="module usersLanguagesStats">
        <h2><?php __('Legend'); ?></h2>
        <?php
        foreach ($membersIcons as $iconClass => $tooltip) {
            $icon = $images->svgIcon(
                'user',
                array(
                    'width' => 16,
                    'height' => 16,
                    'class' => $iconClass
                )
            );
            $legend = $html->tag('span', $tooltip);
            echo $html->tag('p', $icon . $legend, array('class' => 'key'));
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
                <th><?php __('Language'); ?></th>
                <?php
                foreach ($membersIcons as $iconClass => $tooltip) {
                    $icon = $images->svgIcon(
                        'user',
                        array(
                            'width' => 16,
                            'height' => 16,
                            'class' => $iconClass
                        )
                    );
                    echo $html->tag('th', $icon, array('title' => $tooltip));
                }
                ?>
                <th><?php __('Total'); ?></th>
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

                $languageIcon = $languages->icon(
                    $langCode, array('width' => 30, 'height' => 20)
                );

                $languageName = $languages->codeToNameAlone($langCode);
                if (empty($langCode)) {
                    $langCode = 'unknown';
                }

                $languageStatusIcon = null;
                if ($numAdmins + $numCorpusMaintainers < 0) { // TODO for Trang
                    $languageStatusIcon = $images->svgIcon(
                        'warning-small',
                        array('width' => 16, 'height' => 16, 'class' => 'status-warning')
                    );
                }

                echo '<tr>';

                echo $html->tag('td', $rank);
                echo $html->tag('td', $languageIcon);
                echo $html->tag('td', $languageName);
                echo $html->tag('td', $numAdmins);
                echo $html->tag('td', $numCorpusMaintainers);
                echo $html->tag('td', $numAdvancedContributors);
                echo $html->tag('td', $numContributors);
                echo $html->tag('td', $total);

                echo '</tr>';

                $rank++;
            }
            ?>
        </table>
    </div>
</div>
