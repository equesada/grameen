<?php

/*
$mysql_host = "localhost";
$mysql_database = "infokerja_smsg";
$mysql_user = "root";
$mysql_password = "";

define("CORE_URL", "http://localhost/ik_core/");
define("SMS_URL", "http://180.243.231.8:8085/sendsms.php?");
define("BASE_URL", "http://10.99.4.1/grameen/infokerja/");
define("SMSG_URL", "http://localhost/ik_smsg/");
*/

$mysql_host = "localhost";
$mysql_database = "smsg";
$mysql_user = "root";
$mysql_password = "pendekarganteng";

define("CORE_URL", "http://10.99.1.5:8085/infokerja_core/");
define("SMS_URL", "http://10.99.1.5:8085/sendsms.php?");
define("BASE_URL", "http://10.99.4.1/grameen/infokerja/");
define("SMSG_URL", "http://10.99.1.5:8085/smsg/");

mysql_connect($mysql_host,$mysql_user,$mysql_password);
mysql_select_db($mysql_database);


define("SHORTCODE", "818");
$sms_keyword = array();
$sms_keyword[1] = "KERJA";

//diisi 1 kalau double confirmation enabled, diisi 0 kalau double confirmation disabled
define("DOUBLECONF", "1");


$msg_notif = array();

// REPLY DARI SMS MO KALAU KEYWORD SALAH
$msg_notif[1]  = "Maaf keyword yg Anda masukkan salah.Ketik ".$sms_keyword[1]." INFO sms ke ".SHORTCODE." utk pilihan pekerjaan.CS: 02160908000";

// REPLY DARI SMS MO REG KERJA SUPIR
$msg_notif[2]  = "Anda akan mendaftar layanan info pkrjaan %1% dr PT RUMA.Rp3rb/mg.1sms/hr.Jk setuju balas dg ktik ".$sms_keyword[1]." %1% OK.CS:02160908000";

// REPLY DARI SMS MO KERJA SUPIR OK KALAU DOUBLE CONFIRMATION ON
$msg_notif[3]  = "Info pkrjaan %1% aktif s/d %2%.Setuju diprpanjang otomatis?Jk ya,ktk ".$sms_keyword[1]." %1% YA ke ".SHORTCODE.".Utk Stop:UNREG ".$sms_keyword[1]." (pekerjaan) ke ".SHORTCODE;

// REPLY DARI SMS MO KERJA SUPIR OK KALAU DOUBLE CONFIRMATION OFF ATAU SMS MO KERJA SUPIR YA
$msg_notif[4]  = "Terimakasih.Info lowongan pekerjaan %1% akan diperpanjang otomatis setiap minggu.Rp3rb/mg.Utk berhenti ketik UNREG ".$sms_keyword[1]." (pekerjaan) ke ".SHORTCODE;

// REMINDER di hari H-$hari_reminder[1]
$msg_notif[5]  = "Layanan info pekerjaan %1% akan berakhir pd %2% dan diprpnjng otomatis.Utk berhenti, ketik UNREG ".$sms_keyword[1]." (pekerjaan) ke ".SHORTCODE;

// REPLY DARI SMS MO KERJA SUPIR OK/YA KALAU JOBCAT_KEY TIDAK TERDAFTAR
$msg_notif[6]  = "Cek lagi keywrd pekrjaan: PABRIK,BURUH,KASIR,STAF,SALES,PEMBANTU,PELAYAN,MONTIR,SUPIR,SATPAM,PERAWAT,GURU,KOKI,LAINNYA.Daftar: REG ".$sms_keyword[1]." (pekerjaan) sms ke ".SHORTCODE;

// REPLY DARI SMS MO REG KERJA SUPIR KALAU ADA SUBSCRIPTION JOBCAT YANG SAMA YANG MASIH AKTIF
$msg_notif[7]  = "Saat ini Anda masih terdaftar pada lowongan pekerjaan %1%.Utk info pekerjaan selengkapnya ketik ".$sms_keyword[1]." INFO sms ke ".SHORTCODE;

$msg_notif[8]  = "Selamat!Langganan info lowongan pekerjaan %1% telah aktif.Rp3Rb/mg,1SMS/hr.Stop:UNREG ".$sms_keyword[1]." (pekerjaan) sms ke ".SHORTCODE." CS: 02160908000";

// REPLY DARI SMS MO KERJA OK/YA
$msg_notif[9]  = "Langganan info lowongan pekerjaan %1% tidak berhasil diproses karena pulsa tidak cukup.Silahkan lakukan isi ulang pulsa Esia Anda. CS: 02160908000";

// REPLY DARI CRON JIKA RENEWAL GAGAL
$msg_notif[10] = "Langganan info lowongan pekerjaan %1% tidak berhasil diperpanjang karena pulsa tidak cukup.Utk info pekerjaan yg lain ketik ".$sms_keyword[1]." INFO sms ke ".SHORTCODE;

// REPLY DARI CRON JIKA RENEWAL BERHASIL
$msg_notif[11] = "Selamat, langganan info lowongan pekerjaan %1% sudah diperpanjang otomatis.Utk info kerja pekerjaan yg lain ketik ".$sms_keyword[1]." INFO sms ke ".SHORTCODE;

// REPLY DARI SMS MO KERJA LOKASI
$msg_notif[12] = "Utk mndpt lowngan d lokasi pilihn Anda ketik ".$sms_keyword[1]." LOKASI (jaksel/jakbar/jakut/jakpus/jaktim/bekasi/tangerang/depok/bogor) sms ke ".SHORTCODE.".ch:".$sms_keyword[1]." LOKASI DEPOK";

// REPLY DARI SMS MO KERJA PROFIL
$msg_notif[13] = "Promosikn profil Anda k pmberi krja.ktk ".$sms_keyword[1]." PROFIL (nama)#(tg/bln/th lahir)#(pnddkn: sd/sltp/slta/diploma/sarjana) ke ".SHORTCODE.".ch:".$sms_keyword[1]." PROFIL ADI#13/03/1983#SLTP";

// REMINDER di hari H-$hari_reminder[2]
$msg_notif[14]  = "Layanan info pkrjaan %1% berakhir hr ini.Setuju diprpanjang otomatis?Jk ya,ktk ".$sms_keyword[1]." %1% YA ke ".SHORTCODE.".Jk tdk abaikan SMS CS:02160908000";

// REPLY DARI SMS KERJA LOKASI JIKA UPDATE LOKASI BERHASIL
$msg_notif[15]  = "Terima kasih. Data lokasi Anda telah disimpan";

// REPLY DARI SMS KERJA LOKASI/PROFIL JIKA GAGAL UPDATE LOKASI/PROFIL KRN MDN BELUM TERDAFTAR
$msg_notif[16]  = "Anda belum terdaftar.Ktk REG ".$sms_keyword[1]."(staf/sales/supir) sms ke ".SHORTCODE." utk mendaftar.ch:REG ".$sms_keyword[1]." SUPIR.Rp3Rb/mg,1SMS/hr.Kerja Lokal lain ktk ".$sms_keyword[1]." INFO.CS: 02160908000";

// REPLY DARI SMS KERJA LOKASI JIKA GAGAL UPDATE LOKASI KARENA FORMAT SALAH
$msg_notif[17]  = "Maaf keyword yg Anda masukkan tdk lengkap.Ketik ".$sms_keyword[1]." LOKASI (jaksel/jakbar/jakut/jakpus/jaktim/bekasi/tangerang/depok/bogor) sms ke ".SHORTCODE.".ch:".$sms_keyword[1]." LOKASI DEPOK";

// REPLY DARI SMS KERJA LOKASI JIKA UPDATE PROFIL BERHASIL
$msg_notif[18]  = "Terima kasih. Data profil Anda telah disimpan";

// REPLY DARI SMS KERJA PROFIL JIKA GAGAL UPDATE PROFIL KARENA FORMAT SALAH
$msg_notif[19]  = "Maaf keyword tidak lengkap.Ktk ".$sms_keyword[1]." (nama)#(tg/bln/th lahir)#(pnddkn: sd/sltp/slta/diploma/sarjana) ke ".SHORTCODE.".ch:".$sms_keyword[1]." PROFIL ADI#13/03/1983#SLTP";

$hari_reminder = array();

// n hari sebelum subscription berakhir, pada saat subscription masih berjalan, user akan terima SMS[5]
$hari_reminder[1] = 2;

// n hari sebelum subscription berakhir, pada saat subscription masih berjalan, pada saat user tidak mereply KERJA SALES YA dlm 24 jam setelah KERJA SALES OK
$hari_reminder[2] = 0;

// n jam SMS REG KERJA atau KERJA OK akan expired
$expiry_sms=24;

$mdn_test = " 2193415830,2199252936,2185403121,2185403119,2192738742";
?>