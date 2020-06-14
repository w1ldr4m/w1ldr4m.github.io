--
-- Структура таблицы `anketsbot_files`
--

CREATE TABLE IF NOT EXISTS `anketsbot_files` (
  `id` int(111) NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL,
  `file_id` tinytext NOT NULL,
  `key_name` varchar(32) NOT NULL,
  `parent` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

--
-- Структура таблицы `anketsbot_results`
--

CREATE TABLE IF NOT EXISTS `anketsbot_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `worksheet_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'new',
  `hash` varchar(8) NOT NULL,
  `create_at` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

--
-- Структура таблицы `anketsbot_result_items`
--

CREATE TABLE IF NOT EXISTS `anketsbot_result_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `result_id` int(11) NOT NULL,
  `step_id` int(11) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `preview` tinytext NOT NULL,
  `body` text,
  `type` varchar(20) NOT NULL,
  `file_id` varchar(100) DEFAULT NULL,
  `relevant` tinyint(1) NOT NULL DEFAULT '1',
  `hash` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

--
-- Структура таблицы `anketsbot_settings`
--

CREATE TABLE IF NOT EXISTS `anketsbot_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token_bot` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username_bot` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_bot` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Структура таблицы `anketsbot_users`
--

CREATE TABLE IF NOT EXISTS `anketsbot_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telegram_id` bigint(20) DEFAULT NULL,
  `first_name` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lang` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ban` int(1) NOT NULL DEFAULT '0',
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Структура таблицы `anketsbot_worksheets`
--

CREATE TABLE IF NOT EXISTS `anketsbot_worksheets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `hash` varchar(8) NOT NULL,
  `type` tinyint(2) NOT NULL DEFAULT '0',
  `hide` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

--
-- Структура таблицы `anketsbot_worksheet_steps`
--

CREATE TABLE IF NOT EXISTS `anketsbot_worksheet_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `parent_worksheet` int(11) DEFAULT NULL,
  `parent_step` int(11) DEFAULT NULL,
  `parent_btn` int(11) DEFAULT NULL,
  `user_body` text NOT NULL,
  `preview_body` tinytext NOT NULL,
  `type` varchar(10) NOT NULL,
  `file_id` tinytext,
  `expect` varchar(10) NOT NULL DEFAULT 'text',
  `sort` int(11) NOT NULL,
  `group_list` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

--
-- Структура таблицы `anketsbot_worksheet_step_btns`
--

CREATE TABLE IF NOT EXISTS `anketsbot_worksheet_step_btns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_worksheet` int(11) NOT NULL,
  `parent_step` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `sort` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
