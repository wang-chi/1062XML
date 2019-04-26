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

 Date: 24/05/2018 15:49:13
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for distance
-- ----------------------------
DROP TABLE IF EXISTS `distance`;
CREATE TABLE `distance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `airboxid` varchar(12) NOT NULL,
  `stationid` varchar(12) NOT NULL,
  `distance` decimal(13,6) NOT NULL,
  `level` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5879 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
