<?php
include ("conf.php");

$con = mysql_connect($mysql_host,$mysql_user,$mysql_password); //koneksi database


if (!$con) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db($mysql_database, $con); //pilih database

/* Update records */
mysql_query("UPDATE jobs SET date_expired = '2011-12-22' WHERE job_id >= 1");
printf ("Updated records: %d\n", mysql_affected_rows());
mysql_query("COMMIT");
?>
