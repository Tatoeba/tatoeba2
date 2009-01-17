<?php
echo '<h2>';
__('Send new password');
echo '</h2>';

echo $form->create('User', array("action" => "new_password"));
echo $form->input('email');
echo $form->end(__('Send',true));
?>