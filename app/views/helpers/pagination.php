<?php
class PaginationHelper extends AppHelper{
	var $helpers = array('Html');
	const RANGE = 11;
	
	function displaySearchPagination($totalPages, $currentPage, $query){
		if($totalPages > 1){
			echo '<div class="pagination">';
			
			// Navigation arrows
			if($currentPage > 1){
				echo $this->Html->link(
					"<<",
					array("controller" => "sentences", "action" => "search", "?query=".$query."&page=1"),
					array("class" => "navigation")
				);
				
				echo $this->Html->link(
					"<",
					array("controller" => "sentences", "action" => "search", "?query=".$query."&page=".($currentPage-1)),
					array("class" => "navigation")
				);
			}else{
				echo '<strong><<</strong>';
				echo '<strong><</strong>';
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
						array("controller" => "sentences", "action" => "search", "?query=".$query."&page=".$i)
					);
				}
			}
			
			// Navigation arrows
			if($currentPage < $totalPages){
				echo $this->Html->link(
					">",
					array("controller" => "sentences", "action" => "search", "?query=".$query."&page=".($currentPage+1)),
					array("class" => "navigation")
				);
				
				echo $this->Html->link(
					">>",
					array("controller" => "sentences", "action" => "search", "?query=".$query."&page=".$totalPages),
					array("class" => "navigation")
				);
			}else{
				echo '<strong>></strong>';
				echo '<strong>>></strong>';
			}
			
			echo '</div>';
		}
	}
}
?>