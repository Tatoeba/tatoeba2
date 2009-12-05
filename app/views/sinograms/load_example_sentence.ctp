
<?php
if( $sentence == null ){
    echo '<div id="noExampleFound" >' ;
    echo sprintf(__('No sentence using this character has been found, you can add one <a href="%s" >here</a> .', true),
            "/pages/contribute");
    echo "</div>\n";
} else {
?>
    <div class="sentences_set searchResult">
        <?php
            // TODO replace variable names  + add a if no results has been found 
            // TODO add a link to all result or a link to contribute 
            // sentence menu (translate, edit, comment, etc)

            $specialOptions['belongsTo'] = $sentence['User']['username']; // TODO set up a better mechanism
            $sentences->displayMenu($sentence['Sentence']['id'],
                                    $sentence['Sentence']['lang'],
                                    $specialOptions );
            // sentence and translations
            $sentence['User']['canEdit'] = $specialOptions['canEdit']; // TODO set up a better mechanism
            $sentences->displayGroup($sentence['Sentence'], $sentence['Translation'], $sentence['User']);
        ?>
    </div>
    <p>
        <?php
        echo sprintf(
            __('View all sentences using this character <a href="%s" >here</a>',true)
            ,"/sentences/search?query=" .$sinogram
            )
        ?>
    </p>
<?php } ?>

