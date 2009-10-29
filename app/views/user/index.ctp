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
	echo $javascript->link('profile.edit.js', true);
	echo $html->css('tatoeba.profile', true);
?>

<div id="annexe_content">
	<div class="module">
		<h3>Your dashboard</h3>
		<ul>
			<li>Settings</li>
			<li>Inbox (0)</li>
			<li>Favorites</li>
		</ul>
	</div>
</div>

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
			<div class="t"><?php __('Change your profile image?') ?><span class="x" title="<?php __('Close') ?>">[x]</span></div>
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

	<div id="pbasic_edit" class="toolbox">
		<div class="t"><?php __('Complete some informations?') ?><span class="x" title="<?php __('Close') ?>">[x]</span></div>
		<div class="c">
<?php
	echo $form->create('profile_basic', array(
			'url' => array(
				'controller' => 'user',
				'action' => 'save_image'
			)
	));
	echo $form->input('name', array(
			'label' => 'Name',
			'value' => $user['User']['name']
	));

	$sBirthday = !is_null($user['User']['birthday']) ? 'DD/MM/YYYY' : date('d/m/Y', $user['User']['birthday']);
	
	echo $form->input('birthday', array(
			'label' => 'Birthday',
			'value' => $sBirthday
	));
	echo $form->input('country', array(
		'label' => 'Country'
	));
	echo $form->end(__('Edit', true));
?>
		</div>
	</div>

	<div id="pbasic" class="module">
		<h3><?php __('Basic Information') ?><span id="pbasic_edit_link" class="edit_link">[<?php __('Edit') ?>]</span></h3>
		<dl>
			<dt>Name</dt>
			<dd><?php echo empty($user['User']['name']) ? _('Tell us what is your real name to get to know you!') : $user['User']['name'] ?></dd>
			<dt>Birthday</dt>
			<dd><?php echo ($sBirthday == 'DD/MM/YYYY' ? __('You haven\'t set your birthday yet!', true) : $sBirthday) ?></dd>
			<dt>Country</dt>
			<dd><?php echo ($user['User']['country_id'] == 0) ? 'Tells us where you come from!' : $user['User']['country_id'] ?></dd>
		</dl>
	</div>

</div>
<?php
/*
	<div class="module">
		<h3>Activity informations</h3>
		<dl>
			<dt>Joined</dt>
			<dd><?php echo date('r', strtotime($user['User']['since'])) ?></dd>
			<dt>Last login</dt>
			<dd><?php echo date('r', $user['User']['last_time_active']) ?></dd>
			<dt>Comment posted</dt>
			<dd>?</dd>
			<dt>Sentences owned</dt>
			<dd>?</dd>
			<dt>Sentences favorited</dt>
			<dd>?</dd>
		</dl>
	</div>
	<div class="module">
		<h3>Basic informations</h3>
		<dl>
			<dt>Username</dt>
			<dd><?php echo $user['User']['username'] ?></dd>
			<dt>Name</dt>
			<dd><?php echo $user['Profile']['name'] ?></dd>
			<dt>Birthday</dt>
			<dd><?php echo date('r', strtotime($user['Profile']['birthday'])) ?></dd>
			<dt>country</dt>
			<dd><?php echo $user['Profile']['country_id'] ?></dd>
		</dl>
	</div>

	<div class="module">
		<h3>Contact informations</h3>
		<dl>
			<dt>E-mail</dt>
			<dd><?php echo $user['User']['email'] ?></dd>
			<dt>URL</dt>
			<dd><?php echo $user['Profile']['url'] ?></dd>
		</dl>
	</div>

	<div class="module">
		<h3>Settings</h3>
		<dl>
			<dt>Language</dt>
			<dd><?php echo $user['User']['lang'] // array('eng' => 'English', 'fre' => 'Français', 'chi' => '中文', 'spa' => 'Español') ?></dd>
			<dt>Notification</dt>
			<dd><?php echo ($user['User']['send_notifications'] ? 'Activated' : 'Desactivated') ?></dd>
			<dt>Is public ?</dt>
			<dd>Your profile is <?php echo ($user['Profile']['is_public'] ? 'public' : 'private') ?></dd>
		</dl>
	</div>
 */
}
?>