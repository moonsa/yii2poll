-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 21, 2017 at 10:30 PM
-- Server version: 5.6.31-0ubuntu0.14.04.2
-- PHP Version: 5.6.23-1+deprecated+dontuse+deb.sury.org~trusty+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yii2basic`
--

-- --------------------------------------------------------

--
-- Table structure for table `poll_question`
--

CREATE TABLE `poll_question` (
  `id` int(11) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `poll_name` varchar(128) NOT NULL,
  `answer_options` text NOT NULL,
  `is_default` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `poll_response`
--

CREATE TABLE `poll_response` (
  `id` int(11) NOT NULL,
  `poll_id` int(10) UNSIGNED NOT NULL,
  `answers` varchar(128) CHARACTER SET utf8mb4 NOT NULL,
  `value` int(11) NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `poll_user`
--

CREATE TABLE `poll_user` (
  `id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `user_ip` varchar(255) NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `poll_question`
--
ALTER TABLE `poll_question`
  ADD PRIMARY KEY (`id`),
  ADD KEY `poll_name` (`poll_name`);

--
-- Indexes for table `poll_response`
--
ALTER TABLE `poll_response`
  ADD PRIMARY KEY (`id`),
  ADD KEY `poll_name` (`poll_id`);

--
-- Indexes for table `poll_user`
--
ALTER TABLE `poll_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `poll_id` (`poll_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `poll_question`
--
ALTER TABLE `poll_question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `poll_response`
--
ALTER TABLE `poll_response`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `poll_user`
--
ALTER TABLE `poll_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
