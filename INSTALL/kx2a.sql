-- Edit the Boards --
ALTER TABLE `PREFIXboards` ADD `imgedit` tinyint(1) NOT NULL default '0';
ALTER TABLE `PREFIXboards` ADD `enableads` tinyint(1) NOT NULL default '1';
ALTER TABLE `PREFIXboards` ADD `max_files` tinyint(6) NOT NULL default '1';
ALTER TABLE `PREFIXboards` ADD `enableemail` tinyint(1) NOT NULL default '0';
ALTER TABLE `PREFIXboards` ADD `boardclass` tinyint(1) NOT NULL default '1';
ALTER TABLE `PREFIXboards` ADD `fileurl` tinyint(1) NOT NULL default '1';

CREATE TABLE IF NOT EXISTS `PREFIXpost_files` (
  `id` int(10) unsigned NOT NULL,
  `boardid` smallint(5) unsigned NOT NULL,
  `file` varchar(50) NOT NULL,
  `file_md5` char(32) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `file_original` varchar(255) NOT NULL,
  `file_size` int(20) NOT NULL default '0',
  `file_size_formatted` varchar(75) NOT NULL,
  `image_w` smallint(5) NOT NULL default '0',
  `image_h` smallint(5) NOT NULL default '0',
  `thumb_w` smallint(5) unsigned NOT NULL default '0',
  `thumb_h` smallint(5) unsigned NOT NULL default '0',
  `reviewed` tinyint(1) unsigned NOT NULL default '0',
  `IS_DELETED` tinyint(1) NOT NULL default '0',
  `timestamp` int(20) unsigned NOT NULL,
  `deleted_timestamp` int(20) NOT NULL default '0',
  KEY `id` (`id`),
  KEY `boardid` (`boardid`),
  KEY `file_md5` (`file_md5`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `PREFIXpost_files` SELECT `id`, `boardid`, `file`, `file_md5`, `file_type`, `file_original`, `file_size`, `file_size_formatted`, `image_w`, `image_h`, `thumb_w`, `thumb_h`, `reviewed`, `IS_DELETED`, `timestamp`, `deleted_timestamp` FROM `PREFIXposts`;

ALTER TABLE `PREFIXposts` DROP COLUMN `file`;
ALTER TABLE `PREFIXposts` DROP COLUMN `file_md5`;
ALTER TABLE `PREFIXposts` DROP COLUMN `file_type`;
ALTER TABLE `PREFIXposts` DROP COLUMN `file_original`;
ALTER TABLE `PREFIXposts` DROP COLUMN `file_size`;
ALTER TABLE `PREFIXposts` DROP COLUMN `file_size_formatted`;
ALTER TABLE `PREFIXposts` DROP COLUMN `image_w`;
ALTER TABLE `PREFIXposts` DROP COLUMN `image_h`;
ALTER TABLE `PREFIXposts` DROP COLUMN `thumb_w`;
ALTER TABLE `PREFIXposts` DROP COLUMN `thumb_h`;
ALTER TABLE `PREFIXposts` ADD `boardname` varchar(255) NOT NULL;


-- Edit the Staff --

ALTER TABLE `PREFIXstaff` ADD `access` tinyint(1) NOT NULL default '1';
ALTER TABLE `PREFIXstaff` ADD `suspended` int(1) NOT NULL;
ALTER TABLE `PREFIXstaff` MODIFY `salt` varchar(10);
UPDATE `PREFIXstaff` SET `access` = 8;


-- Edit the Embeds --

ALTER TABLE `PREFIXembeds` MODIFY `width` text(1000) COLLATE utf8_general_ci;
ALTER TABLE `PREFIXembeds` MODIFY `height` text(1000) COLLATE utf8_general_ci;

-- Add in options for Version --

CREATE TABLE IF NOT EXISTS `PREFIXoptions` (
 `version` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `PREFIXoptions` (`version`) VALUES ('1.0.3');

-- Drop then readd the ads table --

DROP TABLE `PREFIXads`;
CREATE TABLE IF NOT EXISTS `PREFIXads` (
  `id` smallint(1) unsigned NOT NULL,
  `position` varchar(10) NOT NULL,
  `disp` tinyint(1) NOT NULL,
  `boards` varchar(255) NOT NULL,
  `code` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `PREFIXads` (`id`, `position`, `disp`, `boards`, `code`) VALUES (1, 'sfwtop', 0, '', 'SFW Right Frame Top'), (2, 'sfwbot', 0, '', 'SFW Right Frame Bottom'), (3, 'nsfwtop', 0, '', 'NSFW Right Frame Top'), (4, 'nsfwbot', 0, '', 'NSFW Right Frame Bottom'), (5, 'sfwpost', 0, '', 'SFW Post Box'), (6, 'nsfwpost', 0, '', 'NSFW Post Box');

-- Add in the new Private Messaging system -- 

CREATE TABLE IF NOT EXISTS `pms` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `message` text NOT NULL,
  `to` varchar(255) NOT NULL,
  `from` varchar(255) NOT NULL,
  `read` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- Add in the new email option --

CREATE TABLE IF NOT EXISTS `PREFIXemail` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `email` text NOT NULL,
  `staffname` text NOT NULL,
  `postemail` tinyint(1) NOT NULL default '0',
  `reportemail` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- Edit the banned hashes table --

ALTER TABLE `PREFIXbannedhashes` ADD `dateadded` int(20) NOT NULL;
ALTER TABLE `PREFIXbannedhashes` ADD `addedby` varchar(75) NOT NULL;
-- All Done :) --


