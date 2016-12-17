<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2015  Gilles Bedel
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

echo $this->Form->create(
    'AdvancedSearch',
    array(
        'url' => array(
            'controller' => 'sentences',
            'action' => 'search',
        ),
        'type' => 'get',
        'id' => 'AdvancedSearchSearchForm',
        'class' => 'form'
    )
);
?>
<fieldset id="advsearch-sentences">
<legend><?php __('Sentences'); ?></legend>
<?php
    echo $this->Form->input('query', array(
        'label' => __('Words:', true),
        'value' => $query,
        'lang' => '',
        'dir' => 'auto',
    ));

    echo $this->Search->selectLang('from', $from, array(
        'label' => __('Language:', true),
    ));

    echo $this->Search->selectLang('to', $to, array(
        'label' => __('Show translations in:', true),
        'options' => $this->Languages->languagesArrayForPositiveLists(),
    ));

    $orphansNote = $this->Html->tag(
        'div',
        __('Orphan sentences are likely to be incorrect.', true),
        array(
            'class' => 'note',
        )
    );
    echo $this->Form->input('orphans', array(
        'label' => __('Is orphan:', true),
        'options' => array(
            '' => __p('orphan', 'Any', true),
            'no' => __('No', true),
            'yes' => __('Yes', true),
        ),
        'after' => $orphansNote,
        'value' => $orphans,
    ));

    $unapprNote = $this->Html->tag(
        'div',
        __('Unapproved sentences are likely to be incorrect.', true),
        array(
            'class' => 'note',
        )
    );
    echo $this->Form->input('unapproved', array(
        'label' => __('Is unapproved:', true),
        'options' => array(
            '' => __p('unapproved', 'Any', true),
            'no' => __('No', true),
            'yes' => __('Yes', true),
        ),
        'after' => $unapprNote,
        'value' => $unapproved,
    ));

    echo $this->Form->input('native', array(
        'type' => 'checkbox',
        'hiddenField' => false,
        'label' => __('Owned by a self-identified native', true),
        'value' => 'yes',
        'checked' => $native,
    ));

    echo $this->Form->input('user', array(
        'label' => __('Owner:', true),
        'placeholder' => __('Enter a username', true),
        'value' => $user,
    ));

    $tagsNote = $this->Html->tag(
        'div',
        __('Separate tags with commas.', true),
        array(
            'class' => 'note',
        )
    );
    echo $this->Form->input('tags', array(
        'label' => __('Tags:', true),
        'value' => $tags,
        'after' => $tagsNote,
    ));

    $listOptions = $this->Lists->listsAsSelectable($searchableLists);
    echo $this->Form->input('list', array(
        'label' => __('Belongs to list:', true),
        'value' => $list,
        'options' => $listOptions,
    ));

    echo $this->Form->input('has_audio', array(
        'label' => __('Has audio:', true),
        'options' => array(
            '' => __p('audio', 'Any', true),
            'no' => __('No', true),
            'yes' => __('Yes', true),
        ),
        'value' => $has_audio,
    ));
?>
</fieldset>

<fieldset id="advsearch-translations">
<legend><?php __('Translations'); ?></legend>
<?php
    $filterOption = $this->Form->select(
        'trans_filter',
        array(
            /* @translators This is inserted into another sentence
                            that begins with {action} */
            'limit' => __('Limit to', true),
            /* @translators This is inserted into another sentence
                            that begins with {action} */
            'exclude' => __('Exclude', true),
        ),
        $trans_filter,
        array('empty' => false)
    );
    $label = format(
        __('{action} sentences having translations that match'
          .' all the following criteria.', true),
        array('action' => $filterOption)
    );
    echo "<label>$label</label>";

    echo $this->Search->selectLang('trans_to', $trans_to, array(
        'label' => __('Language:', true),
        'options' => $this->Languages->getSearchableLanguagesArray(),
    ));
    echo $this->Form->input('trans_link', array(
        'label' => __('Link:', true),
        'options' => array(
            '' => __p('link', 'Any', true),
            'direct' => __('Direct', true),
            'indirect' => __('Indirect', true),
        ),
        'value' => $trans_link,
    ));
    echo $this->Form->input('trans_user', array(
        'label' => __('Owner:', true),
        'placeholder' => __('Enter a username', true),
        'value' => $trans_user,
    ));
    echo $this->Form->input('trans_orphan', array(
        'label' => __('Is orphan:', true),
        'options' => array(
            '' => __p('orphan', 'Any', true),
            'no' => __('No', true),
            'yes' => __('Yes', true),
        ),
        'value' => $trans_orphan,
    ));
    echo $this->Form->input('trans_unapproved', array(
        'label' => __('Is unapproved:', true),
        'options' => array(
            '' => __p('unapproved', 'Any', true),
            'no' => __('No', true),
            'yes' => __('Yes', true),
        ),
        'value' => $trans_unapproved,
    ));
    echo $this->Form->input('trans_has_audio', array(
        'label' => __('Has audio:', true),
        'options' => array(
            '' => __p('audio', 'Any', true),
            'no' => __('No', true),
            'yes' => __('Yes', true),
        ),
        'value' => $trans_has_audio,
    ));
?>
</fieldset>

<fieldset id="advsearch-sort">
<legend><?php __('Sort'); ?></legend>
<?php
    echo $this->Form->input('sort', array(
        'label' => __('Order:', true),
        'options' => array(
            'words' => __('Fewest words first', true),
            'created' => __('Last created first', true),
            'modified' => __('Last modified first', true),
            'random' => __('Random', true),
        ),
        'value' => $sort,
    ));
    echo $this->Form->input('sort_reverse', array(
        'type' => 'checkbox',
        'hiddenField' => false,
        'label' => __('Reverse order', true),
        'value' => 'yes',
        'checked' => $sort_reverse,
    ));
?>
</fieldset>

<?php
echo '<p>';
echo $html->link(
    __('More search options', true),
    'http://en.wiki.tatoeba.org/articles/show/text-search',
    array(
        'target' => '_blank'
    )
);
echo '</p>';

echo $this->Form->button(
    __p('button', 'Advanced search', true),
    array('class' => 'button submit')
);

echo $this->Form->end();
