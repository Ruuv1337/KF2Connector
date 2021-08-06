/*
 Navicat Premium Data Transfer

 Target Server Type    : MySQL
 Target Server Version : 50721
 File Encoding         : 65001

 Date: 06/08/2021 18:24:36
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for kf2_trackstats
-- ----------------------------
DROP TABLE IF EXISTS `kf2_trackstats`;
CREATE TABLE `kf2_trackstats`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `headshots` int(11) NULL DEFAULT 0,
  `kills` int(11) NOT NULL,
  `assists` int(11) NOT NULL,
  `deaths` int(11) NOT NULL,
  `doshearn` int(11) NULL DEFAULT 0,
  `dmgdealt` int(11) NOT NULL,
  `dmgtaken` int(11) NULL DEFAULT 0,
  `accuracy` float NULL DEFAULT 0,
  `hsaccuracy` float NULL DEFAULT 0,
  `mapname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `stats_time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_pid`(`pid`) USING BTREE,
  CONSTRAINT `fk_pid` FOREIGN KEY (`pid`) REFERENCES `kf2_players` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
