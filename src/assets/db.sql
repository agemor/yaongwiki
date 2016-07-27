CREATE TABLE `yw_article` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `tags` varchar(500) NOT NULL,
  `hits` int(11) UNSIGNED NOT NULL,
  `today_hits` int(11) UNSIGNED NOT NULL,
  `permission` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `yw_article`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`),
  ADD FULLTEXT KEY `content` (`content`);

ALTER TABLE `yw_article`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `yw_file` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `size` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `uploader` varchar(100) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `yw_file`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `yw_file`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `yw_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip` varbinary(16) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `behavior` varchar(30) NOT NULL,
  `data` varchar(200) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `yw_log`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `yw_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `yw_revision` (
  `id` int(10) UNSIGNED NOT NULL,
  `article_title` varchar(200) NOT NULL,
  `article_id` int(10) UNSIGNED NOT NULL,
  `revision` int(10) UNSIGNED NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `snapshot_content` text NOT NULL,
  `snapshot_tags` varchar(1500) NOT NULL,
  `fluctuation` int(11) NOT NULL,
  `comment` varchar(400) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `yw_revision`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `yw_revision`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

  CREATE TABLE `yw_user` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `permission` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `code` varchar(50) DEFAULT NULL,
  `info` varchar(1500) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `yw_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `yw_user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE EVENT reset_populer
  ON SCHEDULE
    EVERY 1 DAY
    STARTS '2014-04-30 00:20:00' ON COMPLETION PRESERVE ENABLE 
  DO
    UPDATE `yw_article` SET `today_hits`=0;