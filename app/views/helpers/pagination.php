<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
class PaginationHelper extends AppHelper{
	var $helpers = array('Html');
	const RANGE = 12;
	
	function searchUrl($page, $query, $from = null, $to = null){
		$params  = '?page='.$page;
		$params .= '&query='.$query;
		$params .= ($from != null) ? '&from='.$from : '';
		$params .= ($to != null) ? '&to='.$to : '';
		return array("controller" => "sentences", "action" => "search", $params);
	}
	
	function displaySearchPagination($totalPages, $currentPage, $query, $from = null, $to = null){
		if($totalPages > 1){
			$query = urlencode($query);
			
			echo '<div class="pagination">';
			
			// Navigation arrows
			if($totalPages > PaginationHelper::RANGE){
				if($currentPage > 1){
					echo $this->Html->link(
						"<<",
						$this->searchUrl(1, $query, $from, $to),
						array("class" => "navigation")
					);
					
					echo $this->Html->link(
						"<",
						$this->searchUrl($currentPage-1, $query, $from, $to),
						array("class" => "navigation")
					);
				}else{
					echo '<strong><<</strong>';
					echo '<strong><</strong>';
				}
			}
			
			// Setting range
			if($totalPages <= PaginationHelper::RANGE){			
				$start = 1;
				$end = $totalPages;
			}else{
				if($currentPage < ceil(PaginationHelper::RANGE/2)){
					$start = 1;
					$end = PaginationHelper::RANGE;
				}elseif($currentPage > $totalPages-ceil(PaginationHelper::RANGE/2)){
					$start = $totalPages - PaginationHelper::RANGE;
					$end = $totalPages;
				}else{
					$start = $currentPage - floor(PaginationHelper::RANGE/2);
					$end = $currentPage + floor(PaginationHelper::RANGE/2);
				}
			}
			
			for($i = $start ; $i <= $end ; $i++){
				if($i == $currentPage){
					echo '<span class="selected">'.$i.'</span>';
				}else{
					echo $this->Html->link(
						$i, 
						$this->searchUrl($i, $query, $from, $to)
					);
				}
			}
			
			// Navigation arrows
			if($totalPages > PaginationHelper::RANGE){
				if($currentPage < $totalPages){
					echo $this->Html->link(
						">",
						$this->searchUrl($currentPage+1, $query, $from, $to),
						array("class" => "navigation")
					);
					
					echo $this->Html->link(
						">>",
						$this->searchUrl($totalPages, $query, $from, $to),
						array("class" => "navigation")
					);
				}else{
					echo '<strong>></strong>';
					echo '<strong>>></strong>';
				}
			}
			
			echo '</div>';
		}
	}
}
?>
