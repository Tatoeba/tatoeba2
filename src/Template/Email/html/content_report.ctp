<?php
$reporter = $report->getReporter();
$details = $report->getDetails();
$reporterUrl = [
    '_full' => true,
    'lang' => '',
    'controller' => 'user',
    'action' => 'profile',
    $reporter
];
?>
<p>
    Member <?= $this->Html->link($reporter, $reporterUrl) ?> reported <?= $report->getContentName() ?>:<br>
    <?= $this->Html->link($report->getContentUrl()) ?>
</p>
<p>
    <?php if (strlen($details) > 0): ?>
        Below is what <?= $reporter ?> wrote about it:<br>
        <?= h($details) ?>
    <?php else: ?>
        <?= $reporter ?> did not provide any detail.
    <?php endif; ?>
</p>
