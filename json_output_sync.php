<?php
	//	connect to database
    include 'conn.php';
	
	//	get paramater
	$valstdate	= trim(@$_REQUEST["valstdate"]);
	$valendate	= trim(@$_REQUEST["valendate"]);
	$valjnsdok	= trim(@$_REQUEST["valjnsdok"]);
	$valnodok	= trim(@$_REQUEST["valnodok"]);
	$valpartno	= trim(@$_REQUEST["valpartno"]);

    // Ensure $start and $limit are integers with default values
    $start = isset($_REQUEST["page"]) && is_numeric($_REQUEST["page"]) ? (int)$_REQUEST["page"] : 0;
    $limit = isset($_REQUEST["limit"]) && is_numeric($_REQUEST["limit"]) ? (int)$_REQUEST["limit"] : 10;

	// $start		= ($page*$limit);
	
	//	execute query
    // $sql	= "call disp_input_sync($start,$limit, '{$valstdate}', '{$valendate}', '{$valjnsdok}', '{$valnodok}', '{$valpartno}')";
    $sql	= "call sync_disp_output($start,$limit, '{$valstdate}', '{$valendate}', '{$valjnsdok}', '{$valnodok}', '{$valpartno}');";
    
    $rs		= $db->Execute($sql);
    
    $totalcount = $rs->fields[21];
	
	//	array data
	$return 	= array();
    for ($i=0; !$rs->EOF; $i++){
        $return[$i]['id']			= $rs->fields[0];
        $return[$i]['jnsdokbc']  	= $rs->fields[1];
        $return[$i]['nodokbc'] 		= $rs->fields[2];
        $return[$i]['datedokbc'] 	= $rs->fields[3];
        $return[$i]['datedokbcymd']	= $rs->fields[4];
        $return[$i]['buktikirim'] 	= $rs->fields[5];
        $return[$i]['datekirim'] 	= $rs->fields[6];
        $return[$i]['datekirimymd'] 	= $rs->fields[7];
        $return[$i]['buktiinvoice']	= $rs->fields[8];
        $return[$i]['dateinvoice']	= $rs->fields[9];
        $return[$i]['dateinvoiceymd']	= $rs->fields[10];
        $return[$i]['supplier'] 	= $rs->fields[11];
        $return[$i]['partno'] 		= $rs->fields[12];
        $return[$i]['partname'] 	= $rs->fields[13];
        $return[$i]['qty'] 			= number_format($rs->fields[14], 2, '.', ',');
        $return[$i]['unit'] 		= $rs->fields[15];
        $return[$i]['price'] 		= number_format($rs->fields[16], 2, '.', ',');
        $return[$i]['currency'] 	= $rs->fields[17];
        $return[$i]['input_user']	= $rs->fields[18];
        $return[$i]['input_date']	= $rs->fields[19];
        $return[$i]['sync_date']	= $rs->fields[20];
        // $return[$i]['totalcount']	= $rs->fields[21];
        $rs->MoveNext();
    }
    
    $o = array(
        "success"=>true,
        "totalCount"=>$totalcount,
        "rows"=>$return);
        
    echo json_encode($o);
    
	//	connection close
    $rs->Close();
    $db->Close();
?>