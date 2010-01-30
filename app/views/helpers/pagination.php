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
    public $helpers = array('Html');
    const RANGE = 12;
    
    /** 
     * Return URL of search.
     *
     * @param int    $page  Current page.
     * @param string $query Search query.
     * @param string $from  Source language.
     * @param string $to    Target language.
     *
     * @return array
     */
    private function _searchUrl($page, $query, $from = null, $to = null)
    {
        $params  = '?page='.$page;
        $params .= '&amp;query='.$query;
        $params .= ($from != null) ? '&amp;from='.$from : '';
        $params .= ($to != null) ? '&amp;to='.$to : '';
        return array("controller" => "sentences", "action" => "search", $params);
    }
    
    /** 
     * Display navigation links for search
     *
     * @param int    $totalPages  Total number of pages.
     * @param int    $currentPage Current page.
     * @param string $query       Search query.
     * @param string $from        Source language.
     * @param string $to          Target language.
     *
     * @return void
     */
    public function displaySearchPagination(
        $totalPages, $currentPage, $query, $from = null, $to = null
    ) {
        if ($totalPages > 1) {
            $query = urlencode($query);
            
            echo '<div class="pagination">';
            
            // Navigation arrows
            if ($totalPages > PaginationHelper::RANGE) {
                if ($currentPage > 1) {
                    echo $this->Html->link(
                        "<<",
                        $this->_searchUrl(1, $query, $from, $to),
                        array("class" => "navigation")
                    );
                    
                    echo $this->Html->link(
                        "<",
                        $this->_searchUrl($currentPage-1, $query, $from, $to),
                        array("class" => "navigation")
                    );
                } else {
                    echo '<strong>'.htmlentities("<<").'</strong>';
                    echo '<strong>'.htmlentities("<").'</strong>';
                }
            }
            
            // Setting range
            $halfRange = PaginationHelper::RANGE/2;
            if ($totalPages <= PaginationHelper::RANGE) {            
                $start = 1;
                $end = $totalPages;
            } else {
                if ($currentPage < ceil($halfRange)) {
                    $start = 1;
                    $end = PaginationHelper::RANGE;
                } elseif ($currentPage > $totalPages-ceil($halfRange)) {
                    $start = $totalPages - PaginationHelper::RANGE;
                    $end = $totalPages;
                } else {
                    $start = $currentPage - floor($halfRange);
                    $end = $currentPage + floor($halfRange);
                }
            }
            
            for ($i = $start ; $i <= $end ; $i++) {
                if ($i == $currentPage) {
                    echo '<span class="selected">'.$i.'</span>';
                } else {
                    echo $this->Html->link(
                        $i, 
                        $this->_searchUrl($i, $query, $from, $to)
                    );
                }
            }
            
            // Navigation arrows
            if ($totalPages > PaginationHelper::RANGE) {
                if ($currentPage < $totalPages) {
                    echo $this->Html->link(
                        ">",
                        $this->_searchUrl($currentPage+1, $query, $from, $to),
                        array("class" => "navigation")
                    );
                    
                    echo $this->Html->link(
                        ">>",
                        $this->_searchUrl($totalPages, $query, $from, $to),
                        array("class" => "navigation")
                    );
                } else {
                    echo '<strong>'.htmlentities(">").'</strong>';
                    echo '<strong>'.htmlentities(">>").'</strong>';
                }
            }
            
            echo '</div>';
        }
    }
}
?>
