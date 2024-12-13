<?php
//	connect to database
include 'conn.php';
$db->setFetchMode(ADODB_FETCH_ASSOC);
//	get paramater
$accepted_param = ['periode','gudang','kategori', 'kode_barang', 'nama_barang', 'satuan', 'saldo_awal', 'pemasukan', 'pengeluaran','penyesuaian','saldo_buku','saldo_buku', 'selisih', 'keterangan', 'page','limit'];
 
$start          = isset($_REQUEST["page"]) ? $_REQUEST["page"] : 0;
$limit          = isset($_REQUEST["limit"]) ? $_REQUEST["limit"] : 20;
$setLimit       = " limit $start,$limit ";
// $where          = ' WHERE (`muta`.`saldo_awal` <> 0 or `muta`.`pemasukan` <> 0 or `muta`.`pengeluaran`<> 0 or `saldo_awal`.`saldo_awal` <> 0 ) ';
$where      = '';
$where_sd51 = '';
// echo json_encode($_REQUEST);
// die();
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
        if($key == 'periode' || $key == 'gudang'){
            $where .= " muta.$key =  '{$value}' ";
            continue;
        }
        if($key == 'kategori'){
            if($_REQUEST['gudang'] == 'Gudang Service Part' || $_REQUEST['gudang'] == 'Gudang Scrap')
            {
                continue;
            }
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
        if ($where_sd51 == '') {
            $where_sd51 = ' WHERE ';
        } else {
            $where_sd51 .= ' AND ';
        }
        $where_sd51 .= " final.$key like '%{$value}%' ";
        
            
    }
}

$sql = "SELECT 
            muta.`kode_barang`,
            pp.name_template AS nama_barang,
            muta.`satuan`,
            CASE 
                WHEN (muta.gudang = 'Gudang Material' AND muta.kategori = 'Bahan baku')
                    THEN COALESCE((saldo_awal_sd51.saldo_awal), 0)
                ELSE 
                    COALESCE(saldo_awal_invesa.saldo_awal, 0)
            END AS saldo_awal,
            COALESCE(pemasukan.pemasukan, 0) AS pemasukan,
            COALESCE(pengeluaran.pengeluaran, 0) AS pengeluaran,
            CASE 
                WHEN (muta.gudang = 'Gudang Material' AND muta.kategori = 'Bahan baku')
                    THEN COALESCE((saldo_awal_sd51.saldo_awal + pemasukan.pemasukan - pengeluaran.pengeluaran), 0)
                ELSE 
                COALESCE((saldo_awal_invesa.saldo_awal + pemasukan.pemasukan - pengeluaran.pengeluaran), 0)
            END AS saldo_buku,
            muta.`penyesuaian`,
            COALESCE(so.jumlah, 0) AS stock_opname,
            CASE 
                WHEN EXISTS (SELECT 1 FROM `invesa`.`stock_opname_report` sor WHERE sor.periode = REPLACE(muta.periode, '-', '') LIMIT 1)
                THEN COALESCE(so.jumlah, 0) - COALESCE(
                    CASE 
                        WHEN muta.gudang = 'Gudang Material' AND muta.kategori = 'Bahan baku' 
                            THEN COALESCE((saldo_awal_sd51.saldo_awal + pemasukan.pemasukan - pengeluaran.pengeluaran), 0)
                        ELSE COALESCE((saldo_awal_invesa.saldo_awal + pemasukan.pemasukan - pengeluaran.pengeluaran), 0)
                    END, 0)
                ELSE 0
            END AS selisih,
            muta.`keterangan`,
            muta.`kategori`,
            muta.`gudang`,
            muta.`periode`,
            muta.`created_at`
        FROM 
            `invesa`.`monthly_mutation_report` muta
        LEFT JOIN 
            (SELECT kode_barang, periode, gudang, kategori, SUM(saldo_awal) AS saldo_awal
            FROM `invesa`.`sd51_summary` 
            GROUP BY kode_barang, periode, gudang, kategori) saldo_awal_sd51
            ON saldo_awal_sd51.kode_barang = muta.kode_barang 
            AND saldo_awal_sd51.periode = muta.periode 
            AND saldo_awal_sd51.gudang = muta.gudang 
            AND saldo_awal_sd51.kategori = muta.kategori
        LEFT JOIN 
            (SELECT kode_barang, SUM(saldo_awal) AS saldo_awal, gudang, kategori, periode, satuan
            FROM `invesa`.`monthly_mutation_report` 
            WHERE periode = '{$_REQUEST['periode']}' 
            GROUP BY kode_barang, gudang, kategori ) saldo_awal_invesa
            ON saldo_awal_invesa.kode_barang = muta.kode_barang
             AND saldo_awal_invesa.periode = muta.periode 
            AND saldo_awal_invesa.gudang = muta.gudang 
            AND saldo_awal_invesa.kategori = muta.kategori
            AND saldo_awal_invesa.satuan = muta.satuan
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
                muta.`periode`, 
                muta.`created_at` ";

$query = "SELECT (@rownumber := @rownumber + 1) AS no, 
        mutation.kode_barang, mutation.nama_barang, mutation.satuan, 
        mutation.saldo_awal, mutation.pemasukan, mutation.pengeluaran,
        COALESCE((mutation.saldo_awal + mutation.pemasukan - mutation.pengeluaran),0) as saldo_buku, mutation.penyesuaian, mutation.stock_opname,
        mutation.selisih, mutation.keterangan, mutation.kategori, 
        mutation.gudang, mutation.periode, mutation.created_at FROM ( $sql $where $group_by ) AS mutation 
        WHERE (saldo_awal <> 0 or pemasukan <> 0 or pengeluaran <> 0 )";
        $query_count = $query;
                
// echo $sql . $where . $group_by . $setLimit;
// echo $sql . $where . $group_by . $setLimit;
// return;
// ($_REQUEST['gudang'] == 'Gudang Finished Goods' and isset($_REQUEST['kategori']) == 'Hasil produksi') 
// or 
if(($_REQUEST['gudang'] == 'Gudang Material' and isset($_REQUEST['kategori']) == 'Bahan baku'))
{
    $query = " SELECT (@rownumber := @rownumber + 1) AS no, final.kode_barang, final.nama_barang, final.satuan
                    ,final.saldo_awal, final.pemasukan, final.pengeluaran, (final.saldo_awal+final.pemasukan-final.pengeluaran) as saldo_buku
                    ,final.penyesuaian, final.stock_opname, final.selisih, final.keterangan, final.kategori, final.gudang, final.periode
                    ,final.created_at
               FROM
               (SELECT 
                    mutation.kode_barang, mutation.nama_barang, mutation.satuan, 
                    mutation.saldo_awal, mutation.pemasukan, mutation.pengeluaran,
                    mutation.saldo_buku, mutation.penyesuaian, mutation.stock_opname,
                    mutation.selisih, mutation.keterangan, mutation.kategori, 
                    mutation.gudang, mutation.periode, mutation.created_at FROM ( $sql $where $group_by ) AS mutation 
                    WHERE (saldo_awal <> 0 or pemasukan <> 0 or pengeluaran <> 0 )
               union all
               select distinct
                ifnull(git2.kode_barang ,sd51_summary.kode_barang) as kode_barang
                , IFNULL(git2.nama_barang, (select name_template from product_product where product_no = sd51_summary.kode_barang limit 1)) AS nama_barang
                , IFNULL(git2.satuan, (select name from product_uom where id = (select product_uom from product_product where product_no = sd51_summary.kode_barang  limit 1) limit 1)) AS satuan
                , ifnull(git2.saldo_awal, sd51_summary.saldo_awal) as saldo_awal
                , COALESCE((git2.pemasukan),0) as pemasukan
                , COALESCE((git2.pengeluaran),0) as pengeluaran
                , ifnull(git2.saldo_buku, (sd51_summary.saldo_awal + COALESCE((git2.pemasukan), 0) - COALESCE((git2.pengeluaran), 0))) as saldo_buku 
                , COALESCE((git2.penyesuaian), 0) as penyesuaian
                , COALESCE((git2.stock_opname), 0) as stock_opname
                , COALESCE((git2.selisih), 0) as selisih
                , git2.keterangan
                , IFNULL(git2.gudang, sd51_summary.gudang) AS gudang
                , IFNULL(git2.kategori, sd51_summary.kategori) AS kategori
                , IFNULL(git2.periode, sd51_summary.periode) AS periode
                , git2.created_at
                from
                ( SELECT 
                    mutation.kode_barang, mutation.nama_barang, mutation.satuan, 
                    mutation.saldo_awal, mutation.pemasukan, mutation.pengeluaran,
                    mutation.saldo_buku, mutation.penyesuaian, mutation.stock_opname,
                    mutation.selisih, mutation.keterangan, mutation.kategori, 
                    mutation.gudang, mutation.periode, mutation.created_at FROM ( $sql $where $group_by ) AS mutation 
                    WHERE (saldo_awal <> 0 or pemasukan <> 0 or pengeluaran <> 0 ) ) as git2
                right join sd51_summary on sd51_summary.periode = git2.periode 
                                AND sd51_summary.gudang = '{$_REQUEST['gudang']}' 
                                AND sd51_summary.kategori = '{$_REQUEST['kategori']}'
                                and sd51_summary.kode_barang = git2.kode_barang
                where sd51_summary.periode = '{$_REQUEST['periode']}' 
                    and sd51_summary.gudang = '{$_REQUEST['gudang']}' 
                    and sd51_summary.kategori = '{$_REQUEST['kategori']}'
                    and git2.kategori is null
                ) as final
                $where_sd51 ";
                $query_count = $query;
}

$db->execute("SET @rownumber = 0;");
// echo $query;
// die();
$rs = $db->getAll($query . $setLimit);
$totalcount = sizeOf($db->getAll($query_count));

$o = array(
    "success" => true,
    "query" => $query,
    "totalCount" => $totalcount,
    "rows" => $rs
);

echo json_encode($o);
$db->Close();
