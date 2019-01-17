<?php
$this->Csv->setDelimiter("\t");


$this->Csv->addGrid($sentencesWithTranslation, false, $fieldsList);

echo $this->Csv->render('export_list_'.$listId.$translationsLang.'.csv');  

// Just experimenting how to force display of download box.
?>
