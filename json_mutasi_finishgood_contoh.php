<?php
//	connect to database
include 'conn.php';
$db->setFetchMode(ADODB_FETCH_ASSOC);

//	get paramater
$accepted_param = ['periode','partno','category','tempat'];
// $param_converter = ['tglawal', 'tglakhir','tempat', 'category', 'bulanan'];
$params = [];
// $params['tempat'] = 'Gudang Scrap';
$params['bulanan'] = 1;
$start_lha = '20220201';

foreach ($_REQUEST as $key => $value) {
    if (in_array($key, $accepted_param)) {
        if($key == 'periode'){
            $params['tglawal'] = $value.'01';
            $params['tglakhir'] = date('Ymt', strtotime($params['tglawal']));
        }
        else {
            $params[$key] = $value;
        }
    }
}
//	execute query
// call invesa.display_part_for_simulation('2022/02/01', '2024/04/30', 'Gudang Scrap', 0, 1);
$sql    = " call invesa.monthly_mutation_report_barang_jadi_contoh('{$params['tglawal']}','{$params['tglakhir']}','{$params['tempat']}','{$params['bulanan']}') ";
// $sql = "SELECT kode_barang, nama_barang, satuan, saldo_awal, pemasukan, pengeluaran, penyesuaian, saldo_buku, stock_opname, selisih, keterangan 
//         FROM invesa.montly_mutation_report
//         where periode = '{$_REQUEST['periode']}'
//         and kategori in ({$_REQUEST['periode']});";
$predata = $db->getAll($sql);


$totalcount = sizeOf($predata);


echo json_encode([
    "success" => true,
    "totalCount" => $totalcount,
    "rows" => $predata
]);

?>