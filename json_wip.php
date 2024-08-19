<?php
// connect to database
include 'conn.php';
$db->setFetchMode(ADODB_FETCH_ASSOC);
// get paramater
// $accepted_param = ['stdate', 'endate', 'category'];
// $start = isset($_REQUEST["start"]) ? $_REQUEST["start"] : 0;
// $limit = isset($_REQUEST["limit"]) ? $_REQUEST["limit"] : 20;
// $setLimit = " limit $start,$limit ";
// $where = " WHERE SUBSTRING(a.buktiterima,3,2) = 'FG' ";

// $where = '';
// foreach ($_REQUEST as $key => $value) {
//     if (in_array($key, $accepted_param)) {
//         if ($key == 'category') {
//             $where = " AND a.product_tmpl_id = '{$value}' ";
//         }
//     }
// }

// execute query
$sql = " SELECT a.name_template,CAST(a.code as char(50))as CODE,a.product_detail,a.product_no,b.name AS satuan,a.id
            from `product_product` a
            LEFT JOIN product_uom b ON a.product_uom=b.id
            WHERE  a.active=1  
            and a.product_tmpl_id = '{$_GET['category']}'
            order BY a.code ";
$rs = $db->getAll($sql);
$totalcount = sizeOf($db->getAll($sql));

$o = array(
    "success" => true,
    // "query" => $sql,
    "totalCount" => $totalcount,
    "rows" => $rs
);

echo json_encode($o);
$db->Close();
