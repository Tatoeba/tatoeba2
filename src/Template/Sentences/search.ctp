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
 * @link     https://tatoeba.org
 */
use App\Model\CurrentUser;

if ($is_advanced_search) {
    $title = __x('title', 'Advanced search');
} else if (!empty($query)) {
    $title = format(__('Sentences with: {keywords}'), array('keywords' => h($query)));
} else {
    if ($from != 'und' && $to != 'und') {
        if ($trans_filter == 'exclude') {
            $title = format(__('Sentences in {language} not translated into {translationLanguage}'),
                            array('language' => $this->Languages->codeToNameToFormat($from),
                                   'translationLanguage' => $this->Languages->codeToNameToFormat($to)));
        } else {
            $title = format(__('Sentences in {language} translated into {translationLanguage}'),
                            array('language' => $this->Languages->codeToNameToFormat($from),
                                  'translationLanguage' => $this->Languages->codeToNameToFormat($to)));
        }
    } elseif ($from != 'und') {
        $title = format(__('Sentences in {language}'),
                        array('language' => $this->Languages->codeToNameToFormat($from)));
    } elseif ($to != 'und') {
        if ($trans_filter == 'exclude') {
            $title = format(__('Sentences not translated into {language}'),
                            array('language' => $language->codeToNameToFormat($to)));
        } else {
            $title = format(__('Sentences translated into {language}'),
                            array('language' => $this->Languages->codeToNameToFormat($to)));
        }
    } else {
        $title = format(__('All sentences'));
    }
}
$this->set('title_for_layout', $this->Pages->formatTitle($title));

if ($ignored) {
    $list = $this->Html->nestedList($ignored);
    $warn = format(
        __("Warning: the following criteria have been ignored:{list}"),
        compact('list')
    );
    echo $this->Html->tag('div', $warn, array(
        'id' => 'searchWarning',
        'class' => 'message',
    ));
}
?>

<div id="annexe_content">
    <div class="module advanced-search">
    <h2><?php echo __('More search criteria'); ?></h2>
    <?php echo $this->element('advanced_search_form', array(
                   'searchableLists' => $searchableLists,
          )); ?>
    </div>
</div>

<div id="main_content">
<div class="section">
<?php
if (!isset($results)) {
    if (isset($sphinx_markers)) {
    ?>
        <h2><?php echo __('Search error'); ?></h2>
        <p><?php
            echo format(
                __(
                    'Invalid query. '.
                    'Please refer to the '.
                    '<a href="{}">search documentation</a> for more details.', true),
                'http://en.wiki.tatoeba.org/articles/show/text-search'
            );
        ?></p>
    <?php
    } else {
    ?>
    <h2><?php echo __('Search error'); ?></h2>
    <p><?php
        echo format(
            __(
                'An error occurred while performing the search. '.
                'If the problem persists, please '.
                '<a href="{}">let us know</a>.', true),
            $this->Url->build(array('controller' => 'pages', 'action' => 'contact'))
        );
    ?></p>
    <?php
    }
} elseif (count($results) > 0) {

    if (!$is_advanced_search && !empty($query)) {
        $keywords = $this->Languages->tagWithLang(
            'span', '', $query
        );
        $title = format(
            /* @translators: title on the top of a search result page */
            __('Search: {keywords}'),
            compact('keywords')
        );
    }
    echo $this->Pages->formatTitleWithResultCount($this->Paginator, $title, $real_total);

    //echo $this->Pages->sentencesMayNotAppear($vocabulary, $real_total);
    
    $this->Pagination->display();

    if (!CurrentUser::isMember() || CurrentUser::getSetting('use_new_design')) {
        foreach ($results as $sentence) {
            echo $this->element(
                'sentences/sentence_and_translations',
                array(
                    'sentence' => $sentence,
                    'translations' => $sentence->translations,
                    'user' => $sentence->user
                )
            );
        }
    } else {
        foreach ($results as $sentence) {
            $this->Sentences->displaySentencesGroup(
                $sentence,
                array('langFilter' => $to)
            );
        }
    }

    $this->Pagination->display();

} else {
    echo $this->element('search_with_no_result');

    //echo $this->Pages->sentencesMayNotAppear($vocabulary, $real_total);
}
?>
</div>
</div>
