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
 * @author   CK
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo $this->Html->charset(); ?>
        <title>Part of a Frame</title>
        <meta name="robots" content="noindex">
        
        <style>
        .mainSentence .languageFlag{float:left;margin:5px 10px}
        .directTranslation .languageFlag{float:left;margin:0px 10px}
        .mainSentence{font-size:1.5em;}
        .sentence{padding:2px;}
        .sentence a{color:navy;text-decoration: none;}
        .sentence a:hover{color:blue;text-decoration: underline;}
        .translations{font-size: 1.2em;}
        .translations a{color: navy;}
        i{color:#aaa}
        </style>
    </head>
    
    <body>
        <div id="container">
        <?php
        if($this->Session->check('Message.flash')){
            $this->Flash->render();
        }
        echo $content_for_layout;
        ?>
        </div>
    </body>
</html>
