ALTER TABLE `user` CHANGE `groups` `groupType` TINYINT NOT NULL COMMENT '0 is user, 1 is labgroup, 2 is projectgroup';
INSERT INTO user
SELECT ((SELECT COUNT( * )FROM user) + id +1) AS ID, 
	name,'',name,'','',1 
FROM usergroups;

CREATE TABLE permissions
SELECT trackID, owner AS userid, permOwner AS permission FROM tracker;

INSERT INTO permissions SELECT trackID, (SELECT user.id FROM user JOIN usergroups ON usergroups.name=user.userid WHERE usergroups.id = tracker.groupid) AS userid, permGroup AS permission FROM tracker WHERE permGroup > 0;

CREATE TABLE `labdb`.`groups` (`userid` INT NOT NULL ,`belongsToGroup` INT NOT NULL, `defaultPermissions` INT NOT NULL) ENGINE = InnoDB;
INSERT INTO groups SELECT id, (SELECT id FROM user WHERE userid='leemorgroup'),0 FROM user WHERE groupType!=1;
UPDATE `labdb`.`groups` SET `belongsToGroup` = (SELECT id FROM user WHERE userid='hirogroup') WHERE `groups`.`userid` =5 OR `groups`.`userid` =10 OR `groups`.`userid` =11 OR `groups`.`userid` =12;
UPDATE user SET groupType =0 WHERE groupType!=1;
DROP TABLE `usergroups`;
INSERT INTO `labdb`.`user` (`ID`, `userid`, `password`, `fullname`, `email`, `notes`, `groupType`) VALUES (NULL, 'everybody', '', 'everybody', '', NULL, '2');
INSERT INTO permissions SELECT trackID, (SELECT user.ID FROM user WHERE user.userid = 'everybody') AS userid, permOthers AS permission FROM tracker WHERE permOthers > 0;
INSERT INTO groups SELECT id, (SELECT user.ID FROM user WHERE user.userid = 'everybody'), 0 FROM user WHERE groupType=0;
INSERT INTO permissions SELECT trackID, (SELECT user.ID FROM user WHERE user.userid = 'leemorgroup') AS userid, 2 AS permission FROM tracker WHERE  sampletype=10 OR sampletype=11;
ALTER TABLE `sampletypes` ADD `labPermission` INT NOT NULL ;
UPDATE `labdb`.`sampletypes` SET `labPermission` = '2' WHERE `sampletypes`.`id`=10 OR `sampletypes`.`id`=11;
INSERT INTO `labdb`.`user` (`ID`, `userid`, `password`, `fullname`, `email`, `notes`, `groupType`) VALUES (NULL, 'agoGroup', '', 'agoGroup', '', NULL, '2');
INSERT INTO groups SELECT id, (SELECT id FROM user WHERE userid='agoGroup'), 2 FROM user WHERE userid='cfaehnle' OR userid='Claus' OR userid='Lin';
INSERT INTO permissions SELECT trackID,(SELECT id FROM user WHERE userid='agoGroup'),2 FROM tracker WHERE owner=(SELECT id FROM user WHERE userid='cfaehnle') OR owner=(SELECT id FROM user WHERE userid='Claus') OR owner=(SELECT id FROM user WHERE userid='Lin');
DROP TABLE plasmidsinstrains;

modifications applied 080920
**************************************************************

CREATE TABLE `labdb`.`itemstatus` (
`statusNr` INT NOT NULL ,
`statusName` VARCHAR( 250 ) NOT NULL ,
PRIMARY KEY ( `statusNr` )
) ENGINE = InnoDB;

INSERT INTO `labdb`.`itemstatus` (
`statusNr` ,
`statusName`
)
VALUES (
'1', 'to be ordered'
), (
'2', 'order placed'
), (
'3', 'in stock'
), (
'4', 'finished'
);

modifications applied
***********************************************************************
ALTER TABLE `groups` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
UPDATE `labdb`.`user` SET `groupType` = '2' WHERE `user`.`ID` =16 LIMIT 1 ;

081010 mods:
*****************************************************
ALTER TABLE `inventory` CHANGE `quantity` `unitMeas` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
ALTER TABLE `inventory` ADD `quantity` INT NOT NULL AFTER `orderNumber` ;
ALTER TABLE `inventory` CHANGE `quantity` `quantity` VARCHAR( 250 ) NOT NULL ;

090212 mods:
********************************************
ALTER TABLE `connections`
ADD `fragName` VARCHAR(255) NOT NULL COMMENT 'end position on plasimd in bp',
ADD `start` INT NOT NULL COMMENT 'start position on plasmid in bp',
ADD `end` INT NOT NULL COMMENT 'end position on plasimd in bp',
ADD `direction` INT NOT NULL COMMENT 'direction on plasmid. fw is 1, rev is 0';

090406 mods:
*********************************************
ALTER TABLE `vials` DROP `sID`;
ALTER TABLE `vials` DROP `sType`;
ALTER TABLE `vials` CHANGE `trackerID` `sID` INT( 11 ) NOT NULL COMMENT 'trackerID of vial content';
 
090414 mods:
**********************************************
ALTER TABLE `oligos` ADD `tm` FLOAT NOT NULL AFTER `targetmatch` ;

090505 mods:
*********************************************
INSERT INTO `labdb`.`groups` SELECT NULL, ID, ID, 2 FROM user WHERE groupType=0

090506 mods:
*******************************************
INSERT INTO `labdb`.`user` (`ID`, `userid`, `password`, `fullname`, `email`, `notes`, `groupType`) VALUES (NULL, 'leemorlabAdmins', '', 'Admins Leemorlab', '', NULL, '3');
INSERT INTO `labdb`.`user` (`ID`, `userid`, `password`, `fullname`, `email`, `notes`, `groupType`) VALUES (NULL, 'hirolabAdmins', '', 'Admins Hirolab', '', NULL, '3');
INSERT INTO `labdb`.`groups` (`id`, `userid`, `belongsToGroup`, `defaultPermissions`) VALUES (NULL, '30', '17', '1');
INSERT INTO `labdb`.`groups` (`id`, `userid`, `belongsToGroup`, `defaultPermissions`) VALUES (NULL, '31', '18', '1');
INSERT INTO permissions SELECT trackID, 30 AS userid, 1 AS permission FROM tracker JOIN groups ON tracker.owner=groups.userid WHERE groups.belongsToGroup=17;
INSERT INTO permissions SELECT trackID, 31 AS userid, 1 AS permission FROM tracker JOIN groups ON tracker.owner=groups.userid WHERE groups.belongsToGroup=18;
INSERT INTO `labdb`.`groups` (`id`, `userid`, `belongsToGroup`, `defaultPermissions`) VALUES (NULL, '6', '30', ''), (NULL, '21', '30', '');

090519 mods:
******************************************
ALTER TABLE `vials` ADD `creator` VARCHAR( 255 ) NOT NULL AFTER `description` ;

091119 mods:
*******************************************
ALTER TABLE `plasmids` ADD `enzymes` TEXT NOT NULL COMMENT 'comma separated list of restriction enzyme names (emboss restrict syntax)';