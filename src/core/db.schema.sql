CREATE TABLE `[PREFIX]yaongwiki_articles` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `tags` varchar(500) NOT NULL,
  `revisions` int(11) NOT NULL DEFAULT '0',
  `latest_revision_id` int(11) NOT NULL,
  `hits` int(11) UNSIGNED NOT NULL,
  `today_hits` int(11) UNSIGNED NOT NULL,
  `permission` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `[PREFIX]yaongwiki_articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`),
  ADD FULLTEXT KEY `content` (`content`);

ALTER TABLE `[PREFIX]yaongwiki_articles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `[PREFIX]yaongwiki_files` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `size` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `uploader` varchar(100) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `[PREFIX]yaongwiki_files`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `[PREFIX]yaongwiki_files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `[PREFIX]yaongwiki_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip` varbinary(16) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `behavior` varchar(30) NOT NULL,
  `data` varchar(200) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `[PREFIX]yaongwiki_logs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `[PREFIX]yaongwiki_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `[PREFIX]yaongwiki_revisions` (
  `id` int(10) UNSIGNED NOT NULL,
  `predecessor_id` int(11) NOT NULL,
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

ALTER TABLE `[PREFIX]yaongwiki_revisions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `[PREFIX]yaongwiki_revisions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

  CREATE TABLE `[PREFIX]yaongwiki_users` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `permission` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `total_contributions` int(11) NOT NULL DEFAULT '0',
  `code` varchar(50) DEFAULT NULL,
  `info` varchar(1500) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `[PREFIX]yaongwiki_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `[PREFIX]yaongwiki_users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE EVENT reset_populer
  ON SCHEDULE
    EVERY 1 DAY
    STARTS '2000-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE 
  DO
    UPDATE `[PREFIX]yaongwiki_articles` SET `today_hits`=0;