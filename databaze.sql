-- --------------------------------------------------------
-- Hostitel:                     ctgplw90pifdso61.cbetxkdyhwsb.us-east-1.rds.amazonaws.com
-- Verze serveru:                5.7.23-log - Source distribution
-- OS serveru:                   Linux
-- HeidiSQL Verze:               10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Exportování struktury databáze pro
DROP DATABASE IF EXISTS `iis`;
CREATE DATABASE IF NOT EXISTS `iis` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `iis`;

-- Exportování struktury pro tabulka s3mvxw90krbd3ph3.exp
DROP TABLE IF EXISTS `exp`;
CREATE TABLE IF NOT EXISTS `exp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tournament_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `exp` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6BE521B33D1A3E7` (`tournament_id`),
  KEY `IDX_6BE521B296CD8AE` (`team_id`),
  CONSTRAINT `FK_6BE521B296CD8AE` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`),
  CONSTRAINT `FK_6BE521B33D1A3E7` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportování dat pro tabulku s3mvxw90krbd3ph3.exp: ~35 rows (přibližně)
DELETE FROM `exp`;
/*!40000 ALTER TABLE `exp` DISABLE KEYS */;
INSERT INTO `exp` (`id`, `tournament_id`, `team_id`, `exp`) VALUES
	(1, 2, 3, 4),
	(2, 2, 4, 3),
	(3, 2, 5, 7),
	(4, 2, 6, 6),
	(5, 2, 7, 5),
	(6, 2, 8, 1),
	(7, 2, 9, 8),
	(8, 2, 10, 2),
	(9, 2, 12, 0),
	(10, 3, 2, 1),
	(11, 3, 3, 3),
	(12, 3, 4, 5),
	(13, 3, 5, 10),
	(14, 3, 6, 9),
	(15, 3, 7, 2),
	(16, 3, 8, 4),
	(17, 3, 9, 6),
	(18, 3, 10, 8),
	(19, 3, 11, 7),
	(20, 4, 2, 8),
	(21, 4, 3, 9),
	(22, 4, 4, 7),
	(23, 4, 5, 4),
	(24, 4, 6, 1),
	(25, 4, 7, 10),
	(26, 4, 8, 3),
	(27, 4, 9, 2),
	(28, 4, 10, 6),
	(29, 4, 11, 0),
	(30, 4, 12, 5),
	(31, 6, 2, 3),
	(32, 6, 3, 4),
	(33, 6, 4, 5),
	(34, 6, 5, 2),
	(35, 6, 6, 1),
	(36, 6, 7, 6);
/*!40000 ALTER TABLE `exp` ENABLE KEYS */;

-- Exportování struktury pro tabulka s3mvxw90krbd3ph3.game
DROP TABLE IF EXISTS `game`;
CREATE TABLE IF NOT EXISTS `game` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team1_id` int(11) DEFAULT NULL,
  `team2_id` int(11) DEFAULT NULL,
  `tournament_id` int(11) NOT NULL,
  `next_game_id` int(11) DEFAULT NULL,
  `round` int(11) NOT NULL,
  `points_team1` json NOT NULL,
  `points_team2` json NOT NULL,
  `first_in_next_game` tinyint(1) DEFAULT NULL,
  `type` smallint(6) NOT NULL,
  `display_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_232B318CE72BCFA4` (`team1_id`),
  KEY `IDX_232B318CF59E604A` (`team2_id`),
  KEY `IDX_232B318C33D1A3E7` (`tournament_id`),
  KEY `IDX_232B318C2601F3A7` (`next_game_id`),
  CONSTRAINT `FK_232B318C2601F3A7` FOREIGN KEY (`next_game_id`) REFERENCES `game` (`id`),
  CONSTRAINT `FK_232B318C33D1A3E7` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`),
  CONSTRAINT `FK_232B318CE72BCFA4` FOREIGN KEY (`team1_id`) REFERENCES `team` (`id`),
  CONSTRAINT `FK_232B318CF59E604A` FOREIGN KEY (`team2_id`) REFERENCES `team` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=225 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportování dat pro tabulku s3mvxw90krbd3ph3.game: ~104 rows (přibližně)
DELETE FROM `game`;
/*!40000 ALTER TABLE `game` DISABLE KEYS */;
INSERT INTO `game` (`id`, `team1_id`, `team2_id`, `tournament_id`, `next_game_id`, `round`, `points_team1`, `points_team2`, `first_in_next_game`, `type`, `display_order`) VALUES
	(42, 13, 12, 1, 43, 1, '[" ", " ", " "]', '[" ", " ", " "]', 0, 1, NULL),
	(43, 11, NULL, 1, 50, 2, '[" ", " ", " "]', '[" ", " ", " "]', 1, 1, NULL),
	(44, 10, 9, 1, 45, 1, '[" ", " ", " "]', '[" ", " ", " "]', 0, 1, NULL),
	(45, 8, NULL, 1, 50, 2, '[" ", " ", " "]', '[" ", " ", " "]', 0, 1, NULL),
	(46, 7, 6, 1, 47, 1, '[" ", " ", " "]', '[" ", " ", " "]', 0, 1, NULL),
	(47, 5, NULL, 1, 51, 2, '[" ", " ", " "]', '[" ", " ", " "]', 1, 1, NULL),
	(48, 4, 3, 1, 49, 1, '[" ", " ", " "]', '[" ", " ", " "]', 0, 1, NULL),
	(49, 2, NULL, 1, 51, 2, '[" ", " ", " "]', '[" ", " ", " "]', 0, 1, NULL),
	(50, NULL, NULL, 1, 52, 3, '[" ", " ", " "]', '[" ", " ", " "]', 1, 1, NULL),
	(51, NULL, NULL, 1, 52, 3, '[" ", " ", " "]', '[" ", " ", " "]', 0, 1, NULL),
	(52, NULL, NULL, 1, NULL, 4, '[" ", " ", " "]', '[" ", " ", " "]', NULL, 1, NULL),
	(107, 9, 11, 1, NULL, 1, '["4", "5", "4"]', '["5", "4", "5"]', NULL, 2, NULL),
	(108, 9, 4, 1, NULL, 1, '["3", "5", "7"]', '["4", "3", "2"]', NULL, 2, NULL),
	(109, 11, 4, 1, NULL, 1, '["4", "6", "4"]', '["5", "5", "3"]', NULL, 2, NULL),
	(110, 8, 6, 1, NULL, 2, '["3", "5", "5"]', '["4", "4", "4"]', NULL, 2, NULL),
	(111, 8, 7, 1, NULL, 2, '["6", "3", "6"]', '["5", "4", "4"]', NULL, 2, NULL),
	(112, 6, 7, 1, NULL, 2, '["5", "3", "6"]', '["4", "4", "4"]', NULL, 2, NULL),
	(113, 8, 4, 2, NULL, 1, '["5"]', '["4"]', NULL, 2, NULL),
	(114, 8, 7, 2, NULL, 1, '["5"]', '["4"]', NULL, 2, NULL),
	(115, 8, 5, 2, NULL, 1, '["5"]', '["4"]', NULL, 2, NULL),
	(116, 4, 7, 2, NULL, 1, '["5"]', '["4"]', NULL, 2, NULL),
	(117, 4, 5, 2, NULL, 1, '["5"]', '["4"]', NULL, 2, NULL),
	(118, 7, 5, 2, NULL, 1, '["5"]', '["4"]', NULL, 2, NULL),
	(119, 10, 3, 2, NULL, 2, '["5"]', '["4"]', NULL, 2, NULL),
	(120, 10, 6, 2, NULL, 2, '["5"]', '["4"]', NULL, 2, NULL),
	(121, 10, 9, 2, NULL, 2, '["5"]', '["4"]', NULL, 2, NULL),
	(122, 3, 6, 2, NULL, 2, '["5"]', '["4"]', NULL, 2, NULL),
	(123, 3, 9, 2, NULL, 2, '["5"]', '["4"]', NULL, 2, NULL),
	(124, 6, 9, 2, NULL, 2, '["5"]', '["4"]', NULL, 2, NULL),
	(125, 2, 3, 3, NULL, 1, '["5"]', '["4"]', NULL, 2, NULL),
	(126, 2, 4, 3, NULL, 1, '["5"]', '["4"]', NULL, 2, NULL),
	(127, 2, 11, 3, NULL, 1, '["5"]', '["4"]', NULL, 2, NULL),
	(128, 2, 6, 3, NULL, 1, '["5"]', '["4"]', NULL, 2, NULL),
	(129, 3, 4, 3, NULL, 1, '["5"]', '["4"]', NULL, 2, NULL),
	(130, 3, 11, 3, NULL, 1, '["54"]', '["4"]', NULL, 2, NULL),
	(131, 3, 6, 3, NULL, 1, '["5"]', '["4"]', NULL, 2, NULL),
	(132, 4, 11, 3, NULL, 1, '["5"]', '["4"]', NULL, 2, NULL),
	(133, 4, 6, 3, NULL, 1, '["5"]', '["4"]', NULL, 2, NULL),
	(134, 11, 6, 3, NULL, 1, '["5"]', '["4"]', NULL, 2, NULL),
	(135, 7, 8, 3, NULL, 2, '["5"]', '["4"]', NULL, 2, NULL),
	(136, 7, 9, 3, NULL, 2, '["5"]', '["4"]', NULL, 2, NULL),
	(137, 7, 10, 3, NULL, 2, '["5"]', '["4"]', NULL, 2, NULL),
	(138, 7, 5, 3, NULL, 2, '["5"]', '["4"]', NULL, 2, NULL),
	(139, 8, 9, 3, NULL, 2, '["5"]', '["4"]', NULL, 2, NULL),
	(140, 8, 10, 3, NULL, 2, '["45"]', '["4"]', NULL, 2, NULL),
	(141, 8, 5, 3, NULL, 2, '["5"]', '["4"]', NULL, 2, NULL),
	(142, 9, 10, 3, NULL, 2, '["5"]', '["4"]', NULL, 2, NULL),
	(143, 9, 5, 3, NULL, 2, '["5"]', '["4"]', NULL, 2, NULL),
	(144, 10, 5, 3, NULL, 2, '["5"]', '["4"]', NULL, 2, NULL),
	(145, 6, 8, 4, NULL, 1, '["5"]', '["4"]', NULL, 2, NULL),
	(146, 6, 12, 4, NULL, 1, '["5"]', '["4"]', NULL, 2, NULL),
	(147, 6, 4, 4, NULL, 1, '["4"]', '["7"]', NULL, 2, NULL),
	(148, 6, 3, 4, NULL, 1, '["5"]', '["8"]', NULL, 2, NULL),
	(149, 8, 12, 4, NULL, 1, '["4"]', '["8"]', NULL, 2, NULL),
	(150, 8, 4, 4, NULL, 1, '["7"]', '["4"]', NULL, 2, NULL),
	(151, 8, 3, 4, NULL, 1, '["2"]', '["5"]', NULL, 2, NULL),
	(152, 12, 4, 4, NULL, 1, '["5"]', '["8"]', NULL, 2, NULL),
	(153, 12, 3, 4, NULL, 1, '["5"]', '["8"]', NULL, 2, NULL),
	(154, 4, 3, 4, NULL, 1, '["8"]', '["6"]', NULL, 2, NULL),
	(155, 9, 5, 4, NULL, 2, '["7"]', '["4"]', NULL, 2, NULL),
	(156, 9, 10, 4, NULL, 2, '["8"]', '["5"]', NULL, 2, NULL),
	(157, 9, 2, 4, NULL, 2, '["8"]', '["5"]', NULL, 2, NULL),
	(158, 9, 7, 4, NULL, 2, '["7"]', '["4"]', NULL, 2, NULL),
	(159, 9, 11, 4, NULL, 2, '["8"]', '["4"]', NULL, 2, NULL),
	(160, 5, 10, 4, NULL, 2, '["8"]', '["4"]', NULL, 2, NULL),
	(161, 5, 2, 4, NULL, 2, '["8"]', '["5"]', NULL, 2, NULL),
	(162, 5, 7, 4, NULL, 2, '["7"]', '["4"]', NULL, 2, NULL),
	(163, 5, 11, 4, NULL, 2, '["7"]', '["5"]', NULL, 2, NULL),
	(164, 10, 2, 4, NULL, 2, '["87"]', '["4"]', NULL, 2, NULL),
	(165, 10, 7, 4, NULL, 2, '["5"]', '["8"]', NULL, 2, NULL),
	(166, 10, 11, 4, NULL, 2, '["5"]', '["8"]', NULL, 2, NULL),
	(167, 2, 7, 4, NULL, 2, '["7"]', '["5"]', NULL, 2, NULL),
	(168, 2, 11, 4, NULL, 2, '["5"]', '["7"]', NULL, 2, NULL),
	(169, 7, 11, 4, NULL, 2, '["7"]', '["5"]', NULL, 2, NULL),
	(170, 3, 7, 2, 174, 1, '[" "]', '[" "]', 1, 1, 1),
	(171, 9, 8, 2, 174, 1, '[" "]', '[" "]', 0, 1, 2),
	(172, 4, 6, 2, 175, 1, '[" "]', '[" "]', 1, 1, 3),
	(173, 5, 10, 2, 175, 1, '[" "]', '[" "]', 0, 1, 4),
	(174, NULL, NULL, 2, 176, 2, '[" "]', '[" "]', 1, 1, NULL),
	(175, NULL, NULL, 2, 176, 2, '[" "]', '[" "]', 0, 1, NULL),
	(176, NULL, NULL, 2, NULL, 3, '[" "]', '[" "]', NULL, 1, NULL),
	(177, 8, 4, 3, 181, 1, '[" "]', '[" "]', 1, 1, 1),
	(178, 10, 2, 3, 181, 1, '[" "]', '[" "]', 0, 1, 2),
	(179, 3, 9, 3, 182, 1, '[" "]', '[" "]', 1, 1, 3),
	(180, 11, 7, 3, 182, 1, '[" "]', '[" "]', 0, 1, 4),
	(181, NULL, NULL, 3, 183, 2, '[" "]', '[" "]', 1, 1, NULL),
	(182, NULL, NULL, 3, 183, 2, '[" "]', '[" "]', 0, 1, NULL),
	(183, NULL, NULL, 3, NULL, 3, '[" "]', '[" "]', NULL, 1, NULL),
	(184, 8, 5, 4, 188, 1, '[" "]', '[" "]', 1, 1, 1),
	(185, 4, 11, 4, 188, 1, '[" "]', '[" "]', 0, 1, 2),
	(186, 9, 12, 4, 189, 1, '[" "]', '[" "]', 1, 1, 3),
	(187, 10, 6, 4, 189, 1, '[" "]', '[" "]', 0, 1, 4),
	(188, NULL, NULL, 4, 190, 2, '[" "]', '[" "]', 1, 1, NULL),
	(189, NULL, NULL, 4, 190, 2, '[" "]', '[" "]', 0, 1, NULL),
	(190, NULL, NULL, 4, NULL, 3, '[" "]', '[" "]', NULL, 1, NULL),
	(209, 6, 2, 6, NULL, 1, '["4", "43"]', '["3", "3"]', NULL, 2, NULL),
	(210, 6, 4, 6, NULL, 1, '["43", "43"]', '["3", "3"]', NULL, 2, NULL),
	(211, 2, 4, 6, NULL, 1, '["4", "43"]', '["3", "3"]', NULL, 2, NULL),
	(212, 5, 3, 6, NULL, 2, '["43", "43"]', '["3", "3"]', NULL, 2, NULL),
	(213, 5, 7, 6, NULL, 2, '["43", "4"]', '["3", "3"]', NULL, 2, NULL),
	(214, 3, 7, 6, NULL, 2, '["4", "4"]', '["3", "3"]', NULL, 2, NULL),
	(220, 6, 7, 6, 221, 1, '[" ", " "]', '[" ", " "]', 0, 1, NULL),
	(221, 2, NULL, 6, 224, 2, '[" ", " "]', '[" ", " "]', 1, 1, 1),
	(222, 4, 5, 6, 223, 1, '[" ", " "]', '[" ", " "]', 0, 1, NULL),
	(223, 3, NULL, 6, 224, 2, '[" ", " "]', '[" ", " "]', 0, 1, 2),
	(224, NULL, NULL, 6, NULL, 3, '[" ", " "]', '[" ", " "]', NULL, 1, NULL);
/*!40000 ALTER TABLE `game` ENABLE KEYS */;

-- Exportování struktury pro tabulka s3mvxw90krbd3ph3.migration_versions
DROP TABLE IF EXISTS `migration_versions`;
CREATE TABLE IF NOT EXISTS `migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportování dat pro tabulku s3mvxw90krbd3ph3.migration_versions: ~8 rows (přibližně)
DELETE FROM `migration_versions`;
/*!40000 ALTER TABLE `migration_versions` DISABLE KEYS */;
INSERT INTO `migration_versions` (`version`, `executed_at`) VALUES
	('20191128114005', '2019-11-28 11:42:51'),
	('20191129194036', '2019-11-30 17:36:24'),
	('20191129224437', '2019-11-30 17:36:24'),
	('20191130084623', '2019-11-30 17:36:24'),
	('20191130091910', '2019-11-30 17:36:25'),
	('20191130114611', '2019-11-30 17:36:25'),
	('20191201093519', '2019-12-01 20:53:05'),
	('20191201185317', '2019-12-01 20:53:05');
/*!40000 ALTER TABLE `migration_versions` ENABLE KEYS */;

-- Exportování struktury pro tabulka s3mvxw90krbd3ph3.player
DROP TABLE IF EXISTS `player`;
CREATE TABLE IF NOT EXISTS `player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_girl` tinyint(1) NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `path_to_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_98197A65642B8210` (`admin_id`),
  CONSTRAINT `FK_98197A65642B8210` FOREIGN KEY (`admin_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportování dat pro tabulku s3mvxw90krbd3ph3.player: ~16 rows (přibližně)
DELETE FROM `player`;
/*!40000 ALTER TABLE `player` DISABLE KEYS */;
INSERT INTO `player` (`id`, `admin_id`, `name`, `is_girl`, `phone`, `email`, `path_to_logo`) VALUES
	(1, 2, 'Aleš', 0, NULL, NULL, NULL),
	(2, 2, 'Eliška', 1, NULL, NULL, NULL),
	(3, 2, 'František', 0, NULL, NULL, NULL),
	(4, 2, 'August', 0, NULL, NULL, NULL),
	(5, 2, 'Františka', 1, NULL, NULL, NULL),
	(6, 2, 'Alžběta', 1, NULL, NULL, NULL),
	(7, 2, 'Ulrych', 0, NULL, NULL, NULL),
	(8, 2, 'Gustav', 0, NULL, NULL, NULL),
	(9, 2, 'Olina', 1, NULL, NULL, NULL),
	(11, 5, 'Jůlie', 1, NULL, NULL, NULL),
	(12, 5, 'Smaragd', 0, NULL, NULL, NULL),
	(13, 5, 'Esmeralda', 1, NULL, NULL, NULL),
	(14, 5, 'Zora', 1, NULL, NULL, NULL),
	(15, 5, 'Eliška', 1, '735563823', 'houpaci.sit@seznam.cz', NULL),
	(16, 5, 'Břetislav', 0, '+420838859394', 'mc.bretislav@iamdj.com', NULL),
	(17, 5, 'Adam', 0, NULL, NULL, NULL);
/*!40000 ALTER TABLE `player` ENABLE KEYS */;

-- Exportování struktury pro tabulka s3mvxw90krbd3ph3.team
DROP TABLE IF EXISTS `team`;
CREATE TABLE IF NOT EXISTS `team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_C4E0A61F642B8210` (`admin_id`),
  CONSTRAINT `FK_C4E0A61F642B8210` FOREIGN KEY (`admin_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportování dat pro tabulku s3mvxw90krbd3ph3.team: ~11 rows (přibližně)
DELETE FROM `team`;
/*!40000 ALTER TABLE `team` DISABLE KEYS */;
INSERT INTO `team` (`id`, `admin_id`, `name`, `exp`) VALUES
	(2, 1, 'Hustilky', 4),
	(3, 1, 'Knihomolové', 9),
	(4, 1, 'Vrabci', 10),
	(5, 1, 'Žemle', 7),
	(6, 1, 'Heveři', 1),
	(7, 1, 'Lumíci', 2),
	(8, 1, 'Olejničky', 5),
	(9, 2, 'Březáci', 8),
	(10, 2, 'Agamy', 3),
	(11, 2, 'Puntíci', 0),
	(12, 2, 'Kolemjdoucí', 6),
	(13, 2, 'Vítězové', 0);
/*!40000 ALTER TABLE `team` ENABLE KEYS */;

-- Exportování struktury pro tabulka s3mvxw90krbd3ph3.team_player
DROP TABLE IF EXISTS `team_player`;
CREATE TABLE IF NOT EXISTS `team_player` (
  `team_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  PRIMARY KEY (`team_id`,`player_id`),
  KEY `IDX_EE023DBC296CD8AE` (`team_id`),
  KEY `IDX_EE023DBC99E6F5DF` (`player_id`),
  CONSTRAINT `FK_EE023DBC296CD8AE` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_EE023DBC99E6F5DF` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportování dat pro tabulku s3mvxw90krbd3ph3.team_player: ~8 rows (přibližně)
DELETE FROM `team_player`;
/*!40000 ALTER TABLE `team_player` DISABLE KEYS */;
INSERT INTO `team_player` (`team_id`, `player_id`) VALUES
	(9, 1),
	(9, 2),
	(9, 3),
	(9, 4),
	(10, 2),
	(10, 7),
	(10, 8),
	(10, 9);
/*!40000 ALTER TABLE `team_player` ENABLE KEYS */;

-- Exportování struktury pro tabulka s3mvxw90krbd3ph3.tournament
DROP TABLE IF EXISTS `tournament`;
CREATE TABLE IF NOT EXISTS `tournament` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `field_count` int(11) DEFAULT NULL,
  `max_teams_count` int(11) DEFAULT NULL,
  `plays_in_game` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BD5FB8D9642B8210` (`admin_id`),
  CONSTRAINT `FK_BD5FB8D9642B8210` FOREIGN KEY (`admin_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportování dat pro tabulku s3mvxw90krbd3ph3.tournament: ~8 rows (přibližně)
DELETE FROM `tournament`;
/*!40000 ALTER TABLE `tournament` DISABLE KEYS */;
INSERT INTO `tournament` (`id`, `admin_id`, `name`, `date`, `price`, `field_count`, `max_teams_count`, `plays_in_game`) VALUES
	(1, 2, '6 členů s rozstřelem', '2019-12-02', 250, NULL, 16, 3),
	(2, 2, '8 členů s rozstřelem', '2019-12-02', 35, NULL, NULL, 1),
	(3, 1, '10 členů s rozstřelem', '2019-12-02', 35, NULL, NULL, 1),
	(4, 1, '11 členů s rozstřelem', '2019-12-02', 60, NULL, NULL, 1),
	(6, 1, '6 členů s random pavoukem', '2019-12-02', 35, NULL, NULL, 2),
	(7, 1, '8 členů s random pavoukem', '2019-12-02', 35, NULL, NULL, 3),
	(8, 1, '10 členů s random pavoukem', '2019-12-02', 35, NULL, NULL, 3),
	(9, 1, '10 členů s random pavoukem', '2019-12-02', 35, NULL, NULL, 3);
/*!40000 ALTER TABLE `tournament` ENABLE KEYS */;

-- Exportování struktury pro tabulka s3mvxw90krbd3ph3.tournament_team
DROP TABLE IF EXISTS `tournament_team`;
CREATE TABLE IF NOT EXISTS `tournament_team` (
  `tournament_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  PRIMARY KEY (`tournament_id`,`team_id`),
  KEY `IDX_F36D142133D1A3E7` (`tournament_id`),
  KEY `IDX_F36D1421296CD8AE` (`team_id`),
  CONSTRAINT `FK_F36D1421296CD8AE` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_F36D142133D1A3E7` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportování dat pro tabulku s3mvxw90krbd3ph3.tournament_team: ~24 rows (přibližně)
DELETE FROM `tournament_team`;
/*!40000 ALTER TABLE `tournament_team` DISABLE KEYS */;
INSERT INTO `tournament_team` (`tournament_id`, `team_id`) VALUES
	(2, 3),
	(2, 4),
	(2, 5),
	(2, 6),
	(2, 7),
	(2, 8),
	(2, 9),
	(2, 10),
	(3, 2),
	(3, 3),
	(3, 4),
	(3, 5),
	(3, 6),
	(3, 7),
	(3, 8),
	(3, 9),
	(3, 10),
	(3, 11),
	(6, 2),
	(6, 3),
	(6, 4),
	(6, 5),
	(6, 6),
	(6, 7);
/*!40000 ALTER TABLE `tournament_team` ENABLE KEYS */;

-- Exportování struktury pro tabulka s3mvxw90krbd3ph3.user
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportování dat pro tabulku s3mvxw90krbd3ph3.user: ~3 rows (přibližně)
DELETE FROM `user`;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `email`, `roles`, `password`) VALUES
	(1, 'admin@pavoukovnik.cz', '["ROLE_CAPTAIN", "ROLE_ADMIN"]', '$argon2id$v=19$m=65536,t=4,p=1$TWZGdjN1WlhuMFRDZS9yZw$1WjNfA0CaeDUMqrcaeJbW9C1ZLdU9KujVJ8UUMBz/EU'),
	(2, 'user@pavoukovnik.cz', '["ROLE_CAPTAIN"]', '$argon2id$v=19$m=65536,t=4,p=1$cjFHOHQ4WHJtVDRlWVdGbg$2Is1q8LggOUtqNNME6/zDFG+TLpVv59MBXExTu1tmtk'),
	(5, 'user2@pavoukovnik.cz', '["ROLE_CAPTAIN"]', '$argon2id$v=19$m=65536,t=4,p=1$Qm9EWTlkeXJUYkNtMm10QQ$B/3YnhPPF31V40uda8i5lDep0Q4kk+fL/KBsH78wiCc');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
