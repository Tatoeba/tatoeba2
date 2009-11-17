<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

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


<div id="annexe_content">	
	<div class="module">
	<h2><?php __("Tip"); ?></h2>
	<?php __("You can add sentences that you do not know how to translate. Perhaps someone else will know!"); ?>
	</div>
	
	<?php 
	if($session->read('Auth.User.id')){ 
	?>
		<div class="module">
		<h2><?php __("Good translations"); ?></h2>
		<?php __("We know it's difficult, but do NOT translate word for word!"); ?>
		</div>
		
		<div class="module">
		<h2><?php __("Multiple translations"); ?></h2>
		<?php __("If you feel there are several possible translations, note that for a same sentence, you can add several translations in the same language."); ?>
		</div>
	<?php
	}
	?>
</div>

<div id="main_content">
	<?php 
	if($session->read('Auth.User.id')){ 
	?>
	
		<div class="module">
			<?php
			echo '<h2>';
			__('Add new sentences');
			echo '</h2>';
			
			echo $form->create('Sentence', array("action" => "add", "id" => "newSentence"));
			echo $form->input('text', array("label" => __('Sentence : ', true)));
			echo $form->end('OK');
			?>
		</div>
			
		<div class="module">
			<h2><?php 
			echo sprintf(
				__('Translate (%s) or adopt sentences (%s)',true),
				$html->image('translate.png'),
				$html->image('adopt.png')
			); 
			?></h2>
			<p><?php __("It's easy, try it out below with the random sentence.") ?></p>
		</div>
		
		<div class="module">
			<?php echo $this->element('random_sentence'); ?>
		</div>
		
	<?php
	}else{
	?>
		
		<div class="main_module">
		<h2><?php __('We need your help!'); ?></h2>
		
		<p><?php __('You can help us by:') ?></p>
		
		<ul>
			<li>
			<?php 
			echo ' '.$html->link(__('adding new sentences',true), array("controller"=>"pages", "action"=>"help#adding"));
			?>
			</li>
			<li>
			<?php 
			echo ' '.$html->link(__('translating existing sentences',true), array("controller"=>"pages", "action"=>"help#translating"));
			?>
			</li>
			<li>
			<?php 
			echo ' '.$html->link(__('correcting mistakes',true), array("controller"=>"pages", "action"=>"help#correcting"));
			?>
			</li>
			<li>
			<?php 
			echo ' '.$html->link(__('adopting sentences',true), array("controller"=>"pages", "action"=>"help#adopting"));
			?>
			</li>
		</ul>
		
		<?php
		__('If you are interested, please register.');
		echo $html->link(
			'register',
			array("controller" => "users", "action" => "register"),
			array("class"=>"registerButton")
		);
		?>
		</div>
		
	<?php
	}
	?>
</div>

