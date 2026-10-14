-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: instagram_crm
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
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `entity_type` varchar(255) DEFAULT NULL,
  `entity_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `audit_logs_company_id_action_index` (`company_id`,`action`),
  KEY `audit_logs_entity_type_entity_id_index` (`entity_type`,`entity_id`),
  CONSTRAINT `audit_logs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,1,'system.initialized','Company',1,'System initialized with default lead sources, team users, and verified leads.',NULL,NULL,'127.0.0.1','InstaLead Agent/1.0','2026-10-14 01:56:47'),(2,1,1,'lead.csv_exported',NULL,NULL,'Exported 12 leads to CSV.',NULL,NULL,'127.0.0.1','Symfony','2026-10-14 02:12:45'),(3,1,1,'lead.csv_exported',NULL,NULL,'Exported 12 leads to CSV.',NULL,NULL,'127.0.0.1','Symfony','2026-10-14 02:13:05'),(4,1,1,'user.login',NULL,NULL,'User logged in successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-14 02:20:01'),(5,1,1,'user.login',NULL,NULL,'User logged in successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-14 03:45:56');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business_leads`
--

DROP TABLE IF EXISTS `business_leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `business_leads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `lead_source_id` bigint(20) unsigned DEFAULT NULL,
  `business_name` varchar(255) NOT NULL,
  `instagram_username` varchar(255) DEFAULT NULL,
  `instagram_profile_url` varchar(255) DEFAULT NULL,
  `profile_picture_url` varchar(500) DEFAULT NULL,
  `business_description` text DEFAULT NULL,
  `category` varchar(255) NOT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'India',
  `location_address` varchar(255) DEFAULT NULL,
  `official_website` varchar(255) DEFAULT NULL,
  `public_email` varchar(255) DEFAULT NULL,
  `public_phone` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `status` enum('new','contacted','interested','follow_up','converted','not_interested') NOT NULL DEFAULT 'new',
  `priority` enum('low','medium','high') NOT NULL DEFAULT 'medium',
  `source_type` varchar(255) NOT NULL DEFAULT 'instagram_discovery',
  `collected_at` timestamp NULL DEFAULT NULL,
  `last_verified_at` timestamp NULL DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `business_leads_lead_source_id_foreign` (`lead_source_id`),
  KEY `business_leads_company_id_status_index` (`company_id`,`status`),
  KEY `business_leads_company_id_category_index` (`company_id`,`category`),
  KEY `business_leads_company_id_city_index` (`company_id`,`city`),
  KEY `business_leads_business_name_index` (`business_name`),
  KEY `business_leads_instagram_username_index` (`instagram_username`),
  KEY `business_leads_category_index` (`category`),
  KEY `business_leads_city_index` (`city`),
  KEY `business_leads_public_email_index` (`public_email`),
  KEY `business_leads_public_phone_index` (`public_phone`),
  KEY `business_leads_status_index` (`status`),
  KEY `business_leads_priority_index` (`priority`),
  CONSTRAINT `business_leads_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `business_leads_lead_source_id_foreign` FOREIGN KEY (`lead_source_id`) REFERENCES `lead_sources` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_leads`
--

LOCK TABLES `business_leads` WRITE;
/*!40000 ALTER TABLE `business_leads` DISABLE KEYS */;
INSERT INTO `business_leads` VALUES (1,1,1,'The Bombay Canteen & Bar','thebombaycanteen','https://instagram.com/thebombaycanteen','https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=200&auto=format&fit=crop&q=80','Celebrated modern Indian cafe and bar highlighting regional ingredients and artisanal cocktails.','Restaurants and cafes','Mumbai','Maharashtra','India','Unit 1, Process House, Kamala Mills, Lower Parel','https://thebombaycanteen.com','reservations@thebombaycanteen.com','+91 22 4966 6666','Kunal Joshi','interested','high','authorized_directory','2026-10-04 01:56:47','2026-10-13 01:56:47','{\"source_compliance\":\"GDPR \\/ DPDP Compliant Public Profile Data\",\"verification_status\":\"Verified Official Contact\",\"follower_bracket\":\"10k-100k\"}','2026-10-14 01:56:47','2026-10-14 01:56:47',NULL),(2,1,2,'Subko Coffee Roasters & Bakehouse','subkocoffee','https://instagram.com/subkocoffee','https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=200&auto=format&fit=crop&q=80','Specialty coffee roastery and craft craft bakehouse celebrating the subcontinent.','Restaurants and cafes','Mumbai','Maharashtra','India','21A, Chapel Rd, Ranwar, Bandra West','https://subko.co','hello@subko.co','+91 90047 00655','Rahul Reddy','contacted','medium','authorized_directory','2026-09-20 01:56:47','2026-10-09 01:56:47','{\"source_compliance\":\"GDPR \\/ DPDP Compliant Public Profile Data\",\"verification_status\":\"Verified Official Contact\",\"follower_bracket\":\"10k-100k\"}','2026-10-14 01:56:47','2026-10-14 01:56:47',NULL),(3,1,3,'Truefitt & Hill India','truefittandhillindia','https://instagram.com/truefittandhillindia','https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=200&auto=format&fit=crop&q=80','The world oldest barbershop offering royal grooming services, luxury facials and gentlemen haircuts.','Beauty salons and makeup artists','Mumbai','Maharashtra','India','Ground Floor, Phoenix Palladium, High Street Phoenix','https://truefittandhill.in','concierge@truefittandhill.in','+91 22 6666 4321','Devika Singhania','new','high','authorized_directory','2026-09-15 01:56:47','2026-10-09 01:56:47','{\"source_compliance\":\"GDPR \\/ DPDP Compliant Public Profile Data\",\"verification_status\":\"Verified Official Contact\",\"follower_bracket\":\"10k-100k\"}','2026-10-14 01:56:47','2026-10-14 01:56:47',NULL),(4,1,1,'Cult.fit Elite Fitness Studio','cultfit_official','https://instagram.com/cultfit_official','https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=200&auto=format&fit=crop&q=80','State of the art strength, boxing, yoga, and functional fitness conditioning chain.','Gyms and fitness centers','Bengaluru','Karnataka','India','100ft Road, Indiranagar','https://cult.fit','partner@cult.fit','+91 80 4567 8900','Vikram Shenoy','follow_up','high','authorized_directory','2026-09-19 01:56:47','2026-10-11 01:56:47','{\"source_compliance\":\"GDPR \\/ DPDP Compliant Public Profile Data\",\"verification_status\":\"Verified Official Contact\",\"follower_bracket\":\"10k-100k\"}','2026-10-14 01:56:47','2026-10-14 01:56:47',NULL),(5,1,2,'Sotheby\'s International Realty India','indiasothebysrealty','https://instagram.com/indiasothebysrealty','https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=200&auto=format&fit=crop&q=80','Exclusive luxury homes, penthouses, villas, and prime commercial properties across Delhi, Goa, and Mumbai.','Real estate agents','New Delhi','Delhi','India','Barakhamba Road, Connaught Place','https://sothebysrealty.com','delhi@sothebysrealty.in','+91 11 4987 6543','Amit Goyal','converted','high','authorized_directory','2026-10-11 01:56:47','2026-10-09 01:56:47','{\"source_compliance\":\"GDPR \\/ DPDP Compliant Public Profile Data\",\"verification_status\":\"Verified Official Contact\",\"follower_bracket\":\"10k-100k\"}','2026-10-14 01:56:47','2026-10-14 01:56:47',NULL),(6,1,3,'The Postcard Hotel & Hideaways','thepostcardhotel','https://instagram.com/thepostcardhotel','https://images.unsplash.com/photo-1566073771259-6a8506099945?w=200&auto=format&fit=crop&q=80','Intimate luxury boutique hotels situated in historic, transformative holiday destinations.','Hotels and travel agencies','Goa','Goa','India','Moira, North Goa','https://postcardresorts.com','experience@postcardresorts.com','+91 79995 55222','Kapil Chopra','interested','high','authorized_directory','2026-10-03 01:56:47','2026-10-10 01:56:47','{\"source_compliance\":\"GDPR \\/ DPDP Compliant Public Profile Data\",\"verification_status\":\"Verified Official Contact\",\"follower_bracket\":\"10k-100k\"}','2026-10-14 01:56:47','2026-10-14 01:56:47',NULL),(7,1,1,'Skinfiniti Aesthetic Clinic','dr_jaishreesharad','https://instagram.com/dr_jaishreesharad','https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=200&auto=format&fit=crop&q=80','Internationally recognized dermatology, laser aesthetics, and clinical cosmetology practice.','Doctors and clinics','Mumbai','Maharashtra','India','Linking Road, Khar West','https://jaishreesharad.com','clinic@jaishreesharad.com','+91 22 2648 1111','Dr. Jaishree Sharad','follow_up','medium','authorized_directory','2026-09-20 01:56:47','2026-10-10 01:56:47','{\"source_compliance\":\"GDPR \\/ DPDP Compliant Public Profile Data\",\"verification_status\":\"Verified Official Contact\",\"follower_bracket\":\"10k-100k\"}','2026-10-14 01:56:47','2026-10-14 01:56:47',NULL),(8,1,2,'Mokobara Luggage & Travel Gear','mokobara','https://instagram.com/mokobara','https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=200&auto=format&fit=crop&q=80','Modern design D2C premium luggage, backpacks, and accessories engineered for urban travelers.','Ecommerce businesses','Bengaluru','Karnataka','India','Indiranagar 12th Main','https://mokobara.com','partnerships@mokobara.com','+91 80 6902 4444','Sangeet Agrawal','new','high','authorized_directory','2026-10-01 01:56:47','2026-10-14 01:56:47','{\"source_compliance\":\"GDPR \\/ DPDP Compliant Public Profile Data\",\"verification_status\":\"Verified Official Contact\",\"follower_bracket\":\"10k-100k\"}','2026-10-14 01:56:47','2026-10-14 01:56:47',NULL),(9,1,1,'Schbang Digital Solutions','schbang','https://instagram.com/schbang','https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=200&auto=format&fit=crop&q=80','Integrated holistic marketing solutions agency offering brand strategy, media, tech, and production.','Digital marketing agencies','Mumbai','Maharashtra','India','Prathamesh Tower, Raghuvanshi Mills, Lower Parel','https://schbang.com','business@schbang.com','+91 22 6157 7777','Harshil Karia','contacted','medium','authorized_directory','2026-10-02 01:56:47','2026-10-10 01:56:47','{\"source_compliance\":\"GDPR \\/ DPDP Compliant Public Profile Data\",\"verification_status\":\"Verified Official Contact\",\"follower_bracket\":\"10k-100k\"}','2026-10-14 01:56:47','2026-10-14 01:56:47',NULL),(10,1,2,'Nicobar Lifestyle & Home Decor','nicojournal','https://instagram.com/nicojournal','https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=200&auto=format&fit=crop&q=80','Curated modern Indian clothing, homeware, and travel essentials rooted in timeless aesthetic.','Local shops','New Delhi','Delhi','India','Tulsi Farm, Mandi Road, Sultanpur','https://nicobar.com','care@nicobar.com','+91 85888 77772','Simran Lal','interested','high','authorized_directory','2026-09-17 01:56:47','2026-10-10 01:56:47','{\"source_compliance\":\"GDPR \\/ DPDP Compliant Public Profile Data\",\"verification_status\":\"Verified Official Contact\",\"follower_bracket\":\"10k-100k\"}','2026-10-14 01:56:47','2026-10-14 01:56:47',NULL),(11,1,1,'Bastian Hospitality Group','bastianmumbai','https://instagram.com/bastianmumbai','https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=200&auto=format&fit=crop&q=80','Iconic premium culinary destination celebrated for seafood, craft cocktails, and rooftop ambience.','Restaurants and cafes','Mumbai','Maharashtra','India','Kohinoor Square, Dadar West','https://bastianhospitality.com','events@bastianhospitality.com','+91 22 5078 7777','Ranjit Bindra','converted','high','authorized_directory','2026-09-23 01:56:47','2026-10-13 01:56:47','{\"source_compliance\":\"GDPR \\/ DPDP Compliant Public Profile Data\",\"verification_status\":\"Verified Official Contact\",\"follower_bracket\":\"10k-100k\"}','2026-10-14 01:56:47','2026-10-14 01:56:47',NULL),(12,1,2,'Bodycraft Salon & Medispa','bodycraftspasalon','https://instagram.com/bodycraftspasalon','https://images.unsplash.com/photo-1560066984-138dadb4c035?w=200&auto=format&fit=crop&q=80','Award-winning chain of luxury hair salons, wellness spas, and clinical aesthetic treatments.','Beauty salons and makeup artists','Bengaluru','Karnataka','India','Lavelle Road, Shanthala Nagar','https://bodycraft.co.in','info@bodycraft.co.in','+91 80 4099 2200','Manjul Gupta','not_interested','low','authorized_directory','2026-09-20 01:56:47','2026-10-09 01:56:47','{\"source_compliance\":\"GDPR \\/ DPDP Compliant Public Profile Data\",\"verification_status\":\"Verified Official Contact\",\"follower_bracket\":\"10k-100k\"}','2026-10-14 01:56:47','2026-10-14 01:56:47',NULL);
/*!40000 ALTER TABLE `business_leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `communication_logs`
--

DROP TABLE IF EXISTS `communication_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `communication_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_lead_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `channel` enum('email','call','whatsapp','instagram_manual','instagram_api') NOT NULL,
  `direction` enum('outbound','inbound') NOT NULL DEFAULT 'outbound',
  `subject` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `outcome` varchar(255) DEFAULT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `logged_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `communication_logs_user_id_foreign` (`user_id`),
  KEY `communication_logs_company_id_channel_index` (`company_id`,`channel`),
  KEY `communication_logs_business_lead_id_logged_at_index` (`business_lead_id`,`logged_at`),
  CONSTRAINT `communication_logs_business_lead_id_foreign` FOREIGN KEY (`business_lead_id`) REFERENCES `business_leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `communication_logs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `communication_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `communication_logs`
--

LOCK TABLES `communication_logs` WRITE;
/*!40000 ALTER TABLE `communication_logs` DISABLE KEYS */;
INSERT INTO `communication_logs` VALUES (1,1,1,3,'email','outbound','Strategic Partnership Inquiry for The Bombay Canteen & Bar','Sent initial pitch deck and invitation for collaborative Q4 campaign.','sent','reservations@thebombaycanteen.com','2026-10-10 01:56:47',NULL,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(2,2,1,2,'email','outbound','Strategic Partnership Inquiry for Subko Coffee Roasters & Bakehouse','Sent initial pitch deck and invitation for collaborative Q4 campaign.','sent','hello@subko.co','2026-10-13 01:56:47',NULL,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(3,4,1,3,'email','outbound','Strategic Partnership Inquiry for Cult.fit Elite Fitness Studio','Sent initial pitch deck and invitation for collaborative Q4 campaign.','sent','partner@cult.fit','2026-10-12 01:56:47',NULL,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(4,5,1,1,'email','outbound','Strategic Partnership Inquiry for Sotheby\'s International Realty India','Sent initial pitch deck and invitation for collaborative Q4 campaign.','replied','delhi@sothebysrealty.in','2026-10-12 01:56:47',NULL,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(5,6,1,2,'email','outbound','Strategic Partnership Inquiry for The Postcard Hotel & Hideaways','Sent initial pitch deck and invitation for collaborative Q4 campaign.','sent','experience@postcardresorts.com','2026-10-06 01:56:47',NULL,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(6,7,1,3,'email','outbound','Strategic Partnership Inquiry for Skinfiniti Aesthetic Clinic','Sent initial pitch deck and invitation for collaborative Q4 campaign.','sent','clinic@jaishreesharad.com','2026-10-10 01:56:47',NULL,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(7,9,1,2,'email','outbound','Strategic Partnership Inquiry for Schbang Digital Solutions','Sent initial pitch deck and invitation for collaborative Q4 campaign.','sent','business@schbang.com','2026-10-07 01:56:47',NULL,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(8,10,1,3,'email','outbound','Strategic Partnership Inquiry for Nicobar Lifestyle & Home Decor','Sent initial pitch deck and invitation for collaborative Q4 campaign.','sent','care@nicobar.com','2026-10-08 01:56:47',NULL,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(9,11,1,1,'email','outbound','Strategic Partnership Inquiry for Bastian Hospitality Group','Sent initial pitch deck and invitation for collaborative Q4 campaign.','replied','events@bastianhospitality.com','2026-10-10 01:56:47',NULL,'2026-10-14 01:56:47','2026-10-14 01:56:47');
/*!40000 ALTER TABLE `communication_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'India',
  `logo_url` varchar(255) DEFAULT NULL,
  `plan` enum('free','starter','pro','enterprise') NOT NULL DEFAULT 'free',
  `max_leads_quota` int(10) unsigned NOT NULL DEFAULT 100,
  `leads_used_count` int(10) unsigned NOT NULL DEFAULT 0,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies`
--

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES (1,'OmniMedia Growth Agency','omnimediagrowth.com','+91 98200 12345','contact@omnimediagrowth.com','Bandra Kurla Complex, Tower 4, Level 7','Mumbai','Maharashtra','India',NULL,'pro',2500,18,'{\"timezone\":\"Asia\\/Kolkata\",\"currency\":\"INR\",\"enable_email_reminders\":true}','2026-10-14 01:56:45','2026-10-14 01:56:45',NULL);
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `connected_social_accounts`
--

DROP TABLE IF EXISTS `connected_social_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `connected_social_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `platform` varchar(255) NOT NULL DEFAULT 'instagram',
  `account_id` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `profile_picture_url` varchar(255) DEFAULT NULL,
  `account_type` varchar(255) NOT NULL DEFAULT 'BUSINESS',
  `access_token` text NOT NULL,
  `token_expires_at` timestamp NULL DEFAULT NULL,
  `is_connected` tinyint(1) NOT NULL DEFAULT 1,
  `scopes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`scopes`)),
  `meta_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_social_acc_unique` (`company_id`,`platform`,`account_id`),
  KEY `connected_social_accounts_user_id_foreign` (`user_id`),
  KEY `connected_social_accounts_account_id_index` (`account_id`),
  CONSTRAINT `connected_social_accounts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `connected_social_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `connected_social_accounts`
--

LOCK TABLES `connected_social_accounts` WRITE;
/*!40000 ALTER TABLE `connected_social_accounts` DISABLE KEYS */;
INSERT INTO `connected_social_accounts` VALUES (1,1,1,'instagram','17841400293847291','omnimediagrowth','OmniMedia Growth Official','https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=200&auto=format&fit=crop&q=80','BUSINESS','EAAXmock_token_for_authorized_instagram_graph_api_access','2026-12-13 01:56:47',1,'[\"instagram_basic\",\"instagram_manage_messages\",\"pages_read_engagement\",\"business_management\"]','{\"followers_count\":14200,\"follows_count\":380,\"media_count\":152,\"ig_id\":\"17841400293847291\"}','2026-10-14 01:56:47','2026-10-14 01:56:47');
/*!40000 ALTER TABLE `connected_social_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `follow_ups`
--

DROP TABLE IF EXISTS `follow_ups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `follow_ups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_lead_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `due_at` datetime NOT NULL,
  `priority` enum('low','medium','high') NOT NULL DEFAULT 'medium',
  `status` enum('pending','completed','cancelled') NOT NULL DEFAULT 'pending',
  `completed_at` datetime DEFAULT NULL,
  `reminder_sent` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `follow_ups_business_lead_id_foreign` (`business_lead_id`),
  KEY `follow_ups_user_id_foreign` (`user_id`),
  KEY `follow_ups_company_id_status_due_at_index` (`company_id`,`status`,`due_at`),
  KEY `follow_ups_due_at_index` (`due_at`),
  KEY `follow_ups_status_index` (`status`),
  CONSTRAINT `follow_ups_business_lead_id_foreign` FOREIGN KEY (`business_lead_id`) REFERENCES `business_leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `follow_ups_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `follow_ups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `follow_ups`
--

LOCK TABLES `follow_ups` WRITE;
/*!40000 ALTER TABLE `follow_ups` DISABLE KEYS */;
INSERT INTO `follow_ups` VALUES (1,1,1,3,'Follow up on proposal sent to The Bombay Canteen & Bar','Check in regarding custom influencer marketing demo.','2026-10-15 11:30:00','high','pending',NULL,0,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(2,4,1,3,'Follow up on proposal sent to Cult.fit Elite Fitness Studio','Check in regarding custom influencer marketing demo.','2026-10-17 11:30:00','high','pending',NULL,0,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(3,6,1,2,'Follow up on proposal sent to The Postcard Hotel & Hideaways','Check in regarding custom influencer marketing demo.','2026-10-15 11:30:00','high','pending',NULL,0,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(4,7,1,3,'Follow up on proposal sent to Skinfiniti Aesthetic Clinic','Check in regarding custom influencer marketing demo.','2026-10-18 11:30:00','medium','pending',NULL,0,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(5,10,1,3,'Follow up on proposal sent to Nicobar Lifestyle & Home Decor','Check in regarding custom influencer marketing demo.','2026-10-18 11:30:00','high','pending',NULL,0,'2026-10-14 01:56:47','2026-10-14 01:56:47');
/*!40000 ALTER TABLE `follow_ups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `import_jobs`
--

DROP TABLE IF EXISTS `import_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `import_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `total_rows` int(10) unsigned NOT NULL DEFAULT 0,
  `processed_rows` int(10) unsigned NOT NULL DEFAULT 0,
  `successful_rows` int(10) unsigned NOT NULL DEFAULT 0,
  `failed_rows` int(10) unsigned NOT NULL DEFAULT 0,
  `status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
  `mapping_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`mapping_config`)),
  `error_log` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`error_log`)),
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `import_jobs_company_id_foreign` (`company_id`),
  KEY `import_jobs_user_id_foreign` (`user_id`),
  KEY `import_jobs_status_index` (`status`),
  CONSTRAINT `import_jobs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `import_jobs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `import_jobs`
--

LOCK TABLES `import_jobs` WRITE;
/*!40000 ALTER TABLE `import_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `import_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_assignments`
--

DROP TABLE IF EXISTS `lead_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_lead_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `assigned_to_user_id` bigint(20) unsigned NOT NULL,
  `assigned_by_user_id` bigint(20) unsigned NOT NULL,
  `notes` text DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_assignments_business_lead_id_foreign` (`business_lead_id`),
  KEY `lead_assignments_company_id_foreign` (`company_id`),
  KEY `lead_assignments_assigned_to_user_id_foreign` (`assigned_to_user_id`),
  KEY `lead_assignments_assigned_by_user_id_foreign` (`assigned_by_user_id`),
  CONSTRAINT `lead_assignments_assigned_by_user_id_foreign` FOREIGN KEY (`assigned_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lead_assignments_assigned_to_user_id_foreign` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lead_assignments_business_lead_id_foreign` FOREIGN KEY (`business_lead_id`) REFERENCES `business_leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lead_assignments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_assignments`
--

LOCK TABLES `lead_assignments` WRITE;
/*!40000 ALTER TABLE `lead_assignments` DISABLE KEYS */;
INSERT INTO `lead_assignments` VALUES (1,1,1,3,1,'Primary lead ownership assigned during Q4 outreach cycle.','2026-10-09 01:56:47','2026-10-14 01:56:47','2026-10-14 01:56:47'),(2,2,1,2,1,'Primary lead ownership assigned during Q4 outreach cycle.','2026-10-05 01:56:47','2026-10-14 01:56:47','2026-10-14 01:56:47'),(3,3,1,3,1,'Primary lead ownership assigned during Q4 outreach cycle.','2026-10-10 01:56:47','2026-10-14 01:56:47','2026-10-14 01:56:47'),(4,4,1,3,1,'Primary lead ownership assigned during Q4 outreach cycle.','2026-10-13 01:56:47','2026-10-14 01:56:47','2026-10-14 01:56:47'),(5,5,1,1,1,'Primary lead ownership assigned during Q4 outreach cycle.','2026-10-06 01:56:47','2026-10-14 01:56:47','2026-10-14 01:56:47'),(6,6,1,2,1,'Primary lead ownership assigned during Q4 outreach cycle.','2026-10-13 01:56:47','2026-10-14 01:56:47','2026-10-14 01:56:47'),(7,7,1,3,1,'Primary lead ownership assigned during Q4 outreach cycle.','2026-10-06 01:56:47','2026-10-14 01:56:47','2026-10-14 01:56:47'),(8,8,1,3,1,'Primary lead ownership assigned during Q4 outreach cycle.','2026-10-07 01:56:47','2026-10-14 01:56:47','2026-10-14 01:56:47'),(9,9,1,2,1,'Primary lead ownership assigned during Q4 outreach cycle.','2026-10-05 01:56:47','2026-10-14 01:56:47','2026-10-14 01:56:47'),(10,10,1,3,1,'Primary lead ownership assigned during Q4 outreach cycle.','2026-10-12 01:56:47','2026-10-14 01:56:47','2026-10-14 01:56:47'),(11,11,1,1,1,'Primary lead ownership assigned during Q4 outreach cycle.','2026-10-11 01:56:47','2026-10-14 01:56:47','2026-10-14 01:56:47'),(12,12,1,3,1,'Primary lead ownership assigned during Q4 outreach cycle.','2026-10-05 01:56:47','2026-10-14 01:56:47','2026-10-14 01:56:47');
/*!40000 ALTER TABLE `lead_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_contacts`
--

DROP TABLE IF EXISTS `lead_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_lead_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_contacts_business_lead_id_foreign` (`business_lead_id`),
  CONSTRAINT `lead_contacts_business_lead_id_foreign` FOREIGN KEY (`business_lead_id`) REFERENCES `business_leads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_contacts`
--

LOCK TABLES `lead_contacts` WRITE;
/*!40000 ALTER TABLE `lead_contacts` DISABLE KEYS */;
INSERT INTO `lead_contacts` VALUES (1,1,'Kunal Joshi','Managing Director / Marketing Lead','reservations@thebombaycanteen.com','+91 22 4966 6666',1,'Official contact listed publicly on corporate site & social bio.','2026-10-14 01:56:47','2026-10-14 01:56:47'),(2,2,'Rahul Reddy','Managing Director / Marketing Lead','hello@subko.co','+91 90047 00655',1,'Official contact listed publicly on corporate site & social bio.','2026-10-14 01:56:47','2026-10-14 01:56:47'),(3,3,'Devika Singhania','Managing Director / Marketing Lead','concierge@truefittandhill.in','+91 22 6666 4321',1,'Official contact listed publicly on corporate site & social bio.','2026-10-14 01:56:47','2026-10-14 01:56:47'),(4,4,'Vikram Shenoy','Managing Director / Marketing Lead','partner@cult.fit','+91 80 4567 8900',1,'Official contact listed publicly on corporate site & social bio.','2026-10-14 01:56:47','2026-10-14 01:56:47'),(5,5,'Amit Goyal','Managing Director / Marketing Lead','delhi@sothebysrealty.in','+91 11 4987 6543',1,'Official contact listed publicly on corporate site & social bio.','2026-10-14 01:56:47','2026-10-14 01:56:47'),(6,6,'Kapil Chopra','Managing Director / Marketing Lead','experience@postcardresorts.com','+91 79995 55222',1,'Official contact listed publicly on corporate site & social bio.','2026-10-14 01:56:47','2026-10-14 01:56:47'),(7,7,'Dr. Jaishree Sharad','Managing Director / Marketing Lead','clinic@jaishreesharad.com','+91 22 2648 1111',1,'Official contact listed publicly on corporate site & social bio.','2026-10-14 01:56:47','2026-10-14 01:56:47'),(8,8,'Sangeet Agrawal','Managing Director / Marketing Lead','partnerships@mokobara.com','+91 80 6902 4444',1,'Official contact listed publicly on corporate site & social bio.','2026-10-14 01:56:47','2026-10-14 01:56:47'),(9,9,'Harshil Karia','Managing Director / Marketing Lead','business@schbang.com','+91 22 6157 7777',1,'Official contact listed publicly on corporate site & social bio.','2026-10-14 01:56:47','2026-10-14 01:56:47'),(10,10,'Simran Lal','Managing Director / Marketing Lead','care@nicobar.com','+91 85888 77772',1,'Official contact listed publicly on corporate site & social bio.','2026-10-14 01:56:47','2026-10-14 01:56:47'),(11,11,'Ranjit Bindra','Managing Director / Marketing Lead','events@bastianhospitality.com','+91 22 5078 7777',1,'Official contact listed publicly on corporate site & social bio.','2026-10-14 01:56:47','2026-10-14 01:56:47'),(12,12,'Manjul Gupta','Managing Director / Marketing Lead','info@bodycraft.co.in','+91 80 4099 2200',1,'Official contact listed publicly on corporate site & social bio.','2026-10-14 01:56:47','2026-10-14 01:56:47');
/*!40000 ALTER TABLE `lead_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_list_items`
--

DROP TABLE IF EXISTS `lead_list_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_list_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lead_list_id` bigint(20) unsigned NOT NULL,
  `business_lead_id` bigint(20) unsigned NOT NULL,
  `added_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lead_list_items_lead_list_id_business_lead_id_unique` (`lead_list_id`,`business_lead_id`),
  KEY `lead_list_items_business_lead_id_foreign` (`business_lead_id`),
  KEY `lead_list_items_added_by_user_id_foreign` (`added_by_user_id`),
  CONSTRAINT `lead_list_items_added_by_user_id_foreign` FOREIGN KEY (`added_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lead_list_items_business_lead_id_foreign` FOREIGN KEY (`business_lead_id`) REFERENCES `business_leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lead_list_items_lead_list_id_foreign` FOREIGN KEY (`lead_list_id`) REFERENCES `lead_lists` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_list_items`
--

LOCK TABLES `lead_list_items` WRITE;
/*!40000 ALTER TABLE `lead_list_items` DISABLE KEYS */;
INSERT INTO `lead_list_items` VALUES (1,1,1,1,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(2,1,2,1,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(3,2,3,1,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(4,3,4,1,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(5,2,7,1,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(6,1,11,1,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(7,2,12,1,'2026-10-14 01:56:47','2026-10-14 01:56:47');
/*!40000 ALTER TABLE `lead_list_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_lists`
--

DROP TABLE IF EXISTS `lead_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_lists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(255) NOT NULL DEFAULT '#6366F1',
  `is_private` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_lists_company_id_foreign` (`company_id`),
  KEY `lead_lists_user_id_foreign` (`user_id`),
  CONSTRAINT `lead_lists_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lead_lists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_lists`
--

LOCK TABLES `lead_lists` WRITE;
/*!40000 ALTER TABLE `lead_lists` DISABLE KEYS */;
INSERT INTO `lead_lists` VALUES (1,1,1,'Mumbai Cafes & Fine Dining Q4','Top food & beverage establishments in South Mumbai and Bandra','#EF4444',0,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(2,1,2,'Luxury Salons & Wellness Clinics','Aesthetic doctors and premium beauty salons','#EC4899',0,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(3,1,3,'Boutique Gyms & CrossFit','High-end fitness centers across Tier-1 metros','#10B981',0,'2026-10-14 01:56:47','2026-10-14 01:56:47');
/*!40000 ALTER TABLE `lead_lists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_notes`
--

DROP TABLE IF EXISTS `lead_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_lead_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `content` text NOT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_notes_business_lead_id_foreign` (`business_lead_id`),
  KEY `lead_notes_user_id_foreign` (`user_id`),
  CONSTRAINT `lead_notes_business_lead_id_foreign` FOREIGN KEY (`business_lead_id`) REFERENCES `business_leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lead_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_notes`
--

LOCK TABLES `lead_notes` WRITE;
/*!40000 ALTER TABLE `lead_notes` DISABLE KEYS */;
INSERT INTO `lead_notes` VALUES (1,1,3,'Initial screening complete. Public profile verified. High brand resonance in Mumbai.',1,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(2,2,2,'Initial screening complete. Public profile verified. High brand resonance in Mumbai.',0,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(3,3,3,'Initial screening complete. Public profile verified. High brand resonance in Mumbai.',1,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(4,4,3,'Initial screening complete. Public profile verified. High brand resonance in Bengaluru.',1,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(5,5,1,'Initial screening complete. Public profile verified. High brand resonance in New Delhi.',1,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(6,6,2,'Initial screening complete. Public profile verified. High brand resonance in Goa.',1,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(7,7,3,'Initial screening complete. Public profile verified. High brand resonance in Mumbai.',0,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(8,8,3,'Initial screening complete. Public profile verified. High brand resonance in Bengaluru.',1,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(9,9,2,'Initial screening complete. Public profile verified. High brand resonance in Mumbai.',0,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(10,10,3,'Initial screening complete. Public profile verified. High brand resonance in New Delhi.',1,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(11,11,1,'Initial screening complete. Public profile verified. High brand resonance in Mumbai.',1,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(12,12,3,'Initial screening complete. Public profile verified. High brand resonance in Bengaluru.',0,'2026-10-14 01:56:47','2026-10-14 01:56:47');
/*!40000 ALTER TABLE `lead_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_sources`
--

DROP TABLE IF EXISTS `lead_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_sources` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `provider_type` varchar(255) NOT NULL DEFAULT 'meta_api',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `api_endpoint` varchar(255) DEFAULT NULL,
  `rate_limit_per_minute` int(10) unsigned NOT NULL DEFAULT 60,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lead_sources_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_sources`
--

LOCK TABLES `lead_sources` WRITE;
/*!40000 ALTER TABLE `lead_sources` DISABLE KEYS */;
INSERT INTO `lead_sources` VALUES (1,'Official Meta Graph API','meta-graph-api','meta_api','Direct authorized Meta Graph API for Instagram Professional & Business Accounts',1,'https://graph.facebook.com/v21.0',60,'{\"version\":\"v21.0\",\"scopes\":[\"instagram_basic\",\"pages_show_list\"]}','2026-10-14 01:56:45','2026-10-14 01:56:45'),(2,'Verified Business Registry & Public YellowPages','verified-business-registry','official_directory','Licensed public commercial trade registries and local business directories',1,NULL,120,NULL,'2026-10-14 01:56:45','2026-10-14 01:56:45'),(3,'Authorized Places API','places-data-provider','google_places','Authorized Places data provider with public verified contact points',1,NULL,100,NULL,'2026-10-14 01:56:45','2026-10-14 01:56:45'),(4,'Manual CSV & Contact Import','manual-import','manual_import','User-provided records and consented marketing imports',1,NULL,200,NULL,'2026-10-14 01:56:45','2026-10-14 01:56:45');
/*!40000 ALTER TABLE `lead_sources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_tag_assignments`
--

DROP TABLE IF EXISTS `lead_tag_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_tag_assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_lead_id` bigint(20) unsigned NOT NULL,
  `lead_tag_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `lead_tag_assignments_business_lead_id_lead_tag_id_unique` (`business_lead_id`,`lead_tag_id`),
  KEY `lead_tag_assignments_lead_tag_id_foreign` (`lead_tag_id`),
  CONSTRAINT `lead_tag_assignments_business_lead_id_foreign` FOREIGN KEY (`business_lead_id`) REFERENCES `business_leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lead_tag_assignments_lead_tag_id_foreign` FOREIGN KEY (`lead_tag_id`) REFERENCES `lead_tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_tag_assignments`
--

LOCK TABLES `lead_tag_assignments` WRITE;
/*!40000 ALTER TABLE `lead_tag_assignments` DISABLE KEYS */;
INSERT INTO `lead_tag_assignments` VALUES (1,1,1,'2026-10-14 07:26:47'),(2,1,2,'2026-10-14 07:26:47'),(3,1,3,'2026-10-14 07:26:47'),(4,1,5,'2026-10-14 07:26:47'),(5,2,1,'2026-10-14 07:26:47'),(6,2,3,'2026-10-14 07:26:47'),(7,2,4,'2026-10-14 07:26:47'),(8,3,2,'2026-10-14 07:26:47'),(9,3,3,'2026-10-14 07:26:47'),(10,4,2,'2026-10-14 07:26:47'),(11,4,4,'2026-10-14 07:26:47'),(12,5,2,'2026-10-14 07:26:47'),(13,5,5,'2026-10-14 07:26:47'),(14,6,2,'2026-10-14 07:26:47'),(15,7,2,'2026-10-14 07:26:47'),(16,7,3,'2026-10-14 07:26:47'),(17,7,4,'2026-10-14 07:26:47'),(18,8,2,'2026-10-14 07:26:47'),(19,8,5,'2026-10-14 07:26:47'),(20,9,3,'2026-10-14 07:26:47'),(21,10,2,'2026-10-14 07:26:47'),(22,11,1,'2026-10-14 07:26:47'),(23,11,2,'2026-10-14 07:26:47'),(24,11,3,'2026-10-14 07:26:47'),(25,12,2,'2026-10-14 07:26:47');
/*!40000 ALTER TABLE `lead_tag_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_tags`
--

DROP TABLE IF EXISTS `lead_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL DEFAULT '#3B82F6',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lead_tags_company_id_slug_unique` (`company_id`,`slug`),
  CONSTRAINT `lead_tags_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_tags`
--

LOCK TABLES `lead_tags` WRITE;
/*!40000 ALTER TABLE `lead_tags` DISABLE KEYS */;
INSERT INTO `lead_tags` VALUES (1,1,'Restaurant','restaurant','#EF4444','2026-10-14 01:56:47','2026-10-14 01:56:47'),(2,1,'Premium','premium','#8B5CF6','2026-10-14 01:56:47','2026-10-14 01:56:47'),(3,1,'Mumbai','mumbai','#3B82F6','2026-10-14 01:56:47','2026-10-14 01:56:47'),(4,1,'Follow-up','follow-up','#F59E0B','2026-10-14 01:56:47','2026-10-14 01:56:47'),(5,1,'Hot Lead','hot-lead','#10B981','2026-10-14 01:56:47','2026-10-14 01:56:47');
/*!40000 ALTER TABLE `lead_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (5,'0001_01_01_000000_create_companies_table',1),(6,'0001_01_01_000001_create_users_table',1),(7,'0001_01_01_000002_create_cache_table',1),(8,'0001_01_01_000003_create_jobs_table',1),(9,'2026_10_14_000001_create_connected_social_accounts_table',1),(10,'2026_10_14_000002_create_lead_sources_table',1),(11,'2026_10_14_000003_create_business_leads_table',1),(12,'2026_10_14_000004_create_lead_contacts_table',1),(13,'2026_10_14_000005_create_lead_lists_table',1),(14,'2026_10_14_000006_create_lead_list_items_table',1),(15,'2026_10_14_000007_create_lead_tags_table',1),(16,'2026_10_14_000008_create_lead_tag_assignments_table',1),(17,'2026_10_14_000009_create_lead_assignments_table',1),(18,'2026_10_14_000010_create_lead_notes_table',1),(19,'2026_10_14_000011_create_communication_logs_table',1),(20,'2026_10_14_000012_create_follow_ups_table',1),(21,'2026_10_14_000013_create_outreach_templates_table',1),(22,'2026_10_14_000014_create_subscriptions_table',1),(23,'2026_10_14_000015_create_import_jobs_table',1),(24,'2026_10_14_000016_create_audit_logs_table',1),(25,'2026_10_14_071707_create_personal_access_tokens_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outreach_templates`
--

DROP TABLE IF EXISTS `outreach_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `outreach_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `channel` enum('email','whatsapp','instagram') NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variables`)),
  `is_shared` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outreach_templates_company_id_foreign` (`company_id`),
  KEY `outreach_templates_user_id_foreign` (`user_id`),
  CONSTRAINT `outreach_templates_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `outreach_templates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outreach_templates`
--

LOCK TABLES `outreach_templates` WRITE;
/*!40000 ALTER TABLE `outreach_templates` DISABLE KEYS */;
INSERT INTO `outreach_templates` VALUES (1,1,1,'VIP Restaurant Collab Intro','email','Partnership inquiry for {{business_name}}','Hi {{contact_person}},\n\nWe love what {{business_name}} has been doing in {{city}}! Our digital marketing agency specializes in helping premier culinary brands increase footfall and high-ticket table reservations.\n\nWould you be open to a quick 10-minute discovery call this Thursday?\n\nBest regards,\nAarav Sharma\nOmniMedia Growth','[\"business_name\",\"contact_person\",\"city\"]',1,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(2,1,2,'WhatsApp Quick Connect for Salons','whatsapp',NULL,'Hello {{contact_person}}! We spotted {{business_name}} on Instagram and were impressed by your styling portfolio in {{city}}. We have a tailored marketing blueprint that helped similar luxury salons gain 40+ appointments monthly. Can we share the 1-page PDF case study?','[\"business_name\",\"contact_person\",\"city\"]',1,'2026-10-14 01:56:47','2026-10-14 01:56:47'),(3,1,3,'Instagram Direct Outreach Script','instagram',NULL,'Hey team @{{instagram_username}}! Huge fans of your work at {{business_name}}. We are hosting a private masterclass on customer acquisition for {{category}} brands in {{city}}. Would love to invite your founders!','[\"instagram_username\",\"business_name\",\"category\",\"city\"]',1,'2026-10-14 01:56:47','2026-10-14 01:56:47');
/*!40000 ALTER TABLE `outreach_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',1,'api-token','be031e0dbb7dfb0288cb6f3590e19b7b6f8a4b2a34aa2c584710abfa28052b81','[\"*\"]',NULL,NULL,'2026-10-14 02:13:05','2026-10-14 02:13:05'),(2,'App\\Models\\User',1,'test-token','b0eaa7d88613117fecc81e715e2cdb581622066869824abef391049323d8abf3','[\"*\"]','2026-10-14 02:13:05',NULL,'2026-10-14 02:13:05','2026-10-14 02:13:05');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('ly0yypY4BW8d27TEp9l4OQ3NC6zBTcfD8uU8zTW4',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZFl2bUJwOG5IazFnNnZ4VUhtMkhmWFllOXN2b0JEOHQ3ejNFeUUxNSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9zb2NpYWwtYWNjb3VudHMiO3M6NToicm91dGUiO3M6MTI6InNvY2lhbC5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6ImFmMmM1YWJjNTEwZmQ3MzI3ZWVjNDMwODBmMmRiNmVhODc1Y2YwN2M1MTU1ODJhOWU5MmY4YWRkYzNhZDRjNWIiO30=',1791969396),('WwtikVbyRQCtujuWHvxJhoeMkbGgrmotLIbOsVrb',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiWDRQTkVUeU13Uld3THZtd2RaaHM1OG1reFFzS0tGMTRYTmtrQXZUaCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiYWYyYzVhYmM1MTBmZDczMjdlZWM0MzA4MGYyZGI2ZWE4NzVjZjA3YzUxNTU4MmE5ZTkyZjhhZGRjM2FkNGM1YiI7fQ==',1791969343);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `plan_name` varchar(255) NOT NULL DEFAULT 'Free Trial',
  `status` enum('active','past_due','canceled','trialing') NOT NULL DEFAULT 'active',
  `leads_limit` int(10) unsigned NOT NULL DEFAULT 100,
  `monthly_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `billing_cycle` enum('monthly','yearly') NOT NULL DEFAULT 'monthly',
  `starts_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  `canceled_at` timestamp NULL DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_company_id_foreign` (`company_id`),
  CONSTRAINT `subscriptions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
INSERT INTO `subscriptions` VALUES (1,1,'Pro Agency Plan','active',2500,4999.00,'monthly','2026-09-29 01:56:45','2026-10-29 01:56:45',NULL,'[\"unlimited_lists\",\"csv_exports\",\"team_members_5\",\"api_access\"]','2026-10-14 01:56:45','2026-10-14 01:56:45');
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','sales_agent') NOT NULL DEFAULT 'admin',
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `phone` varchar(255) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_company_id_foreign` (`company_id`),
  CONSTRAINT `users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'Aarav Sharma (Admin)','admin@instalead.com','2026-10-14 01:56:46','$2y$12$H9adLN.nPiT8OT/SBMtqWuwtcQpyNSTz9aSfPwLg.ICBH9FtwCvHi','admin','active','+91 98201 11111',NULL,NULL,'2026-10-14 01:56:46','2026-10-14 01:56:46'),(2,1,'Priya Mehta (Manager)','manager@instalead.com','2026-10-14 01:56:46','$2y$12$6axhdp8clGSbMLEtsNbYyOBzd7zALGhRMG4az2tGnS7E2VAkAb5Ze','manager','active','+91 98202 22222',NULL,NULL,'2026-10-14 01:56:46','2026-10-14 01:56:46'),(3,1,'Rohan Verma (Sales Agent)','agent@instalead.com','2026-10-14 01:56:47','$2y$12$YGN2tbZsy8MiLos4DS.iluH4tVH6tcXJhFx.Ott9S2KM1t6xv52/a','sales_agent','active','+91 98203 33333',NULL,NULL,'2026-10-14 01:56:47','2026-10-14 01:56:47');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'instagram_crm'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-14 15:22:53
