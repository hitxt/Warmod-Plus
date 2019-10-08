-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- 主機: 127.0.0.1
-- 產生時間： 
-- 伺服器版本: 10.1.33-MariaDB
-- PHP 版本： 7.1.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `wm`
--

-- --------------------------------------------------------

--
-- 資料表結構 `wm_apilogs`
--

CREATE TABLE `wm_apilogs` (
  `id` int(11) NOT NULL,
  `token` varchar(20) NOT NULL,
  `query` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `wm_license`
--

CREATE TABLE `wm_license` (
  `id` int(11) NOT NULL,
  `steam_id_64` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time_exp` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ftp_a` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ftp_p` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `wm_logo`
--

CREATE TABLE `wm_logo` (
  `id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `wm_match_stats`
--

CREATE TABLE `wm_match_stats` (
  `id` int(11) UNSIGNED NOT NULL,
  `match_id` int(11) UNSIGNED NOT NULL,
  `rounds_played` tinyint(3) UNSIGNED NOT NULL,
  `steam_id_64` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `team` tinyint(1) NOT NULL,
  `kills` tinyint(2) NOT NULL,
  `deaths` tinyint(2) NOT NULL,
  `assists` tinyint(2) NOT NULL,
  `head_shots` tinyint(2) NOT NULL,
  `team_kills` tinyint(2) NOT NULL,
  `assists_team_attack` tinyint(2) NOT NULL,
  `damage` int(4) NOT NULL,
  `hits` int(4) NOT NULL,
  `shots` int(4) NOT NULL,
  `last_alive` tinyint(1) NOT NULL,
  `clutch_won` tinyint(1) NOT NULL,
  `1k` tinyint(2) NOT NULL,
  `2k` tinyint(2) NOT NULL,
  `3k` tinyint(2) NOT NULL,
  `4k` tinyint(2) NOT NULL,
  `5k` tinyint(2) NOT NULL,
  `mvp` int(11) NOT NULL,
  `rws` float NOT NULL,
  `knife` int(10) NOT NULL,
  `glock` int(10) NOT NULL,
  `hkp2000` int(10) NOT NULL,
  `usp_silencer` int(10) NOT NULL,
  `p250` int(10) NOT NULL,
  `deagle` int(10) NOT NULL,
  `elite` int(10) NOT NULL,
  `fiveseven` int(10) NOT NULL,
  `tec9` int(10) NOT NULL,
  `cz75a` int(10) NOT NULL,
  `revolver` int(10) NOT NULL,
  `nova` int(10) NOT NULL,
  `xm1014` int(10) NOT NULL,
  `mag7` int(10) NOT NULL,
  `sawedoff` int(10) NOT NULL,
  `bizon` int(10) NOT NULL,
  `mac10` int(10) NOT NULL,
  `mp9` int(10) NOT NULL,
  `mp7` int(10) NOT NULL,
  `mp5sd` int(10) DEFAULT NULL,
  `ump45` int(10) NOT NULL,
  `p90` int(10) NOT NULL,
  `galilar` int(10) NOT NULL,
  `ak47` int(10) NOT NULL,
  `scar20` int(10) NOT NULL,
  `famas` int(10) NOT NULL,
  `m4a1` int(10) NOT NULL,
  `m4a1_silencer` int(10) NOT NULL,
  `aug` int(10) NOT NULL,
  `ssg08` int(10) NOT NULL,
  `sg556` int(10) NOT NULL,
  `awp` int(10) NOT NULL,
  `g3sg1` int(10) NOT NULL,
  `m249` int(10) NOT NULL,
  `negev` int(10) NOT NULL,
  `hegrenade` int(10) NOT NULL,
  `flashbang` int(10) NOT NULL,
  `smokegrenade` int(10) NOT NULL,
  `inferno` int(10) NOT NULL,
  `incgrenade` int(10) NOT NULL,
  `molotov` int(10) NOT NULL,
  `decoy` int(10) NOT NULL,
  `taser` int(10) NOT NULL,
  `generic` int(10) NOT NULL,
  `head` int(10) NOT NULL,
  `chest` int(10) NOT NULL,
  `stomach` int(10) NOT NULL,
  `left_arm` int(10) NOT NULL,
  `right_arm` int(10) NOT NULL,
  `left_leg` int(10) NOT NULL,
  `right_leg` int(10) NOT NULL,
  `c4_planted` int(10) NOT NULL,
  `c4_exploded` int(10) NOT NULL,
  `c4_defused` int(10) NOT NULL,
  `hostages_rescued` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `wm_notify`
--

CREATE TABLE `wm_notify` (
  `id` int(11) NOT NULL,
  `send` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receive` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `team` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `wm_players`
--

CREATE TABLE `wm_players` (
  `id` int(11) UNSIGNED NOT NULL,
  `steam_id_64` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_ip` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rws` float NOT NULL,
  `team` int(11) NOT NULL,
  `fb` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `twitter` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `twitch` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `youtube` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `wm_results`
--

CREATE TABLE `wm_results` (
  `id` int(11) UNSIGNED NOT NULL,
  `match_start` datetime NOT NULL,
  `match_end` datetime NOT NULL,
  `map` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_rounds` tinyint(3) UNSIGNED NOT NULL,
  `overtime_max_rounds` tinyint(3) UNSIGNED NOT NULL,
  `overtime_count` tinyint(3) UNSIGNED NOT NULL,
  `played_out` tinyint(1) NOT NULL,
  `t_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `t_overall_score` tinyint(3) UNSIGNED NOT NULL,
  `t_first_half_score` tinyint(3) UNSIGNED NOT NULL,
  `t_second_half_score` tinyint(3) UNSIGNED NOT NULL,
  `t_overtime_score` tinyint(3) UNSIGNED NOT NULL,
  `ct_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ct_overall_score` tinyint(3) UNSIGNED NOT NULL,
  `ct_first_half_score` tinyint(3) UNSIGNED NOT NULL,
  `ct_second_half_score` tinyint(3) UNSIGNED NOT NULL,
  `ct_overtime_score` tinyint(3) UNSIGNED NOT NULL,
  `demo` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `competition` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `wm_servers`
--

CREATE TABLE `wm_servers` (
  `id` int(11) NOT NULL,
  `ip` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `port` int(5) NOT NULL,
  `enabled` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `wm_teams`
--

CREATE TABLE `wm_teams` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `leader` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `steam` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fb` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `twitter` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `youtube` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `twitch` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 已匯出資料表的索引
--

--
-- 資料表索引 `wm_apilogs`
--
ALTER TABLE `wm_apilogs`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `wm_license`
--
ALTER TABLE `wm_license`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `wm_logo`
--
ALTER TABLE `wm_logo`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `wm_match_stats`
--
ALTER TABLE `wm_match_stats`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `wm_notify`
--
ALTER TABLE `wm_notify`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `wm_players`
--
ALTER TABLE `wm_players`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `wm_results`
--
ALTER TABLE `wm_results`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `wm_servers`
--
ALTER TABLE `wm_servers`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `wm_teams`
--
ALTER TABLE `wm_teams`
  ADD PRIMARY KEY (`id`);

--
-- 在匯出的資料表使用 AUTO_INCREMENT
--

--
-- 使用資料表 AUTO_INCREMENT `wm_apilogs`
--
ALTER TABLE `wm_apilogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表 AUTO_INCREMENT `wm_license`
--
ALTER TABLE `wm_license`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表 AUTO_INCREMENT `wm_logo`
--
ALTER TABLE `wm_logo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表 AUTO_INCREMENT `wm_match_stats`
--
ALTER TABLE `wm_match_stats`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表 AUTO_INCREMENT `wm_notify`
--
ALTER TABLE `wm_notify`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表 AUTO_INCREMENT `wm_players`
--
ALTER TABLE `wm_players`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表 AUTO_INCREMENT `wm_results`
--
ALTER TABLE `wm_results`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表 AUTO_INCREMENT `wm_servers`
--
ALTER TABLE `wm_servers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表 AUTO_INCREMENT `wm_teams`
--
ALTER TABLE `wm_teams`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
