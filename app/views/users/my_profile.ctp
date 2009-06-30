<?php 
echo '<h2>';
__('You can change your information here');
echo '</h2>';

echo $form->create('User', array("action" => "save_profile"));
echo $form->input('old_password/passwd', array("label" => __('Old password',true)));
echo $form->input('new_password/passwd', array("label" => __('New password',true)));
echo $form->input('email', array("label" => __('Email',true), "value" => $user['User']['email']));
echo $form->end(__('Save',true));
?>
