<?php
if ($result) {
    $numSentences = $result['numSentences'];
    $result['numSentencesLabel'] = $this->Vocabulary->sentenceCountLabel($numSentences);
}
echo json_encode($result);
?>
