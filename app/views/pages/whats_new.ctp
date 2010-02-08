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
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

$this->pageTitle = __("Tatoeba : What's new", true);
?>

<div id="annexe_content">
    <div class="module">
        <p id="whatsNew">
        <?php
        __(
            'You can follow the evolution of the project on '.
            '<a class="twitterLink" target="_blank" '.
            'href="http://twitter.com/tatoeba_project">Twitter</a>.'
        );
        ?>
        </p>
    </div>
</div>

<div id="main_content">
    <div class="module">
        <h2><?php __('What\'s new'); ?></h2>


        <div>
        <?php
        // retrieve data...
        $curl = curl_init();
        curl_setopt(
            $curl,
            CURLOPT_URL,
            "http://twitter.com/statuses/user_timeline.xml?screen_name=tatoeba_project"
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);

        // parse xml
        $xml = simplexml_load_string($result);
        if (!empty($xml)) {
            $fieldsWeWant = array('created_at', 'text');
            foreach ($xml->children() as $child) { // <status>
                echo '<div class="twit">';
                foreach ($child->children() as $twit) {
                    $class = $twit->getName();
                    if (in_array($class, $fieldsWeWant)) {
                        echo '<div class="'.$class.'">';
                        echo $twit;
                        echo '</div>';
                    }
                }
                echo '</div>';
            }
        }
        ?>
        </div>

        <hr/>
        <pre>
        Below is the description of Tatoeba and its features on July 12th, 2009.
        </pre>

        <h2>Quick description of the sections in the menu</h2>

        <h4>HOME</h4>
        <ul>
            <li>
                Displays number of sentences in the five most important languages
            </li>
            <li>Displays a random sentence.</li>
            <ul>
                <li>Clicking on "show another" will load a new random sentence.</li>
            </ul>
            <li>Displays the 10 last contributions.</li>
            <ul>
                <li>
                    Clicking on "show more" will lead to the list 
                    of the 200 last contributions.
                </li>
                <li>
                    Clicking on "show activity timeline" will show the number 
                    of contributions for each day since January 1st, 2008.
                </li>
            </ul>
            <li>
                Displays the 5 latest comments (each comment is linked 
                to a specific sentence).
            </li>
            <ul>
                <li>
                    Clicking on the title of a comment will show all 
                    the comments on the sentence.
                </li>
                <li>
                    Clicking on the username of the member will lead to 
                    the information page of that user.
                </li>
            </ul>
        </ul>

        <h4>BROWSE</h4>
        <ul>
            <li>
                Displays a random sentence if we get there through the "Browse" 
                link in the menu.
            </li>
            <ul>
                <li>Clicking on "previous" will display the previous sentence.</li>
                <li>Clicking on "random" will display another random senetnce.</li>
                <li>Clicking on "next" will display the next sentence.</li>
                <li>
                    It's possible to go to a specific sentence by submitting 
                    its id next to "Show sentence nº".
                </li>
            </ul>
        </ul>

        <h4>SEARCH</h4>
        <ul>
            <li>Displays explanations about the search.</li>
            <ul>
                <li>
                    Clicking on each example will trigger the search in question.
                </li>
            </ul>
        </ul>

        <h4>CONTRIBUTE</h4>
        <ul>
            <li>Enables members to add a new sentence.</li>
            <ul>
                <li>
                    After the sentence is added, the user is redirected to 
                    the "Browse" section, which will be displaying the sentence 
                    the user just added.
                </li>
            </ul>
            <li>Displays a random sentence to translate.</li>
            <ul>
                <li>
                    After the translation is added, the user is also redirected 
                    to the "Browse" section, which will be displaying the sentence 
                    from which the user translated.
                </li>
                <li>
                    Clicking on "show another" will display another sentence 
                    to translate.
                </li>
            </ul>
        </ul>

        <h4>COMMENTS</h4>
        <ul>
            <li>Displays the last 10 comments in each language.</li>
        </ul>

        <h4>MEMBERS</h4>
        <ul>
            <li>
                Displays the list of the members of Tatoeba Project, with the date 
                since when they are members.
            </li>
            <ul>
                <li>
                    Clicking on a username in the list will lead to 
                    the information page of the user.
                </li>
                <li>
                    It's also possible to display the information page of a user 
                    by entering a username in the text input.
                </li>
                <li>
                    Clicking on "random" will show the information page 
                    of a random user.
                </li>
                <li>Clicking on "all" will show the whole list.</li>
            </ul>
        </ul>

        <h2>Other pages</h2>

        <h4>Edit my information</h4>
        <ul>
            <li>Users can change their email and password.</li>
        </ul>

        <h4>Romaji &amp; Furigana</h4>
        <ul>
            <li>Converts Japanese text into romaji or hiragana.</li>
        </ul>

        <h4>Downloads</h4>
        <ul>
            <li>
                Anyone can download the files with the sentences. 
                It hasn't been updated for a while though...
            </li>
        </ul>


        <h2>Summary of what you can do...</h2>

        <ul>
            <li>Browse sentences</li>
            <ul>
                <li>This displays a sentence and its translations.</li>
                <li>You can browse by id from the "Browse" section.</li>
                <li>
                    Or browse randomly from the homepage and the "Browse" section.
                </li>
            </ul>

            <li>Search sentences</li>
            <ul>
                <li>
                    You can search sentences from the search bar, 
                    which is always visible.
                </li>
                <li>
                    You can use quotes and write logic expressions in 
                    the search query.
                </li>
            </ul>

            <li>Add sentences</li>
            <ul>
                <li>
                    You can add your own sentences from the "Contribution" section.
                </li>
            </ul>

            <li>Delete sentences</li>
            <ul>
                <li>
                    Only the admin can delete sentences. If you'd like to delete 
                    a sentence, you can only contact the admin.
                </li>
            </ul>

            <li>Translate sentences</li>
            <ul>
                <li>
                    Sentences can be translated from any "sentences box", 
                    by clicking on the "Translate" link.
                </li>
                <li>Translation is handled with AJAX.</li>
            </ul>

            <li>Modify sentences</li>
            <ul>
                <li>Only the owner and the admin can modify the sentences.</li>
                <li>Modification is handled with AJAX (edit in place).</li>
                <li>
                    Edit in place generally does not work with translations, 
                    only with the main sentences.
                </li>
                <li>
                    However, it is possible to edit a translation that you have 
                    just added (useful if you've noticed a mistake right after 
                    saving your translation).
                </li>
            </ul>

            <li>Adopt sentences</li>
            <ul>
                <li>
                    There are many sentences in Tatoeba that do not have an owner. 
                    These sentencse can be adopted.
                </li>
                <li>
                    If you notice a mistake in a sentence that is not owned by 
                    anyone, you can adopt the sentence, and afterwards modify it.
                </li>
                <li>
                    You can also adopt sentences that are correct. 
                    Adopting is also a way to mark a sentence as correct.
                </li>
            </ul>

            <li>Post comments</li>
            <ul>
                <li>
                    You can post comments about a specific sentence. This is useful 
                    when for instance you don't understand it and would like more 
                    explanations. But of course you can always add explanations 
                    for sentences that you understand, in order to help people who 
                    learn the language. You can also post a comment if you saw 
                    a mistake but are not the owner of the sentence, or if 
                    you are not sure about how to correct it.
                </li>
                <li>
                    The owner of the sentence will receive a notification 
                    by email every time someone posts a comment.
                </li>
                <li>
                    All the people who have participated to 
                    the thread will also receive a notification.
                </li>
            </ul>

            <li>View logs &amp; statistics</li>
            <ul>
                <li>
                    Everytime a sentence is added, modified, or deleted, 
                    the action is logged.
                </li>
                <li>
                    The 10 last logs entries can be seen on the homepage. 
                    The 200 last logs entries can be seen after clicking on 
                    "Show more...".
                </li>
                <li>
                    Based on these logs, we're showing the activity in Tatoeba 
                    in the "activity timeline" : it indicates for each day the sum 
                    of number of sentences added and the number of modificaitons.
                </li>
            </ul>
        </ul>
    </div>
</div>

