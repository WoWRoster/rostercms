# --------------------------------------------------------
### roster rank access

CREATE TABLE `renprefix_guild_rank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rank` tinyint(4) NOT NULL DEFAULT '0',
  `slug` varchar(20) NOT NULL DEFAULT '',
  `title` varchar(30) NOT NULL DEFAULT '',
  `access` varchar(20) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `renprefix_guild_rank`
  ADD PRIMARY KEY (`id`);
--
-- Dumping data for table `roster_guild_rank`
--

INSERT INTO `renprefix_guild_rank` (`id`, `rank`, `slug`, `title`, `access`) VALUES
(1, 0, 'rank_0', 'Rank 0', '0:1:2'),
(2, 1, 'rank_1', 'Rank 1', '0'),
(3, 2, 'rank_2', 'Rank 2', '0'),
(4, 3, 'rank_3', 'Rank 3', '0'),
(5, 4, 'rank_4', 'Rank 4', '0'),
(6, 5, 'rank_5', 'Rank 5', '0'),
(7, 6, 'rank_6', 'Rank 6', '0'),
(8, 7, 'rank_7', 'Rank 7', '0'),
(9, 8, 'rank_8', 'Rank 8', '0'),
(10, 9, 'rank_9', 'Rank 9', '0'),
(11, 10, 'rank_10', 'Rank 10', '0'),
(12, 11, 'rank_11', 'Rank 11', '0'),
(13, 12, 'rank_12', 'Rank 12', '0');


  