<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, minimum-scale=1, initial-scale=1, user-scalable=yes">
<title><?= h(ucfirst($pagetitle ?? $this->fetch('title'))) ?></title>
<link rel="icon" href="/favicon.svg">
<?php
echo $this->Html->css('/css/api.css');
echo $this->fetch('css');
echo $this->fetch('script');
?>
</head>
<body>

<nav>
  <h1><?= $this->Html->Link($this->fetch('title'), '/') ?>
  <?php if (isset($pagetitle)): ?>
    <?= h('> ') . $this->Html->Link($pagetitle, []) ?>
  <?php endif; ?>
  </h1>
</nav>

<?= $this->fetch('content') ?>

<footer>
  <div class="container">
    <ul>
      <?= $this->fetch('navlinks') ?>
      <li><?= $this->Html->Link('Go to Tatoeba', $this->Api->getTatoebaUrl()) ?></li>
    </ul>
  </div>
</footer>
</body>
</html>
