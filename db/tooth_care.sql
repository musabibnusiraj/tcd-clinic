-- Database: `clinic`
-- Engine: InnoDB | Charset: utf8mb4 (utf8mb4_unicode_520_ci)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

START TRANSACTION;

-- ---------------------------------------------------------
-- users
-- ---------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(240) NOT NULL,
  `permission` enum('user','operator','doctor') NOT NULL DEFAULT 'user',
  `is_active` tinyint(5) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `users` (`id`, `username`, `email`, `password`, `permission`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$fNfundFX.rzgSLoUD/AeiOwcujWFAWoAYysuEWAXvnhnbir7XWCLu', 'operator', 1, '2023-10-31 20:47:36', '2023-10-31 20:47:36');

-- ---------------------------------------------------------
-- treatments
-- ---------------------------------------------------------

CREATE TABLE `treatments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(240) NOT NULL,
  `description` text NOT NULL,
  `treatment_fee` float DEFAULT NULL,
  `registration_fee` float DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `treatments` (`id`, `name`, `description`, `treatment_fee`, `registration_fee`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Cleanings', 'Cleanings', 4000, 1000, 1, '2023-11-15 17:46:01', '2023-11-17 23:04:27'),
(2, 'Whitening', 'Whitening', 6000, 1000, 1, '2023-11-15 17:46:22', '2023-11-16 08:23:21'),
(3, 'Filling', 'Filling', 5000, 1000, 1, '2023-11-15 17:46:34', '2023-11-17 23:04:22'),
(4, 'Nerve Filling', 'Nerve Filling', 8000, 1000, 1, '2023-11-15 17:46:48', '2023-11-16 08:23:24'),
(5, 'Root Canal Therapy', 'Root Canal Therapy', 9000, 1000, 1, '2023-11-15 17:47:00', '2023-11-16 08:23:25');

-- ---------------------------------------------------------
-- doctors
-- ---------------------------------------------------------

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `about` varchar(240) DEFAULT NULL,
  `photo` varchar(240) DEFAULT NULL,
  `is_active` tinyint(5) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_doctors_user_id` (`user_id`),
  CONSTRAINT `fk_doctors_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `doctors` (`id`, `name`, `about`, `photo`, `is_active`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Mr. A.D. Ranasinghe', 'Dental surgeon', '', 1, 1, '2023-11-15 17:11:15', '2023-11-15 17:59:35');

-- ---------------------------------------------------------
-- doctor_availability
-- ---------------------------------------------------------

CREATE TABLE `doctor_availability` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `day` varchar(100) NOT NULL,
  `session_from` varchar(240) NOT NULL,
  `session_to` varchar(240) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `is_active` tinyint(5) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_doctor_availability_doctor_id` (`doctor_id`),
  CONSTRAINT `fk_doctor_availability_doctor_id` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `doctor_availability` (`id`, `day`, `session_from`, `session_to`, `doctor_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'monday', '18:00:00', '21:00:00', 1, 1, '2023-11-15 18:16:27', '2023-11-21 02:19:06'),
(3, 'saturday', '15:00:00', '22:00:00', 1, 1, '2023-11-15 18:16:27', '2023-11-17 21:07:44'),
(4, 'sunday', '15:00:00', '22:00:00', 1, 1, '2023-11-15 18:16:27', '2023-11-15 21:33:33'),
(5, 'wednesday', '18:00:00', '21:00:00', 1, 1, '2023-11-15 18:16:27', '2023-11-16 00:41:59');

-- ---------------------------------------------------------
-- appointments
-- ---------------------------------------------------------

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_no` varchar(100) NOT NULL,
  `patient_name` varchar(100) NOT NULL,
  `address` varchar(240) NOT NULL,
  `telephone` varchar(240) NOT NULL,
  `email` varchar(240) NOT NULL,
  `nic` varchar(240) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `time_slot_from` varchar(240) NOT NULL,
  `time_slot_to` varchar(240) NOT NULL,
  `appointment_date` date NOT NULL,
  `treatment_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_appointments_doctor_id` (`doctor_id`),
  KEY `fk_appointments_treatment_id` (`treatment_id`),
  CONSTRAINT `fk_appointments_doctor_id` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`),
  CONSTRAINT `fk_appointments_treatment_id` FOREIGN KEY (`treatment_id`) REFERENCES `treatments` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- ---------------------------------------------------------
-- payments
-- ---------------------------------------------------------

CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_id` int(11) NOT NULL,
  `registration_fee` float DEFAULT NULL,
  `registration_fee_paid` int(11) DEFAULT '0',
  `treatment_fee` float DEFAULT NULL,
  `quantity` int(11) DEFAULT '0',
  `treatment_fee_paid` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_payments_appointment_id` (`appointment_id`),
  CONSTRAINT `fk_payments_appointment_id` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
