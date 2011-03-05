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
?>

<h2><?php __('Currently contributing') ?></h2>

<p>
<?php 
echo sprintf(
    __('Users who participated to the last %s contributions.', true),
    $total
);
?>
</p>

<div>
<?php
foreach($currentContributors as $i=>$currentContributor){
    $numberOfContributions = $currentContributor['numberOfContributions'];
    $highestNumberOfContributions = $currentContributors[0]['numberOfContributions'];
    $percentage = ($numberOfContributions/$total)*100;
    ?>
    <div class="currentContributor">
        <div class="activityBar">
        <?php
        $maxActivity = 5;
        $relativeScore = $numberOfContributions/$highestNumberOfContributions;
        $activity = ceil($relativeScore*$maxActivity);
        for ($j = $activity; $j < $maxActivity; $j++) {
            echo '<div class="level0 box"></div>';
        }
        for ($k = $activity; $k > 0; $k--) {
            echo '<div class="level'.$k.' box"></div>';
        }
        // The contributor who has contributed the most in the last contributions
        // will have a level 5 activity. The activity of all other people will be
        // relative to that number one contributor.
        // For instance if the top contributor for the last contributions has made
        // 100 contributions, and I have made 31 contributions, my activity would
        // be 2 (0.31*5 = 1.55).
        ?>
        </div>
    
    
        <div class="image">
            <?php
            echo $members->image(
                $currentContributor['userName'],
                $currentContributor['image']
            );
            ?>
        </div>
        
        
        <div class="username">
            <?php
            echo $html->link($currentContributor['userName'],
                array(
                    "controller" => "contributions",
                    "action" => "of_user",
                    $currentContributor['userName']
                )
            );
        ?>
        </div>
        
        
        <div class="score">
            <?php
            echo ' #'. ($i + 1);
            echo ' - ';
            echo sprintf(__("%s sentences", true), $numberOfContributions);
            echo ' - ';
            echo $percentage.'%';
            ?>
        </div>
    </div>
    <?php
}
?>
</div>