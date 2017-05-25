--
-- Table structure for table `#__cal_ct_import`
--

CREATE TABLE IF NOT EXISTS `#__cal_ct_import` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `rules` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `#__cal_ct_import`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#__cal_ct_import`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;