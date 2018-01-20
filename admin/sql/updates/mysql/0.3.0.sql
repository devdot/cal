--
-- Table structure for table `#__cal_ct_import`
--

CREATE TABLE IF NOT EXISTS `#__cal_ct_import` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `rules` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `#__cal_ct_import`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


-- Modifications to event tables

ALTER TABLE `#__cal_events` ADD `ct_id` INT NULL DEFAULT NULL , ADD `ct_subid` INT NULL DEFAULT NULL , ADD `ct_modified` DATETIME NULL DEFAULT NULL ;
ALTER TABLE `#__cal_archive` ADD `ct_id` INT NULL DEFAULT NULL , ADD `ct_subid` INT NULL DEFAULT NULL , ADD `ct_modified` DATETIME NULL DEFAULT NULL ;
