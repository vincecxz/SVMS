-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 08, 2025 at 08:13 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `saso`
--

-- --------------------------------------------------------

--
-- Table structure for table `community_service_progress`
--

CREATE TABLE `community_service_progress` (
  `id` int(11) NOT NULL,
  `violation_report_id` int(11) NOT NULL,
  `hours_completed` decimal(5,2) NOT NULL DEFAULT 0.00,
  `service_date` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `remarks` text DEFAULT NULL,
  `updated_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_service_progress`
--

INSERT INTO `community_service_progress` (`id`, `violation_report_id`, `hours_completed`, `service_date`, `date_updated`, `remarks`, `updated_by`) VALUES
(105, 276, 3.00, '2025-03-08 12:48:00', '2025-03-08 12:48:21', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `code`, `name`) VALUES
(1, 'BSIT', 'Bachelor of Science in Information Technology'),
(2, 'BIT', 'Bachelor of Industrial Technology'),
(3, 'BSED-MATH', 'Bachelor of Secondary Education Major in Mathematics'),
(4, 'BTLED-HE', 'Bachelor of Technology and Livelihood Education major in Home Economics');

-- --------------------------------------------------------

--
-- Table structure for table `sec1`
--

CREATE TABLE `sec1` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL,
  `first_sanction` text NOT NULL,
  `second_sanction` text NOT NULL,
  `third_sanction` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sec1`
--

INSERT INTO `sec1` (`id`, `description`, `first_sanction`, `second_sanction`, `third_sanction`, `created_at`, `updated_at`) VALUES
(10, 'Plagiarism. Copying of intellectual materials or writings (including computer programs) in one’s academic assignment without proper citation and acknowledgement of the author/source, and presenting such materials/writings as though one’s own.', 'Forty (40) hours Community Service within eight (8) weeks without affecting his/her class schedule.', '5 days suspension', 'Permanent separation from the university', '2025-02-18 09:10:08', '2025-03-08 06:10:18'),
(11, 'False authorship or contract cheating. Asking a third party to provide written material that is then submitted for assessment presented as one’s own original work', 'Forty (40) hours Community Service within eight (8) weeks without affecting his/her class schedule.', '5 days suspension', 'Suspension for one semester/ Expulsion from the university', '2025-02-18 09:12:05', '2025-03-08 06:10:26'),
(14, 'Collusion. Submitting work produced collaboratively for individual assessment and gain.', 'Forty (40) hours Community Service within eight (8) weeks without affecting his/her class schedule.', '5 days suspension', 'Permanent separation from the university', '2025-03-08 05:06:37', '2025-03-08 06:10:34'),
(15, 'Falsifying data or evidence.', 'Forty (40) hours Community Service within eight (8) weeks without affecting his/her class schedule.', '5 days suspension', 'Permanent separation from the university', '2025-03-08 05:11:09', '2025-03-08 06:14:10'),
(16, 'Taking a test/examination on behalf of another student or submitting works of another student as one’s own.\r\n', 'Suspension for one semester', 'Permanent Separation from the university', '', '2025-03-08 05:12:40', '2025-03-08 06:14:18'),
(17, 'Intentionally changing the grades in official documents for purposes of favorable assessment.', 'Suspension for one semester', 'Permanent Separation from the university', '', '2025-03-08 05:13:32', '2025-03-08 06:14:28'),
(18, 'Copying the answers of another or allowing another student to copy one’s answers during a test/examination.', 'Forty (40) hours Community Service within eight (8) weeks without affecting his/her class schedule.', '5 days suspension', 'Suspension for one semester', '2025-03-08 05:14:10', '2025-03-08 06:14:34'),
(19, 'Leaking questions or answers of a test/examination to another student through the use of cellular phones, pagers, strips of paper or “codigo”, and other similar means.', 'Suspension for one semester', 'Permanent separation from the university', '', '2025-03-08 05:15:11', '2025-03-08 06:14:41'),
(20, 'Non-participation to mandated campus/college programs and activities unless there is valid or justifiable excuse. ', 'Five (5) hours University Service within one (1) week without affecting his/her class schedule.', 'Ten (10) hours University Ser vice within two (2) weeks without affecting his/her class schedule.', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', '2025-03-08 05:16:19', '2025-03-08 06:14:48');

-- --------------------------------------------------------

--
-- Table structure for table `sec2`
--

CREATE TABLE `sec2` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL,
  `category` enum('Light','Serious','Very Serious') NOT NULL,
  `first_sanction` text NOT NULL,
  `second_sanction` text NOT NULL,
  `third_sanction` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sec2`
--

INSERT INTO `sec2` (`id`, `description`, `category`, `first_sanction`, `second_sanction`, `third_sanction`, `created_at`, `updated_at`) VALUES
(11, 'Non-wearing of prescribed school ID card within campus premises.', 'Light', 'Excused and permitted entry to the campus course to the signing of the undertaking in the Office of the Dean of Student Affairs and Services.', 'Excused and permitted entry to the campus course to the signing of the undertaking in the Office of the Dean of Student Affairs and Services.', 'Five (5) hours University Service within one (1) week without affecting his/her class schedule Consecutive offenses shall be given additional five (5) hours per violation. ', '2025-02-18 09:13:21', '2025-03-08 06:15:06'),
(12, 'Acts of disrespect in words, written, electronic and/or in deed committed against any administration official, \r\nfaculty member, staff, student, or visitor.\r\n', 'Serious', 'Oral Reprimand and Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule', 'Forty (40) hours University Service within eight (8) weeks without affecting  his/her class schedule', 'Suspension for Five (5) Days', '2025-02-18 09:14:30', '2025-03-08 06:16:31'),
(13, 'Possessing, selling and consuming of prohibited drugs ', 'Very Serious', 'Expulsion', '', '', '2025-02-18 09:15:43', '2025-03-08 06:18:43'),
(14, 'Non-wearing of prescribed school uniform and not abiding with the prescribed haircut .\r\n      Wearing of earrings (for male students). ', 'Light', 'Five (5) hours University Service within one (1) week without affecting his/her class schedule.', 'Ten (10) hours University Ser vice within two (2) weeks without affecting his/her class schedule.', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', '2025-03-08 05:19:55', '2025-03-08 06:15:27'),
(15, 'Using of mobile phones and or other similar electronic gadgets inside the classroom while classes are ongoing and without permission from the faculty.', 'Light', 'Five (5) hours University Service within one (1) week without affecting his/her class schedule.', 'Ten (10) hours University Ser vice within two (2) weeks without affecting his/her class schedule.', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', '2025-03-08 05:20:45', '2025-03-08 06:15:32'),
(16, 'Making noise and/ or other acts that disturb classes, academic-related activities, and/or school functions.', 'Light', 'Five (5) hours University Service within one (1) week without affecting his/her class schedule.', 'Ten (10) hours University Ser vice within two (2) weeks without affecting his/her class schedule.', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', '2025-03-08 05:21:53', '2025-03-08 06:15:36'),
(17, 'Destruction, Damage, Misuse, or Defacing of fixtures, furniture, facilities and other school properties.\r\n', 'Light', 'Five (5) hours University Service within one (1) week without affecting his/her class schedule.', 'Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule.', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', '2025-03-08 05:22:35', '2025-03-08 06:15:42'),
(18, 'Unauthorized staying in the University campus beyond 10:00 p.m. and non - school days.', 'Light', 'Five (5) hours University Service within one (1) week without affecting his/her class schedule.', 'Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule.', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', '2025-03-08 05:23:12', '2025-03-08 06:15:46'),
(19, 'Speeding of pedaled or motorized vehicles and creating excessive noise (using modified muffler, sound booster) that disturb classes, exceeding the 10kmph inside campus premises. ', 'Light', 'Five (5) hours University Service within one (1) week without affecting his/her class schedule.', 'Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule.', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', '2025-03-08 05:24:06', '2025-03-08 06:15:51'),
(20, 'Unruly behavior while within University premises;', 'Light', 'Five (5) hours University Service within one (1) week without affecting his/her class schedule.', 'Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule.', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', '2025-03-08 05:24:45', '2025-03-08 06:16:07'),
(21, 'Uttering vulgar, profanities, words towards the university students, faculty and personnel.', 'Light', 'Five (5) hours University Service within one (1) week without affecting his/her class schedule.', 'Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule.', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', '2025-03-08 05:25:20', '2025-03-08 06:16:14'),
(22, 'Bringing of gambling instruments;', 'Light', 'Five (5) hours University Service within one (1) week without affecting his/her class schedule.', 'Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule.', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', '2025-03-08 05:25:59', '2025-03-08 06:15:10'),
(23, 'Simple disrespect, whether in words or in deeds, towards any key official, faculty member, staff, student, or visitor of the University;', 'Light', 'Five (5) hours University Service within one (1) week without affecting his/her class schedule.', 'Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule.', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', '2025-03-08 05:26:53', '2025-03-08 06:15:17'),
(24, 'Simple disobedience to lawful orders of University key officials and/or their representatives;', 'Light', 'Five (5) hours University Service within one (1) week without affecting his/her class schedule.', 'Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule.', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', '2025-03-08 05:27:34', '2025-03-08 06:15:22'),
(26, 'Petting, necking and other acts of intimacy against moral standards/norms of society Acts that are sexually suggestive or romantic in nature between or among persons of any sex, including but not limited to: intimate kissing, petting, necking, cuddling, love-making and other similar acts against moral and/or societal standards;', 'Serious', 'Oral Reprimand and Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule.', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', 'Suspension for Five (5) Days.', '2025-03-08 05:32:49', '2025-03-08 06:16:43'),
(27, 'Unauthorized removal of approved posters of organizations and other posts on bulletin boards.', 'Serious', 'Oral Reprimand and Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule.', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', 'Suspension for Five (5) Days.', '2025-03-08 05:33:47', '2025-03-08 06:16:49'),
(28, 'Lending of school uniform or ID to other students for purposes of entering the campus, school building, hall, office or library. ', 'Serious', 'Oral Reprimand and Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule.', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', 'Suspension for Five (5) Days.', '2025-03-08 05:35:08', '2025-03-08 06:17:35'),
(29, 'Illegal gambling in any form inside the campus premises.\r\n', 'Serious', 'Oral Reprimand and Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule.', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', 'Suspension for Five (5) Days.', '2025-03-08 05:35:45', '2025-03-08 06:17:41'),
(30, 'Disrespect for the flag during a flag raising of flag-retreat ceremony.\r\n', 'Serious', 'Oral Reprimand and Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule.', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', 'Suspension for Five (5) Days', '2025-03-08 05:36:38', '2025-03-08 06:17:45'),
(31, 'Unauthorized soliciting, advertising, and distribution of commercial materials  for personal economic gain. ', 'Serious', 'Oral Reprimand and Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', 'Suspension for Five (5) Days.', '2025-03-08 05:37:17', '2025-03-08 06:17:50'),
(32, 'Vandalizing and/or destroying and/or stealing University property, including but not limited to: uprooting of plants, writing or spraying graffiti, and other similar acts.', 'Serious', 'Oral Reprimand and Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule', 'Suspension for Five (5) Days', '2025-03-08 05:37:58', '2025-03-08 06:17:54'),
(33, 'Possession of or being in possession of, or drinking alcoholic beverages inside the campus;', 'Serious', 'Oral Reprimand and Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule.', 'Forty (40) hours University Service within eight (8) weeks without affecting his/her class schedule.', 'Suspension for Five (5) Days', '2025-03-08 05:38:36', '2025-03-08 06:17:59'),
(34, 'Entering the campus under the influence of prohibited drugs/controlled substances, such as shabu, marijuana, rugby, cocaine.', 'Very Serious', 'Expulsion', '', '', '2025-03-08 05:43:19', '2025-03-08 06:20:11'),
(35, ' Participation to any hazing activities ', 'Very Serious', 'Expulsion', '', '', '2025-03-08 05:43:43', '2025-03-08 06:20:22'),
(36, 'Possession of firearms, explosives, toxic chemicals and deadly weapons in the University campus.', 'Very Serious', 'Expulsion', '', '', '2025-03-08 05:44:05', '2025-03-08 06:20:28'),
(37, 'Physically assaulting and injuring others. Any kind of provocation which results to physical violence between students or groups of students, between student/s and university personnel or visitor/s;', 'Very Serious', 'Suspension for five (5) days', 'Suspension for one (1) semester', 'Expulsion', '2025-03-08 05:44:34', '2025-03-08 06:20:38'),
(38, ' Unlawfully accessing, intruding in and interfering with the privacy and confidentiality of computer data\r\nprograms or systems of another student, faculty, University personnel or management office. (Without prejudice to the imposition of applicable Penalties and Sanction under the Data Privacy Act of 2012). ', 'Very Serious', 'Suspension for five (5) days', 'Suspension for one (1) semester', 'Expulsion', '2025-03-08 05:46:56', '2025-03-08 06:20:43'),
(39, 'Engaging in any form of extortion, blackmail, bribery. ', 'Very Serious', 'Suspension for one (1) semester', 'Expulsion ', '', '2025-03-08 05:47:35', '2025-03-08 06:20:48'),
(40, 'False reporting of emergency', 'Very Serious', 'Suspension for five (5) days', 'Suspension for one (1) semester', 'Expulsion', '2025-03-08 05:47:56', '2025-03-08 06:20:53'),
(41, 'Starting fires or other acts of arson', 'Very Serious', 'Expulsion', '', '', '2025-03-08 05:48:09', '2025-03-08 06:21:00'),
(42, 'Unauthorized demonstrations and mass gatherings result in disruption of class or any school activities. ', 'Very Serious', 'Suspension for five (5) days', 'Suspension for one (1) semester', 'Expulsion', '2025-03-08 05:48:40', '2025-03-08 06:19:03'),
(43, 'Gross acts of disrespect in words or in deed that tend to put the University or any Administration official, faculty member, staff, student and visitor in ridicule or contempt;', 'Very Serious', 'Suspension for five (5) days', 'Suspension for one (1) semester', 'Expulsion', '2025-03-08 05:49:12', '2025-03-08 06:19:08'),
(46, ' Threatening another with any act amounting to a crime, delict or wrong, or with the infliction of any injury or harm upon his person, honor or integrity;', 'Very Serious', 'Suspension for five (5) days', 'Suspension for one (1) semester', 'Expulsion', '2025-03-08 05:51:16', '2025-03-08 06:19:14'),
(47, ' Acts of lewdness or viewing, reading, display or distribution of pornographic materials inside the campus;\r\n', 'Very Serious', 'Suspension for thirty (30) days', 'Suspension for one (1) semester', 'Expulsion', '2025-03-08 05:51:50', '2025-03-08 06:19:21'),
(48, 'Misappropriation or misuse of student’s or organization’s funds;', 'Very Serious', 'Suspension for five (5) days if the amount is below Php10,000.00 Suspension for one (1) semester if more than PhP10,000.00 In addition to the refund of the full amount misappropriated. ', 'Suspension for one (1) semester In addition to the refund of the full amount misappropriated.', 'Expulsion In addition to the refund of the full amount misappropriated.', '2025-03-08 05:52:14', '2025-03-08 06:19:28'),
(49, 'Any violation of the provisions of RA No. 7877, otherwise known as the “Anti Sexual Harassment Act of 1995”;', 'Very Serious', 'Suspension for thirty (30) days', 'Suspension for one (1) semester', 'Expulsion', '2025-03-08 05:54:01', '2025-03-08 06:19:36'),
(50, 'Act of lasciviousness as punishable under the Revised Penal Code of the Philippines', 'Very Serious', 'Suspension for thirty (30) days', 'Suspension for one (1) semester', 'Expulsion', '2025-03-08 05:54:28', '2025-03-08 06:19:49'),
(51, 'Violation of the Cybercrime Act and other analogous cases that are done through online platform;', 'Very Serious', 'Suspension for thirty (30) days', 'Suspension for one (1) semester', 'Expulsion', '2025-03-08 05:55:00', '2025-03-08 06:19:55'),
(52, 'Any form of libelous or defamatory statement towards the university students and personnel.\r\n', 'Very Serious', 'Suspension for thirty (30) days', 'Suspension for one (1) semester', 'Expulsion', '2025-03-08 06:06:29', '2025-03-08 06:20:00'),
(53, 'Failure to refund cash to the University coffers received by any member of an organization after 30 days from end of the activity. ', 'Very Serious', 'Suspension for one (1) semester and refund of full amount. ', 'Expulsion', '', '2025-03-08 06:06:58', '2025-03-08 06:20:04'),
(54, ' Loss or neglect in handling any university funds after they have been received.', 'Very Serious', 'Suspension for one (1) semester and refund of full amount', 'Expulsion', '', '2025-03-08 06:07:32', '2025-03-08 06:20:16');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `section` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `program_id`, `section`) VALUES
(54, 1, '1A'),
(55, 1, '1B'),
(56, 1, '2A'),
(57, 1, '3A'),
(58, 1, '4A'),
(59, 1, '4B'),
(45, 2, '1-1'),
(46, 2, '1-2'),
(47, 2, '1A'),
(48, 2, '1B'),
(49, 2, '2-1'),
(50, 2, '2A'),
(51, 2, '2B'),
(52, 2, '2C'),
(53, 2, '4-1'),
(77, 3, '1A'),
(78, 3, '1B'),
(79, 3, '2A'),
(80, 3, '2B'),
(81, 3, '3A'),
(82, 3, '4A'),
(83, 4, '1A'),
(84, 4, '2A'),
(85, 4, '3A'),
(86, 4, '4A');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `id_number` varchar(20) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `program_id` int(11) NOT NULL,
  `section` varchar(3) NOT NULL,
  `contact_number` varchar(11) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `id_number`, `full_name`, `program_id`, `section`, `contact_number`, `email`, `password`, `created_at`) VALUES
(22, '1321050', 'Marian Villanueva', 1, '4A', '09702111266', 'marianvillanueva001@gmail.com', '$2y$10$iaGO7V5wFIZxVJq9k4jReeQw4BC33YL5r0GrrPvRTSlS8hvFo0Pky', '2025-02-27 01:07:11'),
(23, '1321048', 'Edina Villegas', 1, '4B', '09312047080', 'villegasedina0@gmail.com', '$2y$10$zPMQluqmcYGnn7x96u16budM2dJ/bFq7ZNTdQStI58g8.aZ15Gzf6', '2025-02-27 01:08:14'),
(24, '1321100', 'Clint Abad', 1, '4A', NULL, 'clint@gmail.com', NULL, '2025-02-27 01:37:36'),
(25, '1321125', 'Rebecca Abella', 1, '4A', NULL, 'rebecca@gmail.com', NULL, '2025-02-27 01:37:36'),
(26, '1321076', 'John Dave Abellanosa', 1, '4A', NULL, 'john@gmail.com', NULL, '2025-02-27 01:37:36'),
(27, '1321118', 'Key Alburo', 1, '4A', NULL, 'key@gmail.com', NULL, '2025-02-27 01:37:36'),
(28, '1321105', 'Dax Denver Alferez', 1, '4A', NULL, 'dax@gmail.com', '$2y$10$bApqkSQhJAC6E88.LwgfVuFe6RE3fGTa80oIsqCX0NG4v/5L6l1f2', '2025-02-27 01:37:36'),
(29, '1321068', 'Joward Alicaya', 1, '4A', NULL, 'joward@gmail.com', NULL, '2025-02-27 01:37:36'),
(30, '1321071', 'Melbert Kent Arnon', 1, '4A', NULL, 'melbert@gmail.com', '$2y$10$YpZmGWzt0DcvJo2He2ZhOuh0HX5URG3e1zd6jujstp1ayUmIPpJPG', '2025-02-27 01:37:36'),
(31, '1321046', 'Andrey Atay', 1, '4A', NULL, 'andrey@gmail.com', '$2y$10$sSzB08QMuhyYBtUHVsRLne0xLn1r8iWWt4tGaVhnJy5Vi79OFoI7G', '2025-02-27 01:37:36'),
(32, '1321115', 'Derrick Booc', 1, '4A', NULL, 'derrick@gmail.com', NULL, '2025-02-27 01:37:36'),
(33, '1191077', 'Clark Jhon Candol', 1, '4A', NULL, 'clark@gmail.com', NULL, '2025-02-27 01:37:36'),
(34, '1321099', 'Rhyzll Cañada', 1, '4A', NULL, 'rhyzllcanada@gmail.com', NULL, '2025-02-27 01:37:36'),
(35, '1321041', 'Jelona Capablanca', 1, '4A', NULL, 'jelona@gmail.com', NULL, '2025-02-27 01:37:36'),
(36, '1321122', 'Rizaldo Vincent Dobleros', 1, '4A', NULL, 'rizaldo@gmail.com', NULL, '2025-02-27 01:37:36'),
(37, '1321040', 'Jon Bryle Dolorzo', 1, '4A', NULL, 'jon@gmail.com', NULL, '2025-02-27 01:37:36'),
(38, '1321031', 'Michelle Enciso', 1, '4A', NULL, 'michelle@gmail.com', NULL, '2025-02-27 01:37:36'),
(39, '1321133', 'Rachel Franza', 1, '4A', NULL, 'franzarachel18@gmail.com', NULL, '2025-02-27 01:37:36'),
(40, '1321044', 'Vince Anthonie Genobaña', 1, '4A', NULL, 'vince@gmail.com', '$2y$10$7cgLKL5DavdI5GXvhTphy.LLOBCvagBovZoUZfPsVIYUYKs.ZhAA2', '2025-02-27 01:37:36'),
(41, '1321136', 'Lord Jay Geonzon', 1, '4A', NULL, 'lord@gmail.com', NULL, '2025-02-27 01:37:36'),
(42, '1321035', 'Ronshiel Gitalan', 1, '4A', NULL, 'ronshiel@gmail.com', NULL, '2025-02-27 01:37:36'),
(43, '1321110', 'Camila Gorres', 1, '4A', NULL, 'camila@gmail.com', '$2y$10$XZsnWS.CtK2jHqBACw/qc.9TDlV84tA7mahmKd2wgreaRgwxq2zfK', '2025-02-27 01:37:36'),
(44, '1321096', 'Daniel Christian Largo', 1, '4A', NULL, 'daniel@gmail.com', NULL, '2025-02-27 01:37:36'),
(45, '1321101', 'Shella Mag-alasin', 1, '4A', NULL, 'shella@gmail.com', NULL, '2025-02-27 01:37:36'),
(46, '1321084', 'Maricel Manriquez', 1, '4A', NULL, 'maricel@gmail.com', '$2y$10$HpBrjmyV/5Aqi9NE9aK.9u9.RNnY5BPv6hzUii.xY3lVZkZLpNrGq', '2025-02-27 01:37:36'),
(47, '1321067', 'Aiza Oberes', 1, '4A', '09123456789', 'aiza@gmail.com', '$2y$10$SceVqe3D0zpxD9GT33IogOc.lY33aUYRqeeztoSZ2NkHJ7BnbRlUe', '2025-02-27 01:37:36'),
(48, '1321103', 'Trisha Ravanes', 1, '4A', NULL, 'trisha@gmail.com', NULL, '2025-02-27 01:37:36'),
(49, '1321070', 'Jayvie Llyod  Sanson', 1, '4A', '09919340590', 'jayviellyod2@gmail.com', NULL, '2025-02-27 01:37:36'),
(50, '1321029', 'Jonah Mae Solon', 1, '4A', '09751868680', 'solonjmae1485@gmail.com', '$2y$10$Qt2TojTTD8ldm3aNmWgDYugKN.kAatMzesgOOhoPAHI1dZ5Dk1FjG', '2025-02-27 01:37:36'),
(51, '1321037', 'James  Tajor', 1, '4A', NULL, 'james@gmail.com', '$2y$10$KafLOMas9NtJ720Bd3x2XuKduldC3g0GBMJ40nw75ymSlivREmSCK', '2025-02-27 01:37:36'),
(52, '1321106', 'Syvil Mae Tapinit', 1, '4A', NULL, 'syvil@gmail.com', NULL, '2025-02-27 01:37:36'),
(53, '1321069', 'Christine Velasco', 1, '4A', '09669962698', 'christinevelasco0918@gmail.com', '$2y$10$5i9U/4RhBV1EiUxiLfEX5uFogkV79kRFmjM7h.MHLWo1wnl1FuMdq', '2025-02-27 01:37:36'),
(54, '1321036', 'Paulo Abaquita', 1, '4B', '09123456789', 'paulo@gmail.com', NULL, '2025-02-27 01:41:45'),
(55, '1321039', 'John Fritz Alegarbes', 1, '4B', '09987654321', 'fritz@gmail.com', NULL, '2025-02-27 01:41:45'),
(56, '1321060', 'Gian Karlo Aliganga', 1, '4B', NULL, 'karlo@gmail.com', NULL, '2025-02-27 01:41:45'),
(57, '1321033', 'Ralph Lorence Aricayos', 1, '4B', NULL, 'lorence@gmail.com', NULL, '2025-02-27 01:41:45'),
(58, '1321087', 'Perry Vincent Borja', 1, '4B', NULL, 'vincent@gmail.com', NULL, '2025-02-27 01:41:45'),
(59, '1321074', 'Mary Jhaien Cabelis', 1, '4B', NULL, 'jhaien@gmail.com', NULL, '2025-02-27 01:41:45'),
(61, '1321056', 'Jeo Vincent Carretas', 1, '4B', NULL, 'jeo@gmail.com', NULL, '2025-02-27 01:43:52'),
(62, '1321093', 'Bairon Brix Cobacha', 1, '4B', NULL, 'brix@gmail.com', '$2y$10$Hcc6RFOniTshZidOL4El2u45FY1GDZUDmfgA1lJNzR4Y.IE7AxRTO', '2025-02-27 01:43:52'),
(63, '1321045', 'Jose Rene Generosa', 1, '4B', NULL, 'rene@gmail.com', NULL, '2025-02-27 01:43:52'),
(64, '1321032', 'Eleazar Geraldez', 1, '4B', NULL, 'eleazar@gmail.com', NULL, '2025-02-27 01:43:52'),
(65, '1321047', 'Rodel Laput', 1, '4B', '09263003267', 'rodel@gmail.com', NULL, '2025-02-27 01:43:52'),
(66, '1321030', 'Trisha Nicole Lee', 1, '4B', NULL, 'nicole@gmail.com', NULL, '2025-02-27 01:43:52'),
(67, '1321077', 'Niña Jean Llanto', 1, '4B', NULL, 'jean@gmail.com', NULL, '2025-02-27 01:43:52'),
(68, '1321089', 'Apryl Mae Masayon', 1, '4B', '09453521967', 'mae@gmail.com', '$2y$10$Zh8JAqWYdIjLRHkGBJk89eBRLTm1kkXP9xjlhXX0F22hsWG1yd1P6', '2025-02-27 01:43:52'),
(69, '1321085', 'Antonio Mañacap', 1, '4B', NULL, 'antonio@gmail.com', '$2y$10$5o2auvqb8V0DsKuF2qdcz.2IDDYbCmADkkdmZ.JvIeACMGnYn4DoK', '2025-02-27 01:43:52'),
(70, '1321063', 'Lester Olivier Ocampo', 1, '4B', NULL, 'olivier@gmail.com', NULL, '2025-02-27 01:43:52'),
(71, '1321123', 'Emmanuel Pitallar', 1, '4B', NULL, 'emmanuel@gmail.com', '$2y$10$jaKYXtyJTudX.SXaDRPwE.6NRpS0kEyT3SwKJEy4tB/ETV15LTy3m', '2025-02-27 01:43:52'),
(72, '1321082', 'Queencie Shane Resma', 1, '4B', NULL, 'shane@gmail.com', NULL, '2025-02-27 01:43:52'),
(73, '1321083', 'Kaye Dianne Sasil', 1, '4B', '09632130143', 'dianne@gmail.com', '$2y$10$.MlWJy5ZQdIqHuTxfTMU/u0ihoIrowHabOhGqP0iT4DfW0btWcKYO', '2025-02-27 01:43:52'),
(74, '1321072', 'Gelah Setenta', 1, '4B', NULL, 'gelah@gmail.com', '$2y$10$gOCrMUYWwExj006L8a/.qOzugSWSbxhlKvzQztCBRmW/WNZvyilF.', '2025-02-27 01:43:52'),
(75, '1321091', 'Gwyneth Tabliga', 1, '4B', NULL, 'gwyneth@gmail.com', '$2y$10$Kw3DrqezlPseFaGq164UjuA7kg/r9HMGooUkzJCQvrm3EXlL6uz6S', '2025-02-27 01:43:52'),
(76, '1321079', 'Christian Lyle Tapasao', 1, '4B', NULL, 'lyle@gmail.com', NULL, '2025-02-27 01:43:52'),
(77, '1321124', 'Niño Angelo Tidoy', 1, '4B', NULL, 'angelo@gmail.com', NULL, '2025-02-27 01:43:52'),
(78, '1321073', 'Mymae Ugsimar', 1, '4B', NULL, 'mymae@gmail.com', '$2y$10$nxaoyNemu4HMs/L5.f9NMuqldIVnD.0LI9stDq/g5x8lnZhqCUb.K', '2025-02-27 01:43:52'),
(80, '1321108', 'Rodel Villamero', 1, '4B', NULL, 'rodel2@gmail.com', NULL, '2025-02-27 01:44:38'),
(81, '1321095', 'Jessie Villareal', 1, '4B', NULL, 'jessie@gmail.com', NULL, '2025-02-27 01:44:38'),
(82, '1321049', 'Aaron John Requierme', 1, '4B', '', 'aaron@gmail.com', '$2y$10$ZZEg/j9gG0cAL09zwV7wHeluz4wPRa4ICXPgQTjB.tchwSPdDrYPm', '2025-02-27 01:49:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','staff') NOT NULL DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$.Cw6Lvydd7hk60IspTlXtugdjUtwEOxZDyDhzIJ.H2vPopMedtBtO', 'System Administrator', 'admin@ctu.edu.ph', 'admin', '2025-02-13 11:50:17', '2025-02-21 08:22:33');

-- --------------------------------------------------------

--
-- Table structure for table `violation_reports`
--

CREATE TABLE `violation_reports` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `incident_datetime` datetime NOT NULL,
  `section_type` varchar(10) NOT NULL,
  `offense_id` int(11) NOT NULL,
  `offense_level` varchar(20) DEFAULT NULL,
  `violation_count` int(11) DEFAULT 1,
  `sanction` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `resolution_datetime` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `total_hours` int(11) DEFAULT NULL,
  `completed_hours` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `violation_reports`
--

INSERT INTO `violation_reports` (`id`, `student_id`, `incident_datetime`, `section_type`, `offense_id`, `offense_level`, `violation_count`, `sanction`, `status`, `resolution_datetime`, `created_at`, `updated_at`, `total_hours`, `completed_hours`) VALUES
(270, 82, '2025-03-08 12:28:00', 'section1', 10, NULL, 1, 'Forty (40) hours Community Service within eight (8) weeks without affecting his/her class schedule.', 'Active', NULL, '2025-03-08 05:29:29', NULL, 40, 0),
(271, 82, '2025-03-09 09:32:00', 'section1', 10, NULL, 2, '5 days suspension', 'Resolved', '2025-03-08 12:30:00', '2025-03-08 05:29:48', '2025-03-08 05:30:25', NULL, 0),
(272, 22, '2025-03-08 12:40:00', 'section2', 12, 'Serious', 1, 'Oral Reprimand and Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule', 'Resolved', '2025-03-29 12:47:00', '2025-03-08 05:41:01', '2025-03-08 05:47:04', 10, 0),
(273, 22, '2025-03-09 12:41:00', 'section2', 12, 'Serious', 2, 'Forty (40) hours University Service within eight (8) weeks without affecting  his/her class schedule', 'Resolved', '2025-03-29 12:47:00', '2025-03-08 05:41:15', '2025-03-08 05:47:04', 40, 0),
(274, 23, '2025-03-08 12:47:00', 'section2', 11, 'Light', 1, 'Excused and permitted entry to the campus course to the signing of the undertaking in the Office of the Dean of Student Affairs and Services.', 'Resolved', '2025-03-21 12:48:00', '2025-03-08 05:47:38', '2025-03-08 05:48:40', NULL, 0),
(275, 23, '2025-03-09 12:47:00', 'section2', 11, 'Light', 2, 'Excused and permitted entry to the campus course to the signing of the undertaking in the Office of the Dean of Student Affairs and Services.', 'Resolved', '2025-03-21 12:48:00', '2025-03-08 05:47:55', '2025-03-08 05:48:40', NULL, 0),
(276, 23, '2025-03-10 12:47:00', 'section2', 11, 'Light', 3, 'Five (5) hours University Service within one (1) week without affecting his/her class schedule Consecutive offenses shall be given additional five (5) hours per violation. ', 'Resolved', '2025-03-21 12:48:00', '2025-03-08 05:48:10', '2025-03-08 05:48:40', 5, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `community_service_progress`
--
ALTER TABLE `community_service_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `violation_report_id` (`violation_report_id`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `sec1`
--
ALTER TABLE `sec1`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sec2`
--
ALTER TABLE `sec2`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `program_section` (`program_id`,`section`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `violation_reports`
--
ALTER TABLE `violation_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `offense_id` (`offense_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `community_service_progress`
--
ALTER TABLE `community_service_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sec1`
--
ALTER TABLE `sec1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `sec2`
--
ALTER TABLE `sec2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `violation_reports`
--
ALTER TABLE `violation_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=277;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `community_service_progress`
--
ALTER TABLE `community_service_progress`
  ADD CONSTRAINT `community_service_progress_ibfk_1` FOREIGN KEY (`violation_report_id`) REFERENCES `violation_reports` (`id`),
  ADD CONSTRAINT `community_service_progress_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);

--
-- Constraints for table `violation_reports`
--
ALTER TABLE `violation_reports`
  ADD CONSTRAINT `violation_reports_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
