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
 
$this->pageTitle = __('Improve sentences', true);
$changeLink = $html->link(
    '@change', 
    array(
        'controller' => 'tags',
        'action' => 'show_sentences_with_tag',
        '@change'
    )
);
$checkLink = $html->link(
    '@check', 
    array(
        'controller' => 'tags',
        'action' => 'show_sentences_with_tag',
        '@check'
    )
);
$nncLink = $html->link(
    '@Needs Native Check', 
    array(
        'controller' => 'tags',
        'action' => 'show_sentences_with_tag',
        '@Needs_Native_Check'
    )
);
$okLink = $html->link(
    'OK', 
    array(
        'controller' => 'tags',
        'action' => 'show_sentences_with_tag',
        'OK'
    )
);
?>


<div id="annexe_content">
    <div class="module">
    <h2>Related links</h2>
    <ul>
        <li><a href="http://blog.tatoeba.org/2010/11/tags-guidelines.html">Tag guidelines</a></li>
    </ul>
    </div>
</div>

<div id="main_content">
    <div class="module">
    <h2>Tags you should know about</h2>
    
    <p>The community has come up with a few "utility tags" to improve the quality and
    the reliability of the sentences.</p>
    <ul>
        <li><?php echo $changeLink; ?> - The sentence needs to be changed.</li>
        <li><?php echo $checkLink; ?> - The sentence needs to be checked.</li>
        <li><?php echo $nncLink; ?> - The sentence needs to be checked by a native 
        speakers.</li>
        <li><?php echo $okLink; ?> - The sentence is considered correct by at least
        one person.</li>
    </ul>
    </div>
    
    
    <div class="module">
    <h2>How to help</h2>
    <ol>
        <li>You need to be a <a href="/faq#trusted-user">trusted user</a>; 
        otherwise you won't be able to tag sentences.</li>
        <li>Whenever you notice a sentence that is wrong or sounds really strange, 
        add the tag <?php echo $changeLink; ?> and post a comment to suggest a 
        correction or better phrasing.</li>
        <li>Whenever you notice a possible mistake, add the tag 
        <?php echo $checkLink; ?> and post a comment explaining what you think the 
        mistake may be.</li>
        <li>Whenever you add sentences in a foreign language and aren't completely
        sure they are correct, add the tag <?php echo $nncLink; ?>.</li>
        <li>Whenever you can, browse through sentences that are tagged 
        <?php echo $changeLink; ?>,  <?php echo $checkLink; ?>, 
        <?php echo $nncLink; ?> to discuss the sentences with other
        members and help decide what to do with these sentences.</li>
        <li>Once the case of a problematic sentence has been solved, tag it
        <?php echo $okLink; ?>. More generally, you can browse and check other's 
        sentences, and tag them with <?php echo $okLink; ?> to indicate you consider 
        they are correct. But do this when you are 100% sure that the sentence
        is correct, and don't use the tag on your own sentences because it's not
        very useful.</li>
    </ol>
    </div>
</div>