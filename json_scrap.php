<?php
	//	connect to database
    include 'conn.php';
	$db->setFetchMode(ADODB_FETCH_ASSOC);
	//	get paramater
    $accepted_param = ['valstdate','valendate','valpartno'];
    $start          = isset($_REQUEST["start"]) ? $_REQUEST["start"] : 0;
	$limit 		    = isset($_REQUEST["limit"]) ? $_REQUEST["limit"] : 20;
	$setLimit       = " limit $start,$limit ";
    $where          = " WHERE SUBSTRING(a.buktiterima,3,2) = 'SCR' ";

    foreach ($_REQUEST as $key => $value) {
        if(in_array($key,$accepted_param)){
            if($key == 'valstdate'){
                $where .= " AND a.datedokbcymd >=  '$value' ";
            }
            elseif($key == 'valendate'){
                $where .= " AND a.datedokbcymd like  '%{$value}%' ";
            }
            elseif($key == 'valpartno'){
                $where .= " AND partno like  '%{$value}%' ";
            }
        }
    }

	//	execute query
    $sql	= " SELECT 
                a.PARTNO, a.PARTNAME, a.UNIT, '0' as saldo_awal,a.qty as pemasukan, b.qty as pengeluaran,a.qty - b.qty  as saldo_akhir, a.input_user, a.input_date as last_input, a.sync_date as last_sync,
                b.input_user as output_user,b.input_date as last_output, b.sync_date as last_output_sync
                FROM invesa.tbl_sync_input a
                left join invesa.tbl_sync_output b on b.partno = a.partno and b.partname = a.partname and b.datedokbcymd >= a.datedokbcymd
                ";
    $rs = $db->getAll($sql . $where . $setLimit);
    $totalcount = sizeOf($db->getAll($sql . $where));
	
    $o = array(
        "success"=>true,
        // "query" => $sql,
        "totalCount"=>$totalcount,
        "rows"=>$rs);
        
    echo json_encode($o);
    $db->Close();
?>