<div id="annexe_content">
    <?php
    $sentenceAnnotations->displayGoToBox();
    
    $sentenceAnnotations->displaySearchBox();
    ?>
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