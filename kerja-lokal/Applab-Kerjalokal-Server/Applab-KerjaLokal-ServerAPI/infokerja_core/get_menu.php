<?php
	require "conf.php";
	require "func.php";
	$level = isset($_GET["level"]) ? str_clean($_GET["level"]) : "";
	/* menu akses by. ali
	di database field akses ada 4 digit,
	digit pertama buat superadmin
	digit kedua admin
	digit ketiga company
	digit keempat job poster
	tiap digit hanya di isi 1 atau 0
	jika di isi 1 (satu) maka menu itu d tampilkan, jika 0 (nol) tidak tampil	
	*/
	switch ($level) {
		case 'superadmin';
		$akses="and left(akses,1)=1";
		break;
		case 'admin';
		$akses="and mid(akses,2,1)=1";
		break;
		case 'company';
		$akses="and mid(akses,3,1)=1";
		break;
		case 'jobposter';
		$akses="and mid(akses,4,1)=1";
		break;
		case 'btel';
		$akses="and right(akses,1)=1";
		break;
	}
		
	$sql1 = "SELECT kode,url,nama FROM menu WHERE aktif='Y' and length(kode)=1 $akses ORDER BY kode";
	$sql1 = mysql_query($sql1) OR die(output(mysql_error()));
	$arr['kepala'] = array();
	while($row1 = mysql_fetch_assoc($sql1))
	{
		for($j=0;$j<3;$j++)
		{
			$val[mysql_field_name($sql1,$j)] = $row1[mysql_field_name($sql1,$j)];
		}
		array_push($arr["kepala"], $val);
	}

	$sql = "SELECT kode,nama,url,akses FROM menu WHERE aktif='Y' $akses ORDER BY kode";
	$sql = mysql_query($sql) OR die(output(mysql_error()));

//	$arr['nfields'] = mysql_num_fields($sql);
	$arr['results'] = array();

	while($row = mysql_fetch_assoc($sql))
	{
		for($j=0;$j<4;$j++)
		{
			$val[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
		}
		array_push($arr["results"], $val);
	}

	echo output($arr);

?>