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

<div id="annexe_content">
    <?php $commonModules->createFilterByLangMod(2); ?> 
    
    
    <?php
    $workload = count($results); 
    if ($workload > 0) {
        $smileys = array('happy', 'smiling', 'puzzled', 'sad', 'crying');
        $max = count($smileys);
        $num = floor($workload / 20);
        if ($num >= $max){
            $num = $max-1;
        }
        ?>
        <div class="module">            
            <h2>Workload (<?php echo $workload; ?>)</h2>
            
            <div class="workloadWrapper">
            <div 
                class="workload <?php echo 'level'.($num+1); ?>" 
                style="width:<?php echo $workload; ?>%;">
            </div>
            </div>
            
            <div class="smiley">
            <?php echo $html->image(IMG_PATH.'smileys/'.$smileys[$num].'.png'); ?>
            </div>
        </div>
        <?php
    }
    ?>
    
    <div class="module">
        <h2>Guidelines</h2>
        <p>@moderators, this page was made for your convenience and shows sentences 
        that were tagged more than two weeks ago for a certain tag. You can use it
        to find out quickly which sentences you can safely
        <?php
        echo $html->link(
            'change', 
            array(
                'action' => 'for_moderators',
                '@change'
            )
        );
        ?>
        or
        <?php
        echo $html->link(
            'delete', 
            array(
                'action' => 'for_moderators',
                '@delete'
            )
        );
        ?>.
        </p>
        <p>Aside of special situations where your common sense will tell you it's 
        better to react as soon as possible, my recommendation is that you use your 
        moderators powers <strong>ONLY</strong> on sentences that appear in 
        here.</p>
        
        <p>NOTE: The maximum number of sentences displayed at a time is limited 
        to 100.</p>
    </div>
</div>

<div id="main_content">
<div class="module">
<h2>
<?php 
echo 'Tagged '.$tagName.' more than 2 weeks ago'; 
?>
</h2>
<?php 
foreach ($results as $result) {
    $sentence = $result['Sentence'];
    $sentences->displayGenericSentence(
        $sentence, 
        null, 
        'mainSentence', 
        false
    );
}
?>
</div>
</div>