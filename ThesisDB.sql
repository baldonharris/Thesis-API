-- MySQL dump 10.13  Distrib 5.5.44, for debian-linux-gnu (armv7l)
--
-- Host: localhost    Database: ThesisDB
-- ------------------------------------------------------
-- Server version	5.5.44-0+deb8u1

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
-- Table structure for table `check_devices`
--

DROP TABLE IF EXISTS `check_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `check_devices` (
  `check_devices_id` int(11) NOT NULL AUTO_INCREMENT,
  `rooms_id` int(11) NOT NULL,
  `check_devices_status` int(11) NOT NULL,
  PRIMARY KEY (`check_devices_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `check_devices`
--

LOCK TABLES `check_devices` WRITE;
/*!40000 ALTER TABLE `check_devices` DISABLE KEYS */;
/*!40000 ALTER TABLE `check_devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `check_rooms`
--

DROP TABLE IF EXISTS `check_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `check_rooms` (
  `check_rooms_id` int(11) NOT NULL AUTO_INCREMENT,
  `check_rooms_status` int(11) NOT NULL,
  `check_rooms_for` varchar(32) NOT NULL,
  PRIMARY KEY (`check_rooms_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `check_rooms`
--

LOCK TABLES `check_rooms` WRITE;
/*!40000 ALTER TABLE `check_rooms` DISABLE KEYS */;
INSERT INTO `check_rooms` VALUES (47,0,'view'),(48,0,'manual'),(49,0,'update'),(50,0,'manage');
/*!40000 ALTER TABLE `check_rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `floor_room_groups`
--

DROP TABLE IF EXISTS `floor_room_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `floor_room_groups` (
  `floor_room_groups_id` int(11) NOT NULL AUTO_INCREMENT,
  `floors_id` int(11) NOT NULL,
  `rooms_id` int(11) NOT NULL,
  PRIMARY KEY (`floor_room_groups_id`),
  KEY `fk_floor_room_group_floors1_idx` (`floors_id`),
  KEY `fk_floor_room_group_rooms1_idx` (`rooms_id`),
  CONSTRAINT `fk_floor_room_group_floors1` FOREIGN KEY (`floors_id`) REFERENCES `floors` (`floors_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_floor_room_group_rooms1` FOREIGN KEY (`rooms_id`) REFERENCES `rooms` (`rooms_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `floor_room_groups`
--

LOCK TABLES `floor_room_groups` WRITE;
/*!40000 ALTER TABLE `floor_room_groups` DISABLE KEYS */;
INSERT INTO `floor_room_groups` VALUES (5,1,11),(6,2,12);
/*!40000 ALTER TABLE `floor_room_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `floors`
--

DROP TABLE IF EXISTS `floors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `floors` (
  `floors_id` int(11) NOT NULL AUTO_INCREMENT,
  `floors_name` varchar(45) NOT NULL,
  PRIMARY KEY (`floors_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `floors`
--

LOCK TABLES `floors` WRITE;
/*!40000 ALTER TABLE `floors` DISABLE KEYS */;
INSERT INTO `floors` VALUES (1,'1st Floor'),(2,'3rd Floor');
/*!40000 ALTER TABLE `floors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `frames`
--

DROP TABLE IF EXISTS `frames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `frames` (
  `frames_id` int(11) NOT NULL AUTO_INCREMENT,
  `frames_status` int(11) NOT NULL,
  PRIMARY KEY (`frames_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `frames`
--

LOCK TABLES `frames` WRITE;
/*!40000 ALTER TABLE `frames` DISABLE KEYS */;
/*!40000 ALTER TABLE `frames` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room_consumptions`
--

DROP TABLE IF EXISTS `room_consumptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room_consumptions` (
  `room_consumptions_id` int(11) NOT NULL AUTO_INCREMENT,
  `rooms_id` int(11) NOT NULL,
  `room_consumptions` float NOT NULL,
  `room_consumptions_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`room_consumptions_id`),
  KEY `fk_room_consumptions_rooms1_idx` (`rooms_id`),
  CONSTRAINT `fk_room_consumptions_rooms1` FOREIGN KEY (`rooms_id`) REFERENCES `rooms` (`rooms_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=202 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room_consumptions`
--

LOCK TABLES `room_consumptions` WRITE;
/*!40000 ALTER TABLE `room_consumptions` DISABLE KEYS */;
INSERT INTO `room_consumptions` VALUES (199,11,0,'2016-03-30 01:54:06'),(200,11,0,'2016-03-30 01:57:52'),(201,11,0,'2016-03-30 02:07:42');
/*!40000 ALTER TABLE `room_consumptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room_device_schedules`
--

DROP TABLE IF EXISTS `room_device_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room_device_schedules` (
  `room_device_schedules_id` int(11) NOT NULL AUTO_INCREMENT,
  `room_devices_id` int(11) NOT NULL,
  `room_schedules_id` int(11) NOT NULL,
  PRIMARY KEY (`room_device_schedules_id`),
  KEY `fk_room_device_schedules_room_devices1_idx` (`room_devices_id`),
  KEY `fk_room_device_schedules_room_schedules1_idx` (`room_schedules_id`),
  CONSTRAINT `fk_room_device_schedules_room_devices1` FOREIGN KEY (`room_devices_id`) REFERENCES `room_devices` (`room_devices_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_room_device_schedules_room_schedules1` FOREIGN KEY (`room_schedules_id`) REFERENCES `room_schedules` (`room_schedules_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=250 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room_device_schedules`
--

LOCK TABLES `room_device_schedules` WRITE;
/*!40000 ALTER TABLE `room_device_schedules` DISABLE KEYS */;
INSERT INTO `room_device_schedules` VALUES (168,77,39),(169,79,39),(170,76,39),(171,78,39),(172,77,31),(173,79,31),(174,76,31),(175,78,31),(176,77,46),(177,79,46),(178,76,46),(179,78,46),(180,77,40),(181,79,40),(182,76,40),(183,78,40),(184,77,41),(185,79,41),(186,76,41),(187,78,41),(188,77,34),(189,79,34),(190,76,34),(191,78,34),(192,77,35),(193,79,35),(194,76,35),(195,78,35),(196,77,42),(197,79,42),(198,76,42),(199,78,42),(200,77,36),(201,79,36),(202,76,36),(203,78,36),(204,77,47),(205,79,47),(206,76,47),(207,78,47),(208,77,43),(209,79,43),(210,76,43),(211,78,43),(212,77,37),(213,79,37),(214,76,37),(215,78,37),(216,77,38),(217,79,38),(218,76,38),(219,78,38),(220,77,44),(221,79,44),(222,76,44),(223,78,44),(224,77,45),(225,79,45),(226,76,45),(227,78,45),(228,73,53),(229,72,53),(230,73,54),(231,72,54),(232,73,48),(233,72,48),(234,73,49),(235,72,49),(236,73,58),(237,72,58),(238,73,55),(239,72,55),(240,73,50),(241,72,50),(242,73,51),(243,72,51),(244,73,56),(245,72,56),(246,73,52),(247,72,52),(248,73,57),(249,72,57);
/*!40000 ALTER TABLE `room_device_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room_devices`
--

DROP TABLE IF EXISTS `room_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room_devices` (
  `room_devices_id` int(11) NOT NULL AUTO_INCREMENT,
  `rooms_id` int(11) NOT NULL,
  `room_devices_name` varchar(45) NOT NULL,
  `room_devices_port` int(11) DEFAULT NULL,
  `room_devices_status` int(11) NOT NULL,
  PRIMARY KEY (`room_devices_id`),
  KEY `fk_room_devices_rooms1_idx` (`rooms_id`),
  CONSTRAINT `fk_room_devices_rooms1` FOREIGN KEY (`rooms_id`) REFERENCES `rooms` (`rooms_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room_devices`
--

LOCK TABLES `room_devices` WRITE;
/*!40000 ALTER TABLE `room_devices` DISABLE KEYS */;
INSERT INTO `room_devices` VALUES (72,11,'Lights',6,0),(73,11,'Aircon',7,0),(76,12,'Lights',5,0),(77,12,'Aircon',6,0),(78,12,'Projector',7,0),(79,12,'Electric Fan',8,0);
/*!40000 ALTER TABLE `room_devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room_schedules`
--

DROP TABLE IF EXISTS `room_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room_schedules` (
  `room_schedules_id` int(11) NOT NULL AUTO_INCREMENT,
  `room_schedules_name` varchar(45) NOT NULL,
  `rooms_id` int(11) NOT NULL,
  `room_schedules_day` varchar(45) DEFAULT NULL,
  `room_schedules_date` date DEFAULT NULL,
  `room_schedules_start_time` int(11) DEFAULT NULL,
  `room_schedules_end_time` int(11) DEFAULT NULL,
  `room_schedules_duration` int(11) DEFAULT NULL,
  `room_schedules_type` int(11) NOT NULL,
  PRIMARY KEY (`room_schedules_id`),
  KEY `fk_room_schedule_rooms1_idx` (`rooms_id`),
  CONSTRAINT `fk_room_schedule_rooms1` FOREIGN KEY (`rooms_id`) REFERENCES `rooms` (`rooms_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room_schedules`
--

LOCK TABLES `room_schedules` WRITE;
/*!40000 ALTER TABLE `room_schedules` DISABLE KEYS */;
INSERT INTO `room_schedules` VALUES (31,'Es 21',12,'Mon,Wed',NULL,540,630,NULL,0),(34,'Ece 428',12,'Mon,Wed',NULL,630,720,NULL,0),(35,'Ee 320',12,'Mon,Wed',NULL,720,810,NULL,0),(36,'Ee 311n',12,'Mon,Wed',NULL,810,900,NULL,0),(37,'Ee 321n',12,'Mon,Wed',NULL,900,990,NULL,0),(38,'Engl 23',12,'Mon,Wed',NULL,990,1050,NULL,0),(39,'Em 112x',12,'Tue,Thu',NULL,450,540,NULL,0),(40,'Engl 23',12,'Tue,Thu',NULL,540,630,NULL,0),(41,'Ece 522',12,'Tue,Thu',NULL,630,720,NULL,0),(42,'Ece 329n',12,'Tue,Thu',NULL,720,810,NULL,0),(43,'Ece 329n',12,'Tue,Thu',NULL,900,990,NULL,0),(44,'Ece 322n',12,'Tue,Thu',NULL,990,1080,NULL,0),(45,'Ece 526n',12,'Tue,Thu',NULL,1080,1260,NULL,0),(46,'Ece 524n',12,'Sat',NULL,540,720,NULL,0),(47,'Mece 112',12,'Sat',NULL,840,1020,NULL,0),(48,'Em 22',11,'Mon,Wed',NULL,630,720,NULL,0),(49,'Em 123',11,'Mon,Wed',NULL,750,810,NULL,0),(50,'Em 121',11,'Mon,Wed',NULL,810,900,NULL,0),(51,'Em 122',11,'Mon,Wed',NULL,930,1080,NULL,0),(52,'Em 211',11,'Mon,Wed',NULL,1080,1230,NULL,0),(53,'Em 123',11,'Tue,Thu',NULL,450,570,NULL,0),(54,'Em 122',11,'Tue,Thu',NULL,570,720,NULL,0),(55,'Em 121',11,'Tue,Thu',NULL,810,990,NULL,0),(56,'Em 22',11,'Tue,Thu',NULL,990,1080,NULL,0),(57,'Em 31',11,'Tue,Thu',NULL,1080,1170,NULL,0),(58,'Nstp 2',11,'Sat',NULL,750,1110,NULL,0);
/*!40000 ALTER TABLE `room_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rooms` (
  `rooms_id` int(11) NOT NULL AUTO_INCREMENT,
  `rooms_name` varchar(45) NOT NULL,
  `rooms_address` varchar(32) DEFAULT NULL,
  `rooms_port` int(11) DEFAULT NULL,
  `rooms_ble_password` varchar(32) NOT NULL,
  `rooms_key_address` varchar(32) NOT NULL,
  `rooms_status` int(11) NOT NULL,
  PRIMARY KEY (`rooms_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (11,'LB168TC','00 13 A2 00 40 69 8C 8E',9,'','',0),(12,'LB364TC','00 13 A2 00 40 A9 D6 12',9,'','',0);
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms_routing_table`
--

DROP TABLE IF EXISTS `rooms_routing_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rooms_routing_table` (
  `rooms_routing_table_id` int(11) NOT NULL AUTO_INCREMENT,
  `rooms_address` int(11) NOT NULL,
  PRIMARY KEY (`rooms_routing_table_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms_routing_table`
--

LOCK TABLES `rooms_routing_table` WRITE;
/*!40000 ALTER TABLE `rooms_routing_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `rooms_routing_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status_logs`
--

DROP TABLE IF EXISTS `status_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status_logs` (
  `status_logs_id` int(11) NOT NULL AUTO_INCREMENT,
  `status_logs_status` int(11) NOT NULL,
  `status_logs_datetime` varchar(45) DEFAULT 'CURRENT_TIMESTAMP',
  PRIMARY KEY (`status_logs_id`)
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status_logs`
--

LOCK TABLES `status_logs` WRITE;
/*!40000 ALTER TABLE `status_logs` DISABLE KEYS */;
INSERT INTO `status_logs` VALUES (1,1,'2016-03-24 07:35:19'),(2,0,'2016-03-24 09:14:39'),(3,1,'2016-03-24 09:14:58'),(4,1,'2016-03-24 09:50:59'),(5,0,'2016-03-24 09:51:00'),(6,1,'2016-03-24 09:51:01'),(7,0,'2016-03-24 10:22:57'),(8,1,'2016-03-24 10:23:17'),(9,0,'2016-03-24 12:02:02'),(10,1,'2016-03-24 13:32:57'),(11,0,'2016-03-24 19:35:32'),(12,1,'2016-03-25 11:04:19'),(13,1,'2016-03-25 11:06:20'),(14,0,'2016-03-25 11:43:47'),(15,1,'2016-03-25 11:44:07'),(16,0,'2016-03-25 12:00:46'),(17,1,'2016-03-25 12:36:15'),(18,0,'2016-03-25 15:02:10'),(19,1,'2016-03-25 15:54:24'),(20,0,'2016-03-25 20:05:28'),(21,1,'2016-03-25 20:33:47'),(22,0,'2016-03-25 20:34:44'),(23,1,'2016-03-25 23:40:35'),(24,1,'2016-03-26 12:30:49'),(25,0,'2016-03-26 12:34:31'),(26,1,'2016-03-26 12:34:51'),(27,0,'2016-03-26 14:22:26'),(28,1,'2016-03-26 14:22:46'),(29,0,'2016-03-26 14:47:13'),(30,1,'2016-03-26 14:47:33'),(31,0,'2016-03-26 15:44:56'),(32,1,'2016-03-26 15:45:15'),(33,0,'2016-03-26 16:21:47'),(34,1,'2016-03-26 16:22:07'),(35,0,'2016-03-26 16:27:00'),(36,1,'2016-03-26 16:27:20'),(37,0,'2016-03-26 17:05:09'),(38,1,'2016-03-22 07:44:42'),(39,0,'2016-03-22 08:23:46'),(40,1,'2016-03-22 08:24:06'),(41,1,'2016-03-22 14:45:29'),(42,0,'2016-03-22 17:05:00'),(43,1,'2016-03-22 18:00:03'),(44,0,'2016-03-22 19:32:33'),(45,1,'2016-03-30 01:27:32'),(46,0,'2016-03-30 01:33:35'),(47,1,'2016-03-30 01:34:23'),(48,0,'2016-03-30 01:34:24'),(49,1,'2016-03-30 01:34:30'),(50,0,'2016-03-30 01:35:07'),(51,1,'2016-03-30 01:35:16'),(52,0,'2016-03-30 01:35:24'),(53,1,'2016-03-30 01:35:36'),(54,0,'2016-03-30 01:36:36'),(55,1,'2016-03-30 01:36:46'),(56,0,'2016-03-30 01:36:48'),(57,1,'2016-03-30 01:37:11'),(58,0,'2016-03-30 01:38:58'),(59,1,'2016-03-30 01:39:02'),(60,0,'2016-03-30 01:40:35'),(61,1,'2016-03-30 01:41:25'),(62,0,'2016-03-30 01:43:20'),(63,1,'2016-03-30 01:43:47'),(64,0,'2016-03-30 01:44:27'),(65,1,'2016-03-30 01:48:24'),(66,0,'2016-03-30 01:48:37'),(67,1,'2016-03-30 01:48:44'),(68,0,'2016-03-30 01:48:55'),(69,1,'2016-03-30 01:49:25'),(70,0,'2016-03-30 01:49:58'),(71,1,'2016-03-30 01:54:11'),(72,0,'2016-03-30 01:54:13'),(73,1,'2016-03-30 01:54:22'),(74,0,'2016-03-30 01:54:29'),(75,1,'2016-03-30 02:00:02'),(76,0,'2016-03-30 02:00:03'),(77,0,'2016-03-30 02:08:57'),(78,1,'2016-03-30 02:08:58'),(79,0,'2016-03-30 02:09:20'),(80,1,'2016-03-30 02:09:20'),(81,0,'2016-03-30 02:10:09'),(82,1,'2016-03-30 02:10:09'),(83,1,'2016-03-30 02:53:13'),(84,1,'2016-03-30 02:54:39'),(85,0,'2016-03-30 02:54:40'),(86,1,'2016-03-30 02:59:54'),(87,0,'2016-03-30 03:04:55'),(88,1,'2016-03-30 03:18:57'),(89,0,'2016-03-30 03:23:53'),(90,1,'2016-03-30 04:02:11'),(91,0,'2016-03-30 04:06:40'),(92,1,'2016-03-30 04:31:55'),(93,0,'2016-03-30 04:36:57'),(94,1,'2016-03-30 04:47:23'),(95,1,'2016-03-30 04:50:08'),(96,0,'2016-03-30 04:50:10'),(97,1,'2016-03-30 05:03:59'),(98,1,'2016-03-30 05:04:09'),(99,0,'2016-03-30 05:04:10'),(100,1,'2016-03-30 05:04:15'),(101,0,'2016-03-30 05:04:36'),(102,1,'2016-03-30 05:04:54'),(103,1,'2016-03-30 05:05:06'),(104,0,'2016-03-30 05:05:07'),(105,1,'2016-03-30 05:08:09'),(106,0,'2016-03-30 05:08:31'),(107,1,'2016-03-30 05:08:49'),(108,0,'2016-03-30 05:10:09'),(109,1,'2016-03-30 05:12:25'),(110,0,'2016-03-30 05:13:25'),(111,1,'2016-03-30 05:17:48'),(112,1,'2016-03-30 05:17:55'),(113,0,'2016-03-30 05:17:56'),(114,1,'2016-03-30 05:17:57'),(115,1,'2016-03-30 05:18:03'),(116,0,'2016-03-30 05:18:04'),(117,1,'2016-03-30 05:18:06'),(118,1,'2016-03-30 05:18:11'),(119,0,'2016-03-30 05:18:13'),(120,1,'2016-03-30 05:18:14'),(121,0,'2016-03-30 05:18:28'),(122,1,'2016-03-30 05:20:06'),(123,1,'2016-03-30 05:20:11'),(124,0,'2016-03-30 05:20:12'),(125,1,'2016-03-30 05:20:14'),(126,1,'2016-03-30 05:20:19'),(127,0,'2016-03-30 05:20:20'),(128,1,'2016-03-30 05:20:22'),(129,1,'2016-03-30 05:20:26'),(130,0,'2016-03-30 05:20:27'),(131,1,'2016-03-30 05:20:29'),(132,1,'2016-03-30 05:20:34'),(133,1,'2016-03-30 05:23:06'),(134,0,'2016-03-30 05:23:06'),(135,1,'2016-03-30 05:23:23'),(136,1,'2016-03-30 05:24:20'),(137,0,'2016-03-30 05:24:22'),(138,1,'2016-03-30 05:24:23'),(139,0,'2016-03-30 05:26:22'),(140,1,'2016-03-30 05:27:30'),(141,1,'2016-03-30 05:28:03'),(142,0,'2016-03-30 05:28:05'),(143,1,'2016-03-30 05:28:06'),(144,1,'2016-03-30 05:34:55'),(145,1,'2016-03-30 05:35:13'),(146,1,'2016-03-30 05:36:06'),(147,1,'2016-03-30 05:36:07'),(148,1,'2016-03-30 05:36:08'),(149,1,'2016-03-30 05:48:16'),(150,1,'2016-03-30 05:48:51'),(151,1,'2016-03-30 05:50:53'),(152,1,'2016-03-30 05:56:11'),(153,1,'2016-03-30 05:57:18'),(154,0,'2016-03-30 05:58:13'),(155,1,'2016-03-30 09:14:47'),(156,0,'2016-03-30 09:14:48'),(157,1,'2016-03-30 09:14:51'),(158,0,'2016-03-30 09:14:52'),(159,1,'2016-03-30 09:14:55'),(160,0,'2016-03-30 09:14:57'),(161,1,'2016-03-30 09:17:30'),(162,1,'2016-03-30 09:17:53'),(163,0,'2016-03-30 09:17:54'),(164,1,'2016-03-30 09:19:08'),(165,1,'2016-03-30 09:19:20'),(166,0,'2016-03-30 09:19:22'),(167,1,'2016-03-30 09:19:26'),(168,1,'2016-03-30 09:19:36'),(169,0,'2016-03-30 09:19:37'),(170,1,'2016-03-30 09:19:41'),(171,1,'2016-03-30 09:19:59'),(172,0,'2016-03-30 09:20:00'),(173,1,'2016-03-30 09:20:19'),(174,0,'2016-03-30 09:20:38'),(175,1,'2016-03-30 09:20:57'),(176,0,'2016-03-30 09:21:18'),(177,1,'2016-03-30 09:46:20'),(178,0,'2016-03-30 09:46:59'),(179,1,'2016-03-30 09:49:22'),(180,0,'2016-03-30 09:49:48'),(181,1,'2016-03-30 09:49:52'),(182,0,'2016-03-30 09:50:05'),(183,1,'2016-03-30 09:50:40'),(184,0,'2016-03-30 09:54:33'),(185,1,'2016-03-30 09:56:13'),(186,0,'2016-03-30 09:56:23'),(187,1,'2016-03-30 09:56:33'),(188,0,'2016-03-30 09:56:37'),(189,1,'2016-03-30 09:58:38'),(190,0,'2016-03-30 10:01:17'),(191,1,'2016-03-30 10:09:25'),(192,0,'2016-03-30 10:10:25'),(193,1,'2016-03-30 10:10:26'),(194,0,'2016-03-30 10:11:17');
/*!40000 ALTER TABLE `status_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_pan`
--

DROP TABLE IF EXISTS `system_pan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_pan` (
  `system_pan_id` int(11) NOT NULL AUTO_INCREMENT,
  `system_pan_name` int(11) NOT NULL,
  PRIMARY KEY (`system_pan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_pan`
--

LOCK TABLES `system_pan` WRITE;
/*!40000 ALTER TABLE `system_pan` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_pan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `triggers`
--

DROP TABLE IF EXISTS `triggers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `triggers` (
  `triggers_id` int(11) NOT NULL AUTO_INCREMENT,
  `triggers_table_id` int(11) NOT NULL,
  `triggers_table` int(11) NOT NULL COMMENT '1-rooms, 2-room_schedules, 3-room_device, 4-pan_id',
  PRIMARY KEY (`triggers_id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `triggers`
--

LOCK TABLES `triggers` WRITE;
/*!40000 ALTER TABLE `triggers` DISABLE KEYS */;
INSERT INTO `triggers` VALUES (1,11,1),(2,10,6),(3,11,3),(4,11,3),(5,11,3),(6,11,1),(7,30,2),(8,11,3),(9,11,3),(10,11,3),(11,10,1),(12,31,2),(13,32,2),(14,12,3),(15,33,2),(16,12,3),(17,34,2),(18,35,2),(19,36,2),(20,37,2),(21,38,2),(22,39,2),(23,40,2),(24,41,2),(25,42,2),(26,43,2),(27,44,2),(28,45,2),(29,46,2),(30,47,2),(31,12,1),(32,12,1),(33,12,3),(34,12,3),(35,12,3),(36,12,3),(37,12,3),(38,12,3),(39,12,3),(40,12,3),(41,12,3),(42,12,3),(43,12,3),(44,12,3),(45,12,3),(46,12,3),(47,12,3),(48,12,3),(49,12,3),(50,12,3),(51,12,3),(52,12,3),(53,12,3),(54,12,3),(55,12,3),(56,12,3),(57,12,3),(58,12,3),(59,12,3),(60,12,3),(61,12,3),(62,12,3),(63,12,3),(64,12,3),(65,12,3),(66,12,3),(67,12,3),(68,12,3),(69,12,3),(70,48,2),(71,49,2),(72,50,2),(73,51,2),(74,52,2),(75,53,2),(76,54,2),(77,55,2),(78,56,2),(79,57,2),(80,11,3),(81,11,3),(82,58,2),(83,11,3),(84,11,3),(85,11,3),(86,11,3),(87,11,3),(88,11,3),(89,11,3),(90,11,3),(91,11,3),(92,11,3),(93,11,3),(94,11,3);
/*!40000 ALTER TABLE `triggers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_details`
--

DROP TABLE IF EXISTS `user_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_details` (
  `user_details_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_details_password` varchar(45) NOT NULL,
  `user_details_pan_id` int(11) NOT NULL,
  PRIMARY KEY (`user_details_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_details`
--

LOCK TABLES `user_details` WRITE;
/*!40000 ALTER TABLE `user_details` DISABLE KEYS */;
INSERT INTO `user_details` VALUES (1,'f10e2821bbbea527ea02200352313bc059445190',255);
/*!40000 ALTER TABLE `user_details` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-03-30 13:23:18
