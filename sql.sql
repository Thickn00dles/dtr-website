-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 25, 2024 at 04:09 PM
-- Server version: 10.11.8-MariaDB-cll-lve
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u835965540_dictdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `dict_employee`
--

CREATE TABLE `dict_employee` (
  `EmpID` int(4) NOT NULL,
  `FirstName` varchar(15) NOT NULL,
  `MiddleName` varchar(15) NOT NULL,
  `LastName` varchar(15) NOT NULL,
  `Sex` varchar(1) NOT NULL,
  `Email` varchar(30) NOT NULL,
  `Contact` varchar(11) NOT NULL,
  `Password` varchar(50) NOT NULL,
  `Position` varchar(20) NOT NULL,
  `Role` varchar(12) NOT NULL,
  `Status` tinyint(1) NOT NULL,
  `Sig_ID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_records`
--

CREATE TABLE `employee_records` (
  `R_ID` int(4) NOT NULL,
  `EmpID` varchar(4) NOT NULL,
  `Date` varchar(10) NOT NULL,
  `TimeInAM` varchar(6) NOT NULL,
  `TimeOutAM` varchar(6) NOT NULL,
  `TimeInPM` varchar(6) NOT NULL,
  `TimeOutPM` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dict_employee`
--
ALTER TABLE `dict_employee`
  ADD PRIMARY KEY (`EmpID`);

--
-- Indexes for table `employee_records`
--
ALTER TABLE `employee_records`
  ADD PRIMARY KEY (`R_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dict_employee`
--
ALTER TABLE `dict_employee`
  MODIFY `EmpID` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1155;

--
-- AUTO_INCREMENT for table `employee_records`
--
ALTER TABLE `employee_records`
  MODIFY `R_ID` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
