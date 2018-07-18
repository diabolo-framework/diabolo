<?php 
define('TEST_DB_NAME_MYSQL', 'mysqlTestDB');
define('TEST_DB_NAME_SQLITE', 'sqliteTestDB');

require dirname(dirname(dirname(dirname(__FILE__))))."/Core/X.php";
X\Core\X::start(__DIR__.DIRECTORY_SEPARATOR."configuration.php");