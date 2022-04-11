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
 * @link     https://tatoeba.org
 */

$this->set('title_for_layout', $this->Pages->formatTitle(__('Improve sentences')));
$changeURL = $this->Url->build(
                       array(
                             'controller' => 'tags',
                             'action' => 'show_sentences_with_tag',
                             $tagChangeId
                             )
                       );
$checkURL = $this->Url->build(
                         array(
                               'controller' => 'tags',
                               'action' => 'show_sentences_with_tag',
                               $tagCheckId
                               )
                         );
$nncURL = $this->Url->build(
                       array(
                             'controller' => 'tags',
                             'action' => 'show_sentences_with_tag',
                             $tagNeedsNativeCheckId
                             )
                       );
$okURL = $this->Url->build(
                      array(
                            'controller' => 'tags',
                            'action' => 'show_sentences_with_tag',
                            $tagOKId
                            )
                      );
?>

<div id="annexe_content">
    <div class="section md-whiteframe-1dp">
    <h2><?php echo __('Related links'); ?></h2>
    <ul>
    <li><a href="http://blog.tatoeba.org/2010/11/tags-guidelines.html"><?php echo __('Tag guidelines'); ?></a></li>
    </ul>
    </div>
</div>

<div id="main_content">
    <div class="section md-whiteframe-1dp">
    <h2><?php echo __('Tags you should know about'); ?></h2>

    <p><?php echo __('The community has come up with several "utility tags" to improve the quality and '.
    'reliability of sentences.'); ?></p>
    <ul>
    <?php
    $tagChangeLink = sprintf('<a href="%s">%s</a>', $changeURL, $tagChangeName);
    $tagCheckLink  = sprintf('<a href="%s">%s</a>', $checkURL, $tagCheckName);
    $tagNNCLink    = sprintf('<a href="%s">%s</a>', $nncURL, $tagNeedsNativeCheckName);
    $tagOKLink     = sprintf('<a href="%s">%s</a>', $okURL, $tagOKName);

    $str = format(__('{tagChange}: the sentence needs to be changed.'),
                  array('tagChange' => $tagChangeLink));
    printf('<li>%s</li>', $str);

    $str = format(__('{tagCheck}: the sentence needs to be checked.'),
                  array('tagCheck' => $tagCheckLink));
    printf('<li>%s</li>', $str);

    $str = format(__('{tagNNC}: the sentence needs to be checked by a native speaker.'),
                  array('tagNNC' => $tagNNCLink));
    printf('<li>%s</li>', $str);

    $str = format(__('{tagOK}: the sentence is considered correct by at least one person.'),
                  array('tagOK' => $tagOKLink));
    printf('<li>%s</li>', $str);
    ?>
    </ul>
    </div>


    <div class="section md-whiteframe-1dp">
        <h2><?php echo __('How to help'); ?></h2>
    <ol><?php
        $str = format(__('You need to be an <a href="{}">advanced contributor</a>; '.
                         'otherwise you will not be able to tag sentences.', true),
                      $this->Pages->getWikiLink('faq'));
        printf('<li>%s</li>', $str);

        $str = format(__('Whenever you notice a sentence that is wrong or sounds strange, '.
                         'add the tag {tagChange} and post a comment to suggest a '.
                         'correction or better phrasing.', true),
                      array('tagChange' => $tagChangeLink));
        printf('<li>%s</li>', $str);

        $str = format(__('Whenever you notice a possible mistake, add the tag {tagCheck} '.
                         'and post a comment explaining what you think the '.
                         'mistake may be.', true),
                      array('tagCheck' => $tagCheckLink));
        printf('<li>%s</li>', $str);

        $str = format(__('Whenever you add a sentence in a foreign language and are not '.
                         'completely sure that it is correct, add the tag {tagNNC}.', true),
                      array('tagNNC' => $tagNNCLink));
        printf('<li>%s</li>', $str);

        $str = format(__('Whenever you can, browse through sentences that are tagged '.
                         '{tagChange}, {tagCheck}, and {tagNNC} to discuss the '.
                         'sentences with other members and help decide what to do with '.
                         'these sentences.', true),
                      array('tagChange' => $tagChangeLink,
                            'tagCheck'  => $tagCheckLink,
                            'tagNNC'    => $tagNNCLink));
        printf('<li>%s</li>', $str);

        $str = format(__('Once a problematic sentence has been addressed, tag it {tagOK}. '.
                         'More generally, you can browse and check others\' sentences '.
                         'and tag them with {tagOK}. But do this only when you are completely '.
                         'sure that they are correct, and do not use the tag on your own '.
                         'sentences.', true), array('tagOK' => $tagOKLink));
        printf('<li>%s</li>', $str);
       ?>
    </ol>
    </div>
</div>
