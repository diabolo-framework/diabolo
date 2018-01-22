<?php
/**
* Error report email content template.
*
* @author Michael Luthor <michaelluthor@163.com>
* @version 0.0.0
* @since Version 0.0.0
*/
$vars = get_defined_vars();
$error = $vars['error'];
?>
代码 : <?php echo $error['code'];?> 
消息 : <?php echo $error['message'];?> 
文件 : <?php echo $error['file']; ?> 
行号 : <?php echo $error['line']; ?> 
===============================================================
上下文 : 
<?php print_r($error['context']);?><?php echo "\n"; ?> 
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
