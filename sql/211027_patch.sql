ALTER TABLE `locations` ADD `location` INT NULL AFTER `description`;
ALTER TABLE `locations` ADD `obsolete` INT NULL AFTER `location`;
ALTER TABLE `locations` ADD KEY `loc` (`location`);
ALTER TABLE `boxes` CHANGE `location` `old_location` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `boxes` ADD `location` INT NULL AFTER `description`;
ALTER TABLE `locations` ADD CONSTRAINT `loc` FOREIGN KEY (`location`) REFERENCES `tracker`(`trackID`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;
