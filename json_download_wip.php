<?php
ini_set('display_errors', 1);
ini_set('max_execution_time', 3000);
ini_set('memory_limit', '1024M');
error_reporting(E_ALL);
//	connect to database
include 'conn.php';
$db->setFetchMode(ADODB_FETCH_ASSOC);
//	get paramater
$accepted_param = ['periode'];
$where          = '';
foreach ($_REQUEST as $key => $value) {
    if (!in_array($key, $accepted_param)) {
        continue;
    }
    if ($value == '') {
        continue;
    }
    if ($where == '') {
        $where = ' WHERE ';
    } else {
        $where .= ' AND ';
    }
    if (in_array($key, $accepted_param)) {
        if ($key == 'periode') {
            $where .= " left($key,6) =  '{$value}' ";
        } else {
            $where .= " $key like  '%{$value}%' ";
        }
    }
}

$db->execute("SET @rownumber = 0;");
$sql = "    SELECT (@rownumber := @rownumber + 1) AS no,
            `sd47`.`periode`,
            `sd47`.`work_center`,
            `sd47`.`dic`,
            `sd47`.`kode_barang`,
            `sd47`.`nama_barang`,
            `sd47`.`satuan`,
            `sd47`.`jumlah`
        FROM `invesa`.`sd47`";

$query = $sql . $where;

$rs = $db->getAll($query);

$o = array(
    "success" => true,
    "rows" => $rs
);

echo json_encode($o);
$db->Close();
