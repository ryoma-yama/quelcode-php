CREATE TABLE `members` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `email` varchar(255) NOT NULL,
      `password` varchar(100) NOT NULL,
      `picture` varchar(255) NOT NULL,
      `created` datetime NOT NULL,
      `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `posts` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `message` text NOT NULL,
      `member_id` int(11) NOT NULL,
      `reply_post_id` int(11) NOT NULL,
      `retweet_member_id` int(11) NOT NULL,
      `retweet_post_id` int(11) NOT NULL,
      `created` datetime NOT NULL,
      `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `prechallenge3` (
      `value` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `prechallenge3` (`value`) VALUES (1),(17),(3),(13),(11),(7),(19),(5);

CREATE TABLE `likes` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `member_id` int(11) NOT NULL,
      `post_id` int(11) NOT NULL,
      PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;