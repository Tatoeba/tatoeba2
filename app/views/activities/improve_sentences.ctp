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
    $tagChangeLink = sprintf('<a href="%s">%s</a>', $changeURL, $tagChangeName);
    $tagCheckLink  = sprintf('<a href="%s">%s</a>', $checkURL, $tagCheckName);
    $tagNNCLink    = sprintf('<a href="%s">%s</a>', $nncURL, $tagNeedsNativeCheckName);
    $tagOKLink     = sprintf('<a href="%s">%s</a>', $okURL, $tagOKName);

    $str = sprintf(__('%s: the sentence needs to be changed.', true), $tagChangeLink);
    printf('<li>%s</li>', $str);

    $str = sprintf(__('%s: the sentence needs to be checked.', true), $tagCheckLink);
    printf('<li>%s</li>', $str);

    $str = sprintf(__('%s: the sentence needs to be checked by a native speaker.', true), $tagNNCLink);
    printf('<li>%s</li>', $str);

    $str = sprintf(__('%s: the sentence is considered correct by at least one person.', true), $tagOKLink);
    printf('<li>%s</li>', $str);
    ?>
    </ul>
    </div>
    
    
    <div class="module">
        <h2><?php __('How to help'); ?></h2>
    <ol><?php
        $str = sprintf(__('You need to be an <a href="%s">advanced contributor</a>; '.
                          'otherwise you will not be able to tag sentences.', true),
                       "http://wiki.tatoeba.org/articles/show/faq");
        printf('<li>%s</li>', $str);

        $str = sprintf(__('Whenever you notice a sentence that is wrong or sounds strange, '.
                          'add the tag %s and post a comment to suggest a '.
                          'correction or better phrasing.', true), $tagChangeLink);
        printf('<li>%s</li>', $str);

        $str = sprintf(__('Whenever you notice a possible mistake, add the tag %s '.
                          'and post a comment explaining what you think the '.
                          'mistake may be.', true), $tagCheckLink);
        printf('<li>%s</li>', $str);

        $str = sprintf(__('Whenever you add a sentence in a foreign language and are not '.
                          'completely sure that it is correct, add the tag %s.', true),
                       $tagNNCLink);
        printf('<li>%s</li>', $str);

        $str = sprintf(__('Whenever you can, browse through sentences that are tagged '.
                          '%1$s, %2$s, and %3$s to discuss the sentences with other '.
                          'members and help decide what to do with these sentences.', true),
                       $tagChangeLink, $tagCheckLink, $tagNNCLink);
        printf('<li>%s</li>', $str);

        $str = sprintf(__('Once a problematic sentence has been addressed, tag it %1$s. '.
                          'More generally, you can browse and check others\' sentences '.
                          'and tag them with %1$s. But do this only when you are completely '.
                          'sure that they are correct, and do not use the tag on your own '.
                          'sentences.', true), $tagOKLink);
        printf('<li>%s</li>', $str);
       ?>
    </ol>
    </div>
</div>
