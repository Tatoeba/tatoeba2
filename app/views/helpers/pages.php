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

class PagesHelper extends AppHelper
{
    public function formatTitle($pageTitle) {
        return $pageTitle . __(' - Tatoeba', true);
    }

    public function formatTitleWithResultCount($paginator, $title, $real_total = 0) {
        $n = $paginator->counter(array('format' => '%count%'));
        if ($real_total == 0 || $real_total == $n) {
            /* @translators: this formats the title at the top of every page
               that shows a list of sentences (search, browse by language,
               adopt sentences…) by appending the number of results. Note
               the use of &nbsp; which is a non-breaking space. */
            $title = format(__n('{title} (one result)',
                                '{title} ({n}&nbsp;results)',
                                $n, true),
                            compact('title', 'n')
            );
        } else {
            /* @translators: this formats the title at the top of the search
               page by appending the number of results. Tatoeba is only able
               to display {thousand} results (that should always be turned
               into “1000”), but {n} results actually exist in the corpus.
               Note the use of &nbsp; which is a non-breaking space. */
            $title = format(__n('{title} ({thousand}&nbsp;results out of one occurrence)',
                                '{title} ({thousand}&nbsp;results out of {n}&nbsp;occurrences)',
                                $real_total, true),
                            array('title' => $title, 'thousand' => $n, 'n' => $real_total)
            );
        }
        $title = sprintf('<h2>%s</h2>', $title);
        return $title;
    }
}
?>
