<?php
$csv->setDelimiter("\t");


$csv->addGrid($sentencesWithTranslation, false, $fieldsList);

echo $csv->render('export_list_'.$listId.$translationsLang.'.csv');  

// Just experimenting how to force display of download box.
?>
