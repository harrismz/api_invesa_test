<?php
ini_set('display_errors', 1);
ini_set('max_execution_time', 3000);
ini_set('memory_limit', '1024M');
error_reporting(E_ALL);
//	connect to database
include 'conn.php';
$db->setFetchMode(ADODB_FETCH_ASSOC);
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
    if ($where == '') {
        $where = ' WHERE ';
    } else {
        $where .= ' AND ';
    }
    if (in_array($key, $accepted_param)) {
        if ($key == 'periode' || $key == 'gudang' || $key == 'kategori') {
            $where .= " $key =  '{$value}' ";
        } else {
            $where .= " $key like  '%{$value}%' ";
        }
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
            `monthly_mutation_report`.`penyesuaian`,
            `monthly_mutation_report`.`saldo_buku`,
            `monthly_mutation_report`.`stock_opname`,
            `monthly_mutation_report`.`selisih`,
            `monthly_mutation_report`.`keterangan`,
            `monthly_mutation_report`.`kategori`,
            `monthly_mutation_report`.`gudang`,
            `monthly_mutation_report`.`created_at`
        FROM `invesa`.`monthly_mutation_report` ";

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
