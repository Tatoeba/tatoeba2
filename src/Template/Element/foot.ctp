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
?>
<div id="footer" layout="column" ng-cloak>

    <div layout="row" layout-align="center center" flex layout-margin ng-controller="MenuController">
        <?= $this->element('ui_language_button'); ?>
    </div>

    <div layout="row" layout-xs="column" layout-align="space-between start">
    <div class="category" flex-gt-xs>
        <h3><?php echo __('Need some help?'); ?></h3>
        <ul>
            <li>
                <?php
                echo $this->Html->link(
                    __('Quick Start Guide'),
                    $this->Pages->getWikiLink('quick-start')
                );
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
                    __('Tatoeba Wiki'),
                    $this->Pages->getWikiLink('main')
                );
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
                    /* @translators: link text in the footer */
                    __x('footer', 'FAQ'),
                    $this->Pages->getWikiLink('faq')
                );
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
                    /* @translators: link text to the Help page in the footer (noun) */
                    __('Help'),
                    array(
                        "controller" => "pages",
                        "action" => "help"
                    )
                );
                ?>
            </li>
        </ul>
    </div>
    
    <div class="category" flex-gt-xs>
        <?php /* @translators: section name in the footer */ ?>
        <h3><?php echo __('Developers'); ?></h3>
        <ul>
            <li>
                <?php
                echo $this->Html->link(
                    /* @translators: link text to the Downloads page in the footer (noun) */
                    __('Downloads'),
                    array(
                        "controller" => "pages",
                        "action" => "downloads"
                    )
                );
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
                    /* @translators: link text to the Tatoeba GitHub page in the footer (noun) */
                    __('GitHub'),
                    'https://github.com/Tatoeba/tatoeba2',
                    array('target' => '_blank')
                );
                ?>
            </li>
        </ul>
    </div>
    
    <div class="category" flex-gt-xs>
        <?php /* @translators: section name in the footer */ ?>
        <h3><?php echo __('About'); ?></h3>
        <ul>
            <li>
                <?php
                echo $this->Html->link(
                    __('What is Tatoeba?'),
                    array(
                        "controller" => "pages",
                        "action" => "about"
                    )
                );
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
                    __('Contact us'),
                    array(
                        "controller" => 'pages',
                        "action" => 'contact'
                    )
                );
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
                   /* @translators: link text to the Status page in the footer (noun) */
                    __('Status'),
                    'https://status.tatoeba.org'
                );
                ?>
            </li>
            <li>
                <?php
                /* @translators: link text to the Terms of use page in the footer (noun) */
                echo $this->Html->link(
                    __('Terms of use'),
                    array(
                        "controller" => 'pages',
                        "action" => 'terms_of_use'
                    )
                );
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
                    /* @translators: link text to the Blog page in the footer (noun) */
                    __('Blog'),
                    'http://blog.tatoeba.org/',
                    array('target' => '_blank')
                );
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
                    /* @translators: link text to the Tatoeba Twitter account in the footer (noun) */
                    __('Twitter'),
                    'http://twitter.com/tatoeba_org',
                    array('target' => '_blank')
                );
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
                    /* @translators: link text to the Tatoeba Facebook page in the footer (noun) */
                    __('Facebook'),
                    'https://www.facebook.com/groups/129340017083187/',
                    array('target' => '_blank')
                );
                ?>
            </li>
        </ul>
    </div>
    </div>

    <div layout="row" layout-align="center center" class="license">
        <img alt="Creative Commons License" src="/img/cc-logo.png" />
        <div class="text">
            <?php
            echo __(
                'Our data is released under various Creative Commons licenses.'
            );
            echo $this->Html->link(
                /* @translators: link text (in the footer) to the section 6 of the Terms of use page */
                __('More information'),
                array(
                    'controller' => 'pages',
                    'action' => 'terms_of_use#section-6'
                ),
                array(
                    'class' => 'more-info'
                )
            );
            ?>
            <br/>
            <?php
            echo format(
                __(
                    'If you love this content, please consider a '.
                    '<a href={}>donation</a>.', true
                ),
                $this->Url->build(
                    array('controller' => 'pages', 'action' => 'donate')
                )
            );
            ?>
        </div>
    </div>
</div>
