<?php
ini_set('display_errors', 1);
ini_set('max_execution_time', 3000);
ini_set('memory_limit', '1024M');
error_reporting(E_ALL);
//	connect to database
include 'conn.php';
$db->setFetchMode(ADODB_FETCH_NUM);
//	get paramater
$accepted_param = ['periode', 'gudang', 'kategori'];
$where          = '';
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
        if ($key == 'periode' || $key == 'gudang' || $key == 'kategori') {
            $where .= " muta.$key =  '{$value}' ";
            continue;
        }
        if ($key == 'stock_opname') {
            $where .= " so.jumlah = '{$value}' ";
            continue;
        }
        if ($key == 'selisih') {
            $where .= " (muta.saldo_buku - so.jumlah) like '%{$value}%' ";
            continue;
        }
        $where .= " muta.$key like  '%{$value}%' ";
    }
}

$db->execute("SET @rownumber = 0;");
// $sql = "    SELECT (@rownumber := @rownumber + 1) AS no,
//             `monthly_mutation_report`.`kode_barang`,
//             `monthly_mutation_report`.`nama_barang`,
//             `monthly_mutation_report`.`satuan`,
//             `monthly_mutation_report`.`saldo_awal`,
//             `monthly_mutation_report`.`pemasukan`,
//             `monthly_mutation_report`.`pengeluaran`,
//             `monthly_mutation_report`.`penyesuaian`,
//             `monthly_mutation_report`.`saldo_buku`,
//             `monthly_mutation_report`.`stock_opname`,
//             `monthly_mutation_report`.`selisih`,
//             `monthly_mutation_report`.`keterangan`,
//             `monthly_mutation_report`.`kategori`,
//             `monthly_mutation_report`.`gudang`,
//             `monthly_mutation_report`.`created_at`
//         FROM `invesa`.`monthly_mutation_report` ";
$sql = "    SELECT (@rownumber := @rownumber + 1) AS no,
            muta.`kode_barang`,
            muta.`nama_barang`,
            muta.`satuan`,
            muta.`saldo_awal`,
            muta.`pemasukan`,
            muta.`pengeluaran`,
            muta.`saldo_buku`,
            muta.`penyesuaian`,
            ifnull(so.jumlah,0) as stock_opname,
            case when (select count(id) from `invesa`.`stock_opname_report` where periode = REPLACE(muta.periode, '-', '')) > 0
            then 
            (ifnull(so.jumlah,0) - ifnull(muta.saldo_buku,0))
            else 0 
            end 
            as selisih,
            muta.`keterangan`,
            muta.`kategori`,
            muta.`gudang`,
            muta.`created_at`
        FROM `invesa`.`monthly_mutation_report` muta
        left join `invesa`.`stock_opname_report` so 
				on so.gudang = muta.gudang 
				and so.kategori_barang = muta.kategori
                and so.kode_barang = muta.kode_barang
                and so.nama_barang = muta.nama_barang
                and REPLACE(muta.periode, '-', '') <= so.periode ";
// $query = $sql . $where;
// $count = $db->getAll("SELECT COUNT(id) as total FROM `invesa`.`monthly_mutation_report` $where");
// if((isset($count[0]['total']) ? $count[0]['total'] : '0') >= 2500 ){
    $rs = $db->getAll($sql . $where);
// }
// $rs = $db->getAll($query);
// die();



// var_dump($rs);
// $limit = 2500; // Jumlah baris per batch
// $offset = 0;  // Mulai dari baris pertama

// $data = null;
// do {
//     $sql = "SELECT * FROM your_table LIMIT $limit OFFSET $offset";
//     $result = $db->getAll($sql . $where );

//     // Proses data
//     while ($row = $result->fetch_assoc()) {
//         // Tambahkan data ke file CSV atau hasil lainnya
//         $data .= $row;
//     }

//     $offset += $limit; // Pindahkan ke batch berikutnya
// } while ($result->num_rows > 0);


$o = array(
    "success" => true,
    "rows" => $rs
);

echo json_encode($o);
$db->Close();
