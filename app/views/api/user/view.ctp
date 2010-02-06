<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  BEN YAALA Salem <salem.benyaala@gmail.com>
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
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * user/get view for API.
 *
 * @category API
 * @package  View
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

$oXML = new XMLWriter();
$oXML->openURI('php://output');

$oXML->startDocument('1.0');

$oXML->startElement('tatoeba');
$oXML->writeAttribute('version', '1.0');

$oXML->startElement('error');
$oXML->writeAttribute('code', $code);
$oXML->writeAttribute('message', $message);
$oXML->endElement();

$oXML->startElement('user');

$oXML->startElement('id');
$oXML->text($user['User']['id']);
$oXML->endElement();

$oXML->startElement('username');
$oXML->text($user['User']['username']);
$oXML->endElement();

$oXML->startElement('name');
$oXML->text($user['User']['name']);
$oXML->endElement();

$oXML->startElement('image');
$oXML->text($user['User']['image']);
$oXML->endElement();

$oXML->startElement('url');
$oXML->text($user['User']['homepage']);
$oXML->endElement();

$oXML->startElement('email');
$oXML->text($user['User']['email']);
$oXML->endElement();

$oXML->startElement('birthday');
$oXML->text($user['User']['birthday']);
$oXML->endElement();

$oXML->startElement('description');
$oXML->writeAttribute('lang', $user['User']['lang']);
// $oXML->writeAttribute('dir', 'ltr');
$oXML->text($user['User']['description']);
$oXML->endElement();

$oXML->startElement('joined');
$oXML->text($user['User']['since']);
$oXML->endElement();

$oXML->startElement('country');
$oXML->text($user['Country']['name']);
$oXML->endElement();


$oXML->startElement('sentences_count');
$oXML->text(count($user['Sentences']));
$oXML->endElement();

$oXML->startElement('comments_count');
$oXML->text(count($user['SentenceComments']));
$oXML->endElement();

$oXML->startElement('favourites_count');
$oXML->text(count($user['Favorite']));
$oXML->endElement();

$oXML->endElement();

$oXML->endElement();

$oXML->endDocument();

$oXML->flush();
?>