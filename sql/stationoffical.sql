/*
 Navicat Premium Data Transfer

 Source Server         : 163.17.136.150
 Source Server Type    : MySQL
 Source Server Version : 50524
 Source Host           : 163.17.136.150:3306
 Source Schema         : 1062xml

 Target Server Type    : MySQL
 Target Server Version : 50524
 File Encoding         : 65001

 Date: 10/05/2018 20:23:25
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for stationoffical
-- ----------------------------
DROP TABLE IF EXISTS `stationoffical`;
CREATE TABLE `stationoffical` (
  `ID` varchar(5) NOT NULL DEFAULT '',
  `TimeID` varchar(5) DEFAULT NULL,
  `StationName` varchar(10) DEFAULT NULL,
  `lat` varchar(20) DEFAULT NULL,
  `lng` varchar(20) DEFAULT NULL,
  `address` varchar(40) DEFAULT NULL,
  `tel` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
