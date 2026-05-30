<?php
$title = __('Search disabled');
$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<div class="section md-whiteframe-1dp">
<h2><?php echo __('Search disabled'); ?></h2>
<p>
<?php echo __(
    'Due to technical reasons, the search feature is '.
    'currently disabled. We are sorry for the '.
    'inconvenience. Please try again later.'
); ?>
</p>
</div>