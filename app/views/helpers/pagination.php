<?php
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
	
	function displaySearchPagination($totalPages, $currentPage, $query, $from = null){
		if($totalPages > 1){
			$query = urlencode($query);
			
			echo '<div class="pagination">';
			
			// Navigation arrows
			if($totalPages > PaginationHelper::RANGE){
				if($currentPage > 1){
					echo $this->Html->link(
						"<<",
						$this->searchUrl(1, $query, $from),
						array("class" => "navigation")
					);
					
					echo $this->Html->link(
						"<",
						$this->searchUrl($currentPage-1, $query, $from),
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
						$this->searchUrl($i, $query, $from)
					);
				}
			}
			
			// Navigation arrows
			if($totalPages > PaginationHelper::RANGE){
				if($currentPage < $totalPages){
					echo $this->Html->link(
						">",
						$this->searchUrl($currentPage+1, $query, $from),
						array("class" => "navigation")
					);
					
					echo $this->Html->link(
						">>",
						$this->searchUrl($totalPages, $query, $from),
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