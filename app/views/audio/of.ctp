<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2016 Gilles Bedel
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
 */

$title = format(
    __('Audio contributed by {username}', true),
    array('username' => $username)
);
$this->set('title_for_layout', $pages->formatTitle($title));
?>

<?php
if (isset($sentencesWithAudio)) {
    echo $html->div(
        null,
        $this->element(
            'users_menu',
            array('username' => $username)
        ),
        array('id' => 'annexe_content')
    );
}
?>

<div id="main_content">
<div class="module">
<?php
if (isset($sentencesWithAudio)) {
    if (count($sentencesWithAudio) == 0) {
        echo $html->tag('h2', format(
            __('{username} does not have contributed any audio', true),
            array('username' => $username)
        ));
    } else {
        $title = $paginator->counter(
            array(
                'format' => $title . ' ' . __("(total %count%)", true)
            )
        );
        echo $html->tag('h2', $title);

        $paginationUrl = array($username);
        $pagination->display($paginationUrl);

        $type = 'mainSentence';
        $parentId = null;
        $withAudio = true;
        foreach ($sentencesWithAudio as $sentence) {
            $sentences->displayGenericSentence(
                $sentence['Sentence'],
                $sentence['Sentence']['Transcription'],
                $type,
                $withAudio,
                $parentId
            );
        }

        $pagination->display($paginationUrl);
    }
} else {
    echo $html->tag('h2', format(
        __("There's no user called {username}", true),
        array('username' => $username)
    ));
}
?>
</div>
</div>
