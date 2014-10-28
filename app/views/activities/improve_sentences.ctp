<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2011  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 
$this->set('title_for_layout', $pages->formatTitle(__('Improve sentences', true)));
$changeURL = $html->url(
                       array(
                             'controller' => 'tags',
                             'action' => 'show_sentences_with_tag',
                             $tagChangeId
                             )
                       );
$checkURL = $html->url(
                         array(
                               'controller' => 'tags',
                               'action' => 'show_sentences_with_tag',
                               $tagCheckId
                               )
                         );
$nncURL = $html->url(
                       array(
                             'controller' => 'tags',
                             'action' => 'show_sentences_with_tag',
                             $tagNeedsNativeCheckId
                             )
                       );
$okURL = $html->url(
                      array(
                            'controller' => 'tags',
                            'action' => 'show_sentences_with_tag',
                            $tagOKId
                            )
                      );
?>

<div id="annexe_content">
    <div class="module">
    <h2><?php __('Related links'); ?></h2>
    <ul>
    <li><a href="http://blog.tatoeba.org/2010/11/tags-guidelines.html"><?php __('Tag guidelines'); ?></a></li>
    </ul>
    </div>
</div>

<div id="main_content">
    <div class="module">
    <h2><?php __('Tags you should know about'); ?></h2>
    
    <p><?php __('The community has come up with several "utility tags" to improve the quality and '.
    'reliability of sentences.'); ?></p>
    <ul>
    <?php 
    echo sprintf(__('<li><a href="%s">%s</a> - The sentence needs to be changed.</li>', true), $changeURL, $tagChangeName);
    echo sprintf(__('<li><a href="%s">%s</a> - The sentence needs to be checked.</li>', true), $checkURL, $tagCheckName);
    echo sprintf(__('<li><a href="%s">%s</a> - The sentence needs to be checked by a native speaker.</li>', true), $nncURL, $tagNeedsNativeCheckName);
    echo sprintf(__('<li><a href="%s">%s</a> - The sentence is considered correct by at least one person.</li>', true), $okURL, $tagOKName);
    ?>
    </ul>
    </div>
    
    
    <div class="module">
        <h2><?php __('How to help'); ?></h2>
    <ol><?php
        echo sprintf(__('<li>You need to be an <a href="%s">advanced contributor</a>; '.
                        'otherwise you will not be able to tag sentences.</li>', true), 
                        "http://wiki.tatoeba.org/articles/show/faq");
        echo sprintf(__('<li>Whenever you notice a sentence that is wrong or sounds strange, '.
                        'add the tag <a href="%s">%s</a> and post a comment to suggest a '.
                        'correction or better phrasing.</li>', true), $changeURL, $tagChangeName);
        echo sprintf(__('<li>Whenever you notice a possible mistake, add the tag '.
                        '<a href="%s">%s</a> and post a comment explaining what you think the '.
                        'mistake may be.</li>', true), $checkURL, $tagCheckName);
        echo sprintf(__('<li>Whenever you add a sentence in a foreign language and are not completely '.
                        'sure that it is correct, add the tag <a href="%s">%s</a>.</li>', true), $nncURL, $tagNeedsNativeCheckName);
        echo sprintf(__('<li>Whenever you can, browse through sentences that are tagged '.
                        '<a href="%s">%s</a>, <a href="%s">%s</a>, and <a href="%s">%s</a> '.
                        'to discuss the sentences with other '.
                        'members and help decide what to do with these sentences.</li>', true), 
                        $changeURL, $tagChangeName, $checkURL, $tagCheckName, $nncURL, $tagNeedsNativeCheckName);
        echo sprintf(__('<li>Once a problematic sentence has been addressed, tag it '.
                        '<a href="%s">%s</a>. More generally, you can browse and check others\' sentences '.
                        'and tag them with <a href="%s">%s</a>. But do this only when you are completely sure that they '.
                        'are correct, and do not use the tag on your own sentences.</li>', true), $okURL, $tagOKName, $okURL, $tagOKName);
       ?>
    </ol>
    </div>
</div>
