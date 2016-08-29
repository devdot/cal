--
-- Table structure for table `#__cal_events`
--

CREATE TABLE IF NOT EXISTS `#__cal_events` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `introtext` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `fulltext` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `catid` int(10) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `metakey` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `metadesc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `location_id` int(10) unsigned NOT NULL DEFAULT '0',
  `recurring_id` int(10) unsigned NOT NULL DEFAULT '0',
  `recurring_schedule` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `#__cal_events`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#__cal_events`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
  
--
-- Table structure for table `#__cal_events_resources`
--

CREATE TABLE IF NOT EXISTS `#__cal_events_resources` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE `#__cal_events_resources`
  ADD PRIMARY KEY (`id`), ADD KEY `event_id` (`event_id`), ADD KEY `resource_id` (`resource_id`);
  
  ALTER TABLE `#__cal_events_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
  
--
-- Table structure for table `#__cal_locations`
--

CREATE TABLE IF NOT EXISTS `#__cal_locations` (
  `ID` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `addrStreet` varchar(64) DEFAULT NULL,
  `addrZip` varchar(5) DEFAULT NULL,
  `addrCity` varchar(64) NOT NULL,
  `addrCountry` varchar(64) NOT NULL,
  `geoLoc` point DEFAULT NULL,
  `link` varchar(256) NOT NULL,
  `desc` text NOT NULL,
  `published` bit(1) NOT NULL DEFAULT b'1'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE `#__cal_locations`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `#__cal_locations`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
  
--
-- Table structure for table `#__cal_resources`
--

CREATE TABLE IF NOT EXISTS `#__cal_resources` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `catid` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `type_id` int(11) DEFAULT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

ALTER TABLE `#__cal_resources`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#__cal_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
