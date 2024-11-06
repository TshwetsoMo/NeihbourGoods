-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2024 at 12:33 PM
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
-- Database: `neihbourgoods`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `bookmark_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookmarks`
--

INSERT INTO `bookmarks` (`bookmark_id`, `user_id`, `post_id`, `created_at`) VALUES
(2, 6, 3, '2024-10-06 14:34:13'),
(5, 9, 4, '2024-10-06 14:34:13'),
(6, 10, 5, '2024-10-06 14:34:13');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Fruits'),
(2, 'Vegetables'),
(3, 'Dairy'),
(4, 'Baked Goods'),
(5, 'Meat'),
(6, 'Beverages'),
(7, 'Snacks'),
(8, 'Frozen Foods'),
(9, 'Canned Goods'),
(10, 'Pantry Staples');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_type` varchar(20) NOT NULL DEFAULT 'donation',
  `comment_text` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `post_id`, `user_id`, `post_type`, `comment_text`, `created_at`) VALUES
(1, 1, 6, 'donation', 'Great initiative! Excited to be part of this community.', '2024-10-06 14:34:13'),
(2, 2, 7, 'donation', 'Looking forward to the donation drive.', '2024-10-06 14:34:13'),
(4, 3, 9, 'donation', 'How can I request some of these apples?', '2024-10-06 14:34:13'),
(5, 4, 10, 'donation', 'Can’t wait for the potluck event!', '2024-10-06 14:34:13'),
(6, 5, 6, 'donation', 'I would like to claim some dairy products.', '2024-10-06 14:34:13'),
(9, 9, 11, 'donation', 'Each person is allowed only 2kg of the stated quantity so that we are able to help more people', '2024-11-02 09:48:23');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `event_date` datetime NOT NULL,
  `location` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `title`, `description`, `event_date`, `location`, `created_at`, `updated_at`) VALUES
(1, 'Annual Food Donation Drive', 'Join us for our annual food donation drive to help those in need.', '2024-10-20 09:00:00', 'Central Park, Anytown', '2024-10-06 14:34:13', '2024-10-06 14:34:13'),
(2, 'Community Potluck', 'Bring your favorite dish and enjoy a day of community bonding.', '2024-10-14 12:00:00', '456 Oak Avenue, Anytown', '2024-10-06 14:34:13', '2024-10-06 14:34:13'),
(3, 'Fresh Produce Market', 'A marketplace for fresh and organic produce donated by local farmers.', '2024-10-25 08:00:00', '789 Pine Road, Anytown', '2024-10-06 14:34:13', '2024-10-06 14:34:13');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `giver_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `rating` decimal(3,2) NOT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `food_listings`
--

CREATE TABLE `food_listings` (
  `listing_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_description` text NOT NULL,
  `quantity` varchar(50) DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `pickup_location` varchar(255) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` enum('available','claimed','expired') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_listings`
--

INSERT INTO `food_listings` (`listing_id`, `user_id`, `item_description`, `quantity`, `expiration_date`, `pickup_location`, `image_path`, `status`, `created_at`, `updated_at`) VALUES
(4, 4, 'Organic Carrots', '30 kg', '2024-10-18', '321 Birch Lane, Anytown', 'uploads/food_listings/carrots.jpg', 'available', '2024-10-12 08:02:03', '2024-10-12 08:02:03'),
(5, 5, 'Frozen Mixed Vegetables', '25 kg', '2024-12-01', '654 Cedar Court, Anytown', 'uploads/food_listings/mixed_vegetables.jpg', 'available', '2024-10-12 08:02:03', '2024-10-12 08:02:03'),
(6, 11, 'Beef Cuts', '1000kg', '2026-12-12', 'Wonderpark Shopping Centre', 'uploads/food_listings/beef cuts.jpg', 'available', '2024-10-13 16:20:40', '2024-10-13 16:23:12'),
(7, 13, 'Potatoes', '1000kg', '2025-01-12', 'Denlyn Shopping Centre', './uploads/food_listings/8be547de0d96c8b8c822a9b597cc6987.jpg', 'available', '2024-10-24 11:24:28', '2024-10-24 11:24:28'),
(9, 11, 'Onion Farm Haul December Special', '100kg', '2025-01-01', 'Sosha', './uploads/food_listings/851881f1941fa9000d57860a8a0ac0b0.jpeg', 'available', '2024-10-25 11:04:53', '2024-10-25 11:04:53');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `like_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`like_id`, `post_id`, `user_id`, `created_at`) VALUES
(4, 3, 6, '2024-10-06 14:34:13'),
(5, 3, 7, '2024-10-06 14:34:13'),
(7, 4, 9, '2024-10-06 14:34:13'),
(8, 5, 10, '2024-10-06 14:34:13'),
(9, 5, 6, '2024-10-06 14:34:13'),
(10, 5, 7, '2024-10-06 14:34:13');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `media_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` enum('image','video') NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`media_id`, `user_id`, `file_path`, `file_type`, `uploaded_at`) VALUES
(3, 3, 'uploads/media/robertjohnson_profile.jpg', 'image', '2024-10-06 14:34:13'),
(4, 4, 'uploads/media/emilydavis_profile.jpg', 'image', '2024-10-06 14:34:13'),
(5, 5, 'uploads/media/michaelbrown_profile.jpg', 'image', '2024-10-06 14:34:13'),
(6, 6, 'uploads/media/laurawilson_profile.jpg', 'image', '2024-10-06 14:34:13'),
(7, 7, 'uploads/media/davidmiller_profile.jpg', 'image', '2024-10-06 14:34:13'),
(9, 9, 'uploads/media/danieltaylor_profile.jpg', 'image', '2024-10-06 14:34:13'),
(10, 10, 'uploads/media/jessicaanderson_profile.jpg', 'image', '2024-10-06 14:34:13'),
(11, 11, 'uploads/media/tshwetsomokgatlhe_profile.png', 'image', '2024-10-06 14:34:13');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('new_post','new_comment','new_like','new_message','event_reminder','offer_expiry') NOT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `type`, `content`, `is_read`, `created_at`) VALUES
(1, 6, 'new_post', 'A new post titled \"Fresh Organic Fruits Available\" has been added to the News Feed.', 0, '2024-10-06 14:34:13'),
(2, 7, 'new_comment', 'User Emily Davis commented on your post \"Weekly Donation Drive\".', 0, '2024-10-06 14:34:13'),
(4, 9, 'event_reminder', 'Reminder: Community Potluck Event is tomorrow at 456 Oak Avenue.', 0, '2024-10-06 14:34:13'),
(5, 10, 'offer_expiry', 'Your offer on \"Limited-Time Offer on Dairy Products\" is expiring soon.', 0, '2024-10-06 14:34:13');

-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

CREATE TABLE `polls` (
  `poll_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `polls`
--

INSERT INTO `polls` (`poll_id`, `question`, `expires_at`, `created_at`) VALUES
(1, 'What type of food would you like to see more of on NeighbourGoods?', '2024-11-15 23:59:59', '2024-10-06 14:34:13'),
(2, 'How often should we organize food donation drives?', '2024-11-20 23:59:59', '2024-10-06 14:34:13');

-- --------------------------------------------------------

--
-- Table structure for table `poll_options`
--

CREATE TABLE `poll_options` (
  `option_id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `option_text` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poll_options`
--

INSERT INTO `poll_options` (`option_id`, `poll_id`, `option_text`) VALUES
(1, 1, 'Fruits and Vegetables'),
(2, 1, 'Dairy Products'),
(3, 1, 'Baked Goods'),
(4, 1, 'Frozen Foods'),
(5, 2, 'Weekly'),
(6, 2, 'Bi-Weekly'),
(7, 2, 'Monthly');

-- --------------------------------------------------------

--
-- Table structure for table `poll_votes`
--

CREATE TABLE `poll_votes` (
  `vote_id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `voted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poll_votes`
--

INSERT INTO `poll_votes` (`vote_id`, `poll_id`, `option_id`, `user_id`, `voted_at`) VALUES
(1, 1, 1, 6, '2024-10-06 14:34:13'),
(2, 1, 2, 7, '2024-10-06 14:34:13'),
(4, 1, 3, 9, '2024-10-06 14:34:13'),
(5, 2, 2, 10, '2024-10-06 14:34:13'),
(6, 2, 2, 6, '2024-10-06 14:34:13'),
(7, 2, 3, 7, '2024-10-06 14:34:13'),
(8, 1, 1, 11, '2024-10-24 12:33:04');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `media_path` varchar(255) DEFAULT NULL,
  `category` enum('news','offer','event','success_story','announcement') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `user_id`, `title`, `content`, `media_path`, `category`, `created_at`, `updated_at`) VALUES
(3, 3, 'Fresh Organic Fruits Available', 'Donating a variety of fresh organic fruits. Perfect for families and individuals looking for healthy food options.', 'uploads/posts/fresh_fruits.jpg', 'offer', '2024-10-06 14:34:13', '2024-10-06 14:34:13'),
(4, 4, 'Community Potluck Event', 'Participate in our community potluck event this Sunday. Bring a dish to share and enjoy a day of food and fellowship.', 'uploads/posts/potluck_event.jpg', 'event', '2024-10-06 14:34:13', '2024-10-06 14:34:13'),
(5, 5, 'Limited-Time Offer on Dairy Products', 'We have a limited-time offer on dairy products. Grab fresh milk, cheese, and yogurt while supplies last!', 'uploads/posts/dairy_offer.jpg', 'offer', '2024-10-06 14:34:13', '2024-10-06 14:34:13');

-- --------------------------------------------------------

--
-- Table structure for table `post_tags`
--

CREATE TABLE `post_tags` (
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_tags`
--

INSERT INTO `post_tags` (`post_id`, `tag_id`) VALUES
(3, 1),
(3, 2),
(4, 6),
(5, 3),
(5, 4);

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `request_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','approved','denied','canceled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`request_id`, `listing_id`, `user_id`, `request_date`, `status`) VALUES
(4, 4, 9, '2024-10-06 14:34:13', 'pending'),
(5, 5, 10, '2024-10-06 14:34:13', 'pending'),
(7, 6, 13, '2024-10-24 13:16:03', 'approved'),
(9, 7, 11, '2024-10-30 00:02:48', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `tag_id` int(11) NOT NULL,
  `tag_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`tag_id`, `tag_name`) VALUES
(1, 'Organic'),
(2, 'Perishable'),
(3, 'High Demand'),
(4, 'Newly Added'),
(5, 'Urgent'),
(6, 'Local'),
(7, 'Eco-Friendly'),
(8, 'Bulk'),
(9, 'Non-Perishable'),
(10, 'Discounted');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `phone_number` varchar(20) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `date_registered` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `availability` text DEFAULT NULL,
  `needs` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password_hash`, `address`, `rating`, `phone_number`, `bio`, `profile_picture`, `date_registered`, `last_login`, `availability`, `needs`) VALUES
(1, 'Admin', 'admin@example.com', '$2y$10$Os2/d6TxxjawKqk7UHj9FuLL34DfADQ3.s8syF0kiS6hhwErKgeQC', 'Online', 0.00, '0101010101', 'I am the admin for NeighbourGoods.', NULL, '2024-11-05 14:10:09', NULL, 'All day every day', 'Respectful Users'),
(3, 'Robert McJohn', 'rm@example.com', '$2y$10$E5fG6hI7jK8lM9nO0pQ1rS2tU3vW4xY5zA6bC7dE8fG9hI0jK1lM', '789 Pine Road, Anytown', 4.20, '555-8765', 'Happy to donate items I no longer need.', 'uploads/robertjohnson.jpg', '2024-10-04 13:40:12', NULL, 'Flexible availability', NULL),
(4, 'Emily Davis', 'emilydavis@example.com', '$2y$10$A3bC4dE5fG6hI7jK8lM9nO0pQ1rS2tU3vW4xY5zA6bC7dE8fG9hI', '321 Birch Lane, Anytown', 4.70, '555-4321', 'Believer in community support and sustainability.', 'uploads/emilydavis.jpg', '2024-10-04 13:40:12', NULL, 'Weekends only', NULL),
(5, 'Michael Brown', 'michaelbrown@example.com', '$2y$10$N0pQ1rS2tU3vW4xY5zA6bC7dE8fG9hI0jK1lM2nO3pQ4rS5tU6vW', '654 Cedar Court, Anytown', 4.60, '555-6789', 'I have items to share with those who need them.', 'uploads/michaelbrown.jpg', '2024-10-04 13:40:12', NULL, 'Evenings during weekdays', NULL),
(6, 'Laura Wilson', 'laurawilson@example.com', '$2y$10$D4eF5gH6iJ7kL8mN9oP0qR1sT2uV3wX4yZ5aB6cD7eF8gH9iJ0kL', '987 Elm Street, Anytown', 4.30, '555-1357', 'Looking for household items for my family.', 'uploads/laurawilson.jpg', '2024-10-04 13:40:12', NULL, NULL, 'In need of kitchen appliances and furniture.'),
(7, 'David Miller', 'davidmiller@example.com', '$2y$10$S2tU3vW4xY5zA6bC7dE8fG9hI0jK1lM2nO3pQ4rS5tU6vW7xY8zA', '246 Spruce Street, Anytown', 4.10, '555-2468', 'New to the area and in need of some essentials.', 'uploads/davidmiller.jpg', '2024-10-04 13:40:12', NULL, NULL, 'Looking for tools and electronics.'),
(9, 'Daniel Taylor', 'danieltaylor@example.com', '$2y$10$V3wX4yZ5aB6cD7eF8gH9iJ0kL1mN2oP3qR4sT5uV6wX7yZ8aB9cD', '864 Poplar Place, Anytown', 4.20, '555-8642', 'Looking for support while I get back on my feet.', 'uploads/danieltaylor.jpg', '2024-10-04 13:40:12', NULL, NULL, 'In need of clothing and personal care items.'),
(10, 'Jessica Anderson', 'jessicaanderson@example.com', '$2y$10$K8lM9nO0pQ1rS2tU3vW4xY5zA6bC7dE8fG9hI0jK1lM2nO3pQ4rS', '753 Walnut Street, Anytown', 4.40, '555-3579', 'Single parent needing assistance with household goods.', 'uploads/jessicaanderson.jpg', '2024-10-04 13:40:12', NULL, NULL, 'In need of furniture and kitchenware.'),
(11, 'Tshwetso Mokgatlhe', 'tshwetsomokgatlhe@example.com', '$2y$10$slTSmNnKB9qs63Fn7YBRq.FYk8xuzhXE7ApfnmTc92lHvWPKIFkvS', '40 Johan Steyn Avenue, Brooklyn, Durban', 0.00, '0123456789', 'Hello! I am a local farmer looking forward to working with you all!!!!!!!', 'uploads/66ffebbd3c117.png', '2024-10-04 15:21:01', NULL, 'Week days', ''),
(12, 'Emma Watson', 'emmawatson@example.com', '$2y$10$Lp3JGyNNmYGCUeKe1VaQ2eHhlMfSZ6M01sXYGE9AsY6eyZ.XT9JPi', '123 brocks avenue, Lishigan, Mont Everest', 0.00, '0123456789', 'Hello. I am a representative at the Pretoria Central Shelter for homeless women and children. Your support is greatly appreciated', 'uploads/671110f11d2b3.jpg', '2024-10-17 15:28:17', NULL, '', ''),
(13, 'BoyBoy', 'bb@example.com', '$2y$10$iQEODiw.HjLwXvKXR4fQOeqS1wgkOq1pbIlDyF10U8O3VE32/kxYW', '123 Mamsway', 0.00, '0123456789', 'Hi! Pleased to be apart of the community! Looking forward to working with all of you.\r\nAreyeng!', 'uploads/671a2c5d85c2a.jpg', '2024-10-24 13:15:41', NULL, 'All Week', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`bookmark_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `idx_giver_id` (`giver_id`),
  ADD KEY `idx_receiver_id_feedback` (`receiver_id`);

--
-- Indexes for table `food_listings`
--
ALTER TABLE `food_listings`
  ADD PRIMARY KEY (`listing_id`),
  ADD KEY `idx_listing_status` (`status`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`like_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`media_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `idx_sender_id` (`sender_id`),
  ADD KEY `idx_receiver_id` (`receiver_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`poll_id`);

--
-- Indexes for table `poll_options`
--
ALTER TABLE `poll_options`
  ADD PRIMARY KEY (`option_id`),
  ADD KEY `poll_id` (`poll_id`);

--
-- Indexes for table `poll_votes`
--
ALTER TABLE `poll_votes`
  ADD PRIMARY KEY (`vote_id`),
  ADD KEY `poll_id` (`poll_id`),
  ADD KEY `option_id` (`option_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post_tags`
--
ALTER TABLE `post_tags`
  ADD PRIMARY KEY (`post_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `listing_id` (`listing_id`),
  ADD KEY `idx_request_status` (`status`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tag_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookmarks`
--
ALTER TABLE `bookmarks`
  MODIFY `bookmark_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `food_listings`
--
ALTER TABLE `food_listings`
  MODIFY `listing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `media_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `polls`
--
ALTER TABLE `polls`
  MODIFY `poll_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `poll_options`
--
ALTER TABLE `poll_options`
  MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `poll_votes`
--
ALTER TABLE `poll_votes`
  MODIFY `vote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD CONSTRAINT `bookmarks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookmarks_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`giver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_3` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `food_listings`
--
ALTER TABLE `food_listings`
  ADD CONSTRAINT `fk_food_listings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `poll_options`
--
ALTER TABLE `poll_options`
  ADD CONSTRAINT `poll_options_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE;

--
-- Constraints for table `poll_votes`
--
ALTER TABLE `poll_votes`
  ADD CONSTRAINT `poll_votes_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `poll_votes_ibfk_2` FOREIGN KEY (`option_id`) REFERENCES `poll_options` (`option_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `poll_votes_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `post_tags`
--
ALTER TABLE `post_tags`
  ADD CONSTRAINT `post_tags_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE CASCADE;

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `fk_requests_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `food_listings` (`listing_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
