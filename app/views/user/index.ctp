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
<?php
if(!$session->read('Auth.User.id')){
	echo $this->element('login');
} else {
	/*
	 * Include specific css and js files
	 */
	echo $javascript->link('profile.edit.js', false);
	echo $html->css('tatoeba.profile', false);
?>

<div id="main_content">
	<div class="module">
		<h3><?php echo $user['User']['username'] ?></h3>
		<div id="pimg">
<?php
echo $html->image('profiles/' . (empty($user['User']['image']) ? 'tatoeba_user.png' : $user['User']['image'] ), array(
	'alt' => $user['User']['username'],
));
?>
		</div>
		<div id="pimg_edit" class="toolbox">
			<div class="t">
                <?php __('Change your profile image?') ?>
                <span class="x" title="<?php __('Close') ?>">
                    <?php echo $html->image('close.png', array('alt' => __('Close', true))); ?>
                </span>
            </div>
			<div class="c">
<?php
echo $form->create('profile_image', array(
		'url' => array(
			'controller' => 'user',
			'action' => 'save_image'
		),
		'type' => 'file'
	));
echo $form->file('image');
echo $form->end(__('Upload', true));
?>
			</div>
		</div>
	</div>

	<div id="pdescription_edit" class="toolbox">
		<div class="t"><?php __('Tell us something about you') ?><span class="x" title="<?php __('Close') ?>"><?php echo $html->image('close.png', array('alt' => __('Close', true))); ?></span></div>
		<div class="c">
<?php
	echo $form->create('profile_description', array(
			'url' => array(
				'controller' => 'user',
				'action' => 'save_description'
			)
	));
	echo $form->textarea('description', array(
		'value' => $user['User']['description']
	));
	echo $form->end(__('Save', true));
?>
		</div>
	</div>

	<div id="pdescription" class="module">
		<h3><?php __('Something about you') ?><span id="pdescription_edit_link" class="edit_link"><?php echo $html->image('edit.png', array('alt' => __('Edit', true))); ?></span></h3>
		<div id="profile_description"><?php echo (empty($user['User']['description']) ? __('Tell us something about you!', true) : nl2br($user['User']['description'])) ?></div>
	</div>

	<div id="pbasic_edit" class="toolbox">
		<div class="t"><?php __('Complete some information?') ?><span class="x" title="<?php __('Close') ?>"><?php echo $html->image('close.png', array('alt' => __('Close', true))); ?></span></div>
		<div class="c">
<?php
	echo $form->create('profile_basic', array(
			'url' => array(
				'controller' => 'user',
				'action' => 'save_basic'
			)
	));
	echo $form->input('name', array(
			'label' => 'Name',
			'value' => $user['User']['name']
	));
	
	$aBirthday = explode('-', substr($user['User']['birthday'], 0, 10));
	// 0 => YYYY
	// 1 => MM
	// 2 => DD
	
	$iTimestamp = mktime(0, 0, 0, $aBirthday[1], $aBirthday[2], $aBirthday[0]);

	echo $form->input('birthday', array(
			'type' => 'date',
			'dateFormat' => 'MDY',
			'minYear' => date('Y') - 70,
			'maxYear' => date('Y') - 6,
			'label' => 'Birthday',
			'selected' => $iTimestamp
		));
	echo '<label for="profile_basicCountry">' . __('Country', true) . '</label>' . $form->select('country', $countries, (is_null($user['User']['country_id']) ? null : $user['Country']['id']));
	echo $form->end(__('Edit', true));
?>
		</div>
	</div>

	<div id="pbasic" class="module">
		<h3><?php __('Basic Information') ?><span id="pbasic_edit_link" class="edit_link"><?php echo $html->image('edit.png', array('alt' => __('Edit', true))); ?></span></h3>
		<dl>
			<dt><?php __('Name'); ?></dt>
			<dd><?php echo (empty($user['User']['name']) ? _('Tell us what is your real name to get to know you!') : $user['User']['name']) ?></dd>
			<dt><?php __('Birthday'); ?></dt>
			<dd><?php echo (((integer) $aBirthday[0] == 0) ? __('You have not set your birthday yet!', true) : date('F j, Y', $iTimestamp)) ?></dd>
			<dt><?php __('Country'); ?></dt>
			<dd><?php echo (is_null($user['User']['country_id']) ? __('Tells us where you come from!', true) : $user['Country']['name']) ?></dd>
		</dl>
	</div>

	<div class="module">
		<h3><?php __('Activity information'); ?></h3>
		<dl>
			<dt><?php __('Member since'); ?></dt>
			<dd><?php echo date('F j, Y', strtotime($user['User']['since'])) ?></dd>
			<dt><?php __('Last login'); ?></dt>
			<dd><?php echo date('F j, Y \\a\\t G:i', $user['User']['last_time_active']) ?></dd>
			<dt><?php __('Comments posted'); ?></dt>
			<dd><?php echo count($user['SentenceComments']) ?></dd>
			<dt><?php __('Sentences owned'); ?></dt>
			<dd><?php echo count($user['Sentences']) ?></dd>
			<dt><?php __('Sentences favorited'); ?></dt>
			<dd><?php echo count($user['Favorite']) ?></dd>
		</dl>
	</div>

	<div id="pcontact_edit" class="toolbox">
		<div class="t"><?php __('Complete some information?') ?><span class="x" title="<?php __('Close') ?>"><?php echo $html->image('close.png', array('alt' => __('Close', true))); ?></span></div>
		<div class="c">
<?php
	echo $form->create('profile_contact', array(
			'url' => array(
				'controller' => 'user',
				'action' => 'save_contact'
			)
	));
	echo $form->input('email', array(
			'label' => 'E-mail',
			'value' => $user['User']['email']
	));
	echo $form->input('url', array(
			'label' => 'Homepage',
			'value' => (empty($user['User']['homepage']) ? 'http://' : $user['User']['homepage'])
	));
	echo $form->end(__('Edit', true));
?>
		</div>
	</div>

	<div id="pcontact" class="module">
		<h3><?php __('Contact information') ?><span id="pcontact_edit_link" class="edit_link"><?php echo $html->image('edit.png', array('alt' => __('Edit', true))); ?></span></h3>
		<dl>
			<dt><?php __('E-mail'); ?></dt>
			<dd><?php echo $user['User']['email'] ?></dd>
			<dt><?php __('Homepage'); ?></dt>
			<dd><?php echo empty($user['User']['homepage']) ? __('Maybe you have a blog to share?',true) : '<a href="' . $user['User']['homepage'] . '" title="' . $user['User']['username'] . '">' . $user['User']['homepage'] . '</a>' ?></dd>
		</dl>
	</div>

	<div id="psettings" class="module">
		<h3><?php __('Settings') ?></h3>
		<?php echo $form->create('profile_setting', array(
			'url' => array(
				'controller' => 'user',
				'action' => 'save_settings'
			)
		)); ?>
		<div><?php echo $form->checkbox('send_notifications', (($user['User']['send_notifications']) ? array('checked'=>'checked') : array())); ?><label for="profile_settingSendNotifications"><?php __('Email notifications'); ?></label></div>
		<div><?php echo $form->checkbox('public_profile', (($user['User']['is_public']) ? array('checked'=>'checked') : array())); ?><label for="profile_settingPublicProfile"><?php __('Set your profile public?'); ?></label></div>
		<?php echo $form->end(__('Save', true)) ?>
	</div>

	<div id="ppassword" class="module">
		<h3><?php __('Change password'); ?></h3>
		<?php echo $form->create('profile_password', array(
				'url' => array(
					'controller' => 'user',
					'action' => 'save_password'
				)
		)); ?>
		<?php echo $form->input('old_password/passwd', array("label" => __('Old password', true))); ?>
		<?php echo $form->input('new_password/passwd', array("label" => __('New password', true))); ?>
		<?php echo $form->input('new_password2/passwd', array("label" => __('New password again', true))); ?>
		<?php echo $form->end(__('Save', true)); ?>
	</div>

</div>
<?php
}
?>
