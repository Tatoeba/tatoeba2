<div id="UserSettings">
<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>

<div id="main_content">
	<div class="module">
		<h2>Settings</h2>

		<h3><?php __('Change password'); ?></h3>
		<?php
		echo $form->create('User', array("action" => "save_password"));
		echo $form->input('old_password/passwd', array("label" => __('Old password',true)));
		echo $form->input('new_password/passwd', array("label" => __('New password',true)));
		echo $form->input('new_password2/passwd', array("label" => __('New password again',true)));
		echo $form->end(__('Save',true));
		?>

		<h3><?php __('Change email'); ?></h3>
		<?php
		echo '</h2>';
		echo $form->create('User', array("action" => "save_email"));
		echo $form->input('email', array("label" => __('Email',true), "value" => $user['User']['email']));
		echo $form->end(__('Save',true));
		?>

		<h3><?php __('Change options'); ?></h3>
		<?php
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
</div>

</div>
