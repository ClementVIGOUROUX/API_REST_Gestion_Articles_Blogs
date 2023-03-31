-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2023 at 08:58 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_articlesblogs`
--
CREATE DATABASE IF NOT EXISTS `db_articlesblogs` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_articlesblogs`;

-- --------------------------------------------------------

--
-- Table structure for table `aimer`
--

CREATE TABLE `aimer` (
  `idArticle` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `TypeLike` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aimer`
--

INSERT INTO `aimer` (`idArticle`, `idUtilisateur`, `TypeLike`) VALUES
(1, 3, 1),
(2, 3, 0),
(2, 4, 0),
(3, 2, 0),
(4, 4, 1),
(5, 2, 1),
(7, 2, 1),
(7, 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

CREATE TABLE `article` (
  `idArticle` int(11) NOT NULL,
  `datePublication` date NOT NULL,
  `contenu` text NOT NULL,
  `idUtilisateur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `article`
--

INSERT INTO `article` (`idArticle`, `datePublication`, `contenu`, `idUtilisateur`) VALUES
(1, '2023-03-14', 'Article publié par Noguero Vincent le 14 mars 2023', 2),
(2, '2023-03-17', 'Article publié par Noguero Vincent le 17 mars 2023', 2),
(3, '2023-03-17', 'Article publié par Reynolds Ava le 17 mars 2023', 3),
(4, '2023-03-31', 'Article publié par Noguero Vincent le 31 mars 2023', 2),
(5, '2023-03-31', 'Article publié par Reynolds Ava le 31 mars 2023', 3),
(7, '2023-03-31', 'Article publié par Dubois Camille le 31 mars 2023', 4);

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `idUtilisateur` int(11) NOT NULL,
  `nomAuteur` varchar(50) NOT NULL,
  `userlogin` varchar(20) NOT NULL,
  `motDePasse` text NOT NULL,
  `userrole` enum('moderator','publisher') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`idUtilisateur`, `nomAuteur`, `userlogin`, `motDePasse`, `userrole`) VALUES
(1, 'Clement Vigouroux', 'VGC', '$2y$10$IulcCDY9sjlXn8qaZV2qveG7JqAXgf0T4MxbuTwIGgBZgUZmrsYwS', 'moderator'),
(2, 'Vincent Noguero', 'NGV', '$2y$10$KHMVO6t74VC/YEH6dqPcF.d7FJGxHmDFleBBF5zzeH5k2ZZwCJHnO', 'publisher'),
(3, 'Ava Reynolds', 'RNA', '$2y$10$OfwswF7aHAeAJQZfnuGFRuT7xn8GJ8gUnZCJZnqs5xYtjEmlXFczi', 'publisher'),
(4, 'Camille Dubois', 'DBC', '$2y$10$TpmGWymoethrkeUJtCUukOQaV45jjR/OaXBvRwfq7hRFtV131iyyS', 'publisher');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aimer`
--
ALTER TABLE `aimer`
  ADD PRIMARY KEY (`idArticle`,`idUtilisateur`),
  ADD KEY `fk_utilisateur_aimer` (`idUtilisateur`),
  ADD KEY `fk_article_aimer` (`idArticle`) USING BTREE;

--
-- Indexes for table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`idArticle`),
  ADD KEY `fk_auteur_article` (`idUtilisateur`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`idUtilisateur`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `article`
--
ALTER TABLE `article`
  MODIFY `idArticle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `idUtilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aimer`
--
ALTER TABLE `aimer`
  ADD CONSTRAINT `fk_article_aimer` FOREIGN KEY (`idArticle`) REFERENCES `article` (`idArticle`),
  ADD CONSTRAINT `fk_utilisateur_aimer` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`);

--
-- Constraints for table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `fk_auteur_article` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
