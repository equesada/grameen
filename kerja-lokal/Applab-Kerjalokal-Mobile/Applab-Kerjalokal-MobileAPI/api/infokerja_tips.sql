-- MySQL dump 10.11
--
-- Host: localhost    Database: infokerja
-- ------------------------------------------------------
-- Server version	5.0.92

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tips`
--

DROP TABLE IF EXISTS `tips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tips` (
  `tips_id` int(11) NOT NULL auto_increment,
  `tips_title` varchar(100) default NULL,
  `description` text,
  `status` tinyint(1) default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `country_id` varchar(2) default NULL,
  PRIMARY KEY  (`tips_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tips`
--

LOCK TABLES `tips` WRITE;
/*!40000 ALTER TABLE `tips` DISABLE KEYS */;
INSERT INTO `tips` VALUES (1,'Kiat Membuat CV','1. Tulis data diri dengan singkat, padat dan rapi.\n\n2. Ketik menggunakan komputer diatas kertas putih HVS atau tulis tangan jika diperlukan namun harus tetap menjaga kerapihan tulisan agar CV yang anda buat dapat dengan mudah dibaca oleh perusahaan.\n\n3. Gunakan bahasa yang sederhana dan sopan.\n\n4. Tuliskan selengkap mungkin tentang kualifikasi anda seperti riwayat pendidikan, pelatihan yang pernah diikuti maupun pengalaman kerja lengkap dengan tugas dan tanggung jawab. Hal ini akan menjadi nilai jual anda.\n\n5. Jika memungkinkan, cek kembali kesesuaian kualifikasi anda dengan pekerjaan yang akan anda lamar. Uraikan dengan lebih rinci perihal yang menurut anda sesuai dengan kriteria pekerjaan yang akan anda lamar.\n\n6. Cek kembali uraian data diri yang telah anda buat untuk mengoreksi kesalahan.',1,'2011-09-08 13:14:01','2011-09-08 13:14:01','ID'),(2,'Membuat Lamaran','Tips dan Trik Membuat Lamaran:\n\n1. Gunakan tulisan tangan jika diperlukan. Perusahaan seringkali meminta pelamar untuk menulis lamaran menggunakan tulisan tangan. Jika hal tersebut disyaratkan bagi pekerjaan yang hendak anda lamar, maka patuhilah persyaratan tersebut dan jangan sekali-sekali menggunakan print komputer karena perusahaan akan langsung membuang cv anda dan anda kehilangan kesempatan untuk melalui proses seleksi selanjutnya.\n\n2. Sebutkan nama perusahaan yang dituju yakni nama orang ataupun nama departemen yang anda hendak lamar pada surat lamaran anda. \n\n3. Gunakan salam pembuka pada surat lamaran anda yakni salam pembuka yang sopan untuk menggambarkan bahwa anda serius dalam melamar kerja.\n\n4. Buat paragraf pembuka sebagai bagian awal isi surat lamaran. Pada bagian ini, anda dapat mencantumkan posisi yang hendak anda lamar dan darimana anda mendapatkan informasi lowongan tersebut. \n\n5. Kemukakan alasan anda melamar pekerjaan tersebut, pada bagian ini anda dapat mengemukakan alasan ketertarikan anda hingga anda mengajukan lamaran untuk posisi yang ditawarkan. Sebaiknya, anda menceritakan secara garis besar latar belakang pendidikan, kemampuan maupun pengalaman anda yang mungkin dapat memenuhi persyaratan yang diajukan perusahaan.\n\n6. Jangan lupa untuk menuliskan Salam Penutup beserta nama jelas anda dibagian akhir surat.',1,'2011-09-08 13:15:00','2011-09-08 13:15:00','ID'),(3,'Tips WalkIn Interview','Tips ketika anda mengikuti Walk-In Interview:\n\n1. Persiapkan diri anda dengan sarapan pagi (makan dan minum secukupnya) terlebih dahulu\ndan punya cukup istirahat dihari sebelumnya untuk menjaga kebugaran anda yang akan\nmempengaruhi penampilan anda secara keseluruhan. Primanya penampilan anda ketika\nwawancara dapat menciptakan impresi pertama yang positif dan sangat menentukan\nkeberhasilan walk in interview Anda dengan pewawancara.\n\n2. Pilihlah busana yang paling sesuai dan sopan. Pastikan penampilan Anda rapi, bersih, cantik dan\nlayak untuk menghadiri wawancara.\n\n3. Persiapkan Diri Sehari sebelum berangkat, pahami dulu lingkup kerja yang ditawarkan. Jangan sampai Anda datang ke sana masih bertanya soal posisi yang ditawarkan. Misal, jika posisi yang Anda lamar marketing, coba pelajari beberapa hal yang berkaitan dengan dunia marketing.\n\n4. Datang tepat waktu, Kalau perlu Anda sudah tiba satu jam sebelum proses dimulai. Anda\njadi punya kesempatan mempelajari suasana, menenangkan diri dan mempersiapkan segala\nsesuatunya. Datang lebih awal juga membuat Anda tidak perlu menunggu terlalu lama, hal ini\nberguna untuk menghindari antrian yang sangat panjang, karena bisa jadi pada saat yang sama berpuluh-puluh orang datang untuk melamar dan interview.\n\n5. Persiapkan semua surat-surat yang dibutuhkan untuk proses seleksi perusahaan. Atur susunan surat anda sesuai yang telah ditentukan oleh perusahaan yang dapat anda lihat pada iklan lowongan kerja. Masukkan semua kelengkapan ini ke dalam map atau amplop besar.\n\n6. Jangan malu untuk bertanya kepada petugas ataupun kepada sesama peserta/pelamar jika ada hal-hal yang membingungkan anda.\n\n7. Alat Tulis. Bawalah alat tulis dan kertas catatan karena siapa tahu diperlukan saat wawancara. Kesiapan Anda menghadapi interview juga mendapat penilaian tersendiri. Jangan sampai Anda harus meminjam alat tulis pada pewawancara.\n\n8. Menjawab Taktis. Jawab semua pertanyaan dengan penuh percaya diri, jujur dan bersahabat, tapi tidak berlebihan. Jangan ragu menjawab jika Anda diminta untuk memberi penjelasan lebih terperinci. Manfaatkan kesempatan untuk bertanya jika pewawancara mempersilakan Anda. Jangan ragu bertanya hal-hal yang belum jelas misal bertanya kapan hasil interview akan dikabarkan.\n\n9. Kesan Positif. Selesai wawancara ucapkan terima kasih dan berikan jabatan tangan yang paling hangat. Berikan kesan positif saat meninggalkan ruangan. Jika dalam waktu yang dijanjikan pewawancara belum menghubungi Anda, teleponlah pewawancara dan tanyakan hasilnya.',1,'2011-09-08 13:15:29','2011-09-08 13:15:29','ID'),(4,'Negoisasi Gaji','Tips dan Trik Negoisasi Gaji:\n\n1. Sebelum datang wawancara, usahakan anda telah mencari informasi tentang kisaran gaji untuk posisi dan kualifikasi yang anda punya.\n\n2. Pada saat wawancara, jangan menyebutkan besaran gaji yang anda harapkan jika tidak ditanya oleh pewawancara. Hal ini untuk menciptakan kesan positif dan tidak terlalu ambisius.\n\n3. Pada saat wawancara, jangan langsung menolak kisaran gaji yang ditawarkan. Sampaikan dengan sopan bahwa anda berterima kasih dengan penawaran dan tanyakan kemungkiinan benefit lain yang diterima seperti asuransi kesehatan, biaya pensiun, pengganti transport dll.\n\n4. Jika anda memutuskan untuk melakukan negosiasi jumlah gaji kepada pewawancara, yakinkan bahwa anda layak mendapatkan gaji sedikit lebih tinggi dengan yang ditawarkan perusahaan dengan berbekal kualifikasi, pengalaman serta etos kerja yang anda miliki.\n\n5. Mintalah waktu 1 atau 2 hari jika anda memutuskan untuk mempertimbangkan tawaran tersebut.',1,'2011-09-08 13:16:05','2011-09-08 13:16:05','ID'),(5,'Kiat Kontrak Kerja','Berikut langkah yang harus anda perhatikan sebelum menandatangani kontrak kerja:\n\n1. Cek kesesuaian data diri anda yang tercantum pada surat kontrak kerja.\n2. Baca secara keseluruhan isi pasal dalam kontrak kerja sebelum anda menandatangani surat tersebut untuk memastikan bahwa anda memahami dengan benar hal-hal yang diatur pada tiap pasalnya.\n3. Jangan malu untuk bertanya jika anda menemukan kebingungan pada salah satu pasal dalam kontrak kerja.\n4. Perhatikan jumlah gaji yang tertera pada surat kontrak kerja beserta potongan-potongan yang mungkin timbul.',1,'2011-09-08 13:16:41','2011-09-08 13:16:41','ID');
/*!40000 ALTER TABLE `tips` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-04-10 14:43:32
