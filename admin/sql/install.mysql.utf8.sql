CREATE TABLE IF NOT EXISTS `#__cal_settings` ( `ID` INT NOT NULL AUTO_INCREMENT , `data` TEXT NOT NULL , PRIMARY KEY (`ID`) ) ENGINE = InnoDB;
INSERT INTO `#__cal_settings` (`ID`, `data`) VALUES (NULL, '{}');

CREATE TABLE `#__cal_locations` ( `ID` INT NOT NULL AUTO_INCREMENT, `Name` VARCHAR(64) NOT NULL , `AddrStreet` VARCHAR(64) NULL , `AddrZip` MEDIUMINT NULL , `AddrCity` VARCHAR(64) NOT NULL , `AddrCountry` VARCHAR(64) NOT NULL , `GeoLoc` POINT NULL , `Link` VARCHAR(256) NOT NULL , `Desc` INT NOT NULL , PRIMARY KEY (`ID`) ) ENGINE = InnoDB;