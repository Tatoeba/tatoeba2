<?php
class PaginationHelper extends AppHelper{
	var $helpers = array('Html');
	
	function displaySearchPagination($totalPages, $currentPage, $query){
		if($totalPages > 1){
			echo '<div class="pagination">';
			
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
			
			if($totalPages < 16){			
				for($i = 1 ; $i <= $totalPages ; $i++){
					if($i == $currentPage){
						echo '<span class="selected">'.$i.'</span>';
					}else{
						echo $this->Html->link(
							$i, 
							array("controller" => "sentences", "action" => "search", "?query=".$query."&page=".$i)
						);
					}
				}
			}else{
				for($i = 1 ; $i <= 15 ; $i++){
					if($i == $currentPage){
						echo '<span class="selected">'.$i.'</span>';
					}else{
						echo $this->Html->link(
							$i, 
							array("controller" => "sentences", "action" => "search", "?query=".$query."&page=".$i)
						);
					}
				}
			}
			
			
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