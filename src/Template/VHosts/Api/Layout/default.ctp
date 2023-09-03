<!DOCTYPE html>
<html lang="en">
<head>
<title><?= h($this->fetch('title')); ?></title>
<?php
echo $this->Html->css('/css/api.css');
?>
</head>
<body>

<nav>
  <h1><?= h($this->fetch('title')) ?></h1>
</nav>

<?= $this->fetch('content') ?>

</body>
</html>
