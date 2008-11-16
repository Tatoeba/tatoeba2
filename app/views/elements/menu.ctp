<h1>Menu</h1>

<ul>
<li>
<?php 
echo $html->link(__('Home',true), 
	array(
		"controller" => "pages",
		"action" => "display",
		"home"
		));
?>
</li>

<li>
<?php 
echo $html->link(
	__('Show sentences',true),
	array(
		"controller" => "sentences",
		"action" => "index"
	));
?>
</li>

<li>
<?php 
echo $html->link(
	__('Add a sentence',true),
	array(
		"controller" => "sentences",
		"action" => "add"
	));
?>
</li>
</ul>