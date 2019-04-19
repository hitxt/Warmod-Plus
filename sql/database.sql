-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- 主機: localhost:3306
-- 產生時間： 2018 年 06 月 13 日 14:48
-- 伺服器版本: 5.6.39-cll-lve
-- PHP 版本： 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `kentotw_csgo_server`
--

-- --------------------------------------------------------

--
-- 資料表結構 `wm_license_test`
--

CREATE TABLE `wm_license_test` (
  `id` int(11) NOT NULL,
  `steam_id_64` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time_exp` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ftp_a` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ftp_p` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 資料表的匯出資料 `wm_license_test`
--

INSERT INTO `wm_license_test` (`id`, `steam_id_64`, `token`, `time_exp`, `ftp_a`, `ftp_p`) VALUES
(1, '76561198012825619', 'ejjqopmmlzlk', '2018-12-01', 'wmuser1', 'r71rzr4lgi76'),
(4, '76561197975262643', 'HZpUvKlcNu', '2018-06-01', 'wmuser2', 'j78ovt9cmmfn');

-- --------------------------------------------------------

--
-- 資料表結構 `wm_logo_test`
--

CREATE TABLE `wm_logo_test` (
  `id` int(11) NOT NULL,
  `team` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 資料表的匯出資料 `wm_logo_test`
--

INSERT INTO `wm_logo_test` (`id`, `team`) VALUES
(1, '1');

-- --------------------------------------------------------

--
-- 資料表結構 `wm_match_stats_test`
--

CREATE TABLE `wm_match_stats_test` (
  `key_id` int(11) UNSIGNED NOT NULL,
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

--
-- 資料表結構 `wm_notify_test`
--

CREATE TABLE `wm_notify_test` (
  `key_id` int(11) NOT NULL,
  `send` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receive` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `team` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `wm_players_test`
--

CREATE TABLE `wm_players_test` (
  `key_id` int(11) UNSIGNED NOT NULL,
  `steam_id_64` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_ip` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rws` float NOT NULL,
  `team` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fb` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `twitter` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `twitch` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `youtube` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `wm_results_test`
--

CREATE TABLE `wm_results_test` (
  `match_id` int(11) UNSIGNED NOT NULL,
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
-- 資料表結構 `wm_servers_test`
--

CREATE TABLE `wm_servers_test` (
  `id` int(11) NOT NULL,
  `ip` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `port` int(5) NOT NULL,
  `enabled` tinyint(4) NOT NULL,
  `official` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `wm_teams_test`
--

CREATE TABLE `wm_teams_test` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `steam_url` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `leader` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- 資料表索引 `wm_license_test`
--
ALTER TABLE `wm_license_test`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `wm_logo_test`
--
ALTER TABLE `wm_logo_test`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `wm_match_stats_test`
--
ALTER TABLE `wm_match_stats_test`
  ADD PRIMARY KEY (`key_id`);

--
-- 資料表索引 `wm_notify_test`
--
ALTER TABLE `wm_notify_test`
  ADD PRIMARY KEY (`key_id`);

--
-- 資料表索引 `wm_players_test`
--
ALTER TABLE `wm_players_test`
  ADD PRIMARY KEY (`key_id`);

--
-- 資料表索引 `wm_results_test`
--
ALTER TABLE `wm_results_test`
  ADD PRIMARY KEY (`match_id`);

--
-- 資料表索引 `wm_servers_test`
--
ALTER TABLE `wm_servers_test`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `wm_teams_test`
--
ALTER TABLE `wm_teams_test`
  ADD PRIMARY KEY (`id`);

--
-- 在匯出的資料表使用 AUTO_INCREMENT
--

--
-- 使用資料表 AUTO_INCREMENT `wm_license_test`
--
ALTER TABLE `wm_license_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用資料表 AUTO_INCREMENT `wm_logo_test`
--
ALTER TABLE `wm_logo_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表 AUTO_INCREMENT `wm_match_stats_test`
--
ALTER TABLE `wm_match_stats_test`
  MODIFY `key_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用資料表 AUTO_INCREMENT `wm_notify_test`
--
ALTER TABLE `wm_notify_test`
  MODIFY `key_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用資料表 AUTO_INCREMENT `wm_players_test`
--
ALTER TABLE `wm_players_test`
  MODIFY `key_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- 使用資料表 AUTO_INCREMENT `wm_results_test`
--
ALTER TABLE `wm_results_test`
  MODIFY `match_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- 使用資料表 AUTO_INCREMENT `wm_servers_test`
--
ALTER TABLE `wm_servers_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表 AUTO_INCREMENT `wm_teams_test`
--
ALTER TABLE `wm_teams_test`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
