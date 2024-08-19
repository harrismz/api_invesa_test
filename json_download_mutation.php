<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

//	connect to database
include 'conn.php';
$db->setFetchMode(ADODB_FETCH_ASSOC);
//	get paramater
$accepted_param = ['periode', 'kode_barang', 'gudang', 'kategori'];
$where          = '';
foreach ($_REQUEST as $key => $value) {
    if (!in_array($key, $accepted_param)) {
        continue;
    }
    if ($where == '') {
        $where = ' WHERE ';
    } else {
        $where .= ' AND ';
    }
    if (in_array($key, $accepted_param)) {
        $where .= " $key like  '%{$value}%' ";
    }
}

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

$rs = $db->getAll($sql . $where);
// var_dump($rs);
// echo $sql . $where;
$o = array(
    "success" => true,
    "rows" => $rs
);

echo json_encode($o);
$db->Close();
