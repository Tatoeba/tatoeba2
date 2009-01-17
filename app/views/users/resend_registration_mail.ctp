<?php
echo '<h2>';
__('Resend registration email');
echo '</h2>';

echo $form->create('User', array("action" => "resend_registration_mail"));
echo $form->input('email');
echo $form->end(__('Send',true));
?>