<?php
require_once('../ADODB/adodb5/adodb.inc.php');
require_once('../ADODB/adodb5/adodb-exceptions.inc.php');
require_once('../ADODB/adodb5/adodb-errorpear.inc.php');

//server
$db = ADONewConnection("mysqli");
$db->Connect('136.198.117.80', 'root', 'JvcSql@123', 'invesa');
?>
