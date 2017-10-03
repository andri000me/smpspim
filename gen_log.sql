-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 08, 2017 at 04:20 
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simapes`
--

-- --------------------------------------------------------

--
-- Table structure for table `gen_log`
--

CREATE TABLE `gen_log` (
  `ID_LOG` bigint(20) NOT NULL,
  `FROM_LOG` enum('HOOK','LIBRARY') NOT NULL,
  `IP_LOG` varchar(30) NOT NULL,
  `PATH_LOG` varchar(250) DEFAULT NULL,
  `CONTROLLER_LOG` varchar(100) NOT NULL,
  `METHOD_LOG` varchar(100) NOT NULL,
  `SESSION_LOG` text NOT NULL,
  `QUERY_LOG` text NOT NULL,
  `EXECUTION_TIME_LOG` double NOT NULL,
  `DATE_LOG` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gen_log`
--
ALTER TABLE `gen_log`
  ADD PRIMARY KEY (`ID_LOG`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gen_log`
--
ALTER TABLE `gen_log`
  MODIFY `ID_LOG` bigint(20) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
