<?php
//	connect to database
include 'conn.php';
$db->setFetchMode(ADODB_FETCH_ASSOC);

$sql = " SELECT sequence, name from product_category order by sequence asc ";
$rs  = $db->getAll($sql);

$data = [];
foreach ($rs as $value) {
   $data[$value['sequence']] = $value['name'];
}

echo json_encode([
    'data' => $data
]);

$db->Close();
