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
 * index view for API.
 *
 * @category API
 * @package  View
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="content-type"
              ontent="application/xhtml+xml;charset=utf-8"/>
        <title>Tatoeba Project API &mdash; Homepage</title>
        <style type="text/css">
/* Reset */
html, body, div, span, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a,
abbr, acronym, address, code, del, dfn, em, img, q, dl, dt, dd, ol, ul, li, fieldset,
form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td
{margin:0;padding:0;border:0;font-weight:inherit;font-style:inherit;font-size:100%;
    font-family:inherit;vertical-align:baseline;}
body {line-height:1.5;}

/* Typography */
body {font-size:75%;color:#222;background:#fff;
    font-family:"Helvetica Neue", Arial, Helvetica, sans-serif;}
h1, h3 {font-weight:normal;letter-spacing:1px;line-height:1;color:#111;
    margin:0.3em 0 0.125em 0;}
h1 {font-size:2.6em;}
h3 {font-size:1.8em;font-style:italic;}
p {margin-bottom:1em;}
a:hover {color:#c33;}
a:active, a:focus {color:#ccc;}
a:visited {color:#99c;}
a {color:#36c;text-decoration:none;position:relative;padding:0.3em 0 .1em 0;}
strong {font-weight:bold;}
ul, ol {margin:0 1.5em 1.5em 1.5em;}
li ul, li ol {margin:0 1.5em;}
ul li { margin-left: .85em; }
ul { list-style-type: disc; }
ul ul { list-style-type: square; }
ul ul ul { list-style-type: circle; }
ol { list-style-position: outside; list-style-type: decimal; }

/* Grid */
.container {width:950px;margin:0 auto;}
.column, div.span-12, div.span-24 {float:left;margin-right:10px;}
.last, div.last {margin-right:0;}
.span-12 {width:470px;}
.span-24, div.span-24 {width:950px;margin:0;}

/* Fixes */
.clearfix:after, .container:after {content:"\0020";display:block;height:0;clear:both;
    visibility:hidden;overflow:hidden;}
.clearfix, .container {display:block;}
.clear {clear:both;}

/* Customs */
form#UserAddForm {

}
        </style>
    </head>
    <body>
        <div class="container">
<?php
if (!$loggedin) {
    echo $form->create(
        'User',
        array(
            'url' => array(
                'controller' => 'api',
                'action' => 'login'
            )
        )
    );
    echo $form->input('username');
    echo $form->input('password');
    echo $form->end('Login');
} else {
    echo $form->create(
        'User',
        array(
            'url' => array(
                'controller' => 'api',
                'action' => 'logout'
            )
        )
    );
    echo $form->end('Logout');
}
?>
        </div>
    </body>
</html>