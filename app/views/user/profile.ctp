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
			<dt><?php __('Homepage'); ?></dt>
			<dd><?php echo '<a href="' . $user['User']['homepage'] . '" title="' . $user['User']['username'] . '">' . $user['User']['homepage'] . '</a>' ?></dd>
<?php
}
?>
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
			<dt><?php __('Name'); ?></dt>
			<dd><?php echo $user['User']['name'] ?></dd>
<?php
}

$aBirthday = explode('-', substr($user['User']['birthday'], 0, 10));
// 0 => YYYY
// 1 => MM
// 2 => DD
// needed to do a substr because $user['User']['birthday'] is formatted as YYYY-MM-DD HH:mm:ss

if(intval($aBirthday[0] + $aBirthday[1] + $aBirthday[2]) != 0){
	$iTimestamp = mktime(0, 0, 0, $aBirthday[1], $aBirthday[2], $aBirthday[0]);

?>
			<dt><?php __('Birthday'); ?></dt>
			<dd><?php echo date('F j, Y', $iTimestamp) ?></dd>
<?php
}

if(is_string($user['User']['country_id']) and strlen($user['User']['country_id']) == 2){
?>
			<dt><?php __('Country'); ?></dt>
			<dd><?php echo $user['Country']['name'] ?></dd>
<?php
}
?>
		</dl>
	</div>

</div>

<?php
}else{

	echo '<p>';
	__('This profile is protected. You must login to see it.');
	echo '</p>';
	
}
?>
