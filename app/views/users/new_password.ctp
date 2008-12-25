<?php
echo $form->create('User', array("action" => "new_password"));
echo $form->input('email');
echo $form->end(__('Send',true));
?>