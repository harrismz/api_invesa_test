<?php
//	connect to database
include 'conn.php';
$db->setFetchMode(ADODB_FETCH_ASSOC);
$params['bulanan'] = 1;

// setup date
echo $start_date = date("Y-m-01");
echo '<br>';
echo $start_date = eomonth(date("Y-m-01")); 



// 

?>