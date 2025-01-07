<?php
//	connect to database
include 'conn.php';
$db->setFetchMode(ADODB_FETCH_ASSOC);

//	get paramater
$accepted_param = ['periode', 'work_center', 'dic', 'kode_barang', 'nama_barang', 'jumlah', 'page', 'limit'];

$start          = isset($_REQUEST["page"]) ? $_REQUEST["page"] : 0;
$limit          = isset($_REQUEST["limit"]) ? $_REQUEST["limit"] : 20;
$setLimit       = " limit $start,$limit ";
$where          = ' WHERE jumlah > 0 ';

foreach ($_REQUEST as $key => $value) {
    if (!in_array($key, $accepted_param)) {
        continue;
    }
    if ($value == '') {
        continue;
    }
    if ($key == 'page') {
        continue;
    }
    if ($key == 'limit') {
        continue;
    }
    if ($where == '') {
        $where = ' WHERE ';
    } else {
        $where .= ' AND ';
    }
    if (in_array($key, $accepted_param)) {
        if($key == 'periode'){
            $where .= " $key =  '{$value}' ";
        }
        else{
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
        FROM `invesa`.`sd47` ";
        
$rs = $db->getAll($sql . $where . $setLimit);
$totalcount = sizeOf($db->getAll($sql . $where));

$o = array(
    "success" => true,
    "query" => $sql . $where . $setLimit,
    "totalCount" => $totalcount,
    "rows" => $rs
);

echo json_encode($o);
$db->Close();
