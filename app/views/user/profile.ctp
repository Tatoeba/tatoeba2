<?php
if($is_public or $login){

	echo $javascript->includeScript('users.followers_and_following');
?>

<div id="annexe_content">

	<div id="pcontact" class="module">
		<h2><?php __('Contact information') ?></h2>
		<?php /*
		* Lists definitions are hype. Ok... But there I don't think it's the best
		* usability way for users... one title with one link. The link says all and
		* the title just adds overinformation.
		* That's why I propose the simple list variant below. To be test and validated.
		*
		<dl>
			<dt><?php __('Private message'); ?></dt>
			<dd><?php echo $html->link(sprintf(__('Contact %s', true), $user['User']['username']),
			array('controller' => 'privateMessages', 'action' => 'write', $user['User']['username'])); ?></dd>

            <dt><?php __('Others'); ?></dt>
			<dd><?php echo $html->link(sprintf(__("See this user's contributions", true)),
			array('controller' => 'users', 'action' => 'show', $user['User']['id'])); ?></dd>

            <dt><?php __('Follow'); ?></dt>
			<dd><a href="#" id="followingOption"><span class="user" id="<?php echo $user['User']['id']; ?>"><?php echo sprintf(__("Start following %s", true), $user['User']['username']); ?></span></a></dd>
<?php
if(!empty($user['User']['homepage'])){
?>
			<dt><?php __('Homepage'); ?></dt>
			<dd><?php echo '<a href="' . $user['User']['homepage'] . '" title="' . $user['User']['username'] . '">' . $user['User']['homepage'] . '</a>' ?></dd>
<?php
}
?>
		</dl>*/ ?>
		<ul>
			<li><?php echo $html->link(sprintf(__('Contact %s via Private Message', true), $user['User']['username']),
			array('controller' => 'privateMessages', 'action' => 'write', $user['User']['username'])); ?>
			</li>
			
			<?php
			if($session->read('Auth.User.id') AND isset($can_follow)){
				echo '<li class="user" id="_'.$user['User']['id'].'">';
				if($can_follow){
					$style2 = "style='display: none'";
					$style1 = "";
				}else{
					$style1 = "style='display: none'";
					$style2 = "";
				}
				echo '<a id="start" class="followingOption" '.$style1.'>'. __('Start following this person', true). '</a>';
				echo '<a id="stop" class="followingOption" '.$style2.'>'. __('Stop following this person', true). '</a>';
				echo '<span class="in_process"></span>';
				echo '<li>';
			}
			?>
			
			<li>
			</li><?php echo $html->link(sprintf(__("See this user's contributions", true)),
			array('controller' => 'users', 'action' => 'show', $user['User']['id'])); ?>
			<li>
			</li>
		</ul>
	</div>


		<!--<div class="followers"></div>-->

	<div class="module">
		<h2><?php __('Activity information'); ?></h2>
		<dl>
			<dt><?php __('Member since'); ?></dt>
			<dd><?php echo date('F j, Y', strtotime($user['User']['since'])) ?></dd>
			<dt><?php __('Last login'); ?></dt>
			<dd><?php echo date('F j, Y \\a\\t G:i', $user['User']['last_time_active']) ?></dd>
			<dt><?php __('Comments posted'); ?></dt>
			<dd><?php echo $userStats['numberOfComments'] ?></dd>
			<dt><?php __('Sentences owned'); ?></dt>
			<dd><?php echo $userStats['numberOfSentences'] ?></dd>
			<dt><?php __('Sentences favorited'); ?></dt>
			<dd><?php echo $userStats['numberOfFavorites'] ?></dd>
		</dl>
	</div>
</div>

<div id="main_content">
	<div class="module">
		<h2><?php if($user['User']['name'] != '') echo $user['User']['name'] . ' aka. ' . $user['User']['username'];
		else echo $user['User']['username'] ?></h2>
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
		<h2><?php __('Something about you') ?></h2>
		<div id="profile_description"><?php echo nl2br($user['User']['description']) ?></div>
	</div>
<?php
}
?>
	<div id="pbasic" class="module">
		<h2><?php __('Basic Information') ?></h2>
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
