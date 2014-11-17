<?php

if ($saved) {
    $image = 'unlink.svg';
    $alt = __('Unlink', true);
    $title = __('Unlink this translation.', true);
} else {
    $image = 'link.svg';
    $alt = __('Link', true);
    $title = __('Make into direct translation.', true);
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
