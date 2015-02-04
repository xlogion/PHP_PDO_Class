<?php
header("content-type:text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include("inc/config.php");
$db = new xlogion\pdo_class($db_config);
//详细见readme
$db->close();