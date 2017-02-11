<?php
/**
 * Tatoeba Project, free collaborative creation of languages corpuses project
 * Copyright (C) 2009 Allan SIMON <allan.simon@supinfo.com>
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
 * @author   Allan Simon <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
?>
<h3>
    <?php
    echo format(
        __('Information about {sinograph}'),
        array('sinograph' => $sinogramInformations["glyph"])
    );
    ?>
</h3>

<div id="sinogramGlyph" >
    <?php echo $sinogramInformations["glyph"]; ?>
</div>

<div id="thisSinogramOtherInformations" >
    <div id="SinogramStrokes" >
        <?php echo __('number of strokes:'); ?>
        <?php echo $sinogramInformations["strokes"]; ?>
    </div>

    <div id="pinyin" >
        <?php echo __('pinyin:'); ?>
        <?php echo $sinogramInformations["chin-pinyin"]; ?>
    </div>

    <div id="english" >
        <?php echo __('English:'); ?>
        <?php echo $sinogramInformations["english"]; ?>
    </div>

    <div id="japaneseOn" >
        <?php echo __('Japanese On:'); ?>
        <?php echo $sinogramInformations["jap-on"]; ?>
    </div>

    <div id="japaneseKun" >
        <?php echo __('Japanese Kun:'); ?>
        <?php echo $sinogramInformations["jap-kun"]; ?>
    </div>
</div>

<h3><?php echo __('Example of use'); ?></h3>
<div id="example_part" >
</div>
