/*
 Navicat Premium Data Transfer

 Target Server Type    : MySQL
 Target Server Version : 50721
 File Encoding         : 65001

 Date: 06/08/2021 18:24:30
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for kf2_session
-- ----------------------------
DROP TABLE IF EXISTS `kf2_session`;
CREATE TABLE `kf2_session`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` int(11) NOT NULL DEFAULT 0,
  `session` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `mapname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `difficulty` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `totalwave` int(11) NOT NULL DEFAULT 0,
  `currentwave` int(11) NOT NULL DEFAULT 0,
  `wavestarted` int(11) NOT NULL DEFAULT 0,
  `trader` int(11) NOT NULL DEFAULT 0,
  `totalZedKilled` int(11) NOT NULL DEFAULT 0,
  `timestamp` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `lastwave` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
