<div id="annexe_content">
	<div class="module">
	<?php
		echo '<h2>Go to...</h2>';
				
		echo $form->create('SentenceAnnotation', array("action" => "show"));
		echo $form->input('sentence_id', array("label" => "Sentence nº"));
		echo $form->end('OK');
	?>
	</div>

	<div class="module">
	<?php
		echo '<h2>Search</h2>';
		
		echo $form->create('SentenceAnnotation', array("action" => "search"));
		echo $form->input(
            'text', 
            array(
                "label" => "",
                "type" => "text"
            )
        );
		echo $form->end('OK');
	?>
	</div>	
</div>

<div id="main_content">
	<div class="module">
	<?php
	$numberOfResults = count($annotations);
	echo '<h2>';
	echo 'Replaced '.$textToReplace.' by '.$textReplacing
          .' ('.$numberOfResults.' results)';
	echo '</h2>';
	
	if($numberOfResults > 0){
		foreach($annotations as $annotation){
			// sentence
			echo '<p>';
			echo $html->link(
				$annotation['Sentence']['text']
				, array('action' => 'show', $annotation['Sentence']['id'])
			);
			echo '</p>';
			
			
			// annotation
			echo '<p class="annotation">';
			echo $annotation['SentenceAnnotation']['text'];
			echo '</p>';
			
			
			echo '<hr/>';
		}
	}
	?>
	</div>
</div>