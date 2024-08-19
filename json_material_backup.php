<?php
	//	connect to database
    include 'conn.php';
	$db->setFetchMode(ADODB_FETCH_ASSOC);
	//	get paramater
    $start      = isset($_REQUEST["start"]) ? $_REQUEST["start"] : 0;
	$limit 		= isset($_REQUEST["limit"]) ? $_REQUEST["limit"] : 20;
	$setLimit   = " limit $start,$limit ";

    $accepted_param = ['valstdate','valendate','valjnsdok','valnodok','valpartno'];
    $where = " WHERE LEFT(buktiterima,4) = 'BBGM' ";
    foreach ($_REQUEST as $key => $value) {
        if(in_array($key,$accepted_param)){
            // ($where=="") ? $where = " WHERE " : $where = " AND ";
            if($key == 'valstdate'){
                $where .= " AND datedokbcymd >=  '$value' ";
            }
            elseif($key == 'valendate'){
                $where .= " AND datedokbcymd like  '%{$value}%' ";
            }
            elseif($key == 'valjnsdok'){
                $where .= " AND jnsdokbc like  '%{$value}%' ";
            }
            elseif($key == 'valnodok'){
                $where .= " AND nodokbc like  '%{$value}%' ";
            }
            elseif($key == 'valpartno'){
                $where .= " AND partno like  '%{$value}%' ";
            }
        }
    }
	//	execute query
    $sql	= " SELECT * FROM invesa.tbl_sync_input ";
    // echo $sql . $where . $setLimit;
    $rs		= $db->getAll($sql . $where . $setLimit);
    
    $total = $db->getAll($sql . $where);
    $totalcount = sizeOf($total);
	
	// //	array data
	// $return 	= array();
    // for ($i=0; !$rs->EOF; $i++){
    //     $return[$i]['id']			= $rs->fields[0];
    //     $return[$i]['jnsdokbc']  	= $rs->fields[1];
    //     $return[$i]['nodokbc'] 		= $rs->fields[2];
    //     $return[$i]['datedokbc'] 	= $rs->fields[3];
    //     $return[$i]['buktiterima'] 	= $rs->fields[4];
    //     $return[$i]['dateterima'] 	= $rs->fields[5];
    //     $return[$i]['buktiinvoice']	= $rs->fields[6];
    //     $return[$i]['dateinvoice']	= $rs->fields[7];
    //     $return[$i]['supplier'] 	= $rs->fields[8];
    //     $return[$i]['partno'] 		= $rs->fields[9];
    //     $return[$i]['partname'] 	= $rs->fields[10];
    //     $return[$i]['qty'] 			= $rs->fields[11];
    //     $return[$i]['unit'] 		= $rs->fields[12];
    //     $return[$i]['price'] 		= $rs->fields[13];
    //     $return[$i]['currency'] 	= $rs->fields[14];
    //     $return[$i]['datedokbcymd']	= $rs->fields[15];
    //     $rs->MoveNext();
    // }
    
    $o = array(
        "success"=>true,
        "totalCount"=>$totalcount,
        "rows"=>$rs);
        
    echo json_encode($o);
    
	//	connection close
    // $rs->Close();
    $db->Close();
?>