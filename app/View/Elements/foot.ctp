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
 * @link     http://tatoeba.org
 */

if (isset($this->request->params['lang'])) {
    Configure::write('Config.language', $this->request->params['lang']);
}
?>
<div id="footer">

    <div class="container">
    <div class="category">
        <h3><?php echo __('Need some help?'); ?></h3>
        <ul>
            <li>
                <?php
                echo $this->Html->link(
                    __('Quick Start Guide'),
                    'http://en.wiki.tatoeba.org/articles/show/quick-start'
                );
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
                    __('Tatoeba Wiki'),
                    'http://en.wiki.tatoeba.org/articles/show/main'
                );
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
                    __('FAQ'),
                    'http://en.wiki.tatoeba.org/articles/show/faq'
                );
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
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

    <div class="category">
        <h3><?php echo __('Developers'); ?></h3>
        <ul>
            <li>
                <?php
                echo $this->Html->link(
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
                    __('GitHub'),
                    'https://github.com/Tatoeba/tatoeba2',
                    array('target' => '_blank')
                );
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
                    __('Google group'),
                    'https://groups.google.com/forum/#!forum/tatoebaproject'
                );
                ?>
            </li>
        </ul>
    </div>

    <div class="category">
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
                    __('Blog'),
                    'http://blog.tatoeba.org/',
                    array('target' => '_blank')
                );
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
                    __('Twitter'),
                    'http://twitter.com/tatoeba_org',
                    array('target' => '_blank')
                );
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
                    __('Facebook'),
                    'https://www.facebook.com/groups/129340017083187/',
                    array('target' => '_blank')
                );
                ?>
            </li>
        </ul>
    </div>

    <div class="license">
        <a class="cc-by-icon" rel="license"
           href="https://creativecommons.org/licenses/by/2.0/">
            <img alt="Creative Commons License" style="border-width:0"
                 src="https://licensebuttons.net/l/by/2.0/88x31.png" />
        </a>
        <div class="text">
            <?php
            echo __(
                'Our written sentences and translations can be used under the '.
                'Creative Commons Attribution 2.0 license (CC-BY 2.0).'
            );
            echo $this->Html->link(
                __('More information'),
                array(
                    'controller' => 'pages',
                    'action' => 'downloads'
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
                $this->Html->url(
                    array('controller' => 'pages', 'action' => 'donate')
                )
            );
            ?>
        </div>

    </div>
    </div>
</div>
