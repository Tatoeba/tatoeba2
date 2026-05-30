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
 * @link     https://tatoeba.org
 */
?>

<md-list class="annexe-menu md-whiteframe-1dp">
<md-subheader><?php echo __('Currently contributing') ?></md-subheader>

<p class="hint" style="padding: 0 10px">
<?php 
echo format(
    __n('User who did the last contribution.','Users who participated in the last {n}&nbsp;contributions.', $total),
    array('n' => $this->Number->format($total))
);
?>
</p>

<?php
$highestNumberOfContributions = $currentContributors ? $currentContributors[0]->total : 0;
foreach($currentContributors as $i=>$currentContributor){
    $numberOfContributions = $currentContributor->total;
    $percentage = ($numberOfContributions/$total)*100;
    $username = $currentContributor->user->username;
    $url = $this->Url->build([
        'controller' => 'contributions',
        'action' => 'of_user',
        $username
    ]);
    ?>
    <md-list-item class="md-2-line" href="<?= $url ?>">
        <?= $this->Members->image($currentContributor->user, array('class' => 'md-avatar')); ?>
        <div class="md-list-item-text" layout="column">
            <h3><?= $username ?></h3>
            <p>
            <?php
            echo format(
                __n('{n}&nbsp;sentence', '{n}&nbsp;sentences', $numberOfContributions, true),
                ['n' => $this->Number->format($numberOfContributions)]
            );
            ?>
            </p>
        </div>

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
    </md-list-item>
    <?php
}
?>
</md-list>
