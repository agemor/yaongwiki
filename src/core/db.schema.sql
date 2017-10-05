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

CREATE TABLE `[PREFIX]yaongwiki_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `value` varchar(50) CHARACTER SET utf8 NOT NULL,
  `default_value` varchar(50) CHARACTER SET utf8 NOT NULL,
  `comment` varchar(150) CHARACTER SET utf8 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `yaongwiki_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

INSERT INTO `[PREFIX]yaongwiki_settings` (`id`, `name`, `value`, `default_value`, `comment`) VALUES
(1, 'administrator', 'admin', 'admin', 'System administrator'),
(2, 'site_title', 'YaongWiki', 'YaongWiki', 'Site title'),
(3, 'site_description', 'Yet another wiki site', '', 'Site description'),
(4, 'site_keywords', '', '', 'Site keywords'),
(5, 'site_language', 'en_US', 'en_US', 'Site language'),
(6, 'site_theme', 'default', 'default', 'Site theme'),
(7, 'site_main_article', 'Main', 'Main', 'Site main article'),
(8, 'file_directory', 'uploads', 'uploads', 'File upload directory'),
(9, 'file_maximum_size', '10485760', '10485760', 'Upload file maximum size'),
(10, 'file_allowed_extensions', 'jpg, jpeg, png, gif, svg, tiff, bmp', 'jpg, jpeg, png, gif, svg, tiff, bmp', 'Upload file allowed extensions'),
(11, 'search_fulltext_enable', 'false', 'false', 'Fulltext search'),
(12, 'notice_email_address', 'noreply@yaongwiki.org', 'noreply@yaongwiki.org', 'Notice email address'),
(13, 'notice_email_name', 'YaongWiki', 'YaongWiki', 'Notice email name'),
(14, 'recaptcha_enable', 'false', 'false', 'ReCAPTCHA enable'),
(15, 'recaptcha_public_key', '', '', 'ReCAPTCHA public key'),
(16, 'recaptcha_private_key', '', '', 'ReCAPTCHA private key');

CREATE EVENT reset_populer
  ON SCHEDULE
    EVERY 1 DAY
    STARTS '2000-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE 
  DO
    UPDATE `[PREFIX]yaongwiki_articles` SET `today_hits`=0;