-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2026 at 03:17 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ampon_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `adoption_requests`
--

CREATE TABLE `adoption_requests` (
  `id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `address` varchar(200) NOT NULL,
  `housing` varchar(40) NOT NULL,
  `has_other_pets` varchar(10) NOT NULL,
  `reason` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `shelter_id` int(11) NOT NULL,
  `title` varchar(120) NOT NULL,
  `category` enum('Adoption','Lost & Found','Rescue','Vaccination Drive') NOT NULL,
  `description` text NOT NULL,
  `event_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `venue` varchar(120) NOT NULL,
  `barangay` varchar(80) NOT NULL,
  `municipality` varchar(80) NOT NULL,
  `map_query` varchar(200) NOT NULL,
  `slots` int(11) NOT NULL DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `shelter_id`, `title`, `category`, `description`, `event_date`, `start_time`, `end_time`, `venue`, `barangay`, `municipality`, `map_query`, `slots`) VALUES
(1, 1, 'Pet Adoption Day', 'Adoption', 'Meet adoptable dogs and cats, talk with shelter staff, and start your adoption application on the spot.', '2026-10-17', '09:00:00', '15:00:00', 'Tacloban City Hall Grounds', 'Barangay 97', 'Tacloban City', 'Tacloban City Hall, Leyte', 60),
(2, 2, 'Free Anti-Rabies Vaccination Drive', 'Vaccination Drive', 'Free anti-rabies shots for dogs and cats. Bring your pet on a leash or in a carrier.', '2026-10-24', '08:00:00', '12:00:00', 'Palo Municipal Gym', 'Candahug', 'Palo', 'Palo Municipal Hall, Leyte', 120),
(3, 3, 'Stray Rescue and Feeding Outreach', 'Rescue', 'Volunteers join a team that feeds and checks on stray animals around the city and reports injured animals to the shelter.', '2026-11-07', '06:00:00', '10:00:00', 'Ormoc Rescue Haven', 'Cogon', 'Ormoc City', 'Cogon, Ormoc City, Leyte', 25),
(4, 1, 'Lost and Found Pet Board Day', 'Lost & Found', 'Owners can bring photos of missing pets and finders can bring found animals for identification and reunion.', '2026-11-14', '09:00:00', '16:00:00', 'Tacloban Animal Care Center', 'San Jose', 'Tacloban City', 'San Jose, Tacloban City, Leyte', 40),
(5, 4, 'Kitten and Puppy Adoption Weekend', 'Adoption', 'A small meet-and-greet focused on kittens and puppies ready for new homes.', '2026-11-21', '09:00:00', '14:00:00', 'Baybay Pet Sanctuary', 'Pangasugan', 'Baybay City', 'Pangasugan, Baybay City, Leyte', 35),
(6, 2, 'Community Vaccination and Deworming', 'Vaccination Drive', 'Vaccination and deworming for pets owned by residents. First come, first served.', '2026-12-05', '08:00:00', '12:00:00', 'Palo Covered Court', 'Candahug', 'Palo', 'Candahug, Palo, Leyte', 100);

-- --------------------------------------------------------

--
-- Table structure for table `event_signups`
--

CREATE TABLE `event_signups` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `role` enum('Adopter','Volunteer') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--

CREATE TABLE `pets` (
  `id` int(11) NOT NULL,
  `shelter_id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `category` enum('Dog','Cat','Puppy','Kitten') NOT NULL,
  `breed` varchar(80) NOT NULL,
  `age_label` varchar(30) NOT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `size` enum('Small','Medium','Large') NOT NULL,
  `vaccination_status` enum('Fully vaccinated','Partially vaccinated','Not vaccinated') NOT NULL,
  `health_details` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(120) DEFAULT NULL,
  `status` enum('available','adopted') NOT NULL DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`id`, `shelter_id`, `name`, `category`, `breed`, `age_label`, `sex`, `size`, `vaccination_status`, `health_details`, `description`, `image`, `status`, `created_at`) VALUES
(1, 1, 'Bantay', 'Dog', 'Aspin (Asong Pinoy)', '3 years', 'Male', 'Medium', 'Fully vaccinated', 'Dewormed and neutered. No known illness.', 'Bantay is calm, loyal, and good with children. He knows basic commands like sit and stay and walks well on a leash.', NULL, 'available', '2026-10-01 13:12:31'),
(2, 1, 'Mingming', 'Cat', 'Puspin (Pusang Pinoy)', '2 years', 'Female', 'Small', 'Fully vaccinated', 'Spayed. Healthy and litter trained.', 'Mingming is quiet and affectionate. She likes sitting by the window and gets along with other cats.', NULL, 'available', '2026-10-01 13:12:31'),
(3, 1, 'Choco', 'Puppy', 'Aspin mix', '3 months', 'Male', 'Small', 'Partially vaccinated', 'First shots done. Second dose due in two weeks.', 'Choco is a playful puppy who loves chasing balls. He needs a family that can train him patiently.', NULL, 'available', '2026-10-01 13:12:31'),
(4, 2, 'Luna', 'Cat', 'Persian mix', '4 years', 'Female', 'Small', 'Fully vaccinated', 'Spayed. Needs regular grooming.', 'Luna is a gentle lap cat. She prefers quiet homes and does best as the only pet.', NULL, 'available', '2026-10-01 13:12:31'),
(5, 2, 'Kiko', 'Kitten', 'Puspin', '2 months', 'Male', 'Small', 'Not vaccinated', 'Too young for vaccines. Will be ready at 8 weeks.', 'Kiko was rescued from a drainage canal. He is energetic, curious, and already eating solid food.', NULL, 'available', '2026-10-01 13:12:31'),
(6, 2, 'Brownie', 'Dog', 'Labrador mix', '5 years', 'Female', 'Large', 'Fully vaccinated', 'Spayed. Mild arthritis, manageable with supplements.', 'Brownie is a gentle senior who loves slow walks. She is great with kids and other dogs.', NULL, 'available', '2026-10-01 13:12:31'),
(7, 3, 'Tisoy', 'Puppy', 'Aspin mix', '4 months', 'Male', 'Small', 'Partially vaccinated', 'Dewormed. Next vaccine dose scheduled this month.', 'Tisoy is friendly and curious. He follows volunteers around the shelter and is already crate trained.', NULL, 'available', '2026-10-01 13:12:31'),
(8, 3, 'Mayang', 'Cat', 'Puspin', '1 year', 'Female', 'Small', 'Fully vaccinated', 'Spayed. Healthy.', 'Mayang is playful and independent. She loves climbing and does well in homes with a safe indoor space.', NULL, 'available', '2026-10-01 13:12:31'),
(9, 4, 'Rocky', 'Dog', 'Shih Tzu mix', '6 years', 'Male', 'Small', 'Fully vaccinated', 'Neutered. Needs dental cleaning soon.', 'Rocky was surrendered by an owner who moved abroad. He is well mannered and enjoys sitting beside people.', NULL, 'available', '2026-10-01 13:12:31'),
(10, 4, 'Bella', 'Kitten', 'Puspin', '3 months', 'Female', 'Small', 'Partially vaccinated', 'First vaccine given. Dewormed.', 'Bella is shy at first but warms up fast. She loves string toys and sleeping in small boxes.', NULL, 'available', '2026-10-01 13:12:31'),
(11, 1, 'Max', 'Dog', 'Aspin', '2 years', 'Male', 'Medium', 'Fully vaccinated', 'Neutered and healthy.', 'Max was adopted by a family in Tacloban and is now living happily in his new home.', NULL, 'adopted', '2026-10-01 13:12:31');

-- --------------------------------------------------------

--
-- Table structure for table `shelters`
--

CREATE TABLE `shelters` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `municipality` varchar(80) NOT NULL,
  `barangay` varchar(80) NOT NULL,
  `address` varchar(200) NOT NULL,
  `map_query` varchar(200) NOT NULL,
  `contact` varchar(40) NOT NULL,
  `visit_days` varchar(60) NOT NULL,
  `visit_start` time NOT NULL,
  `visit_end` time NOT NULL,
  `feeding_time` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shelters`
--

INSERT INTO `shelters` (`id`, `name`, `municipality`, `barangay`, `address`, `map_query`, `contact`, `visit_days`, `visit_start`, `visit_end`, `feeding_time`) VALUES
(1, 'Tacloban Animal Care Center', 'Tacloban City', 'San Jose', 'San Jose, Tacloban City, Leyte', 'San Jose, Tacloban City, Leyte', '0917 000 0001', 'weekdays', '09:00:00', '16:00:00', '8AM and 4PM'),
(2, 'Palo Paws Shelter', 'Palo', 'Candahug', 'Candahug, Palo, Leyte', 'Candahug, Palo, Leyte', '0917 000 0002', 'weekdays and Saturdays', '08:00:00', '15:00:00', '7:30AM and 3PM'),
(3, 'Ormoc Rescue Haven', 'Ormoc City', 'Cogon', 'Cogon, Ormoc City, Leyte', 'Cogon, Ormoc City, Leyte', '0917 000 0003', 'Tuesdays to Sundays', '10:00:00', '17:00:00', '9AM and 5PM'),
(4, 'Baybay Pet Sanctuary', 'Baybay City', 'Pangasugan', 'Pangasugan, Baybay City, Leyte', 'Pangasugan, Baybay City, Leyte', '0917 000 0004', 'weekdays', '09:00:00', '15:00:00', '8AM and 3:30PM');

-- --------------------------------------------------------

--
-- Table structure for table `stories`
--

CREATE TABLE `stories` (
  `id` int(11) NOT NULL,
  `title` varchar(120) NOT NULL,
  `pet_name` varchar(60) NOT NULL,
  `adopter_name` varchar(80) NOT NULL,
  `municipality` varchar(80) NOT NULL,
  `story` text NOT NULL,
  `story_date` date NOT NULL,
  `image` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stories`
--

INSERT INTO `stories` (`id`, `title`, `pet_name`, `adopter_name`, `municipality`, `story`, `story_date`, `image`) VALUES
(1, 'Max found his people', 'Max', 'The Villanueva family', 'Tacloban City', 'We came to the adoption day just to look. Max walked straight to our youngest and sat on her feet. He sleeps beside her bed now and waits at the gate every afternoon when school lets out.', '2026-08-15', NULL),
(2, 'A second chance for Misty', 'Misty', 'Ana Reyes', 'Ormoc City', 'Misty was found hurt near the market. After two weeks at the shelter she was ready for a home. She is still shy, but she now greets me at the door every evening.', '2026-07-02', NULL),
(3, 'Two kittens, one very busy house', 'Pepper and Salt', 'Mark Dela Cruz', 'Palo', 'I planned to adopt one kitten and went home with two because they would not stop cuddling each other. The shelter staff helped us with the vaccine schedule and kept in touch after.', '2026-06-20', NULL),
(4, 'Brownie the gentle senior', 'Rico', 'Lola Remedios', 'Baybay City', 'At my age I wanted a calm dog. Rico is eight and likes slow walks. The shelter explained his needs clearly before I signed the adoption form, so nothing surprised us.', '2026-05-11', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adoption_requests`
--
ALTER TABLE `adoption_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pet_id` (`pet_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shelter_id` (`shelter_id`);

--
-- Indexes for table `event_signups`
--
ALTER TABLE `event_signups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shelter_id` (`shelter_id`);

--
-- Indexes for table `shelters`
--
ALTER TABLE `shelters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stories`
--
ALTER TABLE `stories`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adoption_requests`
--
ALTER TABLE `adoption_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `event_signups`
--
ALTER TABLE `event_signups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `shelters`
--
ALTER TABLE `shelters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stories`
--
ALTER TABLE `stories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adoption_requests`
--
ALTER TABLE `adoption_requests`
  ADD CONSTRAINT `adoption_requests_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`shelter_id`) REFERENCES `shelters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_signups`
--
ALTER TABLE `event_signups`
  ADD CONSTRAINT `event_signups_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`shelter_id`) REFERENCES `shelters` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
