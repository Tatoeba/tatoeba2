<?php
if($is_public or $login){

	echo $html->css('tatoeba.profile', false);
?>

<div id="annexe_content">

	<div id="pcontact" class="module">
		<h3><?php __('Contact informations') ?></h3>
		<dl>
			<dt><?php __('Private message'); ?></dt>
			<dd><?php echo $html->link(sprintf(__('Contact %s', true), $user['User']['username']),
			array('controller' => 'privateMessages', 'action' => 'write', $user['User']['username'])); ?></dd>
			
            <dt><?php __('Others'); ?></dt>
			<dd><?php echo $html->link(sprintf(__("See this user's contributions", true)),
			array('controller' => 'users', 'action' => 'show', $user['User']['id'])); ?></dd>
<?php
if(!empty($user['User']['homepage'])){
?>
			<dt>Homepage</dt>
			<dd><?php echo '<a href="' . $user['User']['homepage'] . '" title="' . $user['User']['username'] . '">' . $user['User']['homepage'] . '</a>' ?></dd>
<?php
}
?>
		</dl>
	</div>

	<div class="module">
		<h3>Activity informations</h3>
		<dl>
			<dt>Joined</dt>
			<dd><?php echo date('r', strtotime($user['User']['since'])) ?></dd>
			<dt>Last login</dt>
			<dd><?php echo date('r', $user['User']['last_time_active']) ?></dd>
			<dt>Comment posted</dt>
			<dd><?php echo count($user['SentenceComments']) ?></dd>
			<dt>Sentences owned</dt>
			<dd><?php echo count($user['Sentences']) ?></dd>
			<dt>Sentences favorited</dt>
			<dd><?php echo count($user['Favorite']) ?></dd>
		</dl>
	</div>
</div>

<div id="main_content">
	<div class="module">
		<h3><?php if($user['User']['name'] != '') echo $user['User']['name'] . ' aka. ' . $user['User']['username'];
		else echo $user['User']['username'] ?></h3>
		<div id="pimg">
<?php
echo $html->image('profiles/' . (empty($user['User']['image']) ? 'tatoeba_user.png' : $user['User']['image'] ), array(
	'alt' => $user['User']['username'],
));
?>
		</div>
	</div>
<?php
if(!empty($user['User']['description'])){
?>
	<div id="pdescription" class="module">
		<h3><?php __('Something about you') ?></h3>
		<div id="profile_description"><?php echo nl2br($user['User']['description']) ?></div>
	</div>
<?php
}
?>
	<div id="pbasic" class="module">
		<h3><?php __('Basic Information') ?></h3>
		<dl>
<?php
if(!empty($user['User']['name'])){
?>
			<dt>Name</dt>
			<dd><?php echo $user['User']['name'] ?></dd>
<?php
}

$sBirthday = (empty($user['User']['birthday']) or $user['User']['birthday'] == 'DD/MM/YYYY') ? 'DD/MM/YYYY' : date('d/m/Y', strtotime($user['User']['birthday']));

if($sBirthday != 'DD/MM/YYYY'){
?>
			<dt>Birthday</dt>
			<dd><?php echo $sBirthday ?></dd>
<?php
}

if(is_string($user['User']['country_id']) and strlen($user['User']['country_id']) == 2){
?>
			<dt>Country</dt>
			<dd><?php echo $user['Country']['name'] ?></dd>
<?php
}
?>
		</dl>
	</div>

</div>

<?php
}else{
?>
This profile is protected. You must login to see it.
<?php
}
?>
