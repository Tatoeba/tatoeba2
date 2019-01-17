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
<?php
if ($sentenceFound == false) {
    echo '<div id="noExampleFound" >' ;
    echo format(
        __(
            'No sentence using this character has been found,'.
            ' you can add one <a href="{}">here</a>.',
            true
        ),
        "/pages/contribute"
    );
    echo "</div>\n";
} else {
    ?>
    <div class="sentences_set searchResult">
        <?php
        // TODO add a link to all result or a link to contribute
        // sentence menu (translate, edit, comment, etc)
        // TODO set up a better mechanism
        $specialOptions['belongsTo'] = $sentence['User']['username'];
        $sentence['User']['canEdit'] = $specialOptions['canEdit'];
        $sentence['User']['canLinkAndUnlink'] = $specialOptions['canLinkAndUnlink'];

        $this->Sentences->displaySentencesGroup($sentence);
        ?>
    </div>
    <p>
        <?php
        echo format(
            __(
                'View all sentences using this character <a href="{}">here</a>',
                true
            ),
            "/sentences/search?query=".$sinogram
        );
        ?>
    </p>
    <?php
    //
};
?>

