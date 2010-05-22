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
     * @param array $url Array containing the extra params that should appear
     *                   in the pagination URL.
     *
     * @return void
     */
    public function display($url = null)
    {
        // -----------------------------------------------------------
        // So that we can pass GET variables into the pagination links.
        // Took it from here:
        // http://bdsarwar.wordpress.com/2010/01/12/passing-get-variable-in-cakephp-pagination-url/
        $urls = $this->params['url']; $getv = "";
        foreach($urls as $key=>$value)
        {
            if($key == 'url') continue; // we need to ignor the url field
            $getv .= urlencode($key)."=".urlencode($value)."&"; // making the passing parameters
        }
        $getv = substr_replace($getv ,"",-1); // remove the last char '&'
        $this->Paginator->options(array('url' => array("?"=>$getv)));
        // -----------------------------------------------------------
        
        
        $prevNextOptions = array();
        $numbersOptions = array('separator' => '');
        
        if (!empty($url)) {
            $prevNextOptions['url'] = $url;
            $numbersOptions['url'] = $url;
        }        
        ?>
        <div class="paging">
        <?php 
        echo $this->Paginator->prev(
            '<< '.__('previous', true), 
            $prevNextOptions, 
            null, 
            array('class'=>'disabled')
        ); 
        
        echo $this->Paginator->numbers($numbersOptions); 
        
        echo $this->Paginator->next(
            __('next', true).' >>',
            $prevNextOptions,
            null, 
            array('class'=>'disabled')
        ); 
        ?>
        </div>
        <?php
    }
}
?>
