/*
 Navicat Premium Data Transfer

 Target Server Type    : MySQL
 Target Server Version : 50721
 File Encoding         : 65001

 Date: 06/08/2021 18:24:24
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for kf2_players
-- ----------------------------
DROP TABLE IF EXISTS `kf2_players`;
CREATE TABLE `kf2_players`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `unique_net_id` varchar(24) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `steamid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `ip_address` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `gameid` int(11) NULL DEFAULT NULL,
  `ready` int(11) NULL DEFAULT 0,
  `perk_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `perk_level` int(11) NULL DEFAULT NULL,
  `prestige` int(11) NULL DEFAULT 0,
  `damagedealt` int(11) NULL DEFAULT 0,
  `damagetaken` int(11) NULL DEFAULT 0,
  `headshots` int(11) NULL DEFAULT 0,
  `accuracy` float NULL DEFAULT 0,
  `hsaccuracy` float NULL DEFAULT 0,
  `kills` int(11) NULL DEFAULT 0,
  `assists` int(11) NULL DEFAULT 0,
  `deaths` int(11) NULL DEFAULT 0,
  `dosh` int(11) NULL DEFAULT 0,
  `ping` int(11) NULL DEFAULT 999,
  `last_visit` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `last_session` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `idx_steam`(`steamid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
