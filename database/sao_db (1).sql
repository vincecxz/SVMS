-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 20, 2025 at 09:51 AM
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
-- Database: `sao_db`
--

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
(3, 'BSED-MATH', 'Bachelor of Secondary Education Major in Mathematics');

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
(10, '1. Plagiarism. Copying of intellectual materials or writings (including computer programs) in one’s academic assignment without proper citation and acknowledgement of the author/source, and presenting such materials/writings as though one’s own.', 'Forty (40) hours Community Service within eight (8) weeks without affecting his/her class schedule.', '5 days suspension', 'Permanent separation from the university', '2025-02-18 09:10:08', '2025-02-18 09:10:08'),
(11, '2. False authorship or contract cheating. Asking a third party to provide written material that is then submitted for assessment presented as one’s own original work', 'Forty (40) hours Community Service within eight (8) weeks without affecting his/her class schedule.', '5 days suspension', 'Suspension for one semester/ Expulsion from the university', '2025-02-18 09:12:05', '2025-02-18 09:12:05');

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
(11, '1. Non-wearing of prescribed school ID card within campus premises.', 'Light', 'Excused and permitted entry to the campus course to the signing of the undertaking in the Office of the Dean of Student Affairs and Services.', 'Excused and permitted entry to the campus course to the signing of the undertaking in the Office of the Dean of Student Affairs and Services.', 'Five (5) hours University Service within one (1) week without affecting his/her class schedule Consecutive offenses shall be given additional five (5) hours per violation. ', '2025-02-18 09:13:21', '2025-02-18 09:13:21'),
(12, '1. Acts of disrespect in words, written, electronic and/or in deed committed against any administration official, faculty member, staff, student, or visitor.\r\n', 'Serious', 'Oral Reprimand and Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule', 'Forty (40) hours University Service within eight (8) weeks without affecting  his/her class schedule', 'Suspension for Five (5) Days', '2025-02-18 09:14:30', '2025-02-18 09:14:30'),
(13, '1. Possessing, selling and consuming of prohibited drugs ', 'Very Serious', 'Expulsion', 'TBA', 'TBA', '2025-02-18 09:15:43', '2025-02-18 09:15:43');

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
(1, 1, '1A'),
(2, 1, '1B'),
(3, 1, '2A'),
(4, 1, '3A'),
(5, 1, '4A'),
(37, 2, '1-1'),
(38, 2, '1-2'),
(39, 2, '1A'),
(40, 2, '1B'),
(44, 2, '2-1'),
(41, 2, '2A'),
(42, 2, '2B'),
(43, 2, '2C'),
(10, 3, '1A'),
(11, 3, '2A'),
(12, 3, '3A'),
(13, 3, '4A');

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
(4, '1321048', 'Edina C. Villegas', 1, '4A', '09312047080', 'villegasedina0@gmail.com', NULL, '2025-02-18 01:35:22'),
(5, '132100', 'Juan Dela Cruz', 1, '1A', '09123456789', 'juan.delacruz@example.com', NULL, '2025-02-18 02:14:02'),
(6, '1321050', 'Marian Villanueva', 1, '4A', '09702111266', 'marianvillanueva001@gmail.com', NULL, '2025-02-19 00:49:15');

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
(1, 'admin', '$2y$10$DyUnKq4YUsNvEQTcsjiMDOqT.PJv.V8fOO4YTp28ahiFDkMRM1XsS', 'System Administrator', 'admin@ctu.edu.ph', 'admin', '2025-02-13 11:50:17', '2025-02-13 12:15:03');

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
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `violation_reports`
--

INSERT INTO `violation_reports` (`id`, `student_id`, `incident_datetime`, `section_type`, `offense_id`, `offense_level`, `violation_count`, `sanction`, `status`, `created_at`, `updated_at`) VALUES
(13, 6, '2025-03-08 16:47:00', 'section2', 11, 'Light', 2, 'Excused and permitted entry to the campus course to the signing of the undertaking in the Office of the Dean of Student Affairs and Services.', 'Resolved', '2025-02-20 04:56:04', '2025-02-20 09:48:46'),
(17, 4, '2025-03-08 13:56:00', 'section2', 12, 'Serious', 1, 'Oral Reprimand and Ten (10) hours University Service within two (2) weeks without affecting his/her class schedule', 'Resolved', '2025-02-20 06:57:58', '2025-02-20 09:28:47');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sec1`
--
ALTER TABLE `sec1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `sec2`
--
ALTER TABLE `sec2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `violation_reports`
--
ALTER TABLE `violation_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

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
