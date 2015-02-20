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

/**
 * Helper to display pagination.
 *
 * @category Default
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class PaginationHelper extends AppHelper
{
    public $helpers = array('Paginator');


    /**
     * Display pagination.
     *
     * @param array $extramParams Array containing the extra params that should
     *                            appear in the pagination URL.
     *
     * @return void
     */
    public function display($extramParams = array())
    {
        $paging = $this->params['paging'];
        $pagingInfo = array_pop($paging); // In the hope that there's only always
                                          // one element in $paging.
        if ($pagingInfo['pageCount'] < 2) {
            return;
        }

        // -----------------------------------------------------------
        // So that we can pass GET variables into the pagination links.
        // Took it from here:
        // http://bdsarwar.wordpress.com/2010/01/12/passing-get-variable-in-cakephp-pagination-url/
        $urls = $this->params['url'];
        $getv = "";
        foreach ($urls as $key=>$value) {
            if ($key === 'url') {
                continue; // we need to ignore the url field
            }
            // making the passing parameters
            $getv .= urlencode($key)."=".urlencode($value)."&";
        }
        $getv = substr_replace($getv, "", -1); // remove the last char '&'

        $extramParams['?'] = $getv;
        $this->Paginator->options(array('url' => $extramParams));
        // -----------------------------------------------------------


        $numbersOptions = array(
            'separator' => '',
            // Print up to 6/2 = 3 numbered links in rectangles on each side
            // of the central numbered link, in addition to the
            // first and prev links on the far left, and the next
            // and last links on the far right. We should 
            // use a smaller value for this parameter on mobile
            // devices if we ever customize the interface to
            // behave differently based on the display size.
            // Note that the size of the links depends on the number of
            // digits in the number. Unless we can adapt the modulus
            // dynamically based on the number of digits per link, we
            // need to choose the number conservatively so that it will
            // fit the screen "real estate" that we have available for
            // the largest numbers (for example, when we're near the
            // end of the list of English sentences).
            'modulus' => 6,
            'class' => 'pageNumber'
        );
        ?>
        <div class="paging">
            <?php
            $first = $this->Paginator->first(
                '<<',
                array('title' => __('First page', true))
            );
            if (empty($first)) {
                $first = '<span class="disabled">&lt;&lt;</span>';
            }
            echo $first;

            echo $this->Paginator->prev(
                '<',
                // @translators Appears on “previous page” links
                // mouseover. Keyboard shortcut is between brackets.
                array('title' => __('Previous page [Ctrl+←]', true)),
                null,
                array('class' => 'disabled')
            );
            ?>

            <span class="numbers">
            <?php echo $this->Paginator->numbers($numbersOptions);  ?>
            </span>

            <?php
            echo $this->Paginator->next(
                '>',
                // @translators Appears on “next page” links
                // mouseover. Keyboard shortcut is between brackets.
                array('title' => __('Next page [Ctrl+→]', true)),
                null,
                array('class' => 'disabled')
            );

            $last = $this->Paginator->last(
                '>>',
                array('title' => __('Last page', true))
            );
            if (empty($last)) {
                $last = '<span class="disabled">&gt;&gt;</span>';
            }
            echo $last;
            ?>
        </div>
        <?php
    }
}
?>
