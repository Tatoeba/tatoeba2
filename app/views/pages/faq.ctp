<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

$this->pageTitle = 'Tatoeba - ' . __('FAQ', true);
?>

<div id="annexe_content">
    <div class="module">
    <h2>Something not clear?</h2>
    <p>If any of the answers here are not clear, let us know, we'll try to reformulate.</p>
    </div>
</div>
    
<div id="main_content">
    <div class="module">
    
    <h2>FAQ</h2>
    
    <a name="indirect-translations"></a>
    <h3><a href="#indirect-translations">Why are some translations in grey?</a></h3>
    <p>Grey translations are <strong>indirect translations</strong>. In other words, they are translations of the translations, and not translations of the main sentence (the main sentence is the sentence in big letters).</p>
    <p>We display them because they can be useful, but you should be careful. Their meaning may differ a little from the main sentence.</p>
    
    
    <a name="delete-sentence"></a>
    <h3><a href="#delete-sentence">How do I delete a sentence?</a></h3>
    <p>At the moment, normal users cannot delete sentences, only moderators can. We will someday add the possibility for users to delete their own sentences, but in the meantime, if you want to have a sentence deleted, <strong>add a comment</strong> on the sentence asking for deletion and explain why you'd like to delete it.</p>
    <p>If you have added something by mistake, rather of asking for it to be deleted, try to <strong>replace it</strong> by a valid sentence.</p>
    
    
    <a name="new-language"></a>
    <h3><a href="#new-language">I'd like to add a new language in Tatoeba, what do I have to do?</a></h3>
    <p><strong>1)</strong> Contact <a href="/user/profile/Pharamp">Pharamp</a> (by <a href="mailto:pharamp@hotmail.it">email</a> or <a href="/private_messages/write/Pharamp">private message</a>) and indicate in the title the language(s) that you would like us to add.</p>
    <p><strong>2)</strong> In your email, tell her what icon we can use for each requested language. It does not necessarily have to be the flag of a country. We just want a picture that people can easily associate to the language. Keep in mind that our icons are only 30x20 pixels.</p>
    <p>You do not have create the icon yourself. For graphical consistency, it's better that we do it. Simply send us (or link us) an image from which we will create the icon.</p>
    <p><strong>3)</strong> Translate 5 sentences into your language(s). Don't worry if the language detection fails. For instance it's possible that the language is detected as Hungarian, but you are not adding a sentence in Hungarian. This is not a problem. You will be able to set the correct language once it's added in Tatoeba.</p>
    <p><strong>4)</strong> Create a public list and name it with the name of your language. Add your 5 translations into that list. You may translate more than 5 sentences if you wish, but always add them into the list, so that we can easily find them!</p>
    <p><strong>5)</strong> Don't hesitate to ask <a href="/user/profile/Pharamp">Pharamp</a> if you don't know how to do something!</p>
    <p><strong>IMPORTANT:</strong> We will only add your language(s) if you have done all of this.</p>
    
    
    <a name="change-language"></a>
    <h3><a href="#change-language">How do change the language of a sentence?</a></h3>
    <p>Click on the flag; a list of languages will appear. Choose the correct language.</p> 
    <p>You can only change the language of sentences that <strong>belong to you</strong>.</p>
    <p>You also have to make sure the sentence which language you want to change is the <strong>main sentence</strong> (and not a translation). If it's a translation, then click on it. It will redirect to a page where the translation becomes the main sentence.</p>
    <p>If your language is not in the list, you can request it to be added (cf. question above).</p>
    
    
    <a name="add-tag"></a>
    <h3><a href="#add-tag">How can I add tags to a sentence?</a></h3>
    <p>You have to be a trusted user (cf. below).</p>
    
    
    <a name="link-unlink"></a>
    <h3><a href="#link-unlink">How can I link or unlink sentences?</a></h3>
    <p>You have to be a trusted user (cf. below).</p>
    
    
    <a name="trusted-user"></a>
    <h3><a href="#trusted-user">How can I become a trusted user?</a></h3>
    <p>You have to <?php echo $html->link('contact Trang', array('controller' => 'private_messages', 'action' => 'write', 'TRANG')); ?> and she will decide if you can be a trusted user or not. One thing she will ask you to do is to read and understand entirely the <a href="http://blog.tatoeba.org/2010/02/how-to-be-good-contributor-in-tatoeba.html">guide of the good contributor</a>, so take the time to read it.</p>
    
    
    <a name="translate-interface"></a>
    <h3><a href="#translate-interface">Can I help translating the website into other languages?</a></h3>
    <p>Yes, thank you for asking! :D</p>
    <p>We currently use Launchpad to manage the translations of the website: <a href="http://translations.launchpad.net/tatoeba">http://translations.launchpad.net/tatoeba</a></p>
    <p>NOTE: Tatoeba is updating frequently (pretty much every week), and some texts in there may be (or quickly become) outdated. Also, the texts in Launchpad and the texts in Tatoeba are not always synchronized. We synchronize them only when there has been significant modifications.</p>
    
    </div>
</div>