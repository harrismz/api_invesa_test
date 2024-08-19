<?php
	/*
	****	create by Mohamad Yunus
	****	on 29 Sept 2022
	****	revise:
	*/
	//	connect to database
    include 'conn.php';
	
	//	execute query
    $sql	= "select * from tbl_sync_version";
    $rs		= $db->Execute($sql);
	
	$rows = array();
	while(!$rs->EOF) {
		$rows['version'] = $rs->fields[0];
		$rs->MoveNext();
	}
	echo json_encode($rows);
    
	//	connection close
    $rs->Close();
    $db->Close();
?>