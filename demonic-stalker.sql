-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mar. 29 juin 2021 à 18:12
-- Version du serveur :  10.2.39-MariaDB
-- Version de PHP : 7.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `demonicstalker`
--

-- --------------------------------------------------------

--
-- Structure de la table `packets`
--

CREATE TABLE `packets` (
  `packetId` varchar(60) NOT NULL,
  `packetName` varchar(60) DEFAULT NULL,
  `messageId` varchar(60) DEFAULT NULL,
  `userId` varchar(60) NOT NULL,
  `lastState` text DEFAULT NULL,
  `lastEvent` int(11) DEFAULT NULL,
  `deliveryService` enum('LaPoste','','','') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `packets`
--
ALTER TABLE `packets`
  ADD PRIMARY KEY (`packetId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
