CREATE TABLE IF NOT EXISTS `blog_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `guestname` varchar(20) DEFAULT NULL,
  `entryid` int(10) unsigned NOT NULL,
  `text` text NOT NULL,
  `date` int(10) unsigned NOT NULL,
  `ip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entryid` (`entryid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `blog_entries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `title` varchar(512) NOT NULL,
  `text` text NOT NULL,
  `date` int(10) unsigned NOT NULL,
  `ncomments` int(10) unsigned NOT NULL DEFAULT '0',
  `lastcmtid` int(10) unsigned NOT NULL DEFAULT '0',
  `lastcmtuser` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `ipbans` (
  `ip` varchar(50) NOT NULL,
  `reason` varchar(200) NOT NULL,
  PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `misc` (
  `field` varchar(32) NOT NULL,
  `value` varchar(1024) NOT NULL DEFAULT '',
  UNIQUE KEY `field` (`field`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `misc` (`field`, `value`) VALUES
('views', '0'),
('metadescr', 'A Kuriblog install'),
('sitename', 'Kuriblog'),
('botviews', '0'),
('metakeywords', 'kuriblog,kuribo,blog'),
('guestcomments', '0');

CREATE TABLE IF NOT EXISTS `themes` (
  `id` int(11) NOT NULL,
  `filename` varchar(32) NOT NULL,
  `name` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `themes` (`id`, `filename`, `name`) VALUES
(1, 'greennight', 'Green Night (Mega-Mario)');

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `password` varchar(64) NOT NULL,
  `powerlevel` tinyint(4) NOT NULL DEFAULT '0',
  `sex` tinyint(3) unsigned NOT NULL,
  `regdate` int(10) unsigned NOT NULL,
  `ip` varchar(50) NOT NULL,
  `theme` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;