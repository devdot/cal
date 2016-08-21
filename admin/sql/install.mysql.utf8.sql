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

