<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2015 Gilles Bedel
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
    __('Transcriptions of {username}'),
    array('username' => $username)
);
$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<?php
if (isset($sentencesWithTranscription)) {
    echo $this->Html->div(
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
if (isset($sentencesWithTranscription)) {
    if (count($sentencesWithTranscription) == 0) {
        echo $this->Html->tag('h2', format(
            __('{username} does not have any transcriptions'),
            array('username' => $username)
        ));
    } else {
        $title = $this->Paginator->counter(
            array(
                'format' => $title . ' ' . __("(total %count%)")
            )
        );
        echo $this->Html->tag('h2', $title);

        $paginationUrl = array($username);
        $this->Pagination->display($paginationUrl);

        $type = 'mainSentence';
        $parentId = null;
        $withAudio = false;
        foreach ($sentencesWithTranscription as $sentence) {
            $this->Sentences->displayGenericSentence(
                $sentence,
                $type,
                $withAudio,
                $parentId
            );
        }

        $this->Pagination->display($paginationUrl);
    }
} else {
    echo $this->Html->tag('h2', format(
        __("There's no user called {username}"),
        array('username' => $username)
    ));
}
?>
</div>
</div>
