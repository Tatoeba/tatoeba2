<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  Allan SIMON <allan.simon@supinfo.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

echo '<h2>' . __( 'Information for text contributors to Tatoeba project' , true) . '</h2>';

echo '<p>' .__('To grow the commons of free knowledge and free culture, all users contributing to Tatoeba project are required to grant broad permissions to the general public to re-distribute and re-use their contributions freely, as long as the use is attributed and the same freedom to re-use and re-distribute applies to any derivative works. Therefore, for any text you hold the copyright to, by submitting it, you agree to license it under the Creative Commons Attribution/Share-Alike License 2.0 (fr). Please note that this licenses does allow commercial uses of your contributions, as long as such uses are compliant with the terms. ',true) . '</p>';

echo '<p>' . __( 'As an author, you agree to be attributed in any of the following fashions: a) through a hyperlink (where possible) or URL to the
sentence or sentences you contributed to, b) through a hyperlink (where possible) or URL to an alternative, stable online copy which is freely
accessible, which conforms with the license, and which provides credit to the authors in a manner equivalent to the credit given on this website, or
c) through a list of all authors. (Any list of authors may be filtered to exclude very small or irrelevant contributions.)' ,true) . '</p>';

echo '<h3>' . __('Importing text:',true) . '</h3>' ; 

echo '<p>' . __('If you want to import text that you have found elsewhere or that you have co-authored with others, you can only do so if it is available under terms that are compatible with the CC-BY-SA license.',true) . '</p>';

echo '<p>' . __('If you import text under a compatible license which requires attribution, you must, in a reasonable fashion, credit the author(s). Where such credit is commonly given through sentence comments; it is sufficient to give attribution in the edit summary, which is recorded in the sentence history, when importing the text. Regardless of the license, the text you import may be rejected if the required attribution is deemed too intrusive.
Information for non-text media contributors',true) . '</p>';

echo '<h3>' . __('Information for re-users:',true) . '</h3>' ;

echo '<p>' . __('
You can re-use text content from Tatoeba project freely, with the exception of content that is used under "fair use" exemptions, or similar exemptions of copyright law. Please follow the guidelines below:
',true) . '</p>';

echo '<h4>' . __('Re-use of text:',true) . '</h4>' ;

echo '<ul>';
    echo '<li>';
        echo '<p>';
        echo __('Attribution: To re-distribute a text page in any form, provide credit to the authors either by including a) a hyperlink (where
        possible) or URL to the page or pages you are re-using, b) a hyperlink (where possible) or URL to an alternative, stable online copy which is
        freely accessible, which conforms with the license, and which provides credit to the authors in a manner equivalent to the credit given on
        this website, or c) a list of all authors. (Any list of authors may be filtered to exclude very small or irrelevant contributions.) This
        applies to text developed by the Tatoeba project community. Text from external sources may attach additional attribution requirements to the
        work, which we will strive to indicate clearly to you.',true);
        echo '</p>';
    echo '</li>';

    echo '<li>';
        echo '<p>';
        echo __('Copyleft/Share Alike: If you make modifications or additions to the page you re-use, you must license them under the Creative Commons
        Attribution-Share-Alike License 3.0 or later.', true);
        echo '</p>';
    echo '</li>';

    echo '<li>';
        echo '<p>';
        echo __('Indicate changes: If you make modifications or additions, you must indicate in a reasonable fashion that the original work has been
        modified. If you are re-using the sentence in a wiki, for example, indicating this in the page history is sufficient.',true);
 
        echo '</p>';
    echo '</li>';
    
    echo '<li>';
        echo '<p>';
        echo __('  Licensing notice: Each copy or modified version that you distribute must include a licensing notice stating that the work is
        released under CC-BY-SA and either a) a hyperlink or URL to the text of the license or b) a copy of the license. For this purpose, a suitable
        URL is: http://creativecommons.org/licenses/by-sa/2.0/fr/',true);    
        echo '</p>';
    echo '</li>';
    echo '<li>';
        echo '<p>';
        echo __('For further information, please refer to the legal code of the CC-BY-SA License.',true); 
        echo '</p>';
    echo '</li>';
echo '</ul>';

echo '<h3>' . __('Precedence of French terms',true) . '</h3>';

echo '<p>';
echo __('These site terms are not to be modified. If there is any inconsistency between the french terms and any translation into other languages, the
French language version takes precedence.',true);
echo '</p>';

?>
