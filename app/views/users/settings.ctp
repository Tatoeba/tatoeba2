<div id="UserSettings">
<?php 
echo '<h2>';
__('Change password');
echo '</h2>';
echo $form->create('User', array("action" => "save_password"));
echo $form->input('old_password/passwd', array("label" => __('Old password',true)));
echo $form->input('new_password/passwd', array("label" => __('New password',true)));
echo $form->input('new_password2/passwd', array("label" => __('New password again',true)));
echo $form->end(__('Save',true));


echo '<h2>';
__('Change email');
echo '</h2>';
echo $form->create('User', array("action" => "save_email"));
echo $form->input('email', array("label" => __('Email',true), "value" => $user['User']['email']));
echo $form->end(__('Save',true));


echo '<h2>';
__('Change options');
echo '</h2>';
echo $form->create('User', array("action" => "save_options"));
if($user['User']['send_notifications']){
	$options = array('checked'=>'checked');
}else{
	$options = null;
}
echo $form->checkbox('send_notifications', $options); 
echo '<label for="UserSendNotifications">' . __('Send me notification emails', true) . '</label>';
echo $form->end(__('Save',true));
?>
</div>