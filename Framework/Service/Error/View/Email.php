<?php
/**
* Error report email content template.
*
* @author Michael Luthor <michaelluthor@163.com>
* @version 0.0.0
* @since Version 0.0.0
* @var $this \X\Service\Error\Handler\Email
*/
$error = $this->getError();
?>
代码 : <?php echo $error->getCode();?> 
消息 : <?php echo $error->getMessage();?> 
文件 : <?php echo $error->getFile(); ?> 
行号 : <?php echo $error->getLine(); ?> 
===============================================================
上下文 : 
<?php print_r($error->getContext());?><?php echo "\n"; ?> 
===============================================================
堆栈 : 
<?php debug_print_backtrace(); ?> 
===============================================================
$_SERVER : 
<?php var_export($_SERVER); ?> 
===============================================================
$_REQUEST : 
<?php var_export($_REQUEST); ?> 
===============================================================
<?php if ( isset($_SESSION) ) : ?> 
$_SESSION : 
<?php var_export($_SESSION); ?> 
<?php endif; ?> 
===============================================================
(完)
