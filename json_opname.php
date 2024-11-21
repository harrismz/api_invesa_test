<?php
//	connect to database
include 'conn.php';
$db->setFetchMode(ADODB_FETCH_ASSOC);
//	get paramater
$accepted_param = ['nama_barang', 'kategori_barang', 'kode_barang', 'satuan', 'jumlah', 'jenis_dokumen_bc', 'no_bc', 'tanggal_bc', 'keterangan','periode','gudang','created_by', 'created_at', 'upated_at', 'page','limit'];
 
$start          = isset($_REQUEST["page"]) ? $_REQUEST["page"] : 0;
$limit          = isset($_REQUEST["limit"]) ? $_REQUEST["limit"] : 20;
$setLimit       = " limit $start,$limit ";
$where          = '';
// echo json_encode($_REQUEST);
foreach ($_REQUEST as $key => $value) {
    if (!in_array($key, $accepted_param)) {
        continue;
    }
    if($value == '')
    {
        continue;
    }
    if ($key == 'page') {
        continue;
    }
    if ($key == 'limit') {
        continue;
    }
    if($where == ''){
        $where = ' WHERE ';
    }
    else{
        $where .= ' AND ';
    }
    if (in_array($key, $accepted_param)) {
            if($key == 'periode' || $key == 'gudang' || $key == 'kategori'){
                $where .= " $key =  '{$value}' ";
            }
            else{
                $where .= " $key like  '%{$value}%' ";
            }
    }
}

$db->execute("SET @rownumber = 0;");
$sql = "    SELECT (@rownumber := @rownumber + 1) AS no,
            `stock_opname_report`.`nama_barang`,
            `stock_opname_report`.`kategori_barang`,
            `stock_opname_report`.`kode_barang`,
            `stock_opname_report`.`satuan`,
            `stock_opname_report`.`jumlah`,
            `stock_opname_report`.`jenis_dokumen_bc`,
            `stock_opname_report`.`no_bc`,
            `stock_opname_report`.`tanggal_bc`,
            `stock_opname_report`.`keterangan`,
            `stock_opname_report`.`periode`,
            `stock_opname_report`.`gudang`,
            `stock_opname_report`.`created_at`,
            `stock_opname_report`.`updated_at`,
            `stock_opname_report`.`created_by`
        FROM `invesa`.`stock_opname_report` ";

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
