<?php
define("INC_DIR", __DIR__);
define("XLOGION_DB_HOST", '127.0.0.1');
define("XLOGION_DB_PORT", 3306);
define("XLOGION_DB_NAME", 'test');
define("XLOGION_DB_USERNAME", 'root');
define("XLOGION_DB_PASSWORD", '');

$db_config=array(
			'host' => XLOGION_DB_HOST,
			'port' => XLOGION_DB_PORT,
			'dbname' => XLOGION_DB_NAME,
			'username' => XLOGION_DB_USERNAME,
			'password' => XLOGION_DB_PASSWORD,
			'charset' => 'utf8',
			'tablepre' => 'test_'
			);
include(INC_DIR."/autoloader.php");
