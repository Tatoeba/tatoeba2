<?php
if (!$saved) {
    $image = 'unlink.svg';
    $alt = __('Unlink');
    $title = __('Unlink this translation.');
} else {
    $image = 'link.svg';
    $alt = __('Link');
    $title = __('Make into direct translation.');
}

echo $this->Html->image(
    IMG_PATH . $image,
    array(
        "alt"=> $alt,
        "title" => $title,
        "width" => 16,
        "height" => 16
    )
);
?>