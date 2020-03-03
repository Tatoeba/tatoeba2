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
$selectedMonth = format(
    __('{month} {year}'),
    array('month' => $monthName, 'year' => $year)
);

$maxWidth = 400;
$maxTotal = max(array_column($stats, 'total'));
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
    <h2><?php echo $selectedMonth; ?></h2>
    <div id="timeline_legend" layout="row" layout-xs="column" layout-align="space-around">
        <div class="added">Number of added sentences</div>
        <div class="linked">Number of linked sentences</div>
        <div class="unlinked">Number of unlinked sentences</div>
        <div class="deleted">Number of deleted sentences</div>
    </div>

    <?php
    echo '<table id="timeline">';

    $currentDate = null;
    $totalSentences = 0;
    $numberOfDays = 0;

    foreach ($stats as $date => $stat) {
        $numSentences = $stat['total'];
        $bar = '';
        if(isset($stat['added'])){
            $width = ($stat['added'] / $maxTotal) * 100;
            $bar .= $this->Html->div('logs_stats added', $stat['added'],
                array('style' => 'width:'.$width.'%')
            );
        }
        if(isset($stat['linked'])){
            $width = ($stat['linked'] / $maxTotal) * 100;
            $bar .= $this->Html->div('logs_stats linked', $stat['linked'],
                array('style' => 'width:'.$width.'%')
            );
        }
        if(isset($stat['unlinked'])){
            $width = ($stat['unlinked'] / $maxTotal) * 100;
            $bar .= $this->Html->div('logs_stats unlinked', $stat['unlinked'],
                array('style' => 'width:'.$width.'%')
            );
        }
        if(isset($stat['deleted'])){
            $width = ($stat['deleted'] / $maxTotal) * 100;
            $bar .= $this->Html->div('logs_stats deleted', $stat['deleted'],
                array('style' => 'width:'.$width.'%')
            );
        }

        $formattedDate = $this->Time->i18nFormat($date, [IntlDateFormatter::SHORT, IntlDateFormatter::NONE]);
        echo '<tr>';
        echo $this->Html->tag('td', $formattedDate, array('class' => 'date'));
        echo $this->Html->tag('td', $this->Number->format($numSentences), array('class' => 'number'));
        echo $this->Html->tag('td', $bar, array('class' => 'bar'));
        echo '</tr>';

        $totalSentences += $numSentences;
    }
    echo '</table>';

    if( $month == date('m') && $year == date('Y')) {
        $numberOfDays = date('d');
    } else if (($year < date('Y')) || ($year == date('Y') && $month < date('m'))){
        $numberOfDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    if($numberOfDays > 0){
        $dailyAverage = round($totalSentences / $numberOfDays,1);
        $averageString = format(
            __n(
                'Daily Average: {n} sentence',
                'Daily Average: {n} sentences',
                $dailyAverage,
                true
            ),
            array('n' => $this->Number->format($dailyAverage))
        );
        echo $this->Html->div("daily-average", $averageString);
    }

    ?>
    </div>
</div>
