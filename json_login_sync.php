<?php

	//	connect to database
    include 'conn.php';
	
	//	get paramater
	$valuserid	= trim(@$_REQUEST["valuserid"]);
	$valuserpass= trim(@$_REQUEST["valuserpass"]);
	$valipaddress= trim(@$_REQUEST["valipaddress"]);
	$sql= trim(@$_REQUEST["sql"]);
	
	//	execute query
    $sql	= "call sync_check_login('{$valuserid}', '{$valuserpass}', '{$valipaddress}','{$sql}')";
    $rs		= $db->Execute($sql);
	
	$rows = array();
	while(!$rs->EOF) {
		$rows['id'] 		= $rs->fields[0];
		$rows['login'] 		= $rs->fields[1];
		$rows['password'] 	= $rs->fields[2];
		$rows['name'] 		= $rs->fields[3];
		$rows['message']	= $rs->fields[4];
		$rs->MoveNext();
	}
	echo json_encode($rows);
    
	//	connection close
    $rs->Close();
    $db->Close();
?>