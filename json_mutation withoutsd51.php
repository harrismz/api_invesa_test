<?php
//	connect to database
include 'conn.php';
$db->setFetchMode(ADODB_FETCH_ASSOC);
//	get paramater
$accepted_param = ['periode','gudang','kategori', 'kode_barang', 'nama_barang', 'satuan', 'saldo_awal', 'pemasukan', 'pengeluaran','penyesuaian','saldo_buku','saldo_buku', 'selisih', 'keterangan', 'page','limit'];
 
$start          = isset($_REQUEST["page"]) ? $_REQUEST["page"] : 0;
$limit          = isset($_REQUEST["limit"]) ? $_REQUEST["limit"] : 20;
$setLimit       = " limit $start,$limit ";
// $where          = ' and (`muta`.`saldo_awal` <> 0 or `muta`.`pemasukan` <> 0 or `muta`.`pengeluaran`<> 0) ';
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

//	execute query
// $sql    = " SELECT 
//                 a.PARTNO, a.PARTNAME, a.UNIT, '0' as saldo_awal,a.qty as pemasukan, b.qty as pengeluaran,a.qty - b.qty  as saldo_akhir, a.input_user, a.input_date as last_input, a.sync_date as last_sync,
//                 b.input_user as output_user,b.input_date as last_output, b.sync_date as last_output_sync
//                 FROM invesa.tbl_sync_input a
//                 left join invesa.tbl_sync_output b on b.partno = a.partno and b.partname = a.partname and b.datedokbcymd >= a.datedokbcymd ";

$db->execute("SET @rownumber = 0;");
// $sql = "    SELECT (@rownumber := @rownumber + 1) AS no,
//             `monthly_mutation_report`.`kode_barang`,
//             `monthly_mutation_report`.`nama_barang`,
//             `monthly_mutation_report`.`satuan`,
//             `monthly_mutation_report`.`saldo_awal`,
//             `monthly_mutation_report`.`pemasukan`,
//             `monthly_mutation_report`.`pengeluaran`,
//             `monthly_mutation_report`.`saldo_buku`,
//             `monthly_mutation_report`.`penyesuaian`,
//             `monthly_mutation_report`.`stock_opname`,
//             `monthly_mutation_report`.`selisih`,
//             `monthly_mutation_report`.`keterangan`,
//             `monthly_mutation_report`.`kategori`,
//             `monthly_mutation_report`.`created_at`
//         FROM `invesa`.`monthly_mutation_report` ";
// $sql = "    SELECT (@rownumber := @rownumber + 1) AS no,
//             muta.`kode_barang`,
//             muta.`nama_barang`,
//             muta.`satuan`,
//             case 
//             when (muta.gudang = 'Gudang Material' AND muta.kategori = 'Bahan baku')
//                 then sd51_summary.saldo_awal
//             when  (muta.gudang = 'Gudang Finished Goods' AND muta.kategori = 'Hasil produksi')
//                 then sd51_summary.saldo_awal
//             else
//                 ifnull(muta.saldo_awal,0)
//             end as saldo_awal,
//             muta.`pemasukan`,
//             muta.`pengeluaran`,
//             muta.`saldo_buku`,
//             muta.`penyesuaian`,
//             ifnull(so.jumlah,0) as stock_opname,
//             case when (select count(id) from `invesa`.`stock_opname_report` where periode = REPLACE(muta.periode, '-', '')) > 0
//             then 
//             (ifnull(so.jumlah,0) - ifnull(muta.saldo_buku,0))
//             else 0 
//             end 
//             as selisih,
//             muta.`keterangan`,
//             muta.`kategori`,
//             muta.`created_at`
//         FROM `invesa`.`monthly_mutation_report` muta
//         left join `invesa`.`stock_opname_report` so 
// 				on so.gudang = muta.gudang 
// 				and so.kategori_barang = muta.kategori
//                 and so.kode_barang = muta.kode_barang
//                 and so.nama_barang = muta.nama_barang
//                 and REPLACE(muta.periode, '-', '') = so.periode ";
//         // "where muta.`gudang` = 'Gudang Umum'
//         // and muta.`kategori` = 'Barang modal - Mesin'
//         // and muta.`periode` = '2024-08'
//         // and muta.kode_barang = 'SR-1000W' ";
$sql = "SELECT 
            (@rownumber := @rownumber + 1) AS no,
            muta.`kode_barang`,
            (select name_template from product_product where product_no = muta.`kode_barang` limit 1) as nama_barang,
            muta.`satuan`,
            saldo_awal.saldo_awal,
            pemasukan.pemasukan,
            pengeluaran.pengeluaran,
            CASE 
                WHEN muta.gudang = 'Gudang Material' AND muta.kategori = 'Bahan baku' THEN ifnull((saldo_awal.saldo_awal + pemasukan.pemasukan - pengeluaran.pengeluaran),0)
                WHEN muta.gudang = 'Gudang Finished Goods' AND muta.kategori = 'Hasil produksi' THEN ifnull((saldo_awal.saldo_awal + pemasukan.pemasukan - pengeluaran.pengeluaran),0)
                ELSE IFNULL(muta.saldo_buku, 0)
            END AS saldo_buku,
            muta.`penyesuaian`,
            IFNULL(so.jumlah, 0) AS stock_opname,
            CASE 
                WHEN (SELECT COUNT(id) FROM `invesa`.`stock_opname_report` WHERE periode = REPLACE(muta.periode, '-', '')) > 0
                THEN (IFNULL(so.jumlah, 0) - IFNULL(CASE 
                    WHEN muta.gudang = 'Gudang Material' AND muta.kategori = 'Bahan baku' THEN ifnull((saldo_awal.saldo_awal + pemasukan.pemasukan - pengeluaran.pengeluaran),0)
                    WHEN muta.gudang = 'Gudang Finished Goods' AND muta.kategori = 'Hasil produksi' THEN ifnull((saldo_awal.saldo_awal + pemasukan.pemasukan - pengeluaran.pengeluaran),0)
                    ELSE IFNULL(muta.saldo_buku, 0)
                END, 0))
                ELSE 0
            END AS selisih,
            muta.`keterangan`,
            muta.`kategori`,
            muta.`created_at`
        FROM 
            `invesa`.`monthly_mutation_report` muta
        LEFT JOIN 
            (SELECT kode_barang, periode, gudang, kategori, SUM(saldo_awal) AS saldo_awal FROM `invesa`.`sd51_summary` GROUP BY kode_barang, periode, gudang, kategori) saldo_awal
            ON saldo_awal.kode_barang = muta.kode_barang AND saldo_awal.periode = muta.periode AND saldo_awal.gudang = muta.gudang AND saldo_awal.kategori = muta.kategori
        LEFT JOIN 
            (SELECT kode_barang, SUM(pemasukan) AS pemasukan FROM `invesa`.`monthly_mutation_report` WHERE periode = '{$_REQUEST['periode']}' GROUP BY kode_barang) pemasukan
            ON pemasukan.kode_barang = muta.kode_barang
        LEFT JOIN 
            (SELECT kode_barang, SUM(pengeluaran) AS pengeluaran FROM `invesa`.`monthly_mutation_report` WHERE periode = '{$_REQUEST['periode']}' GROUP BY kode_barang) pengeluaran
            ON pengeluaran.kode_barang = muta.kode_barang
        LEFT JOIN 
            `invesa`.`stock_opname_report` so 
            ON so.gudang = muta.gudang AND so.kategori_barang = muta.kategori AND so.kode_barang = muta.kode_barang AND so.nama_barang = muta.nama_barang AND REPLACE(muta.periode, '-', '') = so.periode
        ";

$group_by = " GROUP BY 
                muta.`kode_barang`, 
                muta.`satuan`, 
                so.jumlah, 
                muta.`keterangan`, 
                muta.`kategori`, 
                muta.`created_at`; ";

        // echo $sql . $where . $setLimit;
        // return;
$rs = $db->getAll($sql . $where . $group_by . $setLimit);
$totalcount = sizeOf($db->getAll($sql . $where. $group_by));

$o = array(
    "success" => true,
    "query" => $sql . $where . $setLimit,
    "totalCount" => $totalcount,
    "rows" => $rs
);

echo json_encode($o);
$db->Close();
