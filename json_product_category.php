<?php
// connect to database
include 'conn.php';
$db->setFetchMode(ADODB_FETCH_ASSOC);
// execute query
$sql = " SELECT id,name FROM invesa.product_category ";
$rs = $db->getAll($sql);

$o = array(
    "success" => true,
    "rows" => $rs
);

echo json_encode($o);
$db->Close();
