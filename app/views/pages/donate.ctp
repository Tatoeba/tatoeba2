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

$this->set('title_for_layout', $pages->formatTitle(__('Donate', true)));
?>

<div id="annexe_content">
    <div class="module">
        <h2><?php __("Any question?"); ?></h2>
        <p>
        <?php
        echo format(__(
            "If you have any question regarding donations or if you would ".
            "like to donate in any other way than the ones mentioned ".
            'here, you may contact either <a href="{trangEmail}">Trang</a> '.
            'or the entire <a href="{teamEmail}">team</a>.', true
            ),
            array('trangEmail' => 'mailto:trang@tatoeba.org',
                  'teamEmail' =>'mailto:team@tatoeba.org')
        );
        ?>
        </p>
    </div>

    <div class="module">
        <h2><?php __("Latest donations"); ?></h2>
        <ol>
            <li>Stefanello - 20 €</li>
            <li>Roberto  - 5 €</li>
            <li>William - 40 €</li>
            <li>Xung - 100 €</li>
            <li>Dmitriy - 5 €</li>
        </ol>
        <?php echo $html->link(
            __("All donations", true),
            'http://en.wiki.tatoeba.org/articles/show/donations'
        ); ?>
    </div>
</div>


<div id="main_content">
    <div class="module">
        <h2><?php __("How to donate"); ?></h2>
        <p><?php __("You have two possible ways to make a donation:"); ?></p>
        <ul>
            <li>
            <?php
            __(
                "<b>IBAN transfer.</b> This is probably the cheapest way to donate. ".
                "Tatoeba's bank is based in France, so if you are from an EU ".
                "country, it is likely that the transfer will be free of charge."
            );
            ?>
            </li>
            <li>
            <?php
            __(
                "<b>PayPal.</b> If you can't transfer money with IBAN, ".
                "then you can donate with PayPal. You should ".
                "know that PayPal takes a certain percentage of the donation."
            )
            ?>
            </li>
        </ul>
    </div>

    <div class="module">
        <h2><?php __("IBAN transfer"); ?></h2>
        <b><?php __("Titular:"); ?></b> ASSOCIATION TATOEBA<br/>
        <b><?php __("IBAN:"); ?></b> FR76 3000 3013 8100 0506 7048 647<br/>
        <b><?php __("BIC/SWIFT:"); ?></b> SOGEFRPP<br/>
    </div>

    <div class="module">
        <h2>Paypal</h2>

        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="WVCAMBJVDXAPE">
            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG_global.gif" name="submit" alt="PayPal – The safer, easier way to pay online.">
            <img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
        </form>
    </div>
</div>
