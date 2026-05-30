<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2014  Gilles Bedel
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
namespace App\View\Helper;

use App\View\Helper\AppHelper;

class PagesHelper extends AppHelper
{
    public $helpers = ['Number'];

    public function formatTitle($pageTitle) {
        return $pageTitle . __(' - Tatoeba');
    }

    public function formatTitleWithResultCount($paginator, $title, $real_total = 0, $totalOnly = false) {
        $results = $this->formatResultCount($paginator, $real_total, $totalOnly);
        /* @translators: this formats title at the top of every page
            that shows a list of sentences (search, browse by language,
            adopt sentences…) by appending the number of results. */
        $title = format(__('{title} ({results})'), ['title' => $title, 'results' => $results]);
        $title = sprintf('<h2 flex>%s</h2>', $title);
        return $title;
    }

    public function formatResultCount($paginator, $real_total = 0, $totalOnly = false) {
        $n = $totalOnly ? $real_total : $paginator->param('count');
        if ($real_total == 0 || $real_total == $n || $totalOnly) {
            /* @translators: this formats the number of results on pages
               that show a list of sentences (search, browse by language,
               adopt sentences…). Note the use of &nbsp; which is a non-breaking space. */
            $results = format(
                __n('{n} result', '{n}&nbsp;results', $n, true),
                ['n' => $this->Number->format($n)]
            );
        } else {
            /* @translators: this formats the number of results on pages
               that show a list of sentences (search, browse by language,
               adopt sentences…). Tatoeba is only able
               to display {thousand} results (that should always be turned
               into “1000”), but {n} results actually exist in the corpus.
               Note the use of &nbsp; which is a non-breaking space. */
            $results = format(
                __n(
                    '{thousand}&nbsp;results out of {n} occurrence',
                    '{thousand}&nbsp;results out of {n}&nbsp;occurrences',
                    $real_total, true
                ),
                [
                    'thousand' => $this->Number->format($n),
                    'n' => $this->Number->format($real_total)
                ]
            );
        }

        return $results;
    }

    public function currentPageUrl() {
        return $this->getView()->getRequest()->getRequestTarget();
    }

    /**
     * Formats the text used to display sentence numbers prefixed
     * with a sharp symbol, such as #1234. The sharp symbol may
     * actually be localized in to something more appropriate in
     * the language.
     * Warning: returned value is not HTML-escaped.
     */
    public function formatSentenceIdWithSharp($sentenceId) {
        return format(
            /* @translators: You can translate the sharp in the link
               to a sentence that appears anywhere on the website,
               such as in "Sentence #1234 — belongs to foobar" */
            __('#{sentenceId}'),
            array('sentenceId' => $sentenceId)
        );
    }

    /**
     * Display a message if a vocabulary item exists and its numSentences count
     * is different than the Sphinx real total.
     *
     * @param  array $vocabulary Vocabulary item.
     * @param  int   $real_total Total number of sentences found by Sphinx.
     *
     * @return string
     */
    public function sentencesMayNotAppear($vocabulary, $real_total)
    {
        if ($real_total == null) {
            $real_total = 0;
        }

        if (!empty($vocabulary) && $real_total != $vocabulary['Vocabulary']['numSentences']) {
            echo format(__(
                'Recently added sentences may not appear in search results.',
                true
            ));
        }
    }

    public function getWikiLink($englishSlug) {
        $wikiLinkLocalizer = $this->_View->get('wikiLinkLocalizer');
        if (is_callable($wikiLinkLocalizer)) {
            return $wikiLinkLocalizer($englishSlug);
        } else {
            // fallback if AppController::beforeRender() wasn't called
            $englishSlug = urlencode($englishSlug);
            return "https://en.wiki.tatoeba.org/articles/show/$englishSlug";
        }
    }
}
?>
