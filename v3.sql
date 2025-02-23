-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 23, 2025 at 12:19 AM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `calendar_db`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `championships`
--

CREATE TABLE `championships` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `rounds_count` int(11) NOT NULL,
  `color` varchar(7) NOT NULL,
  `text_color` varchar(7) NOT NULL,
  `weight` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `shortcut` varchar(10) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `championships`
--

INSERT INTO `championships` (`id`, `name`, `rounds_count`, `color`, `text_color`, `weight`, `status`, `shortcut`) VALUES
(1, 'Formuła 1', 24, '#FF0000', '#FFFFFF', 1, 1, 'F1'),
(2, 'RSMP', 7, '#CCCCCC', '#000000', 100, 1, 'RSMP'),
(3, 'HRSMP', 6, '#BBBBBB', '#000000', 101, 1, 'HRSMP'),
(4, 'GSMP', 6, '#AAAAAA', '#000000', 102, 1, 'GSMP'),
(5, 'WSMP', 5, '#999999', '#000000', 103, 1, 'WSMP'),
(6, 'RMPST', 7, '#888888', '#000000', 104, 1, 'RMPST'),
(7, 'RPPST', 7, '#777777', '#000000', 105, 1, 'RPPST'),
(8, 'MPRC', 7, '#666666', '#000000', 106, 1, 'MPRC'),
(9, 'MPAC', 7, '#555555', '#000000', 107, 1, 'MPAC'),
(10, 'DMP', 4, '#444444', '#FFFFFF', 108, 1, 'DMP'),
(11, 'MPWR', 5, '#333333', '#FFFFFF', 109, 1, 'MPWR'),
(12, 'WEC', 9, '#0000FF', '#FFFFFF', 2, 1, 'WEC'),
(13, 'WRC', 14, '#A52A2A', '#FFFFFF', 3, 1, 'WRC'),
(14, 'Formuła E', 16, '#FFC0CB', '#000000', 4, 1, 'F-E'),
(15, 'IndyCar', 17, '#800020', '#FFFFFF', 5, 1, 'IndyCar'),
(16, 'IMSA', 11, '#330066', '#FFFFFF', 7, 1, 'IMSA'),
(17, 'Asian Le Mans Series', 6, '#4B0082', '#FFFFFF', 110, 1, 'ALMS'),
(18, 'European Le Mans Series', 6, '#D8BFD8', '#000000', 111, 1, 'ELMS'),
(19, 'DTM', 8, '#ADD8E6', '#000000', 120, 1, 'DTM'),
(20, 'Formuła 2', 14, '#FFA500', '#000000', 130, 1, 'F2'),
(21, 'Formuła 3', 10, '#FFFF00', '#000000', 140, 1, 'F3'),
(22, 'MotoGP', 22, '#00008B', '#FFFFFF', 150, 1, 'MotoGP'),
(23, 'ERC', 8, '#FFFDD0', '#000000', 160, 1, 'ERC'),
(24, 'Nascar', 36, '#F5E1FF', '#000000', 170, 1, 'Nascar'),
(25, 'Eurocup', 0, '#008000', '#FFFFFF', 180, 1, 'Eurocup'),
(26, 'Eurocup-3', 8, '#008000', '#FFFFFF', 181, 1, 'Eurocup-3');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `drivers`
--

CREATE TABLE `drivers` (
  `id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `color` varchar(7) NOT NULL,
  `weight` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `driver_round`
--

CREATE TABLE `driver_round` (
  `driver_id` int(11) NOT NULL,
  `round_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rounds`
--

CREATE TABLE `rounds` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `season_id` int(11) NOT NULL,
  `championship_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `weight` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rounds`
--

INSERT INTO `rounds` (`id`, `name`, `season_id`, `championship_id`, `start_date`, `end_date`, `weight`, `status`) VALUES
(1, 'Australian Grand Prix', 1, 1, '2025-03-14', '2025-03-16', 1, 1),
(2, 'Chinese Grand Prix', 1, 1, '2025-03-21', '2025-03-23', 2, 1),
(3, 'Japanese Grand Prix', 1, 1, '2025-04-04', '2025-04-06', 3, 1),
(4, 'Bahrain Grand Prix', 1, 1, '2025-04-11', '2025-04-13', 4, 1),
(5, 'Saudi Arabian Grand Prix', 1, 1, '2025-04-18', '2025-04-20', 5, 1),
(6, 'Miami Grand Prix', 1, 1, '2025-05-02', '2025-05-04', 6, 1),
(7, 'Emilia Romagna Grand Prix', 1, 1, '2025-05-16', '2025-05-18', 7, 1),
(8, 'Monaco Grand Prix', 1, 1, '2025-05-23', '2025-05-25', 8, 1),
(9, 'Spanish Grand Prix', 1, 1, '2025-05-30', '2025-06-01', 9, 1),
(10, 'Canadian Grand Prix', 1, 1, '2025-06-13', '2025-06-15', 10, 1),
(11, 'Austrian Grand Prix', 1, 1, '2025-06-27', '2025-06-29', 11, 1),
(12, 'British Grand Prix', 1, 1, '2025-07-04', '2025-07-06', 12, 1),
(13, 'Belgian Grand Prix', 1, 1, '2025-07-25', '2025-07-27', 13, 1),
(14, 'Hungarian Grand Prix', 1, 1, '2025-08-01', '2025-08-03', 14, 1),
(15, 'Dutch Grand Prix', 1, 1, '2025-08-29', '2025-08-31', 15, 1),
(16, 'Italian Grand Prix', 1, 1, '2025-09-05', '2025-09-07', 16, 1),
(17, 'Azerbaijan Grand Prix', 1, 1, '2025-09-19', '2025-09-21', 17, 1),
(18, 'Singapore Grand Prix', 1, 1, '2025-10-03', '2025-10-05', 18, 1),
(19, 'United States Grand Prix', 1, 1, '2025-10-17', '2025-10-19', 19, 1),
(20, 'Mexico City Grand Prix', 1, 1, '2025-10-24', '2025-10-26', 20, 1),
(21, 'São Paulo Grand Prix', 1, 1, '2025-11-07', '2025-11-09', 21, 1),
(22, 'Las Vegas Grand Prix', 1, 1, '2025-11-21', '2025-11-23', 22, 1),
(23, 'Qatar Grand Prix', 1, 1, '2025-11-28', '2025-11-30', 23, 1),
(24, 'Abu Dhabi Grand Prix', 1, 1, '2025-12-05', '2025-12-07', 24, 1),
(25, 'Runda RSMP, Rajd Świdnicki', 1, 2, '2025-04-25', '2025-04-27', 100, 1),
(26, 'Runda RSMP, Rajd Nadwiślański', 1, 2, '2025-05-16', '2025-05-18', 101, 1),
(27, 'Runda RSMP, Rajd Polski', 1, 2, '2025-06-13', '2025-06-15', 102, 1),
(28, 'Runda RSMP, Rajd Małopolski', 1, 2, '2025-07-10', '2025-07-12', 103, 1),
(29, 'Runda RSMP, Rajd Rzeszowski', 1, 2, '2025-08-07', '2025-08-09', 104, 1),
(30, 'Runda RSMP, Rajd Śląska', 1, 2, '2025-09-19', '2025-09-21', 105, 1),
(31, 'Runda RSMP, Rajd Nyski', 1, 2, '2025-10-10', '2025-10-12', 106, 1),
(32, 'Runda HRSMP, Rajd Świdnicki', 1, 3, '2025-04-25', '2025-04-27', 107, 1),
(33, 'Runda HRSMP, Rajd Nadwiślański', 1, 3, '2025-05-16', '2025-05-18', 108, 1),
(34, 'Runda HRSMP, Rajd Małopolski', 1, 3, '2025-07-10', '2025-07-12', 109, 1),
(35, 'Runda HRSMP, Rajd Rzeszowski + FIA ERT', 1, 3, '2025-08-07', '2025-08-09', 110, 1),
(36, 'Runda HRSMP, Rajd Śląska + FIA ERT', 1, 3, '2025-09-19', '2025-09-21', 111, 1),
(37, 'Runda HRSMP, Rajd Nyski', 1, 3, '2025-10-10', '2025-10-12', 112, 1),
(38, '1-2. runda GSMP, Wyścig Górski Tor Kielce', 1, 4, '2025-04-30', '2025-05-02', 113, 1),
(39, '3-4. runda GSMP, Wyścig Górski Prządki', 1, 4, '2025-05-23', '2025-05-25', 114, 1),
(40, '5-6. runda GSMP, Wyścig Górski Magura Małastowska', 1, 4, '2025-06-27', '2025-06-29', 115, 1),
(41, '7-8. runda GSMP, Wyścig Górski Limanowa – Przełęcz Pod Ostrą', 1, 4, '2025-07-18', '2025-07-20', 116, 1),
(42, '9-10. runda GSMP, Grand Prix Sopot – Gdynia', 1, 4, '2025-08-15', '2025-08-17', 117, 1),
(43, '11-12. runda GSMP, Wyścig Górski Szczawne', 1, 4, '2025-09-05', '2025-09-07', 118, 1),
(44, '1. runda WSMP, Poznań', 1, 5, '2025-05-09', '2025-05-11', 119, 1),
(45, '2. runda WSMP, Ryga', 1, 5, '2025-06-06', '2025-06-08', 120, 1),
(46, '3. runda WSMP, Poznań', 1, 5, '2025-06-27', '2025-06-29', 121, 1),
(47, '4. runda WSMP, Poznań', 1, 5, '2025-07-25', '2025-07-27', 122, 1),
(48, '5. runda WSMP, Poznań', 1, 5, '2025-09-19', '2025-09-21', 123, 1),
(49, '1. runda RMPST, Baja Drawsko', 1, 6, '2025-04-05', '2025-04-06', 124, 1),
(50, '2. runda RMPST, Baja Borne Sulinowo', 1, 6, '2025-05-10', '2025-05-11', 125, 1),
(51, '3. runda RMPST, Baja Czarne', 1, 6, '2025-06-07', '2025-06-08', 126, 1),
(52, '4. runda RMPST, Rajd Polskie Safari', 1, 6, '2025-07-25', '2025-07-27', 127, 1),
(53, '5. runda RMPST, Baja Poland', 1, 6, '2025-08-28', '2025-08-31', 128, 1),
(54, '6. runda RMPST, Baja Carpathia', 1, 6, '2025-10-03', '2025-10-05', 129, 1),
(55, '7. runda RMPST, Rajd Niepodległości', 1, 6, '2025-11-08', '2025-11-09', 130, 1),
(56, '1. runda RPPST, Baja Drawsko', 1, 7, '2025-04-05', '2025-04-06', 131, 1),
(57, '2. runda RPPST, Baja Borne Sulinowo', 1, 7, '2025-05-10', '2025-05-11', 132, 1),
(58, '3. runda RPPST, Baja Czarne', 1, 7, '2025-06-07', '2025-06-08', 133, 1),
(59, '4. runda RPPST, Rajd Polskie Safari', 1, 7, '2025-07-25', '2025-07-27', 134, 1),
(60, '5. runda RPPST, Baja Poland', 1, 7, '2025-08-28', '2025-08-31', 135, 1),
(61, '6. runda RPPST, Baja Carpathia', 1, 7, '2025-10-03', '2025-10-05', 136, 1),
(62, '7. runda RPPST, Rajd Niepodległości', 1, 7, '2025-11-08', '2025-11-09', 137, 1),
(63, '1. runda MPRC, Poznań', 1, 8, '2025-03-29', '2025-03-30', 138, 1),
(64, '2. runda MPRC, Słomczyn', 1, 8, '2025-04-26', '2025-04-27', 139, 1),
(65, '3. runda MPRC, Słomczyn', 1, 8, '2025-05-24', '2025-05-25', 140, 1),
(66, '4. runda MPRC, Bauska (Łotwa)', 1, 8, '2025-07-12', '2025-07-14', 141, 1),
(67, '5. runda MPRC, Sedlcany (Czechy)', 1, 8, '2025-08-16', '2025-08-17', 142, 1),
(68, '6. runda MPRC, Słomczyn', 1, 8, '2025-09-13', '2025-09-14', 143, 1),
(69, '7. runda MPRC, Poznań', 1, 8, '2025-10-04', '2025-10-05', 144, 1),
(70, '1. runda MPAC, Poznań', 1, 9, '2025-03-29', '2025-03-30', 145, 1),
(71, '2. runda MPAC, Słomczyn', 1, 9, '2025-04-26', '2025-04-27', 146, 1),
(72, '3. runda MPAC, Słomczyn', 1, 9, '2025-05-24', '2025-05-25', 147, 1),
(73, '4. runda MPAC, Bauska (Łotwa)', 1, 9, '2025-07-12', '2025-07-13', 148, 1),
(74, '5. runda MPAC, Vilkyciai (Litwa)', 1, 9, '2025-08-09', '2025-08-10', 149, 1),
(75, '6. runda MPAC, Słomczyn', 1, 9, '2025-09-13', '2025-09-14', 150, 1),
(76, '7. runda MPAC, Poznań', 1, 9, '2025-10-04', '2025-10-05', 151, 1),
(77, '1. runda DMP, Słomczyn', 1, 10, '2025-06-20', '2025-06-22', 152, 1),
(78, '2. runda DMP, Katowice', 1, 10, '2025-07-18', '2025-07-20', 153, 1),
(79, '3. runda DMP, Kielce', 1, 10, '2025-08-29', '2025-08-31', 154, 1),
(80, '4. runda DMP, Poznań', 1, 10, '2025-09-26', '2025-09-28', 155, 1),
(81, '1. runda MPWR, Piotrków Tryb.', 1, 11, '2025-04-27', '2025-04-27', 156, 1),
(82, '2. runda MPWR, Olsztyn', 1, 11, '2025-05-25', '2025-05-25', 157, 1),
(83, '3. runda MPWR, Piła', 1, 11, '2025-06-21', '2025-06-21', 158, 1),
(84, '4. runda MPWR, Mielec', 1, 11, '2025-07-20', '2025-07-20', 159, 1),
(85, '5. runda MPWR, Piła', 1, 11, '2025-08-23', '2025-08-23', 160, 1),
(86, 'Prologue, Losail International Circuit', 1, 12, '2025-02-21', '2025-02-22', 2, 1),
(87, 'Qatar 1812 km', 1, 12, '2025-02-26', '2025-02-28', 3, 1),
(88, '6 Hours of Imola', 1, 12, '2025-04-18', '2025-04-20', 4, 1),
(89, '6 Hours of Spa-Francorchamps', 1, 12, '2025-05-08', '2025-05-10', 5, 1),
(90, '24 Hours of Le Mans', 1, 12, '2025-06-13', '2025-06-15', 6, 1),
(91, '6 Hours of São Paulo', 1, 12, '2025-07-11', '2025-07-13', 7, 1),
(92, 'Lone Star Le Mans', 1, 12, '2025-09-05', '2025-09-07', 8, 1),
(93, '6 Hours of Fuji', 1, 12, '2025-09-26', '2025-09-28', 9, 1),
(94, '8 Hours of Bahrain', 1, 12, '2025-11-06', '2025-11-08', 10, 1),
(95, 'Monaco Rallye Automobile Monte Carlo', 1, 13, '2025-01-23', '2025-01-26', 3, 1),
(96, 'Sweden Rally Sweden', 1, 13, '2025-02-13', '2025-02-16', 4, 1),
(97, 'Kenya Safari Rally', 1, 13, '2025-03-20', '2025-03-23', 5, 1),
(98, 'Spain Rally Islas Canarias', 1, 13, '2025-04-24', '2025-04-27', 6, 1),
(99, 'Portugal Rally de Portugal', 1, 13, '2025-05-15', '2025-05-18', 7, 1),
(100, 'Italy Rally Italia Sardegna', 1, 13, '2025-06-05', '2025-06-08', 8, 1),
(101, 'Greece Acropolis Rally', 1, 13, '2025-06-26', '2025-06-29', 9, 1),
(102, 'Estonia Rally Estonia', 1, 13, '2025-07-17', '2025-07-20', 10, 1),
(103, 'Finland Rally Finland', 1, 13, '2025-07-31', '2025-08-03', 11, 1),
(104, 'Paraguay Rally del Paraguay', 1, 13, '2025-08-28', '2025-08-31', 12, 1),
(105, 'Chile Rally Chile', 1, 13, '2025-09-11', '2025-09-14', 13, 1),
(106, 'Europe Central European Rally', 1, 13, '2025-10-16', '2025-10-19', 14, 1),
(107, 'Japan Rally Japan', 1, 13, '2025-11-06', '2025-11-09', 15, 1),
(108, 'Saudi Arabia Rally', 1, 13, '2025-11-27', '2025-11-30', 16, 1),
(109, 'Sao Paulo ePrix', 1, 14, '2025-12-07', '2025-12-07', 4, 1),
(110, 'Mexico City ePrix', 1, 14, '2025-01-11', '2025-01-11', 5, 1),
(111, 'Jeddah ePrix', 1, 14, '2025-02-14', '2025-02-14', 6, 1),
(112, 'E-Prix Round 4', 1, 14, '2025-02-15', '2025-02-15', 7, 1),
(113, 'Miami ePrix', 1, 14, '2025-04-12', '2025-04-12', 8, 1),
(114, 'Monaco ePrix', 1, 14, '2025-05-03', '2025-05-03', 9, 1),
(115, 'E-Prix Round 7', 1, 14, '2025-05-04', '2025-05-04', 10, 1),
(116, 'Tokyo ePrix', 1, 14, '2025-05-17', '2025-05-17', 11, 1),
(117, 'E-Prix Round 9', 1, 14, '2025-05-18', '2025-05-18', 12, 1),
(118, 'Shanghai ePrix', 1, 14, '2025-05-31', '2025-05-31', 13, 1),
(119, 'E-Prix Round 11', 1, 14, '2025-06-01', '2025-06-01', 14, 1),
(120, 'Jakarta ePrix', 1, 14, '2025-06-21', '2025-06-21', 15, 1),
(121, 'Berlin ePrix', 1, 14, '2025-07-12', '2025-07-12', 16, 1),
(122, 'E-Prix Round 14', 1, 14, '2025-07-13', '2025-07-13', 17, 1),
(123, 'London ePrix', 1, 14, '2025-07-26', '2025-07-26', 18, 1),
(124, 'E-Prix Round 16', 1, 14, '2025-07-27', '2025-07-27', 19, 1),
(125, 'Firestone Grand Prix of St. Petersburg', 1, 15, '2025-02-28', '2025-03-02', 5, 1),
(126, 'The Thermal Club IndyCar Grand Prix', 1, 15, '2025-03-21', '2025-03-23', 6, 1),
(127, 'Acura Grand Prix of Long Beach', 1, 15, '2025-04-11', '2025-04-13', 7, 1),
(128, 'Children\'s of Alabama Indy Grand Prix', 1, 15, '2025-05-02', '2025-05-04', 8, 1),
(129, 'Sonsio Grand Prix', 1, 15, '2025-05-08', '2025-05-10', 9, 1),
(130, '109th Running of the Indianapolis 500', 1, 15, '2025-05-23', '2025-05-25', 10, 1),
(131, 'Chevrolet Detroit Grand Prix', 1, 15, '2025-05-30', '2025-06-01', 11, 1),
(132, 'Bommarito Automotive Group 500', 1, 15, '2025-06-13', '2025-06-15', 12, 1),
(133, 'XPEL Grand Prix at Road America', 1, 15, '2025-06-20', '2025-06-22', 13, 1),
(134, 'Honda Indy 200 at Mid-Ohio', 1, 15, '2025-07-04', '2025-07-06', 14, 1),
(135, 'Hy-Vee Homefront 250', 1, 15, '2025-07-11', '2025-07-12', 15, 1),
(136, 'Hy-Vee One Step 250', 1, 15, '2025-07-12', '2025-07-13', 16, 1),
(137, 'Ontario Honda Dealers Indy Toronto', 1, 15, '2025-07-18', '2025-07-20', 17, 1),
(138, 'Firestone Grand Prix of Monterey', 1, 15, '2025-07-25', '2025-07-27', 18, 1),
(139, 'BitNile.com Grand Prix of Portland', 1, 15, '2025-08-08', '2025-08-10', 19, 1),
(140, 'Hy-Vee Milwaukee Mile 250', 1, 15, '2025-08-22', '2025-08-24', 20, 1),
(141, 'Big Machine Music City Grand Prix', 1, 15, '2025-08-29', '2025-08-31', 21, 1),
(142, 'Rolex 24 at Daytona', 1, 16, '2025-01-24', '2025-01-26', 1, 1),
(143, 'Mobil 1 Twelve Hours of Sebring', 1, 16, '2025-03-13', '2025-03-15', 2, 1),
(144, 'Acura Grand Prix of Long Beach', 1, 16, '2025-04-10', '2025-04-12', 3, 1),
(145, 'Motul Course de Monterey', 1, 16, '2025-05-09', '2025-05-11', 4, 1),
(146, 'Chevrolet Detroit Sports Car Classic', 1, 16, '2025-05-29', '2025-05-31', 5, 1),
(147, 'Sahlen\'s Six Hours of The Glen', 1, 16, '2025-06-20', '2025-06-22', 6, 1),
(148, 'Chevrolet Grand Prix', 1, 16, '2025-07-11', '2025-07-13', 7, 1),
(149, 'IMSA Sportscar Weekend', 1, 16, '2025-08-01', '2025-08-03', 8, 1),
(150, 'Michelin GT Challenge at VIR', 1, 16, '2025-08-22', '2025-08-24', 9, 1),
(151, 'Tirerack.com Battle on the Bricks', 1, 16, '2025-09-19', '2025-09-21', 10, 1),
(152, 'Motul Petit Le Mans', 1, 16, '2025-10-09', '2025-10-11', 11, 1),
(153, 'Prologue - ELMS', 1, 18, '2025-04-01', '2025-04-01', 1, 1),
(154, '4 Hours of Barcelona', 1, 18, '2025-04-04', '2025-04-06', 2, 1),
(155, '4 Hours of Le Castellet', 1, 18, '2025-05-02', '2025-05-04', 3, 1),
(156, '4 Hours of Imola', 1, 18, '2025-07-04', '2025-07-06', 4, 1),
(157, '4 Hours of Spa-Francorchamps', 1, 18, '2025-08-22', '2025-08-24', 5, 1),
(158, '4 Hours of Silverstone', 1, 18, '2025-09-12', '2025-09-14', 6, 1),
(159, '4 Hours of Portimão', 1, 18, '2025-10-16', '2025-10-18', 7, 1),
(160, 'Germany Motorsport Arena Oschersleben', 1, 19, '2025-04-26', '2025-04-27', 1, 1),
(161, 'Germany Lausitzring', 1, 19, '2025-05-24', '2025-05-25', 2, 1),
(162, 'Netherlands Circuit Zandvoort', 1, 19, '2025-06-07', '2025-06-08', 3, 1),
(163, 'Germany Norisring', 1, 19, '2025-07-05', '2025-07-06', 4, 1),
(164, 'Germany Nürburgring', 1, 19, '2025-08-09', '2025-08-10', 5, 1),
(165, 'Germany Sachsenring', 1, 19, '2025-08-23', '2025-08-24', 6, 1),
(166, 'Austria Red Bull Ring', 1, 19, '2025-09-13', '2025-09-14', 7, 1),
(167, 'Germany Hockenheimring', 1, 19, '2025-10-04', '2025-10-05', 8, 1),
(168, 'Round 1 - Albert Park', 1, 20, '2025-03-14', '2025-03-16', 1, 1),
(169, 'Round 2 - Bahrain', 1, 20, '2025-04-11', '2025-04-13', 2, 1),
(170, 'Round 3 - Jeddah', 1, 20, '2025-04-18', '2025-04-20', 3, 1),
(171, 'Round 4 - Imola', 1, 20, '2025-05-16', '2025-05-18', 4, 1),
(172, 'Round 5 - Monaco', 1, 20, '2025-05-23', '2025-05-25', 5, 1),
(173, 'Round 6 - Barcelona', 1, 20, '2025-05-30', '2025-06-01', 6, 1),
(174, 'Round 7 - Red Bull Ring', 1, 20, '2025-06-27', '2025-06-29', 7, 1),
(175, 'Round 8 - Silverstone', 1, 20, '2025-07-04', '2025-07-06', 8, 1),
(176, 'Round 9 - Spa-Francorchamps', 1, 20, '2025-07-25', '2025-07-27', 9, 1),
(177, 'Round 10 - Hungaroring', 1, 20, '2025-08-01', '2025-08-03', 10, 1),
(178, 'Round 11 - Monza', 1, 20, '2025-09-05', '2025-09-07', 11, 1),
(179, 'Round 12 - Baku', 1, 20, '2025-09-19', '2025-09-21', 12, 1),
(180, 'Round 13 - Qatar', 1, 20, '2025-11-28', '2025-11-30', 13, 1),
(181, 'Round 14 - Abu Dhabi', 1, 20, '2025-12-05', '2025-12-07', 14, 1),
(182, 'Round 1 - Albert Park', 1, 21, '2025-03-14', '2025-03-16', 1, 1),
(183, 'Round 2 - Bahrain', 1, 21, '2025-04-11', '2025-04-13', 2, 1),
(184, 'Round 3 - Imola', 1, 21, '2025-05-16', '2025-05-18', 3, 1),
(185, 'Round 4 - Monaco', 1, 21, '2025-05-23', '2025-05-25', 4, 1),
(186, 'Round 5 - Barcelona', 1, 21, '2025-05-30', '2025-06-01', 5, 1),
(187, 'Round 6 - Red Bull Ring', 1, 21, '2025-06-27', '2025-06-29', 6, 1),
(188, 'Round 7 - Silverstone', 1, 21, '2025-07-04', '2025-07-06', 7, 1),
(189, 'Round 8 - Spa-Francorchamps', 1, 21, '2025-07-25', '2025-07-27', 8, 1),
(190, 'Round 9 - Hungaroring', 1, 21, '2025-08-01', '2025-08-03', 9, 1),
(191, 'Round 10 - Monza', 1, 21, '2025-09-05', '2025-09-07', 10, 1),
(192, 'Thailand Motorcycle GP', 1, 22, '2025-02-28', '2025-03-02', 1, 1),
(193, 'Argentina Motorcycle GP', 1, 22, '2025-03-14', '2025-03-16', 2, 1),
(194, 'Americas Motorcycle GP', 1, 22, '2025-03-28', '2025-03-30', 3, 1),
(195, 'Qatar Motorcycle GP', 1, 22, '2025-04-11', '2025-04-13', 4, 1),
(196, 'Spain Motorcycle GP', 1, 22, '2025-04-25', '2025-04-27', 5, 1),
(197, 'France Motorcycle GP', 1, 22, '2025-05-09', '2025-05-11', 6, 1),
(198, 'UK Motorcycle GP', 1, 22, '2025-05-23', '2025-05-25', 7, 1),
(199, 'Aragon Motorcycle GP', 1, 22, '2025-06-06', '2025-06-08', 8, 1),
(200, 'Italy Motorcycle GP', 1, 22, '2025-06-20', '2025-06-22', 9, 1),
(201, 'Netherlands TT', 1, 22, '2025-06-27', '2025-06-29', 10, 1),
(202, 'Germany Motorcycle GP', 1, 22, '2025-07-11', '2025-07-13', 11, 1),
(203, 'Czech Motorcycle GP', 1, 22, '2025-07-18', '2025-07-20', 12, 1),
(204, 'Austria Motorcycle GP', 1, 22, '2025-08-15', '2025-08-17', 13, 1),
(205, 'Hungary Motorcycle GP', 1, 22, '2025-08-22', '2025-08-24', 14, 1),
(206, 'Catalonia Motorcycle GP', 1, 22, '2025-09-05', '2025-09-07', 15, 1),
(207, 'San Marino Motorcycle GP', 1, 22, '2025-09-12', '2025-09-14', 16, 1),
(208, 'Japan Motorcycle GP', 1, 22, '2025-09-26', '2025-09-28', 17, 1),
(209, 'Indonesia Motorcycle GP', 1, 22, '2025-10-03', '2025-10-05', 18, 1),
(210, 'Australia Motorcycle GP', 1, 22, '2025-10-17', '2025-10-19', 19, 1),
(211, 'Malaysia Motorcycle GP', 1, 22, '2025-10-24', '2025-10-26', 20, 1),
(212, 'Portugal Motorcycle GP', 1, 22, '2025-11-07', '2025-11-09', 21, 1),
(213, 'Valencian Motorcycle GP', 1, 22, '2025-11-14', '2025-11-16', 22, 1),
(214, 'Spain Rallye Sierra Morena', 1, 23, '2025-04-04', '2025-04-06', 1, 1),
(215, 'Hungary Rally Hungary', 1, 23, '2025-05-09', '2025-05-11', 2, 1),
(216, 'Sweden Royal Rally of Scandinavia', 1, 23, '2025-05-29', '2025-05-31', 3, 1),
(217, 'Poland Rally Poland', 1, 23, '2025-06-13', '2025-06-15', 4, 1),
(218, 'Italy Rally di Roma Capitale', 1, 23, '2025-07-04', '2025-07-06', 5, 1),
(219, 'Barum Czech Rally Zlín', 1, 23, '2025-08-15', '2025-08-17', 6, 1),
(220, 'UK Rali Ceredigion', 1, 23, '2025-09-05', '2025-09-07', 7, 1),
(221, 'Croatia Rally', 1, 23, '2025-10-03', '2025-10-05', 8, 1),
(222, 'Cook Out Clash at Bowman Gray Stadium', 1, 24, '2025-02-02', '2025-02-02', 1, 1),
(223, 'The Duel at Daytona', 1, 24, '2025-02-13', '2025-02-13', 2, 1),
(224, 'Daytona 500', 1, 24, '2025-02-16', '2025-02-16', 3, 1),
(225, 'Ambetter Health 400', 1, 24, '2025-02-23', '2025-02-23', 4, 1),
(226, 'EchoPark Automotive Grand Prix', 1, 24, '2025-03-02', '2025-03-02', 5, 1),
(227, 'Shriners Children\'s 500', 1, 24, '2025-03-09', '2025-03-09', 6, 1),
(228, 'Pennzoil 400', 1, 24, '2025-03-16', '2025-03-16', 7, 1),
(229, 'Straight Talk Wireless 400', 1, 24, '2025-03-23', '2025-03-23', 8, 1),
(230, 'Cook Out 400', 1, 24, '2025-03-30', '2025-03-30', 9, 1),
(231, 'Goodyear 400', 1, 24, '2025-04-06', '2025-04-06', 10, 1),
(232, 'Food City 500', 1, 24, '2025-04-13', '2025-04-13', 11, 1),
(233, 'Jack Link\'s 500', 1, 24, '2025-04-27', '2025-04-27', 12, 1),
(234, 'WÜRTH 400 Presented by LIQUI MOLY', 1, 24, '2025-05-04', '2025-05-04', 13, 1),
(235, 'AdventHealth 400', 1, 24, '2025-05-11', '2025-05-11', 14, 1),
(236, 'NASCAR All Star Open', 1, 24, '2025-05-18', '2025-05-18', 15, 1),
(237, 'NASCAR All-Star Race', 1, 24, '2025-05-18', '2025-05-18', 16, 1),
(238, 'Coca-Cola 600', 1, 24, '2025-05-25', '2025-05-25', 17, 1),
(239, 'Ally 400', 1, 24, '2025-06-01', '2025-06-01', 18, 1),
(240, 'FireKeepers Casino 400', 1, 24, '2025-06-08', '2025-06-08', 19, 1),
(241, 'Nascar Cup Series at Mexico City', 1, 24, '2025-06-15', '2025-06-15', 20, 1),
(242, 'The Great American Getaway 400', 1, 24, '2025-06-22', '2025-06-22', 21, 1),
(243, 'Quaker State 400', 1, 24, '2025-06-28', '2025-06-28', 22, 1),
(244, 'Grant Park 165', 1, 24, '2025-07-06', '2025-07-06', 23, 1),
(245, 'Toyota/Save Mart 350', 1, 24, '2025-07-13', '2025-07-13', 24, 1),
(246, 'Autotrader EchoPark Automotive 400', 1, 24, '2025-07-20', '2025-07-20', 25, 1),
(247, 'Brickyard 400 presented by PPG', 1, 24, '2025-07-27', '2025-07-27', 26, 1),
(248, 'Iowa Corn 350 powered by Ethanol', 1, 24, '2025-08-03', '2025-08-03', 27, 1),
(249, 'Go Bowling at The Glen', 1, 24, '2025-08-10', '2025-08-10', 28, 1),
(250, 'Cook Out 400', 1, 24, '2025-08-16', '2025-08-16', 29, 1),
(251, 'Coke Zero Sugar 400', 1, 24, '2025-08-23', '2025-08-23', 30, 1),
(252, 'Cook Out Southern 500', 1, 24, '2025-08-31', '2025-08-31', 31, 1),
(253, 'Enjoy Illinois 300', 1, 24, '2025-09-07', '2025-09-07', 32, 1),
(254, 'Bass Pro Shops Night Race', 1, 24, '2025-09-13', '2025-09-13', 33, 1),
(255, 'USA Today 301', 1, 24, '2025-09-21', '2025-09-21', 34, 1),
(256, 'Hollywood Casino 400', 1, 24, '2025-09-28', '2025-09-28', 35, 1),
(257, 'Bank of America Roval 400', 1, 24, '2025-10-05', '2025-10-05', 36, 1),
(258, 'South Point 400', 1, 24, '2025-10-12', '2025-10-12', 37, 1),
(259, 'YellaWood 500', 1, 24, '2025-10-19', '2025-10-19', 38, 1),
(260, 'Xfinity 500', 1, 24, '2025-10-26', '2025-10-26', 39, 1),
(261, 'Nascar Cup Series Championship Race', 1, 24, '2025-11-02', '2025-11-02', 40, 1),
(262, 'Round 1: Red Bull Ring (Austria)', 1, 26, '2025-05-16', '2025-05-18', 1, 1),
(263, 'Round 2: Autodromo do Algarve (Portugal)', 1, 26, '2025-06-06', '2025-06-08', 2, 1),
(264, 'Round 3: Circuit Paul Ricard (France)', 1, 26, '2025-06-20', '2025-06-22', 3, 1),
(265, 'Round 4: Autodromo Nazionale di Monza (Italy)', 1, 26, '2025-07-05', '2025-07-06', 4, 1),
(266, 'Round 5: TT Circuit Assen (The Netherlands)', 1, 26, '2025-08-08', '2025-08-10', 5, 1),
(267, 'Round 6: Circuit de Spa-Francorchamps (Belgium)', 1, 26, '2025-09-04', '2025-09-05', 6, 1),
(268, 'Round 7: Circuito de Jerez – Ángel Nieto (Spain)', 1, 26, '2025-09-19', '2025-09-21', 7, 1),
(269, 'Round 8: Circuit de Barcelona-Catalunya (Spain)', 1, 26, '2025-11-14', '2025-11-16', 8, 1),
(270, 'Round 1: Red Bull Ring (Austria)', 1, 25, '2025-05-16', '2025-05-18', 1, 1),
(271, 'Round 2: Autodromo do Algarve (Portugal)', 1, 25, '2025-06-06', '2025-06-08', 2, 1),
(272, 'Round 3: Circuit Paul Ricard (France)', 1, 25, '2025-06-20', '2025-06-22', 3, 1),
(273, 'Round 4: Autodromo Nazionale di Monza (Italy)', 1, 25, '2025-07-05', '2025-07-06', 4, 1),
(274, 'Round 5: TT Circuit Assen (The Netherlands)', 1, 25, '2025-08-08', '2025-08-10', 5, 1),
(275, 'Round 6: Circuit de Spa-Francorchamps (Belgium)', 1, 25, '2025-09-04', '2025-09-05', 6, 1),
(276, 'Round 7: Circuito de Jerez – Ángel Nieto (Spain)', 1, 25, '2025-09-19', '2025-09-21', 7, 1),
(277, 'Round 8: Circuit de Barcelona-Catalunya (Spain)', 1, 25, '2025-11-14', '2025-11-16', 8, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `seasons`
--

CREATE TABLE `seasons` (
  `id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seasons`
--

INSERT INTO `seasons` (`id`, `year`, `name`) VALUES
(1, 2025, 'Sezon 2025');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `series`
--

CREATE TABLE `series` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `color` varchar(7) NOT NULL,
  `text_color` varchar(7) NOT NULL,
  `weight` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `series`
--

INSERT INTO `series` (`id`, `name`, `color`, `text_color`, `weight`, `status`) VALUES
(1, 'Formuła 1', '#FF0000', '#FFFFFF', 1, 1),
(2, 'Mistrzostwa polski', '#808080', '#FFFFFF', 50, 1),
(3, 'WEC', '#0000FF', '#FFFFFF', 2, 1),
(4, 'WRC', '#A52A2A', '#FFFFFF', 3, 1),
(5, 'Formuła E', '#FFC0CB', '#000000', 4, 1),
(6, 'IndyCar', '#800020', '#FFFFFF', 5, 1),
(7, 'IMSA', '#330066', '#FFFFFF', 7, 1),
(8, 'Le Mans Series', '#663399', '#FFFFFF', 8, 1),
(9, 'DTM', '#ADD8E6', '#000000', 9, 1),
(10, 'Formuła 2', '#FFA500', '#000000', 10, 1),
(11, 'Formuła 3', '#FFFF00', '#000000', 11, 1),
(12, 'MotoGP', '#00008B', '#FFFFFF', 12, 1),
(13, 'ERC', '#FFFDD0', '#000000', 13, 1),
(14, 'Nascar', '#F5E1FF', '#000000', 14, 1),
(15, 'Eurocup', '#008000', '#FFFFFF', 15, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `series_championship`
--

CREATE TABLE `series_championship` (
  `series_id` int(11) NOT NULL,
  `championship_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `series_championship`
--

INSERT INTO `series_championship` (`series_id`, `championship_id`) VALUES
(1, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 10),
(2, 11),
(3, 12),
(4, 13),
(5, 14),
(6, 15),
(7, 16),
(8, 17),
(8, 18),
(9, 19),
(10, 20),
(11, 21),
(12, 22),
(13, 23),
(14, 24),
(15, 25);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `teams`
--

CREATE TABLE `teams` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `color` varchar(7) NOT NULL,
  `weight` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `team_round`
--

CREATE TABLE `team_round` (
  `team_id` int(11) NOT NULL,
  `round_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `championships`
--
ALTER TABLE `championships`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `driver_round`
--
ALTER TABLE `driver_round`
  ADD PRIMARY KEY (`driver_id`,`round_id`),
  ADD KEY `round_id` (`round_id`);

--
-- Indeksy dla tabeli `rounds`
--
ALTER TABLE `rounds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `season_id` (`season_id`),
  ADD KEY `championship_id` (`championship_id`);

--
-- Indeksy dla tabeli `seasons`
--
ALTER TABLE `seasons`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `series`
--
ALTER TABLE `series`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `series_championship`
--
ALTER TABLE `series_championship`
  ADD PRIMARY KEY (`series_id`,`championship_id`),
  ADD KEY `championship_id` (`championship_id`);

--
-- Indeksy dla tabeli `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `team_round`
--
ALTER TABLE `team_round`
  ADD PRIMARY KEY (`team_id`,`round_id`),
  ADD KEY `round_id` (`round_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `championships`
--
ALTER TABLE `championships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rounds`
--
ALTER TABLE `rounds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=278;

--
-- AUTO_INCREMENT for table `seasons`
--
ALTER TABLE `seasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `series`
--
ALTER TABLE `series`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `driver_round`
--
ALTER TABLE `driver_round`
  ADD CONSTRAINT `driver_round_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `driver_round_ibfk_2` FOREIGN KEY (`round_id`) REFERENCES `rounds` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rounds`
--
ALTER TABLE `rounds`
  ADD CONSTRAINT `rounds_ibfk_1` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rounds_ibfk_2` FOREIGN KEY (`championship_id`) REFERENCES `championships` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `series_championship`
--
ALTER TABLE `series_championship`
  ADD CONSTRAINT `series_championship_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `series_championship_ibfk_2` FOREIGN KEY (`championship_id`) REFERENCES `championships` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `team_round`
--
ALTER TABLE `team_round`
  ADD CONSTRAINT `team_round_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `team_round_ibfk_2` FOREIGN KEY (`round_id`) REFERENCES `rounds` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
