-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Дек 09 2016 г., 01:34
-- Версия сервера: 10.1.13-MariaDB
-- Версия PHP: 7.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `oakcms`
--

-- --------------------------------------------------------

--
-- Структура таблицы `auth_assignment`
--

CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `auth_assignment`
--

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('administrator', '1', 1481243576);

-- --------------------------------------------------------

--
-- Структура таблицы `auth_item`
--

CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `auth_item`
--

INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
('administrator', 1, 'Administrator', NULL, NULL, 1460046593, 1460046593),
('manager', 1, 'Manager', NULL, NULL, 1460046592, 1460046592),
('permAdminPanel', 2, 'Permission Admin Panel', NULL, NULL, 1460046593, 1460046593),
('user', 1, 'User', NULL, NULL, 1460046592, 1460046592);

-- --------------------------------------------------------

--
-- Структура таблицы `auth_item_child`
--

CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `auth_item_child`
--

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
('administrator', 'manager'),
('manager', 'permAdminPanel'),
('manager', 'user');

-- --------------------------------------------------------

--
-- Структура таблицы `auth_rule`
--

CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `content_articles`
--

CREATE TABLE `content_articles` (
  `id` int(11) NOT NULL,
  `create_user_id` int(11) NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `published_at` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `image` varchar(300) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `comment_status` int(11) NOT NULL DEFAULT '1',
  `create_user_ip` varchar(20) NOT NULL,
  `access_type` int(11) NOT NULL DEFAULT '1',
  `category_id` int(11) DEFAULT NULL,
  `main_image` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `content_articles`
--

INSERT INTO `content_articles` (`id`, `create_user_id`, `update_user_id`, `published_at`, `created_at`, `updated_at`, `image`, `status`, `comment_status`, `create_user_ip`, `access_type`, `category_id`, `main_image`) VALUES
(1, 1, 1, 1458900000, 1464815424, 1474735966, '57e6ac2260217.jpg', 1, 1, '127.0.0.1', 1, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `content_articles_lang`
--

CREATE TABLE `content_articles_lang` (
  `id` int(11) NOT NULL,
  `content_articles_id` int(11) NOT NULL,
  `slug` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_keywords` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `settings` text COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(10) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `content_articles_lang`
--

INSERT INTO `content_articles_lang` (`id`, `content_articles_id`, `slug`, `title`, `description`, `content`, `link`, `meta_title`, `meta_keywords`, `meta_description`, `settings`, `language`) VALUES
(2, 1, 'testovaa-stata', 'Тестова стаття', '', '<p>12</p>\r\n', '', 'Еше', '', '', '{"show_title":{"value":true,"type":"checkbox"},"link_titles":{"value":true,"type":"checkbox"},"show_intro":{"value":false,"type":"checkbox"},"show_category":{"value":false,"type":"checkbox"},"link_category":{"value":false,"type":"checkbox"},"show_parent_category":{"value":false,"type":"checkbox"},"link_parent_category":{"value":false,"type":"checkbox"},"show_author":{"value":false,"type":"checkbox"},"link_author":{"value":false,"type":"checkbox"},"show_create_date":{"value":false,"type":"checkbox"},"show_modify_date":{"value":false,"type":"checkbox"},"show_publish_date":{"value":false,"type":"checkbox"},"show_hits":{"value":false,"type":"checkbox"}}', 'ru-RU');

-- --------------------------------------------------------

--
-- Структура таблицы `content_articles_medias`
--

CREATE TABLE `content_articles_medias` (
  `id` int(11) UNSIGNED NOT NULL,
  `content_articles_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `media_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` int(3) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `content_articles_medias`
--

INSERT INTO `content_articles_medias` (`id`, `content_articles_id`, `media_id`, `ordering`) VALUES
(9, 3, 9, 0),
(10, 3, 10, 0),
(13, 3, 13, 0),
(14, 3, 14, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `content_category`
--

CREATE TABLE `content_category` (
  `id` int(11) NOT NULL COMMENT 'Unique tree node identifier',
  `status` smallint(6) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `tree` int(11) DEFAULT NULL COMMENT 'Tree root identifier',
  `lft` int(11) NOT NULL COMMENT 'Nested set left property',
  `rgt` int(11) NOT NULL COMMENT 'Nested set right property',
  `depth` smallint(5) NOT NULL COMMENT 'Nested set level / depth',
  `icon` varchar(255) DEFAULT NULL COMMENT 'The icon to use for the node',
  `icon_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Icon Type: 1 = CSS Class, 2 = Raw Markup',
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Whether the node is active (will be set to false on deletion)',
  `selected` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether the node is selected/checked by default',
  `disabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether the node is enabled',
  `readonly` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether the node is read only (unlike disabled - will allow toolbar actions)',
  `visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Whether the node is visible',
  `collapsed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether the node is collapsed by default',
  `movable_u` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Whether the node is movable one position up',
  `movable_d` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Whether the node is movable one position down',
  `movable_l` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Whether the node is movable to the left (from sibling to parent)',
  `movable_r` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Whether the node is movable to the right (from sibling to child)',
  `removable` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Whether the node is removable (any children below will be moved as siblings before deletion)',
  `removable_all` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether the node is removable along with descendants',
  `order` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `children` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `content_category`
--

INSERT INTO `content_category` (`id`, `status`, `created_at`, `updated_at`, `tree`, `lft`, `rgt`, `depth`, `icon`, `icon_type`, `active`, `selected`, `disabled`, `readonly`, `visible`, `collapsed`, `movable_u`, `movable_d`, `movable_l`, `movable_r`, `removable`, `removable_all`, `order`, `parent`, `children`) VALUES
(1, 1, 1465405031, 1472547378, 1, 1, 2, 0, '', 1, 1, 0, 0, 0, 1, 0, 1, 1, 1, 1, 1, 0, 1, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `content_category_lang`
--

CREATE TABLE `content_category_lang` (
  `id` int(11) NOT NULL,
  `content_category_id` int(11) NOT NULL,
  `slug` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `meta_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_keywords` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `settings` text COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(10) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `content_category_lang`
--

INSERT INTO `content_category_lang` (`id`, `content_category_id`, `slug`, `title`, `content`, `meta_title`, `meta_keywords`, `meta_description`, `settings`, `language`) VALUES
(1, 1, 'novosti', 'Новости', '', 'Все новости', '', '', '', 'ru-RU'),
(2, 1, 'news', 'News', '', '', '', '', '', 'en-US'),
(3, 2, '', 'Подкатегория', '', '', '', '', '', 'ru-ru'),
(4, 3, '2', 'Подкатегория 2', '', '', '', '', '', 'ru-ru');

-- --------------------------------------------------------

--
-- Структура таблицы `content_pages`
--

CREATE TABLE `content_pages` (
  `id` int(11) NOT NULL,
  `layout` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `background_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icon_image` text COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `content_pages`
--

INSERT INTO `content_pages` (`id`, `layout`, `background_image`, `icon_image`, `status`, `created_at`, `updated_at`) VALUES
(1, '', '', '', 1, 1476250048, 1476250048);

-- --------------------------------------------------------

--
-- Структура таблицы `content_pages_lang`
--

CREATE TABLE `content_pages_lang` (
  `id` int(11) NOT NULL,
  `content_pages_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subtitle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title_h1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `meta_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_keywords` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `settings` text COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(10) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `content_pages_lang`
--

INSERT INTO `content_pages_lang` (`id`, `content_pages_id`, `title`, `subtitle`, `title_h1`, `slug`, `description`, `content`, `meta_title`, `meta_keywords`, `meta_description`, `settings`, `language`) VALUES
(1, 1, 'Часто задаваемые вопросы', '', 'Часто задаваемые вопросы', 'questions', '', '', '', '', '', '[]', 'ru-ru');

-- --------------------------------------------------------

--
-- Структура таблицы `content_tags`
--

CREATE TABLE `content_tags` (
  `id` int(11) NOT NULL,
  `frequency` int(10) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `content_tag_assn`
--

CREATE TABLE `content_tag_assn` (
  `content_id` int(11) NOT NULL,
  `content_tags_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `db_state`
--

CREATE TABLE `db_state` (
  `id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `field`
--

CREATE TABLE `field` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `type` text COLLATE utf8_unicode_ci,
  `options` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `relation_model` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `field`
--

INSERT INTO `field` (`id`, `name`, `slug`, `category_id`, `type`, `options`, `description`, `relation_model`) VALUES
(1, 'Цвет', 'color', 1, 'select', NULL, '', 'app\\modules\\shop\\models\\Product');

-- --------------------------------------------------------

--
-- Структура таблицы `field_category`
--

CREATE TABLE `field_category` (
  `id` int(11) NOT NULL,
  `name` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `field_category`
--

INSERT INTO `field_category` (`id`, `name`, `sort`) VALUES
(1, 'Товары 1', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `field_value`
--

CREATE TABLE `field_value` (
  `id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `value` text COLLATE utf8_unicode_ci,
  `numeric_value` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `field_value`
--

INSERT INTO `field_value` (`id`, `field_id`, `variant_id`, `item_id`, `value`, `numeric_value`) VALUES
(1, 1, 3, 2, 'Белый', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `field_variant`
--

CREATE TABLE `field_variant` (
  `id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `numeric_value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `field_variant`
--

INSERT INTO `field_variant` (`id`, `field_id`, `value`, `numeric_value`) VALUES
(1, 1, 'Белый', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `filter`
--

CREATE TABLE `filter` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(155) COLLATE utf8_unicode_ci NOT NULL,
  `sort` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `relation_field_name` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_filter` enum('yes','no') COLLATE utf8_unicode_ci DEFAULT 'no',
  `type` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `relation_field_value` text COLLATE utf8_unicode_ci COMMENT 'PHP serialize'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `filter_relation_value`
--

CREATE TABLE `filter_relation_value` (
  `id` int(11) NOT NULL,
  `filter_id` int(11) NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `filter_value`
--

CREATE TABLE `filter_value` (
  `id` int(11) NOT NULL,
  `filter_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `filter_variant`
--

CREATE TABLE `filter_variant` (
  `id` int(11) NOT NULL,
  `filter_id` int(11) NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `numeric_value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `image`
--

CREATE TABLE `image` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `filePath` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
  `itemId` int(20) NOT NULL,
  `isMain` tinyint(1) DEFAULT NULL,
  `modelName` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `urlAlias` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `gallery_id` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort` int(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `image`
--

INSERT INTO `image` (`id`, `title`, `alt`, `filePath`, `itemId`, `isMain`, `modelName`, `urlAlias`, `description`, `gallery_id`, `sort`) VALUES
(3, NULL, NULL, 'Products/Product2/5afd1d.jpg', 2, 1, 'Product', 'da3c67cde3-1', NULL, NULL, NULL),
(4, NULL, NULL, 'Products/Product2/748397.jpg', 2, NULL, 'Product', 'ed95f0b23c-3', NULL, NULL, NULL),
(5, NULL, NULL, 'Products/Product2/41115a.jpg', 2, NULL, 'Product', 'a802bf451d-2', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `language`
--

CREATE TABLE `language` (
  `language_id` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `name_ascii` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `language`
--

INSERT INTO `language` (`language_id`, `language`, `country`, `url`, `name`, `name_ascii`, `status`) VALUES
('be-BY', 'be', 'by', 'by', 'Беларуская', 'Belarusian', 1),
('en-US', 'en', 'us', 'en', 'English (US)', 'English (US)', 1),
('ru-RU', 'ru', 'ru', 'ru', 'Русский', 'Russian', 1),
('uk-UA', 'uk', 'ua', 'ua', 'Українська', 'Ukrainian', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `language_source`
--

CREATE TABLE `language_source` (
  `id` int(11) NOT NULL,
  `category` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `language_source`
--

INSERT INTO `language_source` (`id`, `category`, `message`) VALUES
(1, 'admin', 'Translatable element'),
(2, 'admin', 'Create'),
(3, 'admin', 'Update'),
(4, 'admin', 'Save & Continue Edit'),
(5, 'admin', 'Control'),
(6, 'admin', 'Back'),
(7, 'admin', 'System'),
(8, 'admin', 'Settings'),
(9, 'admin', 'Modules'),
(10, 'admin', 'Cache'),
(11, 'admin', 'File Manager'),
(12, 'admin', 'Flush cache'),
(13, 'admin', 'Clear assets'),
(14, 'admin', 'Main navigation'),
(15, 'admin', 'Delete'),
(16, 'language', 'Language'),
(17, 'language', 'Country'),
(18, 'language', 'Translation'),
(19, 'language', 'Text'),
(20, 'language', 'Multilingual'),
(21, 'language', 'Category'),
(22, 'admin', 'Live edit'),
(23, 'admin', 'Admin panel');

-- --------------------------------------------------------

--
-- Структура таблицы `language_translate`
--

CREATE TABLE `language_translate` (
  `id` int(11) NOT NULL DEFAULT '0',
  `language` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `translation` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `language_translate`
--

INSERT INTO `language_translate` (`id`, `language`, `translation`) VALUES
(1, 'ru-RU', 'Переводимое поле'),
(2, 'ru-RU', 'Создать'),
(3, 'ru-RU', 'Обновить'),
(4, 'ru-RU', 'Сохранить и вернуться'),
(5, 'ru-RU', 'Контроль'),
(6, 'ru-RU', 'Назад'),
(7, 'ru-RU', 'Система'),
(8, 'ru-RU', 'Настройки'),
(9, 'ru-RU', 'Модули'),
(10, 'ru-RU', 'Кеш'),
(11, 'ru-RU', 'Файловый менеджер'),
(12, 'ru-RU', 'Очистить кеш'),
(13, 'ru-RU', 'Очистить активы'),
(14, 'ru-RU', 'Главное меню'),
(15, 'ru-RU', 'Удалить'),
(16, 'ru-RU', 'Язык'),
(17, 'ru-RU', 'Страна'),
(18, 'ru-RU', 'Перевод'),
(19, 'ru-RU', 'Текст'),
(20, 'ru-RU', 'Мультиязычность'),
(21, 'ru-RU', 'Категория'),
(22, 'ru-RU', 'Жывое редактирование'),
(23, 'ru-RU', 'Администрирование');

-- --------------------------------------------------------

--
-- Структура таблицы `medias`
--

CREATE TABLE `medias` (
  `media_id` int(11) UNSIGNED NOT NULL,
  `file_title` char(126) NOT NULL DEFAULT '',
  `file_description` char(254) NOT NULL DEFAULT '',
  `file_meta` char(254) NOT NULL DEFAULT '',
  `file_mimetype` char(64) NOT NULL DEFAULT '',
  `file_type` char(32) NOT NULL DEFAULT '',
  `file_url` varchar(900) NOT NULL DEFAULT '',
  `file_url_thumb` varchar(900) NOT NULL DEFAULT '',
  `file_params` varchar(17500) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Additional Images and Files which are assigned to products';

-- --------------------------------------------------------

--
-- Структура таблицы `menu_item`
--

CREATE TABLE `menu_item` (
  `id` int(11) NOT NULL,
  `menu_type_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `translation_id` int(11) DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `language` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alias` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `path` varchar(2048) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `link` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `link_type` smallint(6) DEFAULT NULL,
  `link_weight` smallint(6) DEFAULT '0',
  `link_params` text COLLATE utf8_unicode_ci,
  `layout_path` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_rule` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `metakey` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `metadesc` varchar(2048) COLLATE utf8_unicode_ci DEFAULT NULL,
  `robots` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `secure` tinyint(1) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  `level` smallint(6) UNSIGNED DEFAULT NULL,
  `ordering` int(11) UNSIGNED DEFAULT NULL,
  `hits` bigint(20) UNSIGNED DEFAULT NULL,
  `lock` bigint(20) UNSIGNED DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `menu_item` (menu_type_id, parent_id, translation_id, status, language, title, alias, path, note, link, link_type, link_weight, link_params, layout_path, access_rule, metakey, metadesc, robots, secure, created_at, updated_at, created_by, updated_by, lft, rgt, level, ordering, hits, `lock`) VALUES (0, null, null, 1, '', 'Root', null, null, null, null, null, 0, null, null, null, null, null, null, null, 1476552518, null, 1, 1, 1, 22, 1, 1, null, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `menu_type`
--

CREATE TABLE `menu_type` (
  `id` int(11) NOT NULL,
  `title` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alias` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `path` varchar(2048) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `lock` bigint(20) UNSIGNED DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `menu_type`
--

INSERT INTO `menu_type` (`id`, `title`, `alias`, `path`, `note`, `created_at`, `updated_at`, `created_by`, `updated_by`, `lock`) VALUES
(1, 'Главное меню', '', NULL, 'Главное меню', 1476543986, 1476543986, 1, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `language` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `translation` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1459877391),
('m000003_000000_grom_menu_create_tables', 1476543704),
('m140209_132017_init', 1459877401),
('m140403_174025_create_account_table', 1459877403),
('m140504_113157_update_tables', 1459877410),
('m140504_130429_create_token_table', 1459877412),
('m140506_102106_rbac_init', 1460046568),
('m140622_111540_create_image_table', 1477651060),
('m140830_171933_fix_ip_field', 1459877414),
('m140830_172703_change_account_table_name', 1459877415),
('m141002_030233_translate_manager', 1463306003),
('m141106_185632_log_init', 1459891778),
('m141222_110026_update_ip_field', 1459877416),
('m141222_135246_alter_username_length', 1459877417),
('m150207_210500_i18n_init', 1459898106),
('m150614_103145_update_social_account_table', 1459877422),
('m150623_212711_fix_username_notnull', 1459877424),
('m160405_215653_create_user_table', 1459893557),
('m160513_121415_Mass', 1477659178),
('m160513_232135_create_seo_table', 1463182594),
('m160521_112618_Mass', 1476477335),
('m160613_134415_Mass', 1477659224);

-- --------------------------------------------------------

--
-- Структура таблицы `modules_modules`
--

CREATE TABLE `modules_modules` (
  `module_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `class` varchar(128) NOT NULL,
  `bootstrapClass` varchar(128) NOT NULL,
  `isFrontend` tinyint(1) NOT NULL,
  `controllerNamespace` varchar(500) NOT NULL,
  `viewPath` varchar(500) NOT NULL,
  `isAdmin` tinyint(1) NOT NULL,
  `AdminControllerNamespace` varchar(500) NOT NULL,
  `AdminViewPath` varchar(500) NOT NULL,
  `title` varchar(128) NOT NULL,
  `icon` varchar(32) NOT NULL,
  `settings` text NOT NULL,
  `order` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `modules_modules`
--

INSERT INTO `modules_modules` (`module_id`, `name`, `class`, `isFrontend`, `controllerNamespace`, `viewPath`, `isAdmin`, `AdminControllerNamespace`, `AdminViewPath`, `title`, `icon`, `settings`, `order`, `status`) VALUES
(1, 'seo', 'app\\modules\\seo\\Module', 0, '', '', 1, '', '', 'Seo', '', '{"title":{"type":"textInput","value":"OAKCMS"}}', 9, 1),
(2, 'content', 'app\\modules\\content\\Module', 1, '', '', 1, '', '', 'Content', '', '{"show_title":{"type":"checkbox","value":false},"link_titles":{"type":"checkbox","value":false},"show_intro":{"type":"checkbox","value":false},"show_category":{"type":"checkbox","value":false},"link_category":{"type":"checkbox","value":false},"show_parent_category":{"type":"checkbox","value":false},"link_parent_category":{"type":"checkbox","value":false},"show_author":{"type":"checkbox","value":false},"link_author":{"type":"checkbox","value":false},"show_create_date":{"type":"checkbox","value":false},"show_modify_date":{"type":"checkbox","value":false},"show_publish_date":{"type":"checkbox","value":false},"show_hits":{"type":"checkbox","value":false}}', 1, 1),
(3, 'user', 'app\\modules\\user\\Module', 1, '', '', 1, '', '', 'User manager', 'fa fa-user', '{"title":{"type":"textInput","value":"OAKCMS"}}', 4, 1),
(4, 'language', 'app\\modules\\language\\Module', 0, '', '', 1, '', '', 'Language', 'fa fa-flag', '[]', 5, 1),
(5, 'text', 'app\\modules\\text\\Module', 0, '', '', 1, '', '', 'Text', '', '[]', 6, 1),
(6, 'shop', 'app\\modules\\shop\\Module', 1, '', '', 1, '', '', 'Shop', '', '[]', 2, 1),
(7, 'menu', 'app\\modules\\menu\\Module', 1, '', '', 1, '', '', 'Menu', '', '[]', 7, 1),
(8, 'widgets', 'app\\modules\\widgets\\Module', 1, '', '', 1, '', '', 'Widgets', '', '{"googlemapseapikey":{"value":"","type":"textInput"},"disable_frontend_style":{"value":"0","type":"checkbox"}}', 8, 1),
(9, 'system', 'app\\modules\\system\\Module', 1, '', '', 1, '', '', 'System', '', '{"BackCallEmail":{"type":"textInput","value":"script@email.ua"},"BackCallSubject":{"type":"textInput","value":"\\u041d\\u043e\\u0432\\u0430\\u044f \\u0437\\u0430\\u044f\\u0432\\u043a\\u0430 \\u0437 \\u0441\\u0430\\u0439\\u0442\\u0430 falconcity.kz"},"BackCallSuccessText":{"type":"textInput","value":"\\u0412\\u0430\\u0448 \\u0437\\u0430\\u043f\\u0440\\u043e\\u0441 \\u043f\\u043e\\u043b\\u0443\\u0447\\u0435\\u043d!<br>\\u0412 \\u0431\\u043b\\u0438\\u0436\\u0430\\u0439\\u0448\\u0435\\u0435 \\u0432\\u0440\\u0435\\u043c\\u044f \\u043d\\u0430\\u0448 \\u043c\\u0435\\u043d\\u0435\\u0434\\u0436\\u0435\\u0440 \\u0441\\u0432\\u044f\\u0436\\u0438\\u0442\\u0441\\u044f \\u0441 \\u0412\\u0430\\u043c\\u0438!"},"SocialInstagramLink":{"type":"textInput","value":"#"},"SocialTwitterLink":{"type":"textInput","value":"#"},"SocialFacebookLink":{"type":"textInput","value":"#"},"FrequentlyAskedQuestionsLink":{"type":"textInput","value":"#"}}', 0, 1),
(10, 'cart', 'app\\modules\\cart\\Module', 1, '', '', 1, '', '', 'Cart', '', '[]', 10, 1),
(11, 'gallery', 'app\\modules\\gallery\\Module', 1, '', '', 1, '', '', 'Gallery', '', '[]', 11, 1),
(14, 'relations', 'app\\modules\\relations\\Module', 1, '', '', 1, '', '', 'Relations', '', '[]', 13, 1),
(13, 'packagist', 'app\\modules\\packagist\\Module', 0, '', '', 1, '', '', 'Packagist', '', '{"git_hub_username":{"type":"textInput","value":""},"git_hub_password":{"type":"textInput","value":""}}', 12, 1),
(15, 'field', 'app\\modules\\field\\Module', 1, '', '', 1, '', '', 'Fields', '', '[]', 3, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `seo_items`
--

CREATE TABLE `seo_items` (
  `id` int(11) NOT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `keywords` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `canonical` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `seo_items`
--

INSERT INTO `seo_items` (`id`, `link`, `title`, `keywords`, `description`, `canonical`, `status`) VALUES
(1, '/', 'Home', '1', '', '', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `shop_category`
--

CREATE TABLE `shop_category` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `text` text COLLATE utf8_unicode_ci,
  `image` text COLLATE utf8_unicode_ci,
  `sort` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `shop_category`
--

INSERT INTO `shop_category` (`id`, `parent_id`, `name`, `code`, `slug`, `text`, `image`, `sort`) VALUES
(1, NULL, 'Тестовая', NULL, '12', '<p>21</p>\r\n', NULL, 12);

-- --------------------------------------------------------

--
-- Структура таблицы `shop_incoming`
--

CREATE TABLE `shop_incoming` (
  `id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `content` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `shop_outcoming`
--

CREATE TABLE `shop_outcoming` (
  `id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `shop_price`
--

CREATE TABLE `shop_price` (
  `id` int(11) NOT NULL,
  `code` varchar(155) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(155) COLLATE utf8_unicode_ci NOT NULL,
  `price` decimal(11,2) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `available` enum('yes','no') COLLATE utf8_unicode_ci DEFAULT 'yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `shop_price_type`
--

CREATE TABLE `shop_price_type` (
  `id` int(11) NOT NULL,
  `name` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `sort` int(11) DEFAULT NULL,
  `condition` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `shop_price_type`
--

INSERT INTO `shop_price_type` (`id`, `name`, `sort`, `condition`) VALUES
(1, 'Основная цена', NULL, NULL),
(2, 'Акционная цена', NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `shop_producer`
--

CREATE TABLE `shop_producer` (
  `id` int(11) NOT NULL,
  `code` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image` text COLLATE utf8_unicode_ci,
  `text` text COLLATE utf8_unicode_ci,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `shop_product`
--

CREATE TABLE `shop_product` (
  `id` int(11) NOT NULL,
  `category_id` int(10) DEFAULT NULL,
  `producer_id` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `related_products` text COLLATE utf8_unicode_ci COMMENT 'PHP serialize',
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` decimal(11,2) DEFAULT NULL,
  `text` text COLLATE utf8_unicode_ci,
  `short_text` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_new` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `is_popular` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `is_promo` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `images` text COLLATE utf8_unicode_ci,
  `available` enum('yes','no') COLLATE utf8_unicode_ci DEFAULT 'yes',
  `sort` int(11) DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `related_ids` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `shop_product`
--

INSERT INTO `shop_product` (`id`, `category_id`, `producer_id`, `amount`, `related_products`, `name`, `code`, `price`, `text`, `short_text`, `is_new`, `is_popular`, `is_promo`, `images`, `available`, `sort`, `slug`, `related_ids`) VALUES
(2, 1, NULL, NULL, NULL, 'dss', '', NULL, '', '', 'no', 'no', 'no', NULL, 'yes', NULL, 'dss', 'a:0:{}');

-- --------------------------------------------------------

--
-- Структура таблицы `shop_product_modification`
--

CREATE TABLE `shop_product_modification` (
  `id` int(11) NOT NULL,
  `amount` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL,
  `images` text COLLATE utf8_unicode_ci,
  `available` enum('yes','no') COLLATE utf8_unicode_ci DEFAULT 'yes',
  `sort` int(11) DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `filter_values` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `shop_product_to_category`
--

CREATE TABLE `shop_product_to_category` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `shop_product_to_category`
--

INSERT INTO `shop_product_to_category` (`id`, `product_id`, `category_id`) VALUES
(13, 2, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `shop_stock`
--

CREATE TABLE `shop_stock` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `shop_stock_to_product`
--

CREATE TABLE `shop_stock_to_product` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `shop_stock_to_user`
--

CREATE TABLE `shop_stock_to_user` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `system_log`
--

CREATE TABLE `system_log` (
  `id` bigint(20) NOT NULL,
  `level` int(11) DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `log_time` double DEFAULT NULL,
  `prefix` text COLLATE utf8_unicode_ci,
  `message` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `param_name` varchar(100) NOT NULL,
  `param_value` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `system_settings`
--

INSERT INTO `system_settings` (`id`, `param_name`, `param_value`, `type`) VALUES
(1, 'indexing', '1', 'checkbox'),
(2, 'siteName', 'OakCMS', 'textInput'),
(4, 'language', 'ru-RU', 'language'),
(5, 'themeFrontend', 'base', 'getTheme'),
(6, 'themeBackend', 'base', 'getTheme');

-- --------------------------------------------------------

--
-- Структура таблицы `texts`
--

CREATE TABLE `texts` (
  `id` int(11) NOT NULL,
  `layout` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `where_to_place` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `links` text COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `texts_lang`
--

CREATE TABLE `texts_lang` (
  `id` int(11) NOT NULL,
  `texts_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subtitle` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `settings` text COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(10) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `auth_key` varchar(32) DEFAULT NULL,
  `email_confirm_token` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `googleAuthenticator` tinyint(1) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  `role` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `created_at`, `updated_at`, `username`, `auth_key`, `email_confirm_token`, `password_hash`, `password_reset_token`, `email`, `googleAuthenticator`, `status`, `role`) VALUES
(1, 1459981535, 1481243575, 'admin', '-ZSUE3afc2rCB0UXa08ymzhpWsPSHEfk', 'Ht3SjXJ0MUJOXt4P0gwsltk5B0eKJFOH', '$2y$13$KC2ZxM17jpzz6zNnjLzXH.r2vgQ7urxVfRuS/xkZsGzfNnwtcrdWK', NULL, 'legionerblack@yandex.ru', 0, 1, 'administrator');

-- --------------------------------------------------------

--
-- Структура таблицы `user_profile`
--

CREATE TABLE `user_profile` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `middlename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `gender` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `user_profile`
--

INSERT INTO `user_profile` (`user_id`, `firstname`, `middlename`, `lastname`, `avatar`, `locale`, `gender`) VALUES
(1, 'Володимир', 'Ігорович', 'Гривінський', '5740816e01325.jpg', 'uk-UA', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `widgetkit`
--

CREATE TABLE `widgetkit` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `data` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `widgetkit`
--

INSERT INTO `widgetkit` (`id`, `name`, `type`, `data`) VALUES
(1, 'Проверка инстаграм', 'instagram', '{"_widget":{"name":"slideshow","data":{"nav":"dotnav","nav_overlay":true,"nav_align":"center","thumbnail_width":"70","thumbnail_height":"70","thumbnail_alt":false,"slidenav":"default","nav_contrast":true,"animation":"fade","slices":"15","duration":"500","autoplay":false,"interval":"3000","autoplay_pause":true,"kenburns":false,"kenburns_animation":"","kenburns_duration":"15","fullscreen":false,"min_height":"300","media":true,"image_width":"auto","image_height":"auto","overlay":"none","overlay_animation":"fade","overlay_background":true,"title":true,"content":true,"title_size":"h3","content_size":"","link":true,"link_style":"button","link_text":"Read more","badge":true,"badge_style":"badge","link_target":false,"class":"","link_media":false}},"limit":10,"title":"fullname","username":"vizhital","prepared":"[{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u041a\\\\u0443\\\\u0440\\\\u0442\\\\u043a\\\\u0430 \\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0430\\\\u044f\\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 5750 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0430\\\\u044f\\\\u043a\\\\u0443\\\\u0440\\\\u0442\\\\u043a\\\\u0430\\\\\\">#\\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0430\\\\u044f\\\\u043a\\\\u0443\\\\u0440\\\\u0442\\\\u043a\\\\u0430<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0443\\\\u044e\\\\u043a\\\\u0443\\\\u0440\\\\u0442\\\\u043a\\\\u0443\\\\\\">#\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0443\\\\u044e\\\\u043a\\\\u0443\\\\u0440\\\\u0442\\\\u043a\\\\u0443<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0432\\\\u0435\\\\u0440\\\\u0445\\\\u043d\\\\u044f\\\\u044f\\\\u043e\\\\u0434\\\\u0435\\\\u0436\\\\u0434\\\\u0430\\\\u043a\\\\u0438\\\\u0435\\\\u0432\\\\\\">#\\\\u0432\\\\u0435\\\\u0440\\\\u0445\\\\u043d\\\\u044f\\\\u044f\\\\u043e\\\\u0434\\\\u0435\\\\u0436\\\\u0434\\\\u0430\\\\u043a\\\\u0438\\\\u0435\\\\u0432<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/style\\\\\\">#style<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:44:58 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB17O0GQdDg\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/12748247_1693409820948269_231409638_n.jpg?ig_cache_key=MTE4NjExNDU3OTYzODM3NDYyNA%3D%3D.2\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}},{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u041f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e \\\\u0438\\\\u0437 \\\\u0432\\\\u0430\\\\u0440\\\\u0435\\\\u043d\\\\u043e\\\\u0439 \\\\u0448\\\\u0435\\\\u0440\\\\u0441\\\\u0442\\\\u0438\\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 2350 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e\\\\\\">#\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438\\\\\\">#\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0448\\\\u0435\\\\u0440\\\\u0441\\\\u0442\\\\u044f\\\\u043d\\\\u043e\\\\u0435\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e\\\\\\">#\\\\u0448\\\\u0435\\\\u0440\\\\u0441\\\\u0442\\\\u044f\\\\u043d\\\\u043e\\\\u0435\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/oversize\\\\\\">#oversize<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/stylish\\\\\\">#stylish<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:37:14 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB16WLoQdCl\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/12717112_1505445679765394_2100472763_n.jpg?ig_cache_key=MTE4NjExMDY4Nzg5NDY4NzkwOQ%3D%3D.2\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}},{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u041f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e \\\\u0448\\\\u0435\\\\u0440\\\\u0441\\\\u0442\\\\u044f\\\\u043d\\\\u043e\\\\u0435\\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 2880 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438\\\\\\">#\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0448\\\\u0435\\\\u0440\\\\u0441\\\\u0442\\\\u044f\\\\u043d\\\\u043e\\\\u0435\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e\\\\\\">#\\\\u0448\\\\u0435\\\\u0440\\\\u0441\\\\u0442\\\\u044f\\\\u043d\\\\u043e\\\\u0435\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/oversize\\\\\\">#oversize<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/cute\\\\\\">#cute<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:34:01 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB15-unwdB6\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/12717097_193531387674982_1018250677_n.jpg?ig_cache_key=MTE4NjEwOTA3NjE5OTgyMTQzNA%3D%3D.2\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}},{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u0414\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430 \\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0430\\\\u044f\\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 10500 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430\\\\\\">#\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432\\\\\\">#\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430\\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0430\\\\u044f\\\\\\">#\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430\\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0430\\\\u044f<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/style\\\\\\">#style<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/look\\\\\\">#look<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:30:21 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB15jxjwdBa\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/12747667_1038265569578441_215959711_n.jpg?ig_cache_key=MTE4NjEwNzIyMzkyODA2NjEzOA%3D%3D.2\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}},{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u0414\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430 \\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0430\\\\u044f \\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 12500 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430\\\\u043d\\\\u0430\\\\u0442\\\\u0443\\\\u0440\\\\u0430\\\\u043b\\\\u044c\\\\u043d\\\\u0430\\\\u044f\\\\\\">#\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430\\\\u043d\\\\u0430\\\\u0442\\\\u0443\\\\u0440\\\\u0430\\\\u043b\\\\u044c\\\\u043d\\\\u0430\\\\u044f<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432\\\\\\">#\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/cute\\\\\\">#cute<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/stylish\\\\\\">#stylish<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:26:48 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB15J2fwdA8\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/12599056_1672280749694684_1324694833_n.jpg?ig_cache_key=MTE4NjEwNTQ0MjUyMzI3MTIyOA%3D%3D.2\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}},{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u0414\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430 \\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0430\\\\u044f\\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 9800 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430\\\\\\">#\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432\\\\\\">#\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430\\\\u043d\\\\u0430\\\\u0442\\\\u0443\\\\u0440\\\\u0430\\\\u043b\\\\u044c\\\\u043d\\\\u0430\\\\u044f\\\\\\">#\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430\\\\u043d\\\\u0430\\\\u0442\\\\u0443\\\\u0440\\\\u0430\\\\u043b\\\\u044c\\\\u043d\\\\u0430\\\\u044f<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/style\\\\\\">#style<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:22:24 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB14pkXQdAR\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/10632360_1507229832918278_654945332_n.jpg?ig_cache_key=MTE4NjEwMzIyNDAzMDA1NjQ2NQ%3D%3D.2\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}},{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u0428\\\\u0443\\\\u0431\\\\u0430 \\\\u043d\\\\u043e\\\\u0440\\\\u043a\\\\u043e\\\\u0432\\\\u0430\\\\u044f \\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 39500 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438\\\\\\">#\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0448\\\\u0443\\\\u0431\\\\u0443\\\\\\">#\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0448\\\\u0443\\\\u0431\\\\u0443<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u043d\\\\u043e\\\\u0440\\\\u043a\\\\u0430\\\\\\">#\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u043d\\\\u043e\\\\u0440\\\\u043a\\\\u0430<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/style\\\\\\">#style<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/look\\\\\\">#look<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:17:34 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB14GLMwdPa\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/12728551_239872913012570_976925889_n.jpg?ig_cache_key=MTE4NjEwMDc5MTgyODY2NTMwNg%3D%3D.2\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}},{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u0428\\\\u0443\\\\u0431\\\\u0430 \\\\u043d\\\\u043e\\\\u0440\\\\u043a\\\\u043e\\\\u0432\\\\u0430\\\\u044f\\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 27900 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u043d\\\\u043e\\\\u0440\\\\u043a\\\\u0430\\\\\\">#\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u043d\\\\u043e\\\\u0440\\\\u043a\\\\u0430<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0448\\\\u0443\\\\u0431\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432\\\\\\">#\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0448\\\\u0443\\\\u0431\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438\\\\\\">#\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/style\\\\\\">#style<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/cute\\\\\\">#cute<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:12:58 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB13kjjQdO0\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/12748207_1060157127340088_389350033_n.jpg?ig_cache_key=MTE4NjA5ODQ4MTUxMzc0NzM4MA%3D%3D.2.l\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}},{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u0428\\\\u0443\\\\u0431\\\\u0430 \\\\u043a\\\\u043e\\\\u0436\\\\u0430+\\\\u043c\\\\u0435\\\\u0445 \\\\u0447\\\\u0435\\\\u0440\\\\u043d\\\\u043e\\\\u0431\\\\u0443\\\\u0440\\\\u043a\\\\u0438\\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 9800 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0448\\\\u0443\\\\u0431\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432\\\\\\">#\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0448\\\\u0443\\\\u0431\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438\\\\\\">#\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/beautiful\\\\\\">#beautiful<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/stylish\\\\\\">#stylish<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:05:26 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB12tXZQdN6\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/1389450_952919778123648_1346572496_n.jpg?ig_cache_key=MTE4NjA5NDY4ODg4OTg1Mjc5NA%3D%3D.2\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}},{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u0428\\\\u0443\\\\u0431\\\\u0430 \\\\u043c\\\\u0443\\\\u0442\\\\u043e\\\\u043d\\\\u043e\\\\u0432\\\\u0430\\\\u044f\\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 12700 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u043a\\\\u0438\\\\u0435\\\\u0432\\\\\\">#\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u043a\\\\u0438\\\\u0435\\\\u0432<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u043c\\\\u0443\\\\u0442\\\\u043e\\\\u043d\\\\\\">#\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u043c\\\\u0443\\\\u0442\\\\u043e\\\\u043d<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0448\\\\u0443\\\\u0431\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432\\\\\\">#\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0448\\\\u0443\\\\u0431\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/look\\\\\\">#look<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/style\\\\\\">#style<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:00:01 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB12FmdwdNB\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/12724749_458481994348884_184489370_n.jpg?ig_cache_key=MTE4NjA5MTk1NjI5MjQwODEyOQ%3D%3D.2\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}}]","hash":"ad4b720dd7cd8aca7e066ec994a863fb","hashed":1476912118}'),
(2, '1234567', 'custom', '{"_widget":{"name":"slideshow","data":{"nav":"dotnav","nav_overlay":true,"nav_align":"center","thumbnail_width":"70","thumbnail_height":"70","thumbnail_alt":false,"slidenav":"default","nav_contrast":true,"animation":"fade","slices":"15","duration":"500","autoplay":false,"interval":"3000","autoplay_pause":true,"kenburns":false,"kenburns_animation":"","kenburns_duration":"15","fullscreen":false,"min_height":"300","media":true,"image_width":"auto","image_height":"auto","overlay":"none","overlay_animation":"fade","overlay_background":true,"title":true,"content":true,"title_size":"h3","content_size":"","link":true,"link_style":"button","link_text":"Read more","badge":true,"badge_style":"badge","link_target":false,"class":""}},"items":[{"media":"uploads\\/user\\/66.jpg","options":{"media":{"width":500,"height":334,"type":"image"}},"title":"Undefined"},{"media":"uploads\\/user\\/_1\\/5740816e01325.jpg","options":{"media":{"width":342,"height":604,"type":"image"}},"title":"Undefined"}],"random":false,"_fields":[]}');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD PRIMARY KEY (`item_name`,`user_id`);

--
-- Индексы таблицы `auth_item`
--
ALTER TABLE `auth_item`
  ADD PRIMARY KEY (`name`),
  ADD KEY `rule_name` (`rule_name`),
  ADD KEY `idx-auth_item-type` (`type`);

--
-- Индексы таблицы `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD PRIMARY KEY (`parent`,`child`),
  ADD KEY `child` (`child`);

--
-- Индексы таблицы `auth_rule`
--
ALTER TABLE `auth_rule`
  ADD PRIMARY KEY (`name`);

--
-- Индексы таблицы `content_articles`
--
ALTER TABLE `content_articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_yupe_blog_post_create_user_id` (`create_user_id`),
  ADD KEY `ix_yupe_blog_post_update_user_id` (`update_user_id`),
  ADD KEY `ix_yupe_blog_post_status` (`status`),
  ADD KEY `ix_yupe_blog_post_access_type` (`access_type`),
  ADD KEY `ix_yupe_blog_post_comment_status` (`comment_status`),
  ADD KEY `ix_yupe_blog_post_publish_date` (`updated_at`),
  ADD KEY `ix_yupe_blog_post_category_id` (`category_id`);

--
-- Индексы таблицы `content_articles_lang`
--
ALTER TABLE `content_articles_lang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slug` (`slug`);

--
-- Индексы таблицы `content_articles_medias`
--
ALTER TABLE `content_articles_medias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `i_virtuemart_product_id` (`content_articles_id`,`media_id`),
  ADD KEY `i_ordering` (`ordering`);

--
-- Индексы таблицы `content_category`
--
ALTER TABLE `content_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_tree_NK1` (`tree`),
  ADD KEY `tbl_tree_NK2` (`lft`),
  ADD KEY `tbl_tree_NK3` (`rgt`),
  ADD KEY `tbl_tree_NK4` (`depth`),
  ADD KEY `tbl_tree_NK5` (`active`);

--
-- Индексы таблицы `content_category_lang`
--
ALTER TABLE `content_category_lang`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `content_pages`
--
ALTER TABLE `content_pages`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `content_pages_lang`
--
ALTER TABLE `content_pages_lang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_pages_id` (`content_pages_id`);

--
-- Индексы таблицы `content_tags`
--
ALTER TABLE `content_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- Индексы таблицы `db_state`
--
ALTER TABLE `db_state`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `field`
--
ALTER TABLE `field`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `field_category`
--
ALTER TABLE `field_category`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `field_value`
--
ALTER TABLE `field_value`
  ADD PRIMARY KEY (`id`),
  ADD KEY `field_id` (`field_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Индексы таблицы `field_variant`
--
ALTER TABLE `field_variant`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_field` (`field_id`);

--
-- Индексы таблицы `filter`
--
ALTER TABLE `filter`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `filter_relation_value`
--
ALTER TABLE `filter_relation_value`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `filter_value`
--
ALTER TABLE `filter_value`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `variant_item` (`variant_id`,`item_id`);

--
-- Индексы таблицы `filter_variant`
--
ALTER TABLE `filter_variant`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_filter` (`filter_id`);

--
-- Индексы таблицы `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`language_id`),
  ADD KEY `url` (`url`);

--
-- Индексы таблицы `language_source`
--
ALTER TABLE `language_source`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `language_translate`
--
ALTER TABLE `language_translate`
  ADD PRIMARY KEY (`id`,`language`);

--
-- Индексы таблицы `medias`
--
ALTER TABLE `medias`
  ADD PRIMARY KEY (`media_id`),
  ADD KEY `i_published` (`status`);

--
-- Индексы таблицы `menu_item`
--
ALTER TABLE `menu_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `MenuTypeId_idx` (`menu_type_id`),
  ADD KEY `ParentId_idx` (`parent_id`),
  ADD KEY `TranslationId_idx` (`translation_id`),
  ADD KEY `Lft_Rgt_idx` (`lft`,`rgt`),
  ADD KEY `Language_idx` (`language`),
  ADD KEY `Path_idx` (`path`(255)),
  ADD KEY `Alias_idx` (`alias`),
  ADD KEY `Status_idx` (`status`);

--
-- Индексы таблицы `menu_type`
--
ALTER TABLE `menu_type`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`,`language`);

--
-- Индексы таблицы `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Индексы таблицы `modules_modules`
--
ALTER TABLE `modules_modules`
  ADD PRIMARY KEY (`module_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `seo_items`
--
ALTER TABLE `seo_items`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `shop_category`
--
ALTER TABLE `shop_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`,`parent_id`);

--
-- Индексы таблицы `shop_incoming`
--
ALTER TABLE `shop_incoming`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `shop_outcoming`
--
ALTER TABLE `shop_outcoming`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `shop_price`
--
ALTER TABLE `shop_price`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_type` (`type_id`);

--
-- Индексы таблицы `shop_price_type`
--
ALTER TABLE `shop_price_type`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `shop_producer`
--
ALTER TABLE `shop_producer`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `shop_product`
--
ALTER TABLE `shop_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `producer_id` (`producer_id`);

--
-- Индексы таблицы `shop_product_modification`
--
ALTER TABLE `shop_product_modification`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `shop_product_to_category`
--
ALTER TABLE `shop_product_to_category`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `shop_stock`
--
ALTER TABLE `shop_stock`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `shop_stock_to_product`
--
ALTER TABLE `shop_stock_to_product`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `shop_stock_to_user`
--
ALTER TABLE `shop_stock_to_user`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `system_log`
--
ALTER TABLE `system_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_log_level` (`level`),
  ADD KEY `idx_log_category` (`category`);

--
-- Индексы таблицы `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ux_yupe_yupe_settings_module_id_param_name_user_id` (`param_name`),
  ADD KEY `ix_yupe_yupe_settings_param_name` (`param_name`);

--
-- Индексы таблицы `texts`
--
ALTER TABLE `texts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slug` (`slug`);

--
-- Индексы таблицы `texts_lang`
--
ALTER TABLE `texts_lang`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-user-username` (`username`),
  ADD KEY `idx-user-email` (`email`),
  ADD KEY `idx-user-status` (`status`);

--
-- Индексы таблицы `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`user_id`);

--
-- Индексы таблицы `widgetkit`
--
ALTER TABLE `widgetkit`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `content_articles`
--
ALTER TABLE `content_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблицы `content_articles_lang`
--
ALTER TABLE `content_articles_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT для таблицы `content_articles_medias`
--
ALTER TABLE `content_articles_medias`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT для таблицы `content_category`
--
ALTER TABLE `content_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique tree node identifier', AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `content_category_lang`
--
ALTER TABLE `content_category_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `content_pages`
--
ALTER TABLE `content_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `content_pages_lang`
--
ALTER TABLE `content_pages_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `content_tags`
--
ALTER TABLE `content_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `db_state`
--
ALTER TABLE `db_state`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `field`
--
ALTER TABLE `field`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `field_category`
--
ALTER TABLE `field_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `field_value`
--
ALTER TABLE `field_value`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `field_variant`
--
ALTER TABLE `field_variant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `filter`
--
ALTER TABLE `filter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `filter_relation_value`
--
ALTER TABLE `filter_relation_value`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `filter_value`
--
ALTER TABLE `filter_value`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `filter_variant`
--
ALTER TABLE `filter_variant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `image`
--
ALTER TABLE `image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблицы `language_source`
--
ALTER TABLE `language_source`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT для таблицы `medias`
--
ALTER TABLE `medias`
  MODIFY `media_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `menu_item`
--
ALTER TABLE `menu_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `menu_type`
--
ALTER TABLE `menu_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `modules_modules`
--
ALTER TABLE `modules_modules`
  MODIFY `module_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT для таблицы `seo_items`
--
ALTER TABLE `seo_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `shop_category`
--
ALTER TABLE `shop_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `shop_incoming`
--
ALTER TABLE `shop_incoming`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `shop_outcoming`
--
ALTER TABLE `shop_outcoming`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `shop_price`
--
ALTER TABLE `shop_price`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `shop_price_type`
--
ALTER TABLE `shop_price_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `shop_producer`
--
ALTER TABLE `shop_producer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `shop_product`
--
ALTER TABLE `shop_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `shop_product_modification`
--
ALTER TABLE `shop_product_modification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `shop_product_to_category`
--
ALTER TABLE `shop_product_to_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT для таблицы `shop_stock`
--
ALTER TABLE `shop_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `shop_stock_to_product`
--
ALTER TABLE `shop_stock_to_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `shop_stock_to_user`
--
ALTER TABLE `shop_stock_to_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `system_log`
--
ALTER TABLE `system_log`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблицы `texts`
--
ALTER TABLE `texts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `texts_lang`
--
ALTER TABLE `texts_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `widgetkit`
--
ALTER TABLE `widgetkit`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `auth_item`
--
ALTER TABLE `auth_item`
  ADD CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `field`
--
ALTER TABLE `field`
  ADD CONSTRAINT `fk_field_category_id` FOREIGN KEY (`category_id`) REFERENCES `field_category` (`id`);

--
-- Ограничения внешнего ключа таблицы `field_value`
--
ALTER TABLE `field_value`
  ADD CONSTRAINT `fk_field_value_field_id` FOREIGN KEY (`field_id`) REFERENCES `field` (`id`);

--
-- Ограничения внешнего ключа таблицы `field_variant`
--
ALTER TABLE `field_variant`
  ADD CONSTRAINT `fk_field_variant_field_id` FOREIGN KEY (`field_id`) REFERENCES `field` (`id`);

--
-- Ограничения внешнего ключа таблицы `filter_value`
--
ALTER TABLE `filter_value`
  ADD CONSTRAINT `fk_variant` FOREIGN KEY (`variant_id`) REFERENCES `filter_variant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `filter_variant`
--
ALTER TABLE `filter_variant`
  ADD CONSTRAINT `fk_filter` FOREIGN KEY (`filter_id`) REFERENCES `filter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `shop_price`
--
ALTER TABLE `shop_price`
  ADD CONSTRAINT `fk_product` FOREIGN KEY (`product_id`) REFERENCES `shop_product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_type` FOREIGN KEY (`type_id`) REFERENCES `shop_price_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `shop_product`
--
ALTER TABLE `shop_product`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `shop_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_producer` FOREIGN KEY (`producer_id`) REFERENCES `shop_producer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
