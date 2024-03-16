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

if (!CurrentUser::isMember() || CurrentUser::getSetting('use_new_design')) {
    $this->set('isResponsive', true);
}

if ($is_advanced_search) {
    $title = __x('title', 'Advanced search');
} else if (!empty($query)) {
    $title = format(__('Sentences with: {keywords}'), array('keywords' => h($query)));
} else {
    if (!empty($from) && !empty($to)) {
        if ($trans_filter == 'exclude') {
            $title = format(__('Sentences in {language} not translated into {translationLanguage}'),
                            array('language' => $this->Languages->codeToNameToFormat($from),
                                   'translationLanguage' => $this->Languages->codeToNameToFormat($to)));
        } else {
            $title = format(__('Sentences in {language} translated into {translationLanguage}'),
                            array('language' => $this->Languages->codeToNameToFormat($from),
                                  'translationLanguage' => $this->Languages->codeToNameToFormat($to)));
        }
    } elseif (!empty($from)) {
        $title = format(__('Sentences in {language}'),
                        array('language' => $this->Languages->codeToNameToFormat($from)));
    } elseif (!empty($to)) {
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
        'id' => 'flashMessage',
        'class' => 'message',
        'ng-non-bindable' => '',
    ));
}
?>

<md-toolbar class="md-hue-2" hide-xs hide-sm ng-cloak>
    <div class="md-toolbar-tools">
        <?php if (isset($syntax_error) || isset($error_code)): ?>
            <h2 flex><?= __('Search error') ?></h2>
        <?php else: ?>
            <?= $this->Pages->formatTitleWithResultCount($this->Paginator, $title, $real_total); ?>
        <?php endif; ?>

        <?= $this->element('sentences/expand_all_menus_button'); ?>
    </div>
</md-toolbar>

<section layout="row" ng-cloak>

<md-content class="md-whiteframe-1dp" flex>

<md-toolbar class="md-hue-2" hide-gt-sm ng-cloak ng-controller="SidenavController">
    <div class="md-toolbar-tools">
        <?php if (isset($syntax_error) || isset($error_code)): ?>
            <h2 flex><?= __('Search error') ?></h2>
        <?php else: ?>
            <h2 flex><?= $this->Pages->formatResultCount($this->Paginator, $real_total); ?></h2>
        <?php endif; ?>

        <md-button ng-click="toggle('advanced-search')">
            <md-icon>filter_list</md-icon>
            <?php /* @translators: button to open the advanced search option sidebar on mobile */ ?>
            <?= __('Refine search') ?>
        </md-button>
    </div>
</md-toolbar>

<?php
    if (isset($syntax_error)) {
?>
    <div class="section">
        <p><?php
            echo format(
                __(
                    'Invalid query. '.
                    'Please refer to the '.
                    '<a href="{}">search documentation</a> for more details.', true),
                $this->Pages->getWikiLink('text-search')
            );
        ?></p>
    </div>
<?php
    } elseif (isset($error_code)) {
?>
    <div class="section">
        <p><?php
            echo format(
                __(
                    'An error occurred while performing the search. '.
                    'If the problem persists, please '.
                    '<a href="{us}">let us know</a> and include the '.
                    'error code "{errorCode}" in your message.'),
                    [
                        'us' => $this->Url->build(['controller' => 'pages', 'action' => 'contact']),
                        'errorCode' => $error_code,
                    ]
            );
        ?></p>
    </div>
<?php
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

    //echo $this->Pages->sentencesMayNotAppear($vocabulary, $real_total);
    
    $this->Pagination->display();

    if (!CurrentUser::isMember() || CurrentUser::getSetting('use_new_design')) {
        foreach ($results as $sentence) {
            echo $this->element(
                'sentences/sentence_and_translations',
                array(
                    'sentence' => $sentence,
                    'translations' => $sentence->translations,
                    'user' => $sentence->user,
                    'translationLang' => $to
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
</md-content>

<md-sidenav class="md-sidenav-right md-whiteframe-1dp"
            md-component-id="advanced-search"
            md-disable-scroll-target="body"
            md-is-locked-open="$mdMedia('gt-sm')">
    <md-toolbar>
        <div class="md-toolbar-tools" ng-controller="SidenavController">
            <?php /* @translators: title for the sidebar on the search page */ ?>
            <h2 flex><?= __('Refine search'); ?></h2>
            <md-button class="close md-icon-button" ng-click="toggle('advanced-search')">
                <md-icon>close</md-icon>
            </md-button>
        </div>
    </md-toolbar>
    
    <?php echo $this->element('advanced_search_form', [
        'searchableLists' => $searchableLists,
        'isSidebar' => true
    ]); ?>
</md-sidenav>

</section>
