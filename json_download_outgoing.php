<?php
	//	connect to database
    include 'conn.php';
	
	//	get paramater
	$valstdate	= trim(@$_REQUEST["valstdate"]);
	$valendate	= trim(@$_REQUEST["valendate"]);
	$valjnsdok	= trim(@$_REQUEST["valjnsdok"]);
	$valnodok	= trim(@$_REQUEST["valnodok"]);
	$valpartno	= trim(@$_REQUEST["valpartno"]);
    // $start		= ($page*$limit);   

    //	execute query
    // $sql	= "call disp_input_sync($start,$limit, '{$valstdate}', '{$valendate}', '{$valjnsdok}', '{$valnodok}', '{$valpartno}')";
    // $sql = DB::select("call sync_down_input('{$stdate}', '{$endate}', '{$jnsdokbc}', '{$nodokbc}', '{$partno}');");
    $sql	= "call sync_down_output('{$valstdate}', '{$valendate}', '{$valjnsdok}', '{$valnodok}', '{$valpartno}');";
    $rs		= $db->Execute($sql);
	//	array data
	$return 	= array();
    for ($i=0; !$rs->EOF; $i++){

        // <td align="right">
            // {{ $no }}&nbsp;</td>
            // <td>{{ $rowdata->jnsdokbc }}</td>
            // <td>{{ $rowdata->nodokbc }}</td>
            // <td>{{ $rowdata->datedokbc }}</td>
            // <td>{{ $rowdata->buktikirim }}</td>
            // <td>{{ $rowdata->datekirim }}</td>
            // <td>{{ $rowdata->buktiinvoice }}</td>
            // <td>{{ $rowdata->dateinvoice }}</td>
            // <td>{{ $rowdata->supplier }}</td>
            // <td>{{ $rowdata->partno }}</td>
            // <td>{{ $rowdata->partname }}</td>
            // <td align="right">{{ number_format($rowdata->qty, 0) }}</td>
            // <td>{{ $rowdata->unit }}</td>
            // <td align="right">{{ number_format($rowdata->price, 0) }}</td>
            // <td>{{ $rowdata->currency }}</td>
            // <td>{{ $rowdata->input_user }} {{ $rowdata->input_date }}</td>

        $return[$i]['id']			= $rs-> fields['nodokbc'];
        $return[$i]['jnsdokbc']  	= $rs-> fields['jnsdokbc'];
        $return[$i]['nodokbc'] 		= $rs-> fields['nodokbc'];
        $return[$i]['datedokbc'] 	= $rs-> fields['datedokbc'];        
        $return[$i]['datedokbcymd'] = $rs-> fields['datedokbcymd'];        
        $return[$i]['buktikirim']	= $rs-> fields['buktikirim'];        
        $return[$i]['datekirim']	= $rs-> fields['datekirim'];        
        $return[$i]['datekirimymd']	= $rs-> fields['datekirimymd'];       
        $return[$i]['buktiinvoice']	= $rs-> fields['buktiinvoice'];        
        $return[$i]['dateinvoice']	= $rs-> fields['dateinvoice'];
        $return[$i]['dateinvoiceymd']	= $rs-> fields['dateinvoiceymd'];
        $return[$i]['supplier'] 	= $rs-> fields['supplier'];
        $return[$i]['partno'] 		= $rs-> fields['partno'];
        $return[$i]['partname'] 	= $rs-> fields['partname'];
        $return[$i]['qty'] 			= $rs-> fields['qty'];
        $return[$i]['unit'] 		= $rs-> fields['unit'];
        $return[$i]['price'] 		= $rs-> fields['price'];
        $return[$i]['currency'] 	= $rs-> fields['currency'];
        $return[$i]['input_user']	= $rs-> fields['input_user'];
        $return[$i]['input_date']	= $rs-> fields['input_date'];
        $return[$i]['sync_date']	= $rs-> fields['sync_date'];
        $rs->MoveNext();
    }
    
    $o = array(
        "success"=>true,
        "rows"=>$return);
        
    echo json_encode($o);
    
	//	connection close
    $rs->Close();
    $db->Close();
?>