<?php
//	connect to database
include 'conn.php';
$db->setFetchMode(ADODB_FETCH_ASSOC);
//	get paramater
// $lastsyncinvesaweb  = DB::table('tbl_sync_data')->value('sync_date');

$sql_sync_date = "SELECT sync_date from tbl_sync_data;";
$result_sync_date = $db->getAll($sql_sync_date);

$sql_bar_twomonth = "CALL sync_home_2monthago();";
$result_bar_twomonth = $db->getAll($sql_bar_twomonth);
$sql_docin_twomonth = "CALL sync_docin_2monthago();";
$result_docin_twomonth = $db->getAll($sql_docin_twomonth);
$sql_docout_twomonth = "CALL sync_docout_2monthago();";
$result_docout_twomonth = $db->getAll($sql_docout_twomonth);

$sql_bar_onemonth = "CALL sync_home_1monthago();";
$result_bar_onemonth = $db->getAll($sql_bar_onemonth);
$sql_docin_onemonth = "CALL sync_docin_1monthago();";
$result_docin_onemonth = $db->getAll($sql_docin_onemonth);
$sql_docout_onemonth = "CALL sync_docout_1monthago();";
$result_docout_onemonth = $db->getAll($sql_docout_onemonth);

$sql_bar_currmonth = "CALL sync_home_currmonth();";
$result_bar_currmonth = $db->getAll($sql_bar_currmonth);
$sql_docin_currmonth = "CALL sync_docin_currmonth();";
$result_docin_currmonth = $db->getAll($sql_docin_currmonth);
$sql_docout_currmonth = "CALL sync_docout_currmonth();";
$result_docout_currmonth = $db->getAll($sql_docout_currmonth);

// $sql = "";
// $rs = $db->getAll($sql);
// $totalcount = sizeOf($db->getAll($sql));

$o = array(
    "success" => true,
    "lastsyncinvesaweb" => $result_sync_date,

    "sql_bar_twomonth" => $result_bar_twomonth,
    "sql_docin_twomonth" => $result_docin_twomonth,
    "sql_docout_twomonth" => $result_docout_twomonth,

    "sql_bar_onemonth" => $result_bar_onemonth,
    "sql_docin_onemonth" => $result_docin_onemonth,
    "sql_docout_onemonth" => $result_docout_onemonth,

    "sql_bar_currmonth" => $result_bar_currmonth,
    "sql_docin_currmonth" => $result_docin_currmonth,
    "sql_docout_currmonth" => $result_docout_currmonth,
);

echo json_encode($o);
$db->Close();
