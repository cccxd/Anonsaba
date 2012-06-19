-- --------------------------------------------------------

--
-- Table structure for table `ads`
--

CREATE TABLE `PREFIXads` (
  `id` smallint(1) unsigned NOT NULL,
  `position` varchar(8) NOT NULL,
  `disp` tinyint(1) NOT NULL,
  `boards` varchar(255) NOT NULL,
  `code` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `PREFIXannouncements` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parentid` int(10) unsigned NOT NULL default '0',
  `subject` varchar(255) NOT NULL,
  `postedat` int(20) NOT NULL,
  `postedby` varchar(75) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `banlist`
--

CREATE TABLE `PREFIXbanlist` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `type` tinyint(1) NOT NULL default '0',
  `expired` tinyint(1) NOT NULL default '0',
  `allowread` tinyint(1) NOT NULL default '1',
  `ip` varchar(50) NOT NULL,
  `ipmd5` char(32) NOT NULL,
  `globalban` tinyint(1) NOT NULL default '0',
  `boards` varchar(255) NOT NULL,
  `by` varchar(75) NOT NULL,
  `at` int(20) NOT NULL,
  `until` int(20) NOT NULL,
  `reason` text NOT NULL,
  `staffnote` text NOT NULL,
  `appeal` text,
  `appealat` int(20) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bannedhashes`
--

CREATE TABLE `PREFIXbannedhashes` (
  `id` int(10) NOT NULL auto_increment,
  `md5` varchar(255) NOT NULL,
  `bantime` int(10) NOT NULL default '0',
  `description` text NOT NULL,
  `dateadded` int(20) NOT NULL,
  `addedby` varchar(75) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `blotter`
--

CREATE TABLE `PREFIXblotter` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `important` tinyint(1) NOT NULL,
  `at` int(20) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `boards`
--

CREATE TABLE `PREFIXboards` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `order` tinyint(5) NOT NULL default '0',
  `name` varchar(75) collate utf8_unicode_ci NOT NULL default '',
  `type` tinyint(1) NOT NULL default '0',
  `boardclass` tinyint(1) NOT NULL default '1',    
  `start` int(10) UNSIGNED NOT NULL ,
  `uploadtype` tinyint(1) NOT NULL default '0',
  `desc` varchar(75) collate utf8_unicode_ci NOT NULL default '',
  `image` varchar(255) collate utf8_unicode_ci NOT NULL,
  `section` tinyint(2) NOT NULL default '0',
  `max_files` tinyint(6) NOT NULL default '1',
  `maximagesize` int(20) NOT NULL default '1024000',
  `maxpages` int(20) NOT NULL default '11',
  `maxage` int(20) NOT NULL default '0',
  `markpage` tinyint(4) NOT NULL default '9',
  `maxreplies` int(5) NOT NULL default '200',
  `messagelength` int(10) NOT NULL default '8192',
  `createdon` int(20) NOT NULL,
  `locked` tinyint(1) NOT NULL default '0',
  `includeheader` text collate utf8_unicode_ci NOT NULL,
  `redirecttothread` tinyint(1) NOT NULL default '0',
  `anonymous` varchar(255) collate utf8_unicode_ci NOT NULL default 'Anonymous',
  `forcedanon` tinyint(1) NOT NULL default '0',
  `embeds_allowed` varchar(255) NOT NULL default '',
  `trial` tinyint(1) NOT NULL default '0',
  `popular` tinyint(1) NOT NULL default '0',
  `defaultstyle` varchar(50) character set latin1 NOT NULL default '',
  `locale` varchar(30) character set latin1 NOT NULL default '',
  `showid` tinyint(1) NOT NULL default '0',
  `compactlist` tinyint(1) NOT NULL default '0',
  `imgedit` tinyint(1) NOT NULL default '0',
  `enableemail` tinyint(1) NOT NULL default '0', 
  `enableads` tinyint(1) NOT NULL default '1',    
  `enablereporting` tinyint(1) NOT NULL default '1',
  `enablecaptcha` tinyint(1) NOT NULL default '0',
  `enablenofile` tinyint(1) NOT NULL default '0',
  `enablearchiving` tinyint(1) NOT NULL default '0',
  `enablecatalog` tinyint(1) NOT NULL default '1',
  `loadbalanceurl` varchar(255) character set latin1 NOT NULL default '',
  `loadbalancepassword` varchar(255) character set latin1 NOT NULL default '',
`fileurl` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `board_filetypes`
--

CREATE TABLE `PREFIXboard_filetypes` (
  `boardid` tinyint(5) NOT NULL default '0',
  `typeid` mediumint(5) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email`
--

CREATE TABLE `PREFIXemail` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `email` text NOT NULL,
  `staffname` text NOT NULL,
  `postemail` tinyint(1) NOT NULL default '0',
  `reportemail` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `embeds`
--

CREATE TABLE `PREFIXembeds` (
  `id` tinyint(5) unsigned NOT NULL auto_increment,
  `filetype` varchar(3) NOT NULL,
  `name` varchar(255) NOT NULL,
  `videourl` varchar(510) NOT NULL,
  `width` text(1000) collate utf8_general_ci NOT NULL,
  `height` text(1000) collate utf8_general_ci NOT NULL,
  `code` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `PREFIXevents` (
  `name` varchar(255) NOT NULL,
  `at` int(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `filetypes`
--

CREATE TABLE `PREFIXfiletypes` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `filetype` varchar(255) NOT NULL,
  `mime` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `image_w` int(7) NOT NULL default '0',
  `image_h` int(7) NOT NULL default '0',
  `force_thumb` int(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `front`
--

CREATE TABLE `PREFIXfront` (
	`id` smallint(5) unsigned NOT NULL auto_increment,
	`page` smallint(1) unsigned NOT NULL default '0',
	`order` smallint(5) unsigned NOT NULL default '0',
	`subject` varchar(255) NOT NULL,
	`message` text NOT NULL,
	`timestamp` int(20) NOT NULL default '0',
	`poster` varchar(75) NOT NULL,
	`email` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `loginattempts`
--

CREATE TABLE `PREFIXloginattempts` (
  `username` varchar(255) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `timestamp` int(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `modlog`
--

CREATE TABLE `PREFIXmodlog` (
  `entry` text NOT NULL,
  `user` varchar(255) NOT NULL,
  `category` tinyint(2) NOT NULL default '0',
  `timestamp` int(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `module_settings`
--

CREATE TABLE `PREFIXmodule_settings` (
  `module` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text character set utf8 collate utf8_unicode_ci NOT NULL,
  `type` varchar(255) NOT NULL default 'string'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `PREFIXoptions` (
 `version` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `PREFIXposts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `boardid` smallint(5) unsigned NOT NULL,
  `parentid` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL,
  `tripcode` varchar(30) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `ip` varchar(75) NOT NULL,
  `ipmd5` char(32) NOT NULL,
  `tag` varchar(5) NOT NULL,
  `timestamp` int(20) unsigned NOT NULL,
  `stickied` tinyint(1) NOT NULL default '0',
  `locked` tinyint(1) NOT NULL default '0',
  `posterauthority` tinyint(1) NOT NULL default '0',
  `reviewed` tinyint(1) unsigned NOT NULL default '0',
  `deleted_timestamp` int(20) NOT NULL default '0',
  `IS_DELETED` tinyint(1) NOT NULL default '0',
  `bumped` int(20) unsigned NOT NULL default '0',
  `boardname` varchar(255) NOT NULL,
  PRIMARY KEY  (`boardid`,`id`),
  KEY `parentid` (`parentid`),
  KEY `bumped` (`bumped`),
  KEY `stickied` (`stickied`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `post_files`
--

CREATE TABLE `PREFIXpost_files` (
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

-- --------------------------------------------------------

--
-- Table structure for table `pms`
--

CREATE TABLE `pms` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `message` text NOT NULL,
  `to` varchar(255) NOT NULL,
  `from` varchar(255) NOT NULL,
  `read` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `PREFIXreports` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `cleared` tinyint(1) NOT NULL default '0',
  `board` varchar(255) NOT NULL,
  `postid` int(20) NOT NULL,
  `when` int(20) NOT NULL,
  `ip` varchar(75) NOT NULL,
  `reason` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `PREFIXsections` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `order` tinyint(3) NOT NULL default '0',
  `hidden` tinyint(1) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '0',
  `abbreviation` varchar(10) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `PREFIXstaff` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(10) NOT NULL,
  `type` tinyint(1) NOT NULL default '0',
  `boards` text,
  `addedon` int(20) NOT NULL,
  `lastactive` int(20) NOT NULL default '0',
  `suspended` int(1) NOT NULL,
  `access` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `watchedthreads`
--

CREATE TABLE `PREFIXwatchedthreads` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `threadid` int(20) NOT NULL,
  `board` varchar(255) NOT NULL,
  `ip` char(15) NOT NULL,
  `lastsawreplyid` int(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wordfilter`
--

CREATE TABLE `PREFIXwordfilter` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `word` varchar(75) NOT NULL,
  `replacedby` varchar(75) NOT NULL,
  `boards` text NOT NULL,
  `time` int(20) NOT NULL,
  `regex` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



INSERT INTO `PREFIXads` (`id`, `position`, `disp`, `boards`, `code`) VALUES (1, 'sfwtop', 0, '', 'SFW Right Frame Top'), (2, 'sfwbot', 0, '', 'SFW Right Frame Bottom'), (3, 'nsfwtop', 0, '', 'NSFW Right Frame Top'), (4, 'nsfwbot', 0, '', 'NSFW Right Frame Bottom') ;
INSERT INTO `PREFIXoptions` (`version`) VALUES ('1.0.3');
INSERT INTO `PREFIXfiletypes` (`filetype`, `force_thumb`) VALUES ('jpg', 0), ('gif', 0), ('png', 0) ;
INSERT INTO `PREFIXevents` (`name`, `at`) VALUES ('pingback', 0), ('sitemap', 0);
INSERT INTO `PREFIXembeds` (`filetype`, `name`, `videourl`, `width`, `height`, `code`) VALUES ('you', 'Youtube', 'http://www.youtube.com/watch?v=', 200, 164, '<object type="application/x-shockwave-flash" width="SET_WIDTH" height="SET_HEIGHT" data="http://www.youtube.com/v/EMBED_ID"> <param name="movie" value="http://www.youtube.com/v/EMBED_ID" /> </object>'), ('goo', 'Google', 'http://video.google.com/videoplay?docid=', 200, 164, '<embed width="SET_WIDTH" height="SET_HEIGHT" id="VideoPlayback" type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docId=EMBED_ID"></embed>') ;
