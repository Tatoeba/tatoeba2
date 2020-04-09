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
 * Activity timeline for contributions. It displays for each day the number of new
 * sentences.
 *
 * @category Contributions
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

$this->set('title_for_layout', $this->Pages->formatTitle(__("Activity timeline")));

$monthName = $this->Date->monthName($month);

$maxWidth = 400;
if (!empty($stats)) {
    $maxTotal = max(array_column($stats, 'total'));
}
?>

<div id="annexe_content">
    <?php
    echo $this->element(
        'calendar',
        array(
            'currentYear' => $year,
            'currentMonth' => $month
        )
    );
    ?>
</div>

<div id="main_content">
    <div class="section md-whiteframe-1dp">
    <h2><?php
        echo format(__('Activity timeline — {month} {year}'), array('month' => $monthName, 'year' => $year));
    ?></h2>
    <p>
    <?php
        if ($month == date('m') && $year == date('Y')) {
            echo __('Statistics about the number of contributions over this month.');
        } else {
            echo format(__('Statistics about the number of contributions over {month} {year}.'),
                        array('month' => $monthName, 'year' => $year));
        }
    ?>
    </p>

    <?php
    echo '<table id="timeline">';

    $currentDate = null;
    $totalSentences = 0;
    $totalLinks = 0;
    $numberOfDays = 0;
    ?>

    <thead>
        <tr>
            <?php /* @translators: first column header on the Activity timeline page */ ?>
            <td class="date"><?= __('Day'); ?></td>
            <td>
                <div layout="row">
                    <div flex="25" class="added"><?= __('Sentences added'); ?></div>
                    <div flex="25" class="linked"><?= __('Links added'); ?></div>
                    <div flex="25" class="unlinked"><?= __('Links removed'); ?></div>
                    <div flex="25" class="deleted"><?= __('Sentences removed'); ?></div>
                </div>
            </td>
        </tr>
    </thead>

    <?php
    foreach ($stats as $date => $stat) {
        $bar = '';
        $bar .= $this->ContributionsStats->statBar($stat, 'added', $maxTotal);
        $bar .= $this->ContributionsStats->statBar($stat, 'linked', $maxTotal);
        $bar .= $this->ContributionsStats->statBar($stat, 'unlinked', $maxTotal);
        $bar .= $this->ContributionsStats->statBar($stat, 'deleted', $maxTotal);
        $totalSentences += $stat['added'] ?? 0;
        $totalLinks += $stat['linked'] ?? 0;

        $formattedDate = $this->Time->i18nFormat($date, [IntlDateFormatter::SHORT, IntlDateFormatter::NONE]);
        echo '<tr>';
        echo $this->Html->tag('td', $formattedDate, array('class' => 'date'));
        $contents = '<div layout="row">' . $bar . '</div>';
        echo $this->Html->tag('td', $contents, array('class' => 'bar'));
        echo '</tr>';
    }
    echo '</table>';

    if( $month == date('m') && $year == date('Y')) {
        $numberOfDays = date('d');
    } else if (($year < date('Y')) || ($year == date('Y') && $month < date('m'))){
        $numberOfDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    if($numberOfDays > 0){
        $dailyAverageSentences = round($totalSentences / $numberOfDays,1);
        $averageSentencesString = format(
            __n(
                'Daily Average: {n} sentence added',
                'Daily Average: {n} sentences added',
                $dailyAverageSentences,
                true
            ),
            array('n' => $this->Number->format($dailyAverageSentences))
        );
        $dailyAverageLinks = round($totalLinks / $numberOfDays,1);
        $averageLinksString = format(
            __n(
                ', {n} link added',
                ', {n} links added',
                $dailyAverageLinks,
                true
            ),
            array('n' => $this->Number->format($dailyAverageLinks))
        );
        echo $this->Html->div("daily-average", $averageSentencesString . $averageLinksString);
    }

    ?>
    </div>
</div>
