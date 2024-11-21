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

$sql = "    SELECT 
            muta.`kode_barang`,
            pp.name_template AS nama_barang,
            muta.`satuan`,
            CASE 
                WHEN (muta.gudang = 'Gudang Material' AND muta.kategori = 'Bahan baku')
                    THEN COALESCE((saldo_awal.saldo_awal), 0)
                WHEN (muta.gudang = 'Gudang Finished Goods' AND muta.kategori = 'Hasil produksi')
                    THEN COALESCE((saldo_awal.saldo_awal), 0)
                ELSE 
                    COALESCE(muta.saldo_awal, 0)
            END AS saldo_awal,
            COALESCE(pemasukan.pemasukan, 0) AS pemasukan,
            COALESCE(pengeluaran.pengeluaran, 0) AS pengeluaran,
            CASE 
                WHEN (muta.gudang = 'Gudang Material' AND muta.kategori = 'Bahan baku')
                    THEN COALESCE((ifnull(saldo_awal.saldo_awal,0) + pemasukan.pemasukan - pengeluaran.pengeluaran), 0)
                WHEN (muta.gudang = 'Gudang Finished Goods' AND muta.kategori = 'Hasil produksi')
                    THEN COALESCE((ifnull(saldo_awal.saldo_awal,0) + pemasukan.pemasukan - pengeluaran.pengeluaran), 0)
                ELSE 
                COALESCE(muta.saldo_buku, 0)
            END AS saldo_buku,
            muta.`penyesuaian`,
            COALESCE(so.jumlah, 0) AS stock_opname,
            CASE 
                WHEN EXISTS (SELECT 1 FROM `invesa`.`stock_opname_report` sor WHERE sor.periode = REPLACE(muta.periode, '-', '') LIMIT 1)
                THEN COALESCE(so.jumlah, 0) - COALESCE(
                    CASE 
                        WHEN muta.gudang = 'Gudang Material' AND muta.kategori = 'Bahan baku' 
                            THEN saldo_awal.saldo_awal + pemasukan.pemasukan - pengeluaran.pengeluaran
                        WHEN muta.gudang = 'Gudang Finished Goods' AND muta.kategori = 'Hasil produksi' 
                            THEN saldo_awal.saldo_awal + pemasukan.pemasukan - pengeluaran.pengeluaran
                        ELSE COALESCE(muta.saldo_buku, 0)
                    END, 0)
                ELSE 0
            END AS selisih,
            muta.`keterangan`,
            muta.`kategori`,
            muta.`gudang`,
            muta.`created_at`
        FROM 
            `invesa`.`monthly_mutation_report` muta
        LEFT JOIN 
            (SELECT kode_barang, periode, gudang, kategori, SUM(saldo_awal) AS saldo_awal
            FROM `invesa`.`sd51_summary` 
            GROUP BY kode_barang, periode, gudang, kategori) saldo_awal
            ON saldo_awal.kode_barang = muta.kode_barang 
            AND saldo_awal.periode = muta.periode 
            AND saldo_awal.gudang = muta.gudang 
            AND saldo_awal.kategori = muta.kategori
        LEFT JOIN 
            (SELECT kode_barang, SUM(pemasukan) AS pemasukan, gudang, kategori, periode, satuan
            FROM `invesa`.`monthly_mutation_report` 
            WHERE periode = '{$_REQUEST['periode']}' 
            GROUP BY kode_barang, gudang, kategori ) pemasukan
            ON pemasukan.kode_barang = muta.kode_barang
             AND pemasukan.periode = muta.periode 
            AND pemasukan.gudang = muta.gudang 
            AND pemasukan.kategori = muta.kategori
            AND pemasukan.satuan = muta.satuan
        LEFT JOIN 
            (SELECT kode_barang, SUM(pengeluaran) AS pengeluaran , gudang, kategori, periode, satuan
            FROM `invesa`.`monthly_mutation_report` 
            WHERE periode = '{$_REQUEST['periode']}' 
            GROUP BY kode_barang, gudang, kategori ) pengeluaran
            ON pengeluaran.kode_barang = muta.kode_barang
             AND pengeluaran.periode = muta.periode 
            AND pengeluaran.gudang = muta.gudang 
            AND pengeluaran.kategori = muta.kategori
            AND pemasukan.satuan = muta.satuan
        LEFT JOIN 
            `invesa`.`stock_opname_report` so 
            ON so.gudang = muta.gudang 
            AND so.kategori_barang = muta.kategori 
            AND so.kode_barang = muta.kode_barang 
            AND REPLACE(muta.periode, '-', '') = so.periode
        LEFT JOIN 
            product_product pp ON pp.product_no = muta.`kode_barang`
        ";

$group_by = " GROUP BY 
                muta.`kode_barang`, 
                muta.`satuan`, 
                so.jumlah, 
                muta.`keterangan`, 
                muta.`kategori`, 
                muta.`gudang`, 
                muta.`created_at` ";

// $query = "SELECT (@rownumber := @rownumber + 1) AS no, * FROM ( $sql $where $group_by ) AS mutation WHERE (saldo_awal <> 0 or pemasukan <> 0 or pengeluaran <> 0 )";
// $query = $sql . $where;
// $count = $db->getAll("SELECT COUNT(id) as total FROM `invesa`.`monthly_mutation_report` $where");
// if((isset($count[0]['total']) ? $count[0]['total'] : '0') >= 2500 ){
// echo $sql . $where . $group_by;
// die();
$query = "SELECT (@rownumber := @rownumber + 1) AS no
                , mutation.kode_barang, mutation.nama_barang, mutation.satuan, mutation.saldo_awal
                , mutation.pemasukan, mutation.pengeluaran, mutation.penyesuaian
                , mutation.saldo_buku, mutation.stock_opname,mutation.selisih
                , mutation.keterangan, mutation.kategori, mutation.gudang
                , mutation.created_at
            FROM ($sql $where $group_by ) AS mutation";
        
    $db->execute("SET @rownumber = 0;");
    $rs = $db->getAll($query);
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
