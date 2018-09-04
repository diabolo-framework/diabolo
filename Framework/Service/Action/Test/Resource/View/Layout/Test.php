<?php use X\Service\Action\Test\Resource\Widget\Hello\Widget as HelloWidget;?>
<?php if(false) {$globalKey001 = 1;} # never goes here, just wanna remove the ide warning. ?>
<body>
GLOBAL KEY 001 : <?php echo $globalKey001; ?>

<?php echo $this->getParticleViewManager()->toString(); ?>

<?php HelloWidget::setup(array('user'=>'diabolo'), $this)->display(); ?>
</body>