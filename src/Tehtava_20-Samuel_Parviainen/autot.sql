-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Isäntä: db
-- Luontiaika: 05.10.2026 klo 08:45
-- Palvelimen versio: 8.0.46
-- PHP-versio 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Tietokanta: `autot`
--

-- --------------------------------------------------------

--
-- Rakenne taululle `autot`
--

CREATE TABLE `autot` (
  `ID` int NOT NULL,
  `merkki` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tyyppi` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vuosimalli` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vedos taulusta `autot`
--

INSERT INTO `autot` (`ID`, `merkki`, `tyyppi`, `vuosimalli`) VALUES
(1, 'Toyota', 'Corolla', 2015),
(3, 'Tesla', 'Model 3', 2025),
(9, 'Tojora', 'mmmm', 1902);

--
-- Indexes for dumped tables
--

--
-- Indeksit taulukolle `autot`
--
ALTER TABLE `autot`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `autot`
--
ALTER TABLE `autot`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
