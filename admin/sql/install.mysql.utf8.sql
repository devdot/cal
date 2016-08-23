CREATE TABLE IF NOT EXISTS `#__cal_settings` ( `ID` INT NOT NULL AUTO_INCREMENT , `data` TEXT NOT NULL , PRIMARY KEY (`ID`) ) ENGINE = InnoDB;
INSERT INTO `#__cal_settings` (`ID`, `data`) VALUES (NULL, '{}');

CREATE TABLE IF NOT EXISTS `#__cal_locations` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `addrStreet` varchar(64) DEFAULT NULL,
  `addrZip` varchar(5) DEFAULT NULL,
  `addrCity` varchar(64) NOT NULL,
  `addrCountry` varchar(64) NOT NULL,
  `geoLoc` point DEFAULT NULL,
  `link` varchar(256) NOT NULL,
  `desc` text NOT NULL,
  `published` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__cal_resources` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(64) NOT NULL , `catid` INT NOT NULL DEFAULT '0' , `type` TINYINT(4) NOT NULL DEFAULT '0' , `type_id` INT DEFAULT NULL , `description` TEXT NOT NULL , PRIMARY KEY (`id`) ) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__cal_events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;