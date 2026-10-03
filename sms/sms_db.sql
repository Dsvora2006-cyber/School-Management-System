-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sms_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `s_no` int(20) NOT NULL,
  `id` varchar(30) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `dob` varchar(20) NOT NULL,
  `image` varchar(40) NOT NULL DEFAULT '1701517055user.png',
  `phone` varchar(20) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `address` varchar(700) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (2,'A9876543210','Admin','Kumar','12/11/2024','A98765432101718792069.png','1234567890','male','no where');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendence`
--

DROP TABLE IF EXISTS `attendence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendence` (
  `s_no` int(20) NOT NULL,
  `student_id` varchar(40) NOT NULL,
  `attendence` varchar(10) NOT NULL,
  `class` varchar(30) NOT NULL,
  `section` varchar(5) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendence`
--

LOCK TABLES `attendence` WRITE;
/*!40000 ALTER TABLE `attendence` DISABLE KEYS */;
INSERT INTO `attendence` VALUES (1,'S1718791292','1','12c','A','2024-06-19 15:32:37');
/*!40000 ALTER TABLE `attendence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bus_root`
--

DROP TABLE IF EXISTS `bus_root`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bus_root` (
  `s_no` int(11) NOT NULL,
  `bus_id` varchar(20) NOT NULL,
  `location` varchar(255) NOT NULL,
  `arrival_time` varchar(20) NOT NULL,
  `serial` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bus_root`
--

LOCK TABLES `bus_root` WRITE;
/*!40000 ALTER TABLE `bus_root` DISABLE KEYS */;
INSERT INTO `bus_root` VALUES (7,'1718791847','Stop 1','06:50 AM',1),(8,'1718791847','Stop 2','07:00 AM',2),(9,'1718791847','Stop 3','07:10 AM',3),(10,'1718791847','SCHOOL','10:00 AM',4),(13,'1718791949','Stop z','06:35 AM',1),(14,'1718791949','SCHOOL','10:00 AM',2),(15,'1718791984','Stop x','06:45 AM',1),(16,'1718791984','SCHOOL','10:00 AM',2);
/*!40000 ALTER TABLE `bus_root` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bus_staff`
--

DROP TABLE IF EXISTS `bus_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bus_staff` (
  `s_no` int(11) NOT NULL,
  `id` varchar(20) NOT NULL,
  `bus_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(10) NOT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bus_staff`
--

LOCK TABLES `bus_staff` WRITE;
/*!40000 ALTER TABLE `bus_staff` DISABLE KEYS */;
INSERT INTO `bus_staff` VALUES (1,'B1718791847','1718791847','driver ','8080808080','driver'),(2,'B1718791847','1718791847','helper ','0000000000','helper'),(3,'B1718791949','1718791949','driver 2','7897898988','driver'),(4,'B1718791949','1718791949','helper','7897898988','helper'),(5,'B1718791984','1718791984','another driver','7897897898','driver'),(6,'B1718791984','1718791984','another helper','7894568796','helper');
/*!40000 ALTER TABLE `bus_staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `buses`
--

DROP TABLE IF EXISTS `buses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `buses` (
  `s_no` int(11) NOT NULL,
  `bus_id` varchar(20) NOT NULL,
  `bus_title` varchar(100) NOT NULL,
  `bus_number` varchar(20) NOT NULL,
  `request` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buses`
--

LOCK TABLES `buses` WRITE;
/*!40000 ALTER TABLE `buses` DISABLE KEYS */;
INSERT INTO `buses` VALUES (10,'1718791847','Bus 1','XXXXXX',''),(11,'1718791949','Bus 2','999999',''),(12,'1718791984','Another bus','OOOOOOOO','');
/*!40000 ALTER TABLE `buses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` varchar(50) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `subject` varchar(100) NOT NULL DEFAULT 'General',
  `standard` varchar(50) NOT NULL,
  `teacher_id` varchar(50) DEFAULT NULL,
  `room_number` varchar(50) NOT NULL,
  `schedule` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `class_id` (`class_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
INSERT INTO `classes` VALUES (1,'CLS-2026-101','Grade 10 - Division A','General','10','TCH-2026-4819','Room 101','Mon-Fri 08:00 AM - 01:30 PM','2026-07-23 14:37:19'),(2,'CLS-2026-102','Grade 11 Science - Div A','General','11 (Science)','TCH-2026-9281','Lab 1','Mon-Fri 08:30 AM - 02:00 PM','2026-07-23 14:37:19'),(3,'CLS-2026-103','Grade 12 Science - Div A','General','12 (Science)','TCH-2026-3049','Room 305','Mon-Fri 09:00 AM - 02:30 PM','2026-07-23 14:37:19'),(4,'cls-101','div A','General','11 (Commerce)','TCH-1','101','8:30 to 9:15','2026-07-23 14:40:56');
/*!40000 ALTER TABLE `classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exams`
--

DROP TABLE IF EXISTS `exams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exams` (
  `s_no` int(20) NOT NULL,
  `exam_id` varchar(40) NOT NULL,
  `exam_title` varchar(512) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `class` varchar(30) NOT NULL,
  `section` varchar(10) NOT NULL,
  `total_marks` varchar(10) NOT NULL,
  `passing_marks` varchar(10) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exams`
--

LOCK TABLES `exams` WRITE;
/*!40000 ALTER TABLE `exams` DISABLE KEYS */;
INSERT INTO `exams` VALUES (3,'E17187917486672ae442b976','Monthly test ','ALL','12c','A','100','33','2024-06-19 15:39:08'),(4,'E17187928006672b26095672','Hindi exam result','Hindi','12c','A','100','33','2024-06-19 15:56:40'),(5,'E17187929656672b305cbb25','sldfj','ALL','12c','A','100','33','2024-06-19 15:59:25');
/*!40000 ALTER TABLE `exams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_record`
--

DROP TABLE IF EXISTS `fee_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fee_record` (
  `s_no` int(11) NOT NULL,
  `id` varchar(20) NOT NULL,
  `month` varchar(20) NOT NULL,
  `other_collection` int(5) NOT NULL,
  `total` int(5) NOT NULL,
  `paid` int(5) NOT NULL,
  `balance` int(5) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_record`
--

LOCK TABLES `fee_record` WRITE;
/*!40000 ALTER TABLE `fee_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `fee_record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feedback` (
  `s_no` int(11) NOT NULL,
  `sender_id` varchar(20) NOT NULL,
  `receiver_id` varchar(20) NOT NULL,
  `msg` varchar(500) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedback`
--

LOCK TABLES `feedback` WRITE;
/*!40000 ALTER TABLE `feedback` DISABLE KEYS */;
INSERT INTO `feedback` VALUES (7,'T1718791191','S1718791292','Hello student','2024-06-19 15:46:58'),(8,'T1718791191','S1718791292','You are so naughty\n','2024-06-19 15:47:11');
/*!40000 ALTER TABLE `feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `homework`
--

DROP TABLE IF EXISTS `homework`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `homework` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `homework_id` varchar(50) NOT NULL,
  `class_id` varchar(50) NOT NULL,
  `teacher_id` varchar(50) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `due_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `homework_id` (`homework_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `homework`
--

LOCK TABLES `homework` WRITE;
/*!40000 ALTER TABLE `homework` DISABLE KEYS */;
INSERT INTO `homework` VALUES (1,'HW-2026-0001','CLS-2026-101','TCH-2026-4819','English & Literature','Essay on Macbeth Act I','Write a 500-word analysis of Macbeth\'s internal conflict in Act I, Scene 7. Focus on his ambition versus his moral integrity.','2026-08-05','2026-07-24 08:45:49'),(2,'HW-2026-0002','CLS-2026-102','TCH-2026-9281','Physics','Electromagnetism Problem Set','Complete problems 1 to 10 on page 142 of the textbook. Show all work and formulas used.','2026-08-07','2026-07-24 08:45:49'),(3,'HW-2026-5146','cls-101','TCH-1','General','Account','solve the account no1 to 5','2026-07-26','2026-07-24 08:46:50');
/*!40000 ALTER TABLE `homework` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `due_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Unpaid',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1,'INV-2026-0001','APX-2026-9481','Tuition Fees - Term 1',850.00,'2026-08-01','Paid','Online','2026-07-23','2026-07-23 14:49:25'),(2,'INV-2026-0002','APX-2026-4927','Admission Charges',600.00,'2026-08-10','Unpaid',NULL,NULL,'2026-07-23 14:49:25'),(3,'INV-2026-0003','APX-2026-3024','Exam Fees - Midterm',150.00,'2026-08-15','Unpaid',NULL,NULL,'2026-07-23 14:49:25');
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leaves`
--

DROP TABLE IF EXISTS `leaves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leaves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `leave_id` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `reason` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `leave_id` (`leave_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leaves`
--

LOCK TABLES `leaves` WRITE;
/*!40000 ALTER TABLE `leaves` DISABLE KEYS */;
INSERT INTO `leaves` VALUES (1,'LV-2026-0001','APX-2026-9481','Family Wedding Ceremony','I request leave to attend my sister\'s wedding ceremony out of state. I will make sure to catch up on any missed classwork.','2026-08-10','2026-08-12','Approved','2026-07-24 08:53:58'),(2,'LV-2026-0002','APX-2026-9481','Dental Checkup & Surgery','I have a scheduled wisdom tooth extraction and dentist follow-up appointment.','2026-08-20','2026-08-20','Approved','2026-07-24 08:53:58'),(3,'LV-2026-8307','APX-1','for a merrage event','i am went a my brother`s merrage so you can accept my request','2026-07-25','2026-07-28','Approved','2026-07-24 08:55:14');
/*!40000 ALTER TABLE `leaves` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marks`
--

DROP TABLE IF EXISTS `marks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(50) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `exam_name` varchar(100) NOT NULL,
  `marks_obtained` decimal(5,2) NOT NULL,
  `max_marks` decimal(5,2) NOT NULL DEFAULT 100.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_exam_subject` (`student_id`,`subject`,`exam_name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marks`
--

LOCK TABLES `marks` WRITE;
/*!40000 ALTER TABLE `marks` DISABLE KEYS */;
INSERT INTO `marks` VALUES (1,'APX-2026-9481','English & Literature','Midterm Exam',88.50,100.00,'2026-08-07 13:50:12','2026-08-07 13:50:12'),(2,'APX-2026-9481','English & Literature','Final Exam',92.00,100.00,'2026-08-07 13:50:12','2026-08-07 13:50:12'),(3,'APX-2026-4927','English & Literature','Midterm Exam',76.00,100.00,'2026-08-07 13:50:12','2026-08-07 13:50:12'),(4,'APX-1','Account','sem-1',88.00,100.00,'2026-08-07 14:07:41','2026-08-07 14:07:41'),(5,'APX-1','Statistics','sem-1',89.00,100.00,'2026-08-07 14:07:41','2026-08-07 14:07:41'),(6,'APX-1','BO','sem-1',93.00,100.00,'2026-08-07 14:07:41','2026-08-07 14:07:41'),(7,'APX-1','Economics','sem-1',95.00,100.00,'2026-08-07 14:07:41','2026-08-07 14:07:41'),(8,'APX-1','SPCC','sem-1',90.00,100.00,'2026-08-07 14:07:41','2026-08-07 14:07:41'),(9,'APX-1','Gujarati','sem-1',91.00,100.00,'2026-08-07 14:07:41','2026-08-07 14:07:41'),(10,'APX-1','English','sem-1',87.00,100.00,'2026-08-07 14:07:41','2026-08-07 14:07:41');
/*!40000 ALTER TABLE `marks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notes` (
  `s_no` int(20) NOT NULL,
  `sender_id` varchar(40) NOT NULL,
  `editor_id` varchar(40) NOT NULL,
  `class` varchar(50) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `comment` varchar(1000) NOT NULL,
  `file` varchar(50) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notes`
--

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
INSERT INTO `notes` VALUES (1,'A9876543210','A9876543210','12c','Hindi','Hindi Homework ','do this on time','A98765432101718791715.png','2024-06-19 15:38:35');
/*!40000 ALTER TABLE `notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notice`
--

DROP TABLE IF EXISTS `notice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notice` (
  `s_no` int(20) NOT NULL,
  `sender_id` varchar(40) NOT NULL,
  `editor_id` varchar(40) NOT NULL,
  `title` varchar(100) NOT NULL,
  `body` varchar(1000) NOT NULL,
  `file` varchar(100) NOT NULL,
  `importance` varchar(5) NOT NULL DEFAULT '1',
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `role` varchar(11) NOT NULL,
  `class` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notice`
--

LOCK TABLES `notice` WRITE;
/*!40000 ALTER TABLE `notice` DISABLE KEYS */;
INSERT INTO `notice` VALUES (51,'A9876543210','A9876543210','Notice title','body','A98765432101718791385.png','2','2024-06-19 15:33:05','',''),(52,'A9876543210','A9876543210','Title 2','body 2','A98765432101718791411.png','3','2024-06-19 15:33:31','',''),(53,'A9876543210','A9876543210','Holiday notice','enjoy your holidays','A98765432101718791447.png','1','2024-06-19 15:34:07','','');
/*!40000 ALTER TABLE `notice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `s_no` int(20) NOT NULL,
  `sender_id` varchar(40) NOT NULL,
  `class` varchar(30) NOT NULL,
  `section` varchar(10) NOT NULL,
  `type` varchar(10) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `file` varchar(50) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll`
--

DROP TABLE IF EXISTS `payroll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll` (
  `s_no` int(11) NOT NULL,
  `id` varchar(30) NOT NULL,
  `amount` int(11) NOT NULL,
  `date` varchar(30) NOT NULL,
  `name` varchar(50) NOT NULL,
  `account_no` varchar(40) NOT NULL,
  `ifsc_code` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll`
--

LOCK TABLES `payroll` WRITE;
/*!40000 ALTER TABLE `payroll` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reminders`
--

DROP TABLE IF EXISTS `reminders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reminders` (
  `s_no` int(20) NOT NULL,
  `id` varchar(40) NOT NULL,
  `message` varchar(2000) NOT NULL,
  `status` varchar(15) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reminders`
--

LOCK TABLES `reminders` WRITE;
/*!40000 ALTER TABLE `reminders` DISABLE KEYS */;
INSERT INTO `reminders` VALUES (68,'T1718791191','Post a homework daily','completed'),(69,'T1718791191','principal meeting','pending'),(70,'A9876543210','Reminder for myself : have a good day','pending'),(71,'A9876543210','\nBest of luck','completed');
/*!40000 ALTER TABLE `reminders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'school_name','AWS','2026-08-07 13:12:06','2026-08-07 13:39:00'),(2,'school_email','info@apexacademy.com','2026-08-07 13:12:06','2026-08-07 13:12:06'),(3,'school_phone','+1 (555) 019-2834','2026-08-07 13:12:06','2026-08-07 13:12:06'),(4,'school_address','123 Academic Way, Springfield, IL','2026-08-07 13:12:06','2026-08-07 13:12:06'),(5,'academic_year','2026-2027','2026-08-07 13:12:06','2026-08-07 13:12:06'),(6,'current_term','Term 1','2026-08-07 13:12:06','2026-08-07 13:12:06'),(7,'currency','USD','2026-08-07 13:12:06','2026-08-07 13:12:06'),(8,'theme_color','indigo','2026-08-07 13:12:06','2026-08-07 13:12:06'),(9,'density','normal','2026-08-07 13:12:06','2026-08-07 13:12:06'),(10,'admin_username','darvora575','2026-08-07 13:25:01','2026-08-07 13:25:01'),(11,'admin_email','darvora575@gmail.com','2026-08-07 13:25:01','2026-08-07 13:25:01'),(12,'admin_password','2642006','2026-08-07 13:25:01','2026-08-07 13:25:01');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_guardian`
--

DROP TABLE IF EXISTS `student_guardian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_guardian` (
  `s_no` int(20) NOT NULL,
  `id` varchar(40) NOT NULL,
  `gname` varchar(200) NOT NULL,
  `gphone` varchar(20) NOT NULL,
  `gaddress` varchar(200) NOT NULL,
  `gcity` varchar(100) NOT NULL,
  `gzip` varchar(50) NOT NULL,
  `relation` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_guardian`
--

LOCK TABLES `student_guardian` WRITE;
/*!40000 ALTER TABLE `student_guardian` DISABLE KEYS */;
INSERT INTO `student_guardian` VALUES (1,'S1718791292','Regan Clemons','4567894562','Eum sit et laboriosa','Abbot','Hunter','Culpa odio laboriosa');
/*!40000 ALTER TABLE `student_guardian` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `grade` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `gender` varchar(20) NOT NULL,
  `blood_group` varchar(10) NOT NULL,
  `mobile_number` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT 'student123',
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `pin_code` varchar(20) NOT NULL,
  `nationality` varchar(100) NOT NULL,
  `religion` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `father_name` varchar(100) NOT NULL,
  `mother_name` varchar(100) NOT NULL,
  `parent_mobile` varchar(20) NOT NULL,
  `parent_email` varchar(100) NOT NULL,
  `parent_occupation` varchar(100) NOT NULL,
  `attendance` decimal(5,2) DEFAULT 100.00,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,'APX-2026-9481','Liam','Anderson','Grade 10 - Science','2010-05-14','Male','A+','123-456-7890','liam@example.com','student123','123 Forest Dr','Springfield','Illinois','62701','American','Christian','General','Robert Anderson','Sarah Anderson','555-019-2834','robert@example.com','Engineer',98.20,'Active','2026-07-23 08:21:25'),(2,'APX-2026-3024','Sophia','Martinez','Grade 12 - Commerce','2008-09-22','Female','O+','234-567-8901','sophia@example.com','student123','456 Valley View','Springfield','Illinois','62702','American','Christian','OBC','Miguel Martinez','Maria Martinez','555-020-3849','miguel@example.com','Doctor',99.40,'Active','2026-07-23 08:21:25'),(3,'APX-2026-8839','Jackson','Reed','10','2009-02-08','Male','B-','345-678-9012','jackson@example.com','student123','789 Oak Ave','Springfield','Illinois','62703','American','Christian','SC/ST','David Reed','Karen Reed','555-031-4920','david@example.com','Teacher',94.70,'Active','2026-07-23 08:21:25'),(5,'APX-1','DARSHAN','VORA','11 (Commerce)','2010-12-21','Male','A+','9909091465','S@GMAIL.COM','123','JAMNAGAR','JAMNAGAR','Gujrat','3611070','INDIAN','HINDU','General','SANDIP BHAI','ANJANABEN','990901465','s@GMAIL.COM','FARMER',100.00,'Active','2026-07-23 08:28:14');
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subjects` (
  `s_no` int(20) NOT NULL,
  `subject_id` varchar(40) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `class` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,'12c6672ac911a253','Hindi','12c'),(2,'12c6672ac9c45d68','Commerce','12c'),(3,'12c6672aca78c3c7','English','12c');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `syllabus`
--

DROP TABLE IF EXISTS `syllabus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `syllabus` (
  `s_no` int(20) NOT NULL,
  `class` varchar(50) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `file` varchar(100) NOT NULL DEFAULT '-1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `syllabus`
--

LOCK TABLES `syllabus` WRITE;
/*!40000 ALTER TABLE `syllabus` DISABLE KEYS */;
INSERT INTO `syllabus` VALUES (12,'12c','Hindi','T17187911911718792274.png'),(13,'12c','English','T17187911911718792285.png');
/*!40000 ALTER TABLE `syllabus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teacher_guardian`
--

DROP TABLE IF EXISTS `teacher_guardian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teacher_guardian` (
  `s_no` int(20) NOT NULL,
  `id` varchar(40) NOT NULL,
  `gname` varchar(256) NOT NULL,
  `gphone` varchar(20) NOT NULL,
  `gaddress` varchar(256) NOT NULL,
  `gcity` varchar(50) NOT NULL,
  `gzip` varchar(20) NOT NULL,
  `relation` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher_guardian`
--

LOCK TABLES `teacher_guardian` WRITE;
/*!40000 ALTER TABLE `teacher_guardian` DISABLE KEYS */;
INSERT INTO `teacher_guardian` VALUES (1,'T1718791191','Velma Walker','1234567895','Sit voluptas nisi v','Maggie','Mckee','Consequatur Volupta');
/*!40000 ALTER TABLE `teacher_guardian` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teachers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT 'teacher123',
  `mobile_number` varchar(20) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `standard` varchar(50) NOT NULL DEFAULT '10',
  PRIMARY KEY (`id`),
  UNIQUE KEY `teacher_id` (`teacher_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teachers`
--

LOCK TABLES `teachers` WRITE;
/*!40000 ALTER TABLE `teachers` DISABLE KEYS */;
INSERT INTO `teachers` VALUES (1,'TCH-2026-4819','Sarah','Jenkins','sarah.j@apexacademy.com','teacher123','111-222-3333','English & Literature','Active','2026-07-23 08:42:16','10'),(2,'TCH-2026-9281','Marcus','Vance','marcus.v@apexacademy.com','teacher123','222-333-4444','Physics & Chemistry','Active','2026-07-23 08:42:16','10'),(4,'TCH-1','PARTH','RABADIYA','parth@gmail.com','123','9524620103','Account','Active','2026-07-23 09:00:30','11 (Commerce)');
/*!40000 ALTER TABLE `teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `time_table`
--

DROP TABLE IF EXISTS `time_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `time_table` (
  `s_no` int(20) NOT NULL,
  `class` varchar(50) NOT NULL,
  `section` varchar(10) NOT NULL,
  `start_time` varchar(20) NOT NULL,
  `end_time` varchar(20) NOT NULL,
  `mon` varchar(30) NOT NULL,
  `tue` varchar(30) NOT NULL,
  `wed` varchar(30) NOT NULL,
  `thu` varchar(30) NOT NULL,
  `fri` varchar(30) NOT NULL,
  `sat` varchar(30) NOT NULL,
  `editor_id` varchar(40) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `time_table`
--

LOCK TABLES `time_table` WRITE;
/*!40000 ALTER TABLE `time_table` DISABLE KEYS */;
INSERT INTO `time_table` VALUES (1,'12c','A','07:00','08:00','Hindi','','','','','','A9876543210','2024-06-19 15:37:37'),(2,'12c','A','08:00','09:00','English','','','','','','A9876543210','2024-06-19 15:37:37'),(3,'12c','A','09:00','10:00','Math','','','','','','A9876543210','2024-06-19 15:37:37'),(4,'12c','A','10:00','11:00','Hindi','','','','','','A9876543210','2024-06-19 15:37:37'),(5,'12c','A','11:00','12:00','English','','','','','','A9876543210','2024-06-19 15:37:37'),(6,'12c','A','12:00','01:00','Commerce','','','','','','A9876543210','2024-06-19 15:37:37'),(7,'12c','A','01:00','02:00','Commerce','','','','','','A9876543210','2024-06-19 15:37:37'),(8,'12c','A','02:00','03:00','Hindi','','','','','','A9876543210','2024-06-19 15:37:37');
/*!40000 ALTER TABLE `time_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `s_no` int(15) NOT NULL,
  `id` varchar(40) NOT NULL,
  `email` varchar(256) NOT NULL,
  `password_hash` varchar(700) NOT NULL,
  `role` varchar(20) NOT NULL,
  `theme` varchar(20) NOT NULL DEFAULT 'light'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'A9876543210','admin@gmail.com','$2y$10$2MrhbQa30mll8mKG6LPyjuI7CQPC4abCvqrSvczxXVRu4RVueRfoe','admin','light'),(2,'T1718791191','teacher@gmail.com','$2y$10$2MrhbQa30mll8mKG6LPyjuI7CQPC4abCvqrSvczxXVRu4RVueRfoe','teacher','light'),(3,'S1718791292','student@gmail.com','$2y$10$2MrhbQa30mll8mKG6LPyjuI7CQPC4abCvqrSvczxXVRu4RVueRfoe','student','light'),(4,'O7898987845','owner@gmail.com','$2y$10$2MrhbQa30mll8mKG6LPyjuI7CQPC4abCvqrSvczxXVRu4RVueRfoe','owner','light');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-04  0:44:46
