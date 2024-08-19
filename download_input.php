<?php
	//	connect to database
    include 'conn.php';
	
	//	get paramater
	$stdate	= trim(@$_REQUEST["stdate"]);
	$endate	= trim(@$_REQUEST["endate"]);
	$jnsdokbc	= trim(@$_REQUEST["jnsdokbc"]);
	$nodokbc	= trim(@$_REQUEST["nodokbc"]);
	$partno	= trim(@$_REQUEST["partno"]);
	$filename	= trim(@$_REQUEST["filename"]);
    // $start  = @$_REQUEST["page"];
	// $limit  = @$_REQUEST["limit"];
	
	//	execute query
    $sql	= "call sync_down_input('{$stdate}', '{$endate}', '{$jnsdokbc}', '{$nodokbc}', '{$partno}');";
    $rs		= $db->Execute($sql);
  
  	//	array data
	$return 	= array();
    for ($i=0; !$rs->EOF; $i++){
        $return[$i]['id']			= $rs->fields[0];
        $return[$i]['jnsdokbc']  	= $rs->fields[1];
        $return[$i]['nodokbc'] 		= $rs->fields[2];
        $return[$i]['datedokbc'] 	= $rs->fields[3];
        $return[$i]['datedokbcymd']	= $rs->fields[4];
        $return[$i]['buktiterima'] 	= $rs->fields[5];
        $return[$i]['dateterima'] 	= $rs->fields[6];
        $return[$i]['dateterimaymd'] 	= $rs->fields[7];
        $return[$i]['buktiinvoice']	= $rs->fields[8];
        $return[$i]['dateinvoice']	= $rs->fields[9];
        $return[$i]['dateinvoiceymd']	= $rs->fields[10];
        $return[$i]['supplier'] 	= $rs->fields[11];
        $return[$i]['partno'] 		= $rs->fields[12];
        $return[$i]['partname'] 	= $rs->fields[13];
        $return[$i]['qty'] 			= $rs->fields[14];
        $return[$i]['unit'] 		= $rs->fields[15];
        $return[$i]['price'] 		= $rs->fields[16];
        $return[$i]['currency'] 	= $rs->fields[17];
        $return[$i]['input_user']	= $rs->fields[18];
        $return[$i]['input_date']	= $rs->fields[19];
        $return[$i]['sync_date']	= $rs->fields[20];
        $rs->MoveNext();
    }

    echo json_encode([
        "rows" => $return
    ]);
    
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $filename .".xls");
    ?>
        <!DOCTYPE html>
            <html>
                <head>
                <title>Download Excel</title>
                </head>
                <body>
        <table>
            <tr>
            <th colspan="6" style="font-size:18pt;" align="left">LAPORAN PEMASUKAN PER DOKUMEN</th>
            </tr>
            <tr>
                <th></th>
            </tr>
        </table>
        <table border="1">
            <tr>
                <th bgcolor="#C0C0C0" rowspan="2">No</th>
                <th bgcolor="#C0C0C0" colspan="3">Dokumen Pabean</th>
                <th bgcolor="#C0C0C0" colspan="2">Bukti Penerimaan Barang</th>
                <th bgcolor="#C0C0C0" colspan="2">Invoice</th>
                <th bgcolor="#C0C0C0" rowspan="2">Pengirim</th>
                <th bgcolor="#C0C0C0" rowspan="2">Kode Barang</th>
                <th bgcolor="#C0C0C0" rowspan="2">Nama Barang</th>
                <th bgcolor="#C0C0C0" rowspan="2">Jumlah</th>
                <th bgcolor="#C0C0C0" rowspan="2">Satuan</th>
                <th bgcolor="#C0C0C0" rowspan="2">Nilai</th>
                <th bgcolor="#C0C0C0" rowspan="2">Mata Uang</th>
                <th bgcolor="#C0C0C0" rowspan="2">User</th>
            </tr>
            <tr>
                <th bgcolor="#C0C0C0">Jenis</th>
                <th bgcolor="#C0C0C0">No</th>
                <th bgcolor="#C0C0C0">TGL</th>
                <th bgcolor="#C0C0C0">No</th>
                <th bgcolor="#C0C0C0">TGL</th>
                <th bgcolor="#C0C0C0">No</th>
                <th bgcolor="#C0C0C0">TGL</th>
            </tr>
            <?php
        $no = 1;
        for ($i = 0; $i < count($return); $i++) {
            $rowdata = $return[$i];

            echo '<tr>';
                echo '<td align="right">'.$no.'</td>';
                echo '<td>'.$rowdata['jnsdokbc'].'</td>';
                echo '<td>'.$rowdata['nodokbc'].'</td>';
                echo '<td>'.$rowdata['datedokbc'].'</td>';
                echo '<td>'.$rowdata['buktiterima'].'</td>';
                echo '<td>'.$rowdata['dateterima'].'</td>';
                echo '<td>'.$rowdata['buktiinvoice'].'</td>';
                echo '<td>'.$rowdata['dateinvoice'].'</td>';
                echo '<td>'.$rowdata['supplier'].'</td>';
                echo '<td>'.$rowdata['partno'].'</td>';
                echo '<td>'.$rowdata['partname'].'</td>';
                echo '<td align="right">'.number_format($rowdata['qty'], 0).'</td>';
                echo '<td>'.$rowdata['unit'].'</td>';
                echo '<td align="right">'.number_format($rowdata['price'], 0).'</td>';
                echo '<td>'.$rowdata['currency'].'</td>';
                echo '<td>'.$rowdata['input_user'].'<br>'.$rowdata['input_date'].'</td>';
            echo '</tr>';
            $no++;
        }
        ?>
        </table>
        </body></html>
?>