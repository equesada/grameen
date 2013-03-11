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
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(45) default NULL,
  `password` varchar(45) default NULL,
  `userlevel` varchar(45) default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `status` int(11) default NULL,
  `country_id` varchar(2) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `broadcast_email`
--

DROP TABLE IF EXISTS `broadcast_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `broadcast_email` (
  `email_id` int(11) NOT NULL auto_increment,
  `jobposter_id` int(10) default NULL,
  `sender_name` varchar(100) default NULL,
  `sender_email` varchar(100) default NULL,
  `title` varchar(255) default NULL,
  `msg` varchar(170) default NULL,
  `email` text,
  `email_type` varchar(100) default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `status` tinyint(1) default NULL,
  PRIMARY KEY  (`email_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `broadcast_sms`
--

DROP TABLE IF EXISTS `broadcast_sms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `broadcast_sms` (
  `sms_id` int(11) NOT NULL auto_increment,
  `jobposter_id` int(10) default NULL,
  `title` varchar(255) default NULL,
  `msg` varchar(170) default NULL,
  `mdn` text,
  `mdn_type` varchar(100) default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `status` tinyint(1) default NULL,
  PRIMARY KEY  (`sms_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies` (
  `comp_id` int(10) unsigned NOT NULL auto_increment,
  `industry_id` tinyint(3) unsigned NOT NULL,
  `creator_id` int(10) default '1',
  `name` varchar(100) default NULL,
  `cp` varchar(100) default NULL,
  `description` varchar(255) default NULL,
  `address1` varchar(255) default NULL,
  `address2` varchar(255) default NULL,
  `loc_id` int(10) default NULL,
  `pos_lat` varchar(20) default NULL,
  `pos_lng` varchar(20) default NULL,
  `tel` varchar(20) default NULL,
  `fax` varchar(20) default NULL,
  `email` varchar(100) default NULL,
  `view_cv` tinyint(1) default '2',
  `status` tinyint(1) unsigned default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `notes` text NOT NULL,
  `country_id` varchar(2) default 'ID',
  PRIMARY KEY  (`comp_id`),
  KEY `index_employer` (`comp_id`,`industry_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1047 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_position`
--

DROP TABLE IF EXISTS `content_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_position` (
  `id` int(11) NOT NULL,
  `pos` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contents`
--

DROP TABLE IF EXISTS `contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contents` (
  `content_id` tinyint(4) NOT NULL,
  `title` varchar(100) default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `status` tinyint(1) default NULL,
  `position` tinyint(2) default NULL,
  `country_id` varchar(2) default NULL,
  PRIMARY KEY  (`content_id`),
  KEY `index_content` (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `country` (
  `country_id` varchar(2) NOT NULL,
  `country_name` varchar(100) default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `is_current` tinyint(1) default NULL,
  `status` tinyint(1) default NULL,
  PRIMARY KEY  (`country_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `country_setting`
--

DROP TABLE IF EXISTS `country_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `country_setting` (
  `country_id` varchar(2) NOT NULL,
  `format_date` varchar(20) NOT NULL,
  `format_time` varchar(20) NOT NULL,
  `format_number` varchar(10) NOT NULL,
  `format_currency` varchar(10) NOT NULL,
  `lang` varchar(10) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_update` datetime NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY  (`country_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `education`
--

DROP TABLE IF EXISTS `education`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `education` (
  `edu_id` int(11) NOT NULL auto_increment,
  `edu_title` varchar(100) default NULL,
  `status` tinyint(1) default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `country_id` varchar(2) default NULL,
  PRIMARY KEY  (`edu_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `help`
--

DROP TABLE IF EXISTS `help`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `help` (
  `help_id` int(11) NOT NULL auto_increment,
  `help_title` varchar(100) default NULL,
  `description` text,
  `status` tinyint(1) default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `country_id` varchar(2) default NULL,
  PRIMARY KEY  (`help_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `industry`
--

DROP TABLE IF EXISTS `industry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `industry` (
  `industry_id` tinyint(3) unsigned NOT NULL auto_increment,
  `industry_title` varchar(100) default NULL,
  `description` varchar(255) default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `status` tinyint(1) unsigned default NULL,
  `country_id` varchar(2) default 'ID',
  PRIMARY KEY  (`industry_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_match`
--

DROP TABLE IF EXISTS `job_match`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_match` (
  `jobmatch_id` int(11) NOT NULL auto_increment,
  `title` varchar(100) default NULL,
  `description` varchar(255) default NULL,
  `max_dis` int(4) default NULL,
  `dis` int(3) default NULL,
  `max_nsend` int(10) default NULL,
  `nsend` int(3) default NULL,
  `expired` int(3) default NULL,
  `status` int(1) default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `country_id` varchar(2) default NULL,
  `is_current` tinyint(1) default NULL,
  PRIMARY KEY  (`jobmatch_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_posters`
--

DROP TABLE IF EXISTS `job_posters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_posters` (
  `jobposter_id` int(11) NOT NULL auto_increment,
  `comp_id` int(11) default NULL,
  `username` varchar(45) default NULL,
  `password` varchar(20) default NULL,
  `userlevel` varchar(45) default NULL,
  `email` varchar(100) default NULL,
  `status` tinyint(1) default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `country_id` varchar(2) default 'ID',
  PRIMARY KEY  (`jobposter_id`)
) ENGINE=MyISAM AUTO_INCREMENT=139 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_posters_company`
--

DROP TABLE IF EXISTS `job_posters_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_posters_company` (
  `jobposter_id` int(11) NOT NULL auto_increment,
  `comp_id` int(11) default NULL,
  `username` varchar(45) default NULL,
  `phone` varchar(45) default NULL,
  `mobile` varchar(45) default NULL,
  `password` varchar(20) default NULL,
  `userlevel` varchar(45) default NULL,
  `email` varchar(100) default NULL,
  `position` varchar(45) default NULL,
  `status` tinyint(1) default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `country_id` varchar(2) default 'ID',
  PRIMARY KEY  (`jobposter_id`)
) ENGINE=MyISAM AUTO_INCREMENT=115 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `job_id` int(10) unsigned NOT NULL auto_increment,
  `jobcat_id` int(2) default NULL,
  `jobposter_id` int(11) default NULL,
  `comp_id` int(11) NOT NULL,
  `comp_name` varchar(100) default NULL,
  `comp_cp` varchar(100) default NULL,
  `comp_tel` varchar(100) default NULL,
  `comp_fax` varchar(45) default NULL,
  `comp_email` varchar(100) default NULL,
  `title` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `loc_id` int(11) default NULL,
  `pos_lat` varchar(20) default NULL,
  `pos_lng` varchar(20) default NULL,
  `jobtype_id` int(3) default NULL,
  `gender` varchar(1) default NULL,
  `edu_min` int(10) unsigned default NULL,
  `age_min` tinyint(2) unsigned default NULL,
  `age_max` tinyint(2) unsigned default NULL,
  `exp_min` tinyint(2) unsigned default NULL,
  `salary_min` int(10) unsigned default NULL,
  `salary_max` int(10) unsigned default NULL,
  `status` tinyint(1) unsigned default NULL,
  `sms` varchar(160) NOT NULL,
  `revision` int(4) NOT NULL,
  `approver_id` int(11) default NULL,
  `approved_date` datetime default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `date_lastsent` datetime default NULL,
  `date_active` date default NULL,
  `date_expired` date default NULL,
  `n_send` int(11) default NULL,
  `n_applied` int(11) default NULL,
  `country_id` varchar(2) default 'ID',
  `reason` varchar(255) default NULL,
  PRIMARY KEY  (`job_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10095 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs_apply`
--

DROP TABLE IF EXISTS `jobs_apply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs_apply` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `subscriber_id` int(10) unsigned NOT NULL,
  `job_id` int(10) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `reljobsubscriber_FKIndex1` (`subscriber_id`),
  KEY `job_id` (`job_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs_category`
--

DROP TABLE IF EXISTS `jobs_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs_category` (
  `jobcat_id` tinyint(3) unsigned NOT NULL auto_increment,
  `jobcat_title` varchar(100) default NULL,
  `jobcat_key` varchar(25) default NULL,
  `description` varchar(255) default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `status` tinyint(1) unsigned default NULL,
  `country_id` varchar(2) default 'ID',
  PRIMARY KEY  (`jobcat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs_send`
--

DROP TABLE IF EXISTS `jobs_send`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs_send` (
  `jobsend_id` bigint(20) NOT NULL auto_increment,
  `job_id` int(11) default NULL,
  `subscriber_id` int(11) default NULL,
  `date_add` datetime default NULL,
  `date_send` date default NULL,
  `date_update` datetime default NULL,
  `status` tinyint(1) default NULL,
  `rel_id` bigint(20) default NULL,
  `job_n` tinyint(2) default NULL,
  PRIMARY KEY  (`jobsend_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13133 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location` (
  `loc_id` int(11) NOT NULL auto_increment,
  `loc_type` varchar(20) default NULL,
  `country_id` varchar(2) default 'ID',
  `parent_id` int(11) default NULL,
  `name` varchar(100) default NULL,
  `loc_lat` varchar(20) default NULL,
  `loc_lng` varchar(20) default NULL,
  `status` tinyint(1) default NULL,
  `zipcode` varchar(10) default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  PRIMARY KEY  (`loc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1913013044 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_charging`
--

DROP TABLE IF EXISTS `log_charging`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_charging` (
  `log_id` bigint(20) NOT NULL auto_increment,
  `tx_id` varchar(100) default NULL,
  `mdn` varchar(20) default NULL,
  `date_add` datetime default NULL,
  `charging_status` varchar(10) default NULL,
  `charging_msg` varchar(200) default NULL,
  `pospre` varchar(10) default NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1634 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_dms`
--

DROP TABLE IF EXISTS `log_dms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_dms` (
  `log_id` bigint(20) NOT NULL auto_increment,
  `tx_id` varchar(100) default NULL,
  `page_title` varchar(100) default NULL,
  `mdn` char(15) NOT NULL,
  `time` datetime NOT NULL,
  `lang` int(4) NOT NULL,
  `cid` int(10) NOT NULL,
  `cmd` int(2) NOT NULL,
  `status` int(10) NOT NULL,
  `pageidsrc` int(10) default NULL,
  `pageidreq` int(10) default NULL,
  `request` text NOT NULL,
  `response` text NOT NULL,
  PRIMARY KEY  (`log_id`,`mdn`,`time`)
) ENGINE=MyISAM AUTO_INCREMENT=16270 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_sms`
--

DROP TABLE IF EXISTS `log_sms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_sms` (
  `log_id` bigint(20) NOT NULL auto_increment,
  `tx_id` varchar(45) default NULL,
  `subscriber_id` bigint(20) default NULL,
  `job_id` bigint(20) default NULL,
  `jobcat_id` bigint(20) default NULL,
  `rel_id` bigint(20) default NULL,
  `jobsend_id` bigint(20) default NULL,
  `mdn` varchar(20) default NULL,
  `date_send` datetime default NULL,
  `title` varchar(200) NOT NULL,
  `msg` varchar(200) default NULL,
  `status` tinyint(4) default NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22447 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_sms_reminder`
--

DROP TABLE IF EXISTS `log_sms_reminder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_sms_reminder` (
  `log_id` int(11) NOT NULL auto_increment,
  `tx_id` varchar(100) default NULL,
  `subscriber_id` int(11) default NULL,
  `sms_type` tinyint(3) default NULL,
  `sms_status` tinyint(1) default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `msg` varchar(200) default NULL,
  `mdn` varchar(20) default NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=81 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_smsg`
--

DROP TABLE IF EXISTS `log_smsg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_smsg` (
  `log_id` bigint(20) NOT NULL auto_increment,
  `tx_id` varchar(45) NOT NULL,
  `mdn` varchar(20) NOT NULL,
  `sms_type` varchar(160) NOT NULL,
  `message` varchar(160) NOT NULL,
  `dtime_add` varchar(50) NOT NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=495 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_web`
--

DROP TABLE IF EXISTS `log_web`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_web` (
  `log_id` bigint(20) NOT NULL auto_increment,
  `tx_id` varchar(100) default NULL,
  `date_add` datetime default NULL,
  `src` text,
  `filename` varchar(255) default NULL,
  `param` text,
  `response` longtext,
  `country_id` varchar(2) default NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2017943 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_web2`
--

DROP TABLE IF EXISTS `log_web2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_web2` (
  `log_id` bigint(20) NOT NULL auto_increment,
  `jobposter_id` int(10) default NULL,
  `tx_id` varchar(45) default NULL,
  `title` varchar(100) default NULL,
  `date_add` datetime default NULL,
  `src` text,
  `response` text,
  PRIMARY KEY  (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=83724 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manual`
--

DROP TABLE IF EXISTS `manual`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manual` (
  `id` int(4) NOT NULL auto_increment,
  `title` varchar(254) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mentors`
--

DROP TABLE IF EXISTS `mentors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mentors` (
  `mentor_id` int(10) unsigned NOT NULL auto_increment,
  `subscriber_id` int(11) default NULL,
  `mdn` varchar(20) default NULL,
  `pin` varchar(20) default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `status` tinyint(1) default NULL,
  `country_id` varchar(2) default NULL,
  PRIMARY KEY  (`mentor_id`)
) ENGINE=MyISAM AUTO_INCREMENT=139 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu` (
  `kode` char(5) NOT NULL,
  `nama` varchar(50) default NULL,
  `url` varchar(100) default '0',
  `akses` char(15) default NULL,
  `aktif` set('Y','N') default 'N',
  PRIMARY KEY  (`kode`),
  UNIQUE KEY `kode` (`kode`),
  KEY `kode_2` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rel_subscriber_cat`
--

DROP TABLE IF EXISTS `rel_subscriber_cat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rel_subscriber_cat` (
  `rel_id` int(11) NOT NULL auto_increment,
  `subscriber_id` int(11) default NULL,
  `jobcat_id` tinyint(4) default NULL,
  `jobcat_key` varchar(25) default NULL,
  `date_add` datetime default NULL,
  `date_active` date default NULL,
  `dtime_active` datetime default NULL,
  `date_expired` date default NULL,
  `date_update` datetime default NULL,
  `date_unsub` date default NULL,
  `status` tinyint(1) default '1',
  `update_by` varchar(100) default NULL,
  `update_notes` varchar(255) default NULL,
  `update_by_name` varchar(100) default NULL,
  `n_jobreceived` tinyint(3) default NULL,
  PRIMARY KEY  (`rel_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1224 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rel_subscriber_company`
--

DROP TABLE IF EXISTS `rel_subscriber_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rel_subscriber_company` (
  `rel_id` int(10) unsigned NOT NULL auto_increment,
  `subscriber_id` int(10) default NULL,
  `comp_id` int(10) default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `status` tinyint(1) default NULL,
  `sms_key` varchar(3) default NULL,
  PRIMARY KEY  (`rel_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rel_subscription_smsg`
--

DROP TABLE IF EXISTS `rel_subscription_smsg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rel_subscription_smsg` (
  `rel_id` bigint(20) NOT NULL auto_increment,
  `rel_subscription_id` bigint(20) NOT NULL,
  `dtime_add` varchar(50) NOT NULL,
  `sms_type` varchar(160) NOT NULL,
  PRIMARY KEY  (`rel_id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status` (
  `status_id` int(11) NOT NULL,
  `title` varchar(100) default NULL,
  `country_id` varchar(2) default NULL,
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  PRIMARY KEY  (`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subscriber_pulsa`
--

DROP TABLE IF EXISTS `subscriber_pulsa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriber_pulsa` (
  `pulsa_id` int(11) NOT NULL auto_increment,
  `subscriber_id` int(11) default NULL,
  `pulsa` varchar(45) default NULL,
  PRIMARY KEY  (`pulsa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=528 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subscribers`
--

DROP TABLE IF EXISTS `subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscribers` (
  `subscriber_id` int(10) unsigned NOT NULL auto_increment,
  `mentor_id` int(10) unsigned default NULL,
  `name` varchar(100) default NULL,
  `gender` char(1) default NULL,
  `birthday` date default NULL,
  `place_birth` varchar(100) default NULL,
  `idcard` varchar(100) default NULL,
  `edu_id` int(11) default NULL,
  `salary` int(11) unsigned default NULL,
  `address1` varchar(255) default NULL,
  `address2` varchar(255) default NULL,
  `rt` varchar(5) default NULL,
  `rw` varchar(5) default NULL,
  `loc_id` int(11) default NULL,
  `pos_lat` varchar(20) default NULL,
  `pos_lng` varchar(20) default NULL,
  `country_id` varchar(2) default 'ID',
  `date_add` datetime default NULL,
  `date_update` datetime default NULL,
  `status` tinyint(1) unsigned default NULL,
  `mdn` varchar(20) default NULL,
  `n_jobapplied` int(11) default NULL,
  PRIMARY KEY  (`subscriber_id`)
) ENGINE=MyISAM AUTO_INCREMENT=921 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tarif`
--

DROP TABLE IF EXISTS `tarif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tarif` (
  `id` int(10) NOT NULL auto_increment,
  `tarif` int(10) default NULL,
  `date_update` datetime default NULL,
  `jobposter_id` int(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_prefix`
--

DROP TABLE IF EXISTS `tb_prefix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_prefix` (
  `id` int(4) NOT NULL auto_increment,
  `opprefix` varchar(35) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `themes`
--

DROP TABLE IF EXISTS `themes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `themes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `date_add` date NOT NULL,
  `date_update` date NOT NULL,
  `creator` varchar(100) NOT NULL,
  `folder` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `is_current` tinyint(1) NOT NULL,
  `country_id` varchar(2) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `wwwsqldesigner`
--

DROP TABLE IF EXISTS `wwwsqldesigner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wwwsqldesigner` (
  `keyword` varchar(30) NOT NULL default '',
  `data` mediumtext,
  `dt` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-03-21 17:15:39
