<?php
//	connect to database
include 'conn.php';
$db->setFetchMode(ADODB_FETCH_ASSOC);
//	get paramater
$accepted_param = ['periode', 'kode_barang', 'gudang','kategori','page','limit'];
$start          = isset($_REQUEST["page"]) ? $_REQUEST["page"] : 0;
$limit          = isset($_REQUEST["limit"]) ? $_REQUEST["limit"] : 20;
$setLimit       = " limit $start,$limit ";
$where          = '';
// echo json_encode($_REQUEST);
foreach ($_REQUEST as $key => $value) {
    if (!in_array($key, $accepted_param)) {
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
        $where .= " $key like  '%{$value}%' ";
    }
}

//	execute query
// $sql    = " SELECT 
//                 a.PARTNO, a.PARTNAME, a.UNIT, '0' as saldo_awal,a.qty as pemasukan, b.qty as pengeluaran,a.qty - b.qty  as saldo_akhir, a.input_user, a.input_date as last_input, a.sync_date as last_sync,
//                 b.input_user as output_user,b.input_date as last_output, b.sync_date as last_output_sync
//                 FROM invesa.tbl_sync_input a
//                 left join invesa.tbl_sync_output b on b.partno = a.partno and b.partname = a.partname and b.datedokbcymd >= a.datedokbcymd ";

$db->execute("SET @rownumber = 0;");
$sql = "    SELECT (@rownumber := @rownumber + 1) AS no,
            `monthly_mutation_report`.`kode_barang`,
            `monthly_mutation_report`.`nama_barang`,
            `monthly_mutation_report`.`satuan`,
            `monthly_mutation_report`.`saldo_awal`,
            `monthly_mutation_report`.`pemasukan`,
            `monthly_mutation_report`.`pengeluaran`,
            `monthly_mutation_report`.`saldo_buku`,
            `monthly_mutation_report`.`penyesuaian`,
            `monthly_mutation_report`.`stock_opname`,
            `monthly_mutation_report`.`selisih`,
            `monthly_mutation_report`.`keterangan`,
            `monthly_mutation_report`.`kategori`,
            `monthly_mutation_report`.`created_at`
        FROM `invesa`.`monthly_mutation_report` ";

        // echo $sql . $where . $setLimit;
        // return;
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
