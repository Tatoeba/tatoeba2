<?php
if (!$saved) {
    $image = 'unlink.png';
    $alt = __('Unlink', true);
    $title = __('Unlink this translation.', true);
} else {
    $image = 'link.png';
    $alt = __('Link', true);
    $title = __('Make as direct translation.', true);
}

echo $html->image(
    IMG_PATH . $image,
    array(
        "alt"=> $alt,
        "title" => $title,
        "width" => 16,
        "height" => 16
    )
);
?>