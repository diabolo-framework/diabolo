<?php
define('TEST_DB_NAME_MYSQL', 'mysqlTestDB');
define('TEST_DB_NAME_SQLITE', 'sqliteTestDB');
define('TEST_DB_NAME_POSTGRESQL', 'postgresqlTestDB');
define('TEST_DB_NAME_ORACLE', 'oracleTestDB');
define('TEST_DB_NAME_MSSQL', 'mssqlTestDB');
define('TEST_DB_NAME_FIREBIRD', 'firebirdTestDB');

require dirname(dirname(dirname(dirname(__FILE__))))."/Core/X.php";
X\Core\X::start(__DIR__.DIRECTORY_SEPARATOR."configuration.php");
