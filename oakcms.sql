-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Час створення: Бер 03 2017 р., 10:20
-- Версія сервера: 5.7.17-0ubuntu0.16.04.1
-- Версія PHP: 7.0.15-0ubuntu0.16.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `oakcms`
--

-- --------------------------------------------------------

--
-- Структура таблиці `auth_assignment`
--

CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `auth_assignment`
--

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('administrator', '1', 1488464537);

-- --------------------------------------------------------

--
-- Структура таблиці `auth_item`
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
-- Дамп даних таблиці `auth_item`
--

INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
('administrator', 1, 'Administrator', NULL, NULL, 1460046593, 1460046593),
('manager', 1, 'Manager', NULL, NULL, 1460046592, 1460046592),
('permAdminPanel', 2, 'Permission Admin Panel', NULL, NULL, 1460046593, 1460046593),
('user', 1, 'User', NULL, NULL, 1460046592, 1460046592);

-- --------------------------------------------------------

--
-- Структура таблиці `auth_item_child`
--

CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `auth_item_child`
--

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
('administrator', 'manager'),
('manager', 'permAdminPanel'),
('manager', 'user');

-- --------------------------------------------------------

--
-- Структура таблиці `auth_rule`
--

CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `created_time` int(11) NOT NULL,
  `updated_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `cart_element`
--

CREATE TABLE `cart_element` (
  `id` int(11) NOT NULL,
  `parent_id` int(55) DEFAULT NULL,
  `model` varchar(110) COLLATE utf8_unicode_ci NOT NULL,
  `cart_id` int(11) NOT NULL,
  `item_id` int(55) NOT NULL,
  `count` int(11) NOT NULL,
  `price` decimal(11,2) DEFAULT NULL,
  `hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `options` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `content_articles`
--

CREATE TABLE `content_articles` (
  `id` int(11) NOT NULL,
  `create_user_id` int(11) NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `published_at` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `image` varchar(300) NOT NULL DEFAULT '',
  `layout` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `comment_status` int(11) NOT NULL DEFAULT '1',
  `create_user_ip` varchar(20) NOT NULL,
  `access_type` int(11) NOT NULL DEFAULT '1',
  `category_id` int(11) DEFAULT NULL,
  `main_image` int(11) NOT NULL DEFAULT '1',
  `hits` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `content_articles`
--

INSERT INTO `content_articles` (`id`, `create_user_id`, `update_user_id`, `published_at`, `created_at`, `updated_at`, `image`, `layout`, `status`, `comment_status`, `create_user_ip`, `access_type`, `category_id`, `main_image`, `hits`) VALUES
(1, 1, 1, 1487853420, 1487853435, 1488311594, '', 'tag', 1, 0, '194.126.183.254', 1, 2, 1, 0),
(2, 1, 1, 1488310980, 1488310993, 1488311049, '', 'tag', 1, 0, '127.0.0.1', 1, 1, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблиці `content_articles_lang`
--

CREATE TABLE `content_articles_lang` (
  `id` int(11) NOT NULL,
  `content_articles_id` int(11) NOT NULL,
  `slug` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `meta_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_keywords` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `settings` text COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(10) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `content_articles_lang`
--

INSERT INTO `content_articles_lang` (`id`, `content_articles_id`, `slug`, `title`, `description`, `content`, `link`, `meta_title`, `meta_keywords`, `meta_description`, `settings`, `language`) VALUES
(1, 1, 'test-article', 'Name of the Internal news Lorem Ipsum', '<p>1233</p>\r\n', '<p>31</p>\r\n', '', '', '', '', '[]', 'ru-ru'),
(2, 2, 'name-of-the-internal-news-lorem-ipsum2', 'Name of the Internal news Lorem Ipsum', '', '<p>213</p>\r\n', '', '', '', '', '[]', 'ru-ru'),
(5, 1, 'name-of-the-internal-news-lorem-ipsum', 'Recruitment news', '', '<p>1241</p>\r\n', '', '', '', '', '[]', 'en-us');

-- --------------------------------------------------------

--
-- Структура таблиці `content_articles_medias`
--

CREATE TABLE `content_articles_medias` (
  `id` int(11) UNSIGNED NOT NULL,
  `content_articles_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `media_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` int(3) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `content_category`
--

CREATE TABLE `content_category` (
  `id` int(11) NOT NULL COMMENT 'Unique tree node identifier',
  `layout` varchar(255) NOT NULL,
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
-- Дамп даних таблиці `content_category`
--

INSERT INTO `content_category` (`id`, `layout`, `status`, `created_at`, `updated_at`, `tree`, `lft`, `rgt`, `depth`, `icon`, `icon_type`, `active`, `selected`, `disabled`, `readonly`, `visible`, `collapsed`, `movable_u`, `movable_d`, `movable_l`, `movable_r`, `removable`, `removable_all`, `order`, `parent`, `children`) VALUES
(1, '', 1, 1465405031, 1472547378, 1, 1, 2, 0, '', 1, 1, 0, 0, 0, 1, 0, 1, 1, 1, 1, 1, 0, 1, 0, 0),
(2, 'view', 1, 1487853478, 1487853478, 2, 1, 2, 0, NULL, 1, 1, 0, 0, 0, 1, 0, 1, 1, 1, 1, 1, 0, 2, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблиці `content_category_lang`
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
-- Дамп даних таблиці `content_category_lang`
--

INSERT INTO `content_category_lang` (`id`, `content_category_id`, `slug`, `title`, `content`, `meta_title`, `meta_keywords`, `meta_description`, `settings`, `language`) VALUES
(1, 1, 'novosti', 'Новости', '', 'Все новости', '', '', '', 'ru-RU'),
(2, 1, 'news', 'News', '', '', '', '', '', 'en-US'),
(3, 2, '', 'Подкатегория', '', '', '', '', '', 'ru-ru'),
(4, 3, '2', 'Подкатегория 2', '', '', '', '', '', 'ru-ru'),
(5, 2, 'novosti-2', 'Новости 2', '<p>1234</p>\r\n', '', '', '', '', 'ru-ru');

-- --------------------------------------------------------

--
-- Структура таблиці `content_pages`
--

CREATE TABLE `content_pages` (
  `id` int(11) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `layout` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `background_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icon_image` text COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `ordering` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `content_pages`
--

INSERT INTO `content_pages` (`id`, `lft`, `rgt`, `level`, `parent_id`, `layout`, `background_image`, `icon_image`, `status`, `created_at`, `updated_at`, `ordering`) VALUES
(1, 1, 4, 0, 0, '', '', '', 1, 1476250048, 1483477411, 1),
(2, 2, 3, 1, 1, 'default', '', '', 1, 1484921308, 1488312567, 0);

-- --------------------------------------------------------

--
-- Структура таблиці `content_pages_lang`
--

CREATE TABLE `content_pages_lang` (
  `id` int(11) NOT NULL,
  `content_pages_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subtitle` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `title_h1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
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
-- Дамп даних таблиці `content_pages_lang`
--

INSERT INTO `content_pages_lang` (`id`, `content_pages_id`, `title`, `subtitle`, `title_h1`, `slug`, `description`, `content`, `meta_title`, `meta_keywords`, `meta_description`, `settings`, `language`) VALUES
(1, 2, 'Часто задаваемые вопросы', '', 'Главная', 'home', '', '<p>Главная страница сайта</p>\r\n', '', '', '', '[]', 'ru-ru'),
(2, 2, 'About us3', '', '', 'about-us3', '', '<p>12321313</p>\r\n', '', '', '', '[]', 'en-us');

-- --------------------------------------------------------

--
-- Структура таблиці `content_tags`
--

CREATE TABLE `content_tags` (
  `id` int(11) NOT NULL,
  `frequency` int(10) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `content_tag_assn`
--

CREATE TABLE `content_tag_assn` (
  `content_id` int(11) NOT NULL,
  `content_tags_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `db_state`
--

CREATE TABLE `db_state` (
  `id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `db_state`
--

INSERT INTO `db_state` (`id`, `timestamp`) VALUES
(1, 1486568461),
(2, 1486568471),
(3, 1486655328),
(4, 1486655331);

-- --------------------------------------------------------

--
-- Структура таблиці `field`
--

CREATE TABLE `field` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `model_category_id` text COLLATE utf8_unicode_ci,
  `type` text COLLATE utf8_unicode_ci,
  `options` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `relation_model` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `field`
--

INSERT INTO `field` (`id`, `name`, `slug`, `category_id`, `model_category_id`, `type`, `options`, `description`, `relation_model`) VALUES
(1, 'Цвет', 'color', 1, '["1","2"]', 'select', NULL, '', 'app\\modules\\content\\models\\ContentArticles'),
(2, 'Габариты ширина', 'dimensions_width', 1, NULL, 'text', NULL, '', 'app\\modules\\shop\\models\\Product'),
(3, 'Габариты висота', 'dimensions_height', 1, NULL, 'text', NULL, '', 'app\\modules\\shop\\models\\Product'),
(4, 'Габариты глубина', 'dimensions_depth', 1, NULL, 'text', NULL, '', 'app\\modules\\shop\\models\\Product'),
(5, 'Матириал', 'material', 1, NULL, 'text', NULL, '', 'app\\modules\\shop\\models\\Product'),
(6, 'Гарантия', 'guarantee', 1, NULL, 'text', NULL, '', 'app\\modules\\shop\\models\\Product'),
(7, 'ві', 'фів', 1, NULL, 'select', NULL, '', 'app\\modules\\content\\models\\ContentArticles');

-- --------------------------------------------------------

--
-- Структура таблиці `field_category`
--

CREATE TABLE `field_category` (
  `id` int(11) NOT NULL,
  `name` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `field_category`
--

INSERT INTO `field_category` (`id`, `name`, `sort`) VALUES
(1, 'Товары', 1);

-- --------------------------------------------------------

--
-- Структура таблиці `field_value`
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
-- Дамп даних таблиці `field_value`
--

INSERT INTO `field_value` (`id`, `field_id`, `variant_id`, `item_id`, `value`, `numeric_value`) VALUES
(11, 3, 0, 5, '600', 600),
(12, 2, 0, 5, '200', 200),
(13, 4, 0, 5, '300', 300),
(14, 5, 0, 5, 'ДСП', NULL),
(15, 6, 0, 5, '10 лет', 10),
(16, 1, 1, 5, 'Белый', NULL);

-- --------------------------------------------------------

--
-- Структура таблиці `field_variant`
--

CREATE TABLE `field_variant` (
  `id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `numeric_value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `field_variant`
--

INSERT INTO `field_variant` (`id`, `field_id`, `value`, `numeric_value`) VALUES
(1, 1, 'Белый', 0),
(2, 1, 'Черный', 0),
(3, 1, 'Золотой', 0);

-- --------------------------------------------------------

--
-- Структура таблиці `filter`
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

--
-- Дамп даних таблиці `filter`
--

INSERT INTO `filter` (`id`, `name`, `slug`, `sort`, `description`, `relation_field_name`, `is_filter`, `type`, `relation_field_value`) VALUES
(3, 'Цвет', 'color', NULL, '', 'category_id', 'no', 'radio', 'a:16:{i:0;s:1:"4";i:1;s:1:"5";i:2;s:1:"6";i:3;s:1:"7";i:4;s:1:"8";i:5;s:1:"9";i:6;s:2:"10";i:7;s:2:"11";i:8;s:2:"12";i:9;s:2:"13";i:10;s:2:"14";i:11;s:2:"15";i:12;s:2:"16";i:13;s:2:"17";i:14;s:2:"18";i:15;s:2:"19";}'),
(4, 'Цвет', 'color', NULL, '213123', 'category_id', 'yes', 'checkbox', 'a:16:{i:0;s:1:"4";i:1;s:1:"5";i:2;s:1:"6";i:3;s:1:"7";i:4;s:1:"8";i:5;s:1:"9";i:6;s:2:"10";i:7;s:2:"11";i:8;s:2:"12";i:9;s:2:"13";i:10;s:2:"14";i:11;s:2:"15";i:12;s:2:"16";i:13;s:2:"17";i:14;s:2:"18";i:15;s:2:"19";}'),
(5, 'Цена', 'price', NULL, 'Цена товара', 'category_id', 'yes', 'select', 'a:6:{i:0;s:1:"4";i:1;s:1:"5";i:2;s:1:"6";i:3;s:1:"7";i:4;s:1:"8";i:5;s:2:"10";}'),
(6, 'Стиль', 'style', 4, '', 'category_id', 'yes', 'select', 'a:6:{i:0;s:1:"5";i:1;s:1:"6";i:2;s:1:"7";i:3;s:1:"9";i:4;s:2:"10";i:5;s:2:"13";}');

-- --------------------------------------------------------

--
-- Структура таблиці `filter_relation_value`
--

CREATE TABLE `filter_relation_value` (
  `id` int(11) NOT NULL,
  `filter_id` int(11) NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `filter_relation_value`
--

INSERT INTO `filter_relation_value` (`id`, `filter_id`, `value`) VALUES
(26, 4, 4),
(27, 4, 5),
(28, 4, 6),
(29, 4, 7),
(30, 4, 8),
(31, 4, 9),
(32, 4, 10),
(33, 4, 11),
(34, 4, 12),
(35, 4, 13),
(36, 4, 14),
(37, 4, 15),
(38, 4, 16),
(39, 4, 17),
(40, 4, 18),
(41, 4, 19),
(42, 3, 4),
(43, 3, 5),
(44, 3, 6),
(45, 3, 7),
(46, 3, 8),
(47, 3, 9),
(48, 3, 10),
(49, 3, 11),
(50, 3, 12),
(51, 3, 13),
(52, 3, 14),
(53, 3, 15),
(54, 3, 16),
(55, 3, 17),
(56, 3, 18),
(57, 3, 19),
(58, 5, 4),
(59, 5, 5),
(60, 5, 6),
(61, 5, 7),
(62, 5, 8),
(63, 5, 10);

-- --------------------------------------------------------

--
-- Структура таблиці `filter_value`
--

CREATE TABLE `filter_value` (
  `id` int(11) NOT NULL,
  `filter_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `filter_value`
--

INSERT INTO `filter_value` (`id`, `filter_id`, `variant_id`, `item_id`) VALUES
(12, 3, 11, 2);

-- --------------------------------------------------------

--
-- Структура таблиці `filter_variant`
--

CREATE TABLE `filter_variant` (
  `id` int(11) NOT NULL,
  `filter_id` int(11) NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `numeric_value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `filter_variant`
--

INSERT INTO `filter_variant` (`id`, `filter_id`, `value`, `numeric_value`) VALUES
(9, 3, 'Белый', 0),
(10, 3, 'Черный', 0),
(11, 3, 'Красный', 0),
(12, 3, 'Зеленый', 0),
(13, 3, 'Желтый', 0),
(14, 4, 'Белый', 0),
(15, 4, 'Черый', 0),
(16, 4, 'Красный', 0),
(17, 3, 'Бежевый', 0),
(18, 3, 'Оранжевый', 0),
(24, 3, 'Зеленый 2', 0),
(33, 4, 'Проверка', 0),
(38, 5, '', 0),
(39, 6, '2345678', 2345678),
(40, 6, 'fsd', 0),
(41, 6, 'fdsghjkl', 0),
(42, 6, 'yrt23456789', 0),
(43, 6, 'dsafhj', 0),
(44, 6, 'sdafjhdfss', 0);

-- --------------------------------------------------------

--
-- Структура таблиці `image`
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
-- Дамп даних таблиці `image`
--

INSERT INTO `image` (`id`, `title`, `alt`, `filePath`, `itemId`, `isMain`, `modelName`, `urlAlias`, `description`, `gallery_id`, `sort`) VALUES
(6, NULL, NULL, 'FilterVariants/FilterVariant2/8a4b36.png', 2, NULL, 'FilterVariant', 'a72231f5c2-2', NULL, NULL, NULL),
(7, NULL, NULL, 'Products/Product2/771c4d.png', 2, NULL, 'Product', 'a6089a04b3-3', NULL, NULL, NULL),
(8, NULL, NULL, 'Products/Product2/82f741.png', 2, NULL, 'Product', 'f6a7b78a09-2', NULL, NULL, NULL),
(9, NULL, NULL, 'Producers/Producer1/7cd299.png', 1, NULL, 'Producer', 'db6a8574e2-2', NULL, NULL, NULL),
(10, NULL, NULL, 'Products/Product3/6cb267.png', 3, NULL, 'Product', 'b9fe03ab3e-2', NULL, NULL, NULL),
(11, NULL, NULL, 'Products/Product4/dbd9f7.png', 4, 1, 'Product', 'c697acdd89-1', NULL, NULL, NULL),
(12, NULL, NULL, 'FilterVariants/FilterVariant6/11818c.png', 6, NULL, 'FilterVariant', '4502659cd7-2', NULL, NULL, NULL),
(13, NULL, NULL, 'FilterVariants/FilterVariant7/0ce243.jpg', 7, NULL, 'FilterVariant', 'b379dd4d93-2', NULL, NULL, NULL),
(14, NULL, NULL, 'FilterVariants/FilterVariant8/ba671e.jpeg', 8, NULL, 'FilterVariant', 'a69c52cb83-2', NULL, NULL, NULL),
(15, NULL, NULL, 'Products/Product2/784241.jpg', 2, 1, 'Product', 'b6c959b6aa-1', NULL, NULL, NULL),
(16, NULL, NULL, 'Producers/Producer2/717682.png', 2, NULL, 'Producer', 'dcbdd59986-2', NULL, NULL, NULL),
(17, NULL, NULL, 'Products/Product5/c536b5.jpg', 5, 1, 'Product', 'dc001ccf5e-1', NULL, NULL, NULL),
(18, NULL, NULL, 'Products/Product5/5f4342.jpg', 5, NULL, 'Product', '8caf91b59a-2', NULL, NULL, NULL),
(19, NULL, NULL, 'Products/Product5/82c009.jpg', 5, NULL, 'Product', '7cf66ed514-3', NULL, NULL, NULL),
(20, NULL, NULL, 'Products/Product5/182a11.jpg', 5, NULL, 'Product', '9d69b5958c-4', NULL, NULL, NULL),
(21, NULL, NULL, 'Products/Product5/d3b7f0.jpg', 5, NULL, 'Product', '3603c94078-5', NULL, NULL, NULL),
(22, NULL, NULL, 'Products/Product5/f2c87b.jpg', 5, NULL, 'Product', '2779665e6b-6', NULL, NULL, NULL),
(23, NULL, NULL, 'Products/Product6/66e5f1.jpg', 6, 1, 'Product', 'bb407fe355-1', NULL, NULL, NULL),
(24, NULL, NULL, 'FilterVariants/FilterVariant14/4173ca.jpeg', 14, NULL, 'FilterVariant', '3d04e5111c-2', NULL, NULL, NULL),
(25, NULL, NULL, 'FilterVariants/FilterVariant15/46bb81.jpg', 15, NULL, 'FilterVariant', 'ecf7332b48-2', NULL, NULL, NULL),
(26, NULL, NULL, 'FilterVariants/FilterVariant16/a4d922.jpeg', 16, NULL, 'FilterVariant', '4378bb97f9-2', NULL, NULL, NULL),
(27, NULL, NULL, 'Products/Product7/95507f.jpg', 7, 1, 'Product', '018800ab2f-1', NULL, NULL, NULL),
(28, NULL, NULL, 'Products/Product7/7bc1e9.jpg', 7, NULL, 'Product', 'bbb7a69f37-2', NULL, NULL, NULL),
(29, NULL, NULL, 'Products/Product7/03d9d0.jpg', 7, NULL, 'Product', '2dacea34dc-3', NULL, NULL, NULL),
(30, NULL, NULL, 'FilterVariants/FilterVariant42/c942cf.png', 42, NULL, 'FilterVariant', '10151af083-2', NULL, NULL, NULL),
(31, NULL, NULL, 'Products/Product20/3628b9.jpg', 20, 1, 'Product', '603bb0f5ad-1', NULL, NULL, NULL),
(32, NULL, NULL, 'Products/Product20/d8dcd2.jpg', 20, NULL, 'Product', '34b70c194a-2', NULL, NULL, NULL),
(33, NULL, NULL, 'Products/Product18863/336135.jpg', 18863, NULL, 'Product', 'bdfd456d8e-2', NULL, NULL, NULL),
(34, NULL, NULL, 'Products/Product18863/680309.jpg', 18863, NULL, 'Product', '60bfda3365-3', NULL, NULL, NULL),
(35, NULL, NULL, 'Products/Product20406/f9d40a.jpg', 20406, NULL, 'Product', 'a95ca54022-2', NULL, NULL, NULL),
(36, NULL, NULL, 'Products/Product20406/47bfba.jpg', 20406, NULL, 'Product', 'e9b6a3ae58-3', NULL, NULL, NULL),
(37, NULL, NULL, 'Products/Product20435/4197c7.jpg', 20435, NULL, 'Product', '84291b50d4-2', NULL, NULL, NULL),
(38, NULL, NULL, 'Products/Product20435/43dd41.jpg', 20435, NULL, 'Product', 'f944f703c1-3', NULL, NULL, NULL),
(39, NULL, NULL, 'Products/Product20364/133c60.jpg', 20364, NULL, 'Product', '9a27de21bc-2', NULL, NULL, NULL),
(40, NULL, NULL, 'Products/Product20364/6c35a8.jpg', 20364, NULL, 'Product', '6a74944373-3', NULL, NULL, NULL),
(41, NULL, NULL, 'Products/Product20346/8c6749.jpg', 20346, NULL, 'Product', '58a3842286-2', NULL, NULL, NULL),
(42, NULL, NULL, 'Products/Product20346/731101.jpg', 20346, NULL, 'Product', '51348cdbb6-3', NULL, NULL, NULL),
(43, NULL, NULL, 'Products/Product20346/483579.jpg', 20346, NULL, 'Product', 'db490c8d47-4', NULL, NULL, NULL),
(44, NULL, NULL, 'Products/Product20346/a3ae62.jpg', 20346, NULL, 'Product', '5dd38ebb96-5', NULL, NULL, NULL),
(45, NULL, NULL, 'Products/Product20331/123e5d.jpg', 20331, NULL, 'Product', '3b673e58f6-2', NULL, NULL, NULL),
(46, NULL, NULL, 'Products/Product20331/d52361.jpg', 20331, NULL, 'Product', 'c96eb5df66-3', NULL, NULL, NULL),
(47, NULL, NULL, 'Products/Product20331/1db110.jpg', 20331, NULL, 'Product', '90371955ed-4', NULL, NULL, NULL),
(48, NULL, NULL, 'Products/Product20343/94beed.jpg', 20343, NULL, 'Product', '4cc9df334d-2', NULL, NULL, NULL),
(49, NULL, NULL, 'Products/Product20343/74d716.jpg', 20343, NULL, 'Product', 'ddd43aac0c-3', NULL, NULL, NULL),
(50, NULL, NULL, 'Products/Product20343/a9c976.jpg', 20343, NULL, 'Product', 'cebb00055a-4', NULL, NULL, NULL),
(51, NULL, NULL, 'Products/Product19916/03a79b.jpg', 19916, NULL, 'Product', 'd38144277f-2', NULL, NULL, NULL),
(52, NULL, NULL, 'Products/Product19916/31deab.jpg', 19916, NULL, 'Product', 'e12a13d2ff-3', NULL, NULL, NULL),
(53, NULL, NULL, 'Products/Product19897/3645b9.jpg', 19897, NULL, 'Product', '2e2a231adf-2', NULL, NULL, NULL),
(54, NULL, NULL, 'Products/Product19897/61d831.jpg', 19897, NULL, 'Product', '449de3e4c0-3', NULL, NULL, NULL),
(55, NULL, NULL, 'Products/Product19891/d2da80.jpg', 19891, NULL, 'Product', '03a005afb8-2', NULL, NULL, NULL),
(56, NULL, NULL, 'Products/Product19891/2976b7.jpg', 19891, NULL, 'Product', '89f027cc55-3', NULL, NULL, NULL),
(57, NULL, NULL, 'Products/Product19887/6868d6.jpg', 19887, NULL, 'Product', '5ab8b23c3f-2', NULL, NULL, NULL),
(58, NULL, NULL, 'Products/Product19887/a24257.jpg', 19887, NULL, 'Product', '503e6f7807-3', NULL, NULL, NULL),
(59, NULL, NULL, 'Products/Product19887/678d28.jpg', 19887, NULL, 'Product', 'a3311a6d6f-4', NULL, NULL, NULL),
(60, NULL, NULL, 'Products/Product19886/254825.jpg', 19886, NULL, 'Product', '8c374b6bd9-2', NULL, NULL, NULL),
(61, NULL, NULL, 'Products/Product19886/9d2f9a.jpg', 19886, NULL, 'Product', '424c414499-3', NULL, NULL, NULL),
(62, NULL, NULL, 'Products/Product19886/c8b7fd.jpg', 19886, NULL, 'Product', 'd2e1dfc14e-4', NULL, NULL, NULL),
(63, NULL, NULL, 'Products/Product19678/9af434.jpg', 19678, NULL, 'Product', '70878241a4-2', NULL, NULL, NULL),
(64, NULL, NULL, 'Products/Product19678/21a4ac.jpg', 19678, NULL, 'Product', '6541b9fafa-3', NULL, NULL, NULL),
(65, NULL, NULL, 'Products/Product19678/27adfc.jpg', 19678, NULL, 'Product', '38b3f1f1f8-4', NULL, NULL, NULL),
(66, NULL, NULL, 'Products/Product19678/ab0ae9.jpg', 19678, NULL, 'Product', 'ebe5ec30e3-5', NULL, NULL, NULL),
(67, NULL, NULL, 'Products/Product19425/34a39a.jpg', 19425, NULL, 'Product', 'bdf8aa7a48-2', NULL, NULL, NULL),
(68, NULL, NULL, 'Products/Product19425/9c90dd.jpg', 19425, NULL, 'Product', '08d7942436-3', NULL, NULL, NULL),
(69, NULL, NULL, 'Products/Product19425/63c24d.jpg', 19425, NULL, 'Product', 'b79c1e8ab3-4', NULL, NULL, NULL),
(70, NULL, NULL, 'Products/Product19425/cba4ff.jpg', 19425, NULL, 'Product', 'b97c5cf851-5', NULL, NULL, NULL),
(71, NULL, NULL, 'Products/Product19425/0005d2.jpg', 19425, NULL, 'Product', 'b0078d66dd-6', NULL, NULL, NULL),
(72, NULL, NULL, 'Products/Product19425/6d8acc.jpg', 19425, NULL, 'Product', '594ffc85d3-7', NULL, NULL, NULL),
(73, NULL, NULL, 'Products/Product19732/70cb3b.jpg', 19732, NULL, 'Product', '0d5cd3e0ba-2', NULL, NULL, NULL),
(74, NULL, NULL, 'Products/Product19732/0823ad.jpg', 19732, NULL, 'Product', '0694d3a001-3', NULL, NULL, NULL),
(75, NULL, NULL, 'Products/Product19732/cfdd27.jpg', 19732, NULL, 'Product', 'fa6fa5df96-4', NULL, NULL, NULL),
(76, NULL, NULL, 'Products/Product19732/a1e37d.jpg', 19732, NULL, 'Product', '680afe61ab-5', NULL, NULL, NULL),
(77, NULL, NULL, 'Products/Product19732/efe54a.jpg', 19732, NULL, 'Product', '99f3ccfc5f-6', NULL, NULL, NULL),
(78, NULL, NULL, 'Products/Product19732/9d0f87.jpg', 19732, NULL, 'Product', '1ccab28818-7', NULL, NULL, NULL),
(79, NULL, NULL, 'Products/Product19426/58ac1b.jpg', 19426, NULL, 'Product', '57f8c5e795-2', NULL, NULL, NULL),
(80, NULL, NULL, 'Products/Product19426/1d5aae.jpg', 19426, NULL, 'Product', '7f743d7add-3', NULL, NULL, NULL),
(81, NULL, NULL, 'Products/Product19426/9dc9b1.jpg', 19426, NULL, 'Product', '61a3e65f39-4', NULL, NULL, NULL),
(82, NULL, NULL, 'Products/Product19426/c74173.jpg', 19426, NULL, 'Product', '34daf953ec-5', NULL, NULL, NULL),
(83, NULL, NULL, 'Products/Product19426/5cbe12.jpg', 19426, NULL, 'Product', 'c675f95a30-6', NULL, NULL, NULL),
(84, NULL, NULL, 'Products/Product19426/bfc0d8.jpg', 19426, NULL, 'Product', '7d80b13343-7', NULL, NULL, NULL),
(85, NULL, NULL, 'Products/Product19663/685432.jpg', 19663, NULL, 'Product', 'f4feb7e26c-2', NULL, NULL, NULL),
(86, NULL, NULL, 'Products/Product19663/4c8339.jpg', 19663, NULL, 'Product', 'a898734228-3', NULL, NULL, NULL),
(87, NULL, NULL, 'Products/Product19663/3ac530.jpg', 19663, NULL, 'Product', '2873e3c586-4', NULL, NULL, NULL),
(88, NULL, NULL, 'Products/Product19663/52d167.jpg', 19663, NULL, 'Product', 'ce48751ecf-5', NULL, NULL, NULL),
(89, NULL, NULL, 'Products/Product19663/8df698.jpg', 19663, NULL, 'Product', '47d47d341d-6', NULL, NULL, NULL),
(90, NULL, NULL, 'Products/Product19663/3a285f.jpg', 19663, NULL, 'Product', 'de492b37d6-7', NULL, NULL, NULL),
(91, NULL, NULL, 'Products/Product19121/f15bbf.jpg', 19121, NULL, 'Product', 'a55a1c0ce9-2', NULL, NULL, NULL),
(92, NULL, NULL, 'Products/Product19121/2dfeeb.jpg', 19121, NULL, 'Product', '44258d0323-3', NULL, NULL, NULL),
(93, NULL, NULL, 'Products/Product19118/198b5c.jpg', 19118, NULL, 'Product', '6b957f737a-2', NULL, NULL, NULL),
(94, NULL, NULL, 'Products/Product19118/467e81.jpg', 19118, NULL, 'Product', '8d34d227a0-3', NULL, NULL, NULL),
(95, NULL, NULL, 'Products/Product19120/45bedd.jpg', 19120, NULL, 'Product', 'c04fdddccb-2', NULL, NULL, NULL),
(96, NULL, NULL, 'Products/Product19120/f4051f.jpg', 19120, NULL, 'Product', 'aa0a8ea15d-3', NULL, NULL, NULL),
(97, NULL, NULL, 'Products/Product19117/b6f32b.jpg', 19117, NULL, 'Product', '3f3164cb86-2', NULL, NULL, NULL),
(98, NULL, NULL, 'Products/Product19117/b24391.jpg', 19117, NULL, 'Product', '3990960401-3', NULL, NULL, NULL),
(99, NULL, NULL, 'Products/Product19100/77ebf2.jpg', 19100, NULL, 'Product', '576fe81665-2', NULL, NULL, NULL),
(100, NULL, NULL, 'Products/Product19100/3ac042.jpg', 19100, NULL, 'Product', '0db6b9bb8d-3', NULL, NULL, NULL),
(101, NULL, NULL, 'Products/Product18865/20e880.jpg', 18865, NULL, 'Product', '52bcb20c25-2', NULL, NULL, NULL),
(102, NULL, NULL, 'Products/Product18865/ec4a23.jpg', 18865, NULL, 'Product', '9c09472752-3', NULL, NULL, NULL),
(103, NULL, NULL, 'Products/Product18866/aee051.jpg', 18866, NULL, 'Product', '7017c1d6ae-2', NULL, NULL, NULL),
(104, NULL, NULL, 'Products/Product18866/047e52.jpg', 18866, NULL, 'Product', '867389699d-3', NULL, NULL, NULL),
(105, NULL, NULL, 'Products/Product18862/bf42dd.jpg', 18862, NULL, 'Product', '3f3a95e8ae-2', NULL, NULL, NULL),
(106, NULL, NULL, 'Products/Product18862/847606.jpg', 18862, NULL, 'Product', '00dc236603-3', NULL, NULL, NULL),
(107, NULL, NULL, 'Products/Product18861/ef7be2.jpg', 18861, NULL, 'Product', '660940df81-2', NULL, NULL, NULL),
(108, NULL, NULL, 'Products/Product18861/0fdb93.jpg', 18861, NULL, 'Product', '4bd2852864-3', NULL, NULL, NULL),
(109, NULL, NULL, 'Products/Product18864/eced22.jpg', 18864, NULL, 'Product', 'f82924a176-2', NULL, NULL, NULL),
(110, NULL, NULL, 'Products/Product18864/c9def3.jpg', 18864, NULL, 'Product', '42c6eb225b-3', NULL, NULL, NULL),
(111, NULL, NULL, 'Products/Product18860/3654f0.jpg', 18860, NULL, 'Product', '06e381ce7a-2', NULL, NULL, NULL),
(112, NULL, NULL, 'Products/Product18860/dfd235.jpg', 18860, NULL, 'Product', '6e6f92321a-3', NULL, NULL, NULL),
(113, NULL, NULL, 'Products/Product18859/cce4cb.jpg', 18859, NULL, 'Product', '54e8542eac-2', NULL, NULL, NULL),
(114, NULL, NULL, 'Products/Product18859/dbbcad.jpg', 18859, NULL, 'Product', 'f60c5bf4f1-3', NULL, NULL, NULL),
(115, NULL, NULL, 'Products/Product18857/a3def8.jpg', 18857, NULL, 'Product', '9348c2e70d-2', NULL, NULL, NULL),
(116, NULL, NULL, 'Products/Product18857/b9bbb8.jpg', 18857, NULL, 'Product', 'fb5957f499-3', NULL, NULL, NULL),
(117, NULL, NULL, 'Products/Product18858/e38053.jpg', 18858, NULL, 'Product', 'dff7465275-2', NULL, NULL, NULL),
(118, NULL, NULL, 'Products/Product18858/d5eb0f.jpg', 18858, NULL, 'Product', '197b3147f3-3', NULL, NULL, NULL),
(119, NULL, NULL, 'Products/Product18592/9691f8.jpg', 18592, NULL, 'Product', 'ec1285e3a4-2', NULL, NULL, NULL),
(120, NULL, NULL, 'Products/Product18592/3927fc.jpg', 18592, NULL, 'Product', '90abcc1a85-3', NULL, NULL, NULL),
(121, NULL, NULL, 'Products/Product18487/575fde.jpg', 18487, NULL, 'Product', '14f8ce0c3c-2', NULL, NULL, NULL),
(122, NULL, NULL, 'Products/Product18487/2be380.jpg', 18487, NULL, 'Product', '08f76d075a-3', NULL, NULL, NULL),
(123, NULL, NULL, 'Products/Product18490/3707cc.jpg', 18490, NULL, 'Product', '1db5195bce-2', NULL, NULL, NULL),
(124, NULL, NULL, 'Products/Product18490/a4cc70.jpg', 18490, NULL, 'Product', '3dc7c99aea-3', NULL, NULL, NULL),
(125, NULL, NULL, 'Products/Product18439/f6fde5.jpg', 18439, NULL, 'Product', '24d220e340-2', NULL, NULL, NULL),
(126, NULL, NULL, 'Products/Product18439/d659a4.jpg', 18439, NULL, 'Product', 'cae3f33e00-3', NULL, NULL, NULL),
(127, NULL, NULL, 'Products/Product18441/5f5ea1.jpg', 18441, NULL, 'Product', '5387dba95d-2', NULL, NULL, NULL),
(128, NULL, NULL, 'Products/Product18441/b8a86e.jpg', 18441, NULL, 'Product', '0aa6d9afac-3', NULL, NULL, NULL),
(129, NULL, NULL, 'Products/Product18488/32e9bb.jpg', 18488, NULL, 'Product', '89c5e632cd-2', NULL, NULL, NULL),
(130, NULL, NULL, 'Products/Product18488/3adc4a.jpg', 18488, NULL, 'Product', 'a8f40a7758-3', NULL, NULL, NULL),
(131, NULL, NULL, 'Products/Product18472/cf34f8.jpg', 18472, NULL, 'Product', '426882de00-2', NULL, NULL, NULL),
(132, NULL, NULL, 'Products/Product18472/e2de88.jpg', 18472, NULL, 'Product', 'd62ebf4970-3', NULL, NULL, NULL),
(133, NULL, NULL, 'Products/Product18440/031a42.jpg', 18440, NULL, 'Product', '392ed9a2af-2', NULL, NULL, NULL),
(134, NULL, NULL, 'Products/Product18440/374330.jpg', 18440, NULL, 'Product', '9c024c1fe0-3', NULL, NULL, NULL),
(135, NULL, NULL, 'Products/Product18489/ee6d36.jpg', 18489, NULL, 'Product', '55ca7bc109-2', NULL, NULL, NULL),
(136, NULL, NULL, 'Products/Product18489/52bdc6.jpg', 18489, NULL, 'Product', '50b4d5869e-3', NULL, NULL, NULL),
(137, NULL, NULL, 'Products/Product18438/fab0f2.jpg', 18438, NULL, 'Product', 'c30a4cda54-2', NULL, NULL, NULL),
(138, NULL, NULL, 'Products/Product18438/8981b6.jpg', 18438, NULL, 'Product', '46eaf1c831-3', NULL, NULL, NULL),
(139, NULL, NULL, 'Products/Product18473/8e8309.jpg', 18473, NULL, 'Product', 'a8fe71140b-2', NULL, NULL, NULL),
(140, NULL, NULL, 'Products/Product18473/a5170c.jpg', 18473, NULL, 'Product', 'f26086f980-3', NULL, NULL, NULL),
(141, NULL, NULL, 'Products/Product18473/b7ac4f.jpg', 18473, NULL, 'Product', '0d3c39e5b7-4', NULL, NULL, NULL),
(142, NULL, NULL, 'Products/Product18473/a5d9e8.jpg', 18473, NULL, 'Product', '41deb8e2c3-5', NULL, NULL, NULL),
(143, NULL, NULL, 'Products/Product18305/e0fd8b.jpg', 18305, NULL, 'Product', 'f364e4c796-2', NULL, NULL, NULL),
(144, NULL, NULL, 'Products/Product18305/1c11f6.jpg', 18305, NULL, 'Product', '10e252b791-3', NULL, NULL, NULL),
(145, NULL, NULL, 'Products/Product18091/08148e.jpg', 18091, NULL, 'Product', '644d8aa1cd-2', NULL, NULL, NULL),
(146, NULL, NULL, 'Products/Product18091/49af3b.jpg', 18091, NULL, 'Product', 'c5339dbb50-3', NULL, NULL, NULL),
(147, NULL, NULL, 'Products/Product18091/e5eaa1.jpg', 18091, NULL, 'Product', '3fe8bd25dc-4', NULL, NULL, NULL),
(148, NULL, NULL, 'Products/Product18306/a886a0.jpg', 18306, NULL, 'Product', '343b41c925-2', NULL, NULL, NULL),
(149, NULL, NULL, 'Products/Product18306/207fbf.jpg', 18306, NULL, 'Product', '67771b806c-3', NULL, NULL, NULL),
(150, NULL, NULL, 'Products/Product18364/19c436.jpg', 18364, NULL, 'Product', 'ed1d531a1d-2', NULL, NULL, NULL),
(151, NULL, NULL, 'Products/Product18364/7427b4.jpg', 18364, NULL, 'Product', 'b5dcce3c32-3', NULL, NULL, NULL),
(152, NULL, NULL, 'Products/Product18364/aed74c.jpg', 18364, NULL, 'Product', '2b7fe86b94-4', NULL, NULL, NULL),
(153, NULL, NULL, 'Products/Product18090/21700a.jpg', 18090, NULL, 'Product', '8af28786e8-2', NULL, NULL, NULL),
(154, NULL, NULL, 'Products/Product18090/2fedd6.jpg', 18090, NULL, 'Product', '6a5211e654-3', NULL, NULL, NULL),
(155, NULL, NULL, 'Products/Product18090/d55da3.jpg', 18090, NULL, 'Product', 'fa2ba888b3-4', NULL, NULL, NULL),
(156, NULL, NULL, 'Products/Product18304/9e5151.jpg', 18304, NULL, 'Product', 'c9440d7027-2', NULL, NULL, NULL),
(157, NULL, NULL, 'Products/Product18304/0f2d1c.jpg', 18304, NULL, 'Product', 'f1605076de-3', NULL, NULL, NULL),
(158, NULL, NULL, 'Products/Product18363/78f899.jpg', 18363, NULL, 'Product', '34cf2451f0-2', NULL, NULL, NULL),
(159, NULL, NULL, 'Products/Product18363/95191d.jpg', 18363, NULL, 'Product', 'cac649d642-3', NULL, NULL, NULL),
(160, NULL, NULL, 'Products/Product18363/a0a54a.jpg', 18363, NULL, 'Product', '73412b2d83-4', NULL, NULL, NULL),
(161, NULL, NULL, 'Products/Product18303/f6f986.jpg', 18303, NULL, 'Product', 'c0107c4551-2', NULL, NULL, NULL),
(162, NULL, NULL, 'Products/Product18303/549a86.jpg', 18303, NULL, 'Product', '118a2720bb-3', NULL, NULL, NULL),
(163, NULL, NULL, 'Products/Product18071/e936e2.jpg', 18071, NULL, 'Product', '0dfaf816d7-2', NULL, NULL, NULL),
(164, NULL, NULL, 'Products/Product18071/a912bb.jpg', 18071, NULL, 'Product', 'fafd9cfd2d-3', NULL, NULL, NULL),
(165, NULL, NULL, 'Products/Product18071/3a1b5d.jpg', 18071, NULL, 'Product', '8fe869c149-4', NULL, NULL, NULL),
(166, NULL, NULL, 'Products/Product18071/af2cc9.jpg', 18071, NULL, 'Product', '900df693e1-5', NULL, NULL, NULL),
(167, NULL, NULL, 'Products/Product18081/5e2743.jpg', 18081, NULL, 'Product', 'abb9eea90f-2', NULL, NULL, NULL),
(168, NULL, NULL, 'Products/Product18081/379d2e.jpg', 18081, NULL, 'Product', '142f7c4fda-3', NULL, NULL, NULL),
(169, NULL, NULL, 'Products/Product18081/0b913c.jpg', 18081, NULL, 'Product', 'acf924d213-4', NULL, NULL, NULL),
(170, NULL, NULL, 'Products/Product18052/a514b3.jpg', 18052, NULL, 'Product', '0a45ce4e51-2', NULL, NULL, NULL),
(171, NULL, NULL, 'Products/Product18052/47bcc3.jpg', 18052, NULL, 'Product', '6bd6815f13-3', NULL, NULL, NULL),
(172, NULL, NULL, 'Products/Product18052/95878b.jpg', 18052, NULL, 'Product', '399d9d3681-4', NULL, NULL, NULL),
(173, NULL, NULL, 'Products/Product18032/385bb0.jpg', 18032, NULL, 'Product', '3d08f92d11-2', NULL, NULL, NULL),
(174, NULL, NULL, 'Products/Product18032/dc08b9.jpg', 18032, NULL, 'Product', 'dcb28fdc4d-3', NULL, NULL, NULL),
(175, NULL, NULL, 'Products/Product18032/ada0f1.jpg', 18032, NULL, 'Product', '3d27b3d251-4', NULL, NULL, NULL),
(176, NULL, NULL, 'Products/Product18030/5833ae.jpg', 18030, NULL, 'Product', 'bfac653f24-2', NULL, NULL, NULL),
(177, NULL, NULL, 'Products/Product18030/9a89c4.jpg', 18030, NULL, 'Product', 'f34e4d6754-3', NULL, NULL, NULL),
(178, NULL, NULL, 'Products/Product18030/eba8b2.jpg', 18030, NULL, 'Product', '4fa5e468ec-4', NULL, NULL, NULL),
(179, NULL, NULL, 'Products/Product18051/4cf2e9.jpg', 18051, NULL, 'Product', 'd5fcba1237-2', NULL, NULL, NULL),
(180, NULL, NULL, 'Products/Product18051/9c162a.jpg', 18051, NULL, 'Product', 'db81f7876c-3', NULL, NULL, NULL),
(181, NULL, NULL, 'Products/Product18051/83b788.jpg', 18051, NULL, 'Product', 'e1142c97c1-4', NULL, NULL, NULL),
(182, NULL, NULL, 'Products/Product18051/934025.jpg', 18051, NULL, 'Product', '10040cfdb9-5', NULL, NULL, NULL),
(183, NULL, NULL, 'Products/Product18302/3b14d8.jpg', 18302, NULL, 'Product', '403eb4a143-2', NULL, NULL, NULL),
(184, NULL, NULL, 'Products/Product18302/94e981.jpg', 18302, NULL, 'Product', '820dc0d14c-3', NULL, NULL, NULL),
(185, NULL, NULL, 'Products/Product18302/1550f6.jpg', 18302, NULL, 'Product', 'e5db2d9036-4', NULL, NULL, NULL),
(186, NULL, NULL, 'Products/Product18302/e28e4c.jpg', 18302, NULL, 'Product', 'aa511ec163-5', NULL, NULL, NULL),
(187, NULL, NULL, 'Products/Product18302/02d25a.jpg', 18302, NULL, 'Product', '2568ca9379-6', NULL, NULL, NULL),
(188, NULL, NULL, 'Products/Product18028/70a739.jpg', 18028, NULL, 'Product', '780f4559db-2', NULL, NULL, NULL),
(189, NULL, NULL, 'Products/Product18028/dcb3eb.jpg', 18028, NULL, 'Product', 'edd46415cb-3', NULL, NULL, NULL),
(190, NULL, NULL, 'Products/Product18025/7e6daa.jpg', 18025, NULL, 'Product', '9cc5cd2a09-2', NULL, NULL, NULL),
(191, NULL, NULL, 'Products/Product18025/7bfeb2.jpg', 18025, NULL, 'Product', '1575158484-3', NULL, NULL, NULL),
(192, NULL, NULL, 'Products/Product18031/95039b.jpg', 18031, NULL, 'Product', 'd56e96ac2c-2', NULL, NULL, NULL),
(193, NULL, NULL, 'Products/Product18031/78d56b.jpg', 18031, NULL, 'Product', '9b7056e1ec-3', NULL, NULL, NULL),
(194, NULL, NULL, 'Products/Product18031/1bdf41.jpg', 18031, NULL, 'Product', '4981928d00-4', NULL, NULL, NULL),
(195, NULL, NULL, 'Products/Product18029/d39e21.jpg', 18029, NULL, 'Product', '3ca827a5f6-2', NULL, NULL, NULL),
(196, NULL, NULL, 'Products/Product18029/e0c383.jpg', 18029, NULL, 'Product', '305cd30778-3', NULL, NULL, NULL),
(197, NULL, NULL, 'Products/Product18029/522593.jpg', 18029, NULL, 'Product', '0d7bb2ee27-4', NULL, NULL, NULL),
(198, NULL, NULL, 'Products/Product18018/31a378.jpg', 18018, NULL, 'Product', '907547ab09-2', NULL, NULL, NULL),
(199, NULL, NULL, 'Products/Product18018/f8b85f.jpg', 18018, NULL, 'Product', '3fedba3c6e-3', NULL, NULL, NULL),
(200, NULL, NULL, 'Products/Product18016/536131.jpg', 18016, NULL, 'Product', '747a8bef0b-2', NULL, NULL, NULL),
(201, NULL, NULL, 'Products/Product18016/089022.jpg', 18016, NULL, 'Product', '2c8da42794-3', NULL, NULL, NULL),
(202, NULL, NULL, 'Products/Product18016/788ab9.jpg', 18016, NULL, 'Product', '0a095b8255-4', NULL, NULL, NULL),
(203, NULL, NULL, 'Products/Product18015/bd756d.jpg', 18015, NULL, 'Product', 'd1ccda027b-2', NULL, NULL, NULL),
(204, NULL, NULL, 'Products/Product18015/b22cf6.jpg', 18015, NULL, 'Product', 'bbe93c82fd-3', NULL, NULL, NULL),
(205, NULL, NULL, 'Products/Product16228/41aac3.jpg', 16228, NULL, 'Product', '1a3b1781f4-2', NULL, NULL, NULL),
(206, NULL, NULL, 'Products/Product16228/22554f.jpg', 16228, NULL, 'Product', '08e2ed6c78-3', NULL, NULL, NULL),
(207, NULL, NULL, 'Products/Product16228/28a417.jpg', 16228, NULL, 'Product', 'cd5117e166-4', NULL, NULL, NULL),
(208, NULL, NULL, 'Products/Product16228/e37d98.jpg', 16228, NULL, 'Product', 'dc71af5ffb-5', NULL, NULL, NULL),
(209, NULL, NULL, 'Products/Product16228/bc0ca3.jpg', 16228, NULL, 'Product', '5955db2901-6', NULL, NULL, NULL),
(210, NULL, NULL, 'Products/Product16228/5d34dc.jpg', 16228, NULL, 'Product', '74caef73d1-7', NULL, NULL, NULL),
(211, NULL, NULL, 'Products/Product16228/30f7a8.jpg', 16228, NULL, 'Product', 'c0b6c09f18-8', NULL, NULL, NULL),
(212, NULL, NULL, 'Products/Product16228/01d3e5.jpg', 16228, NULL, 'Product', '6f0b3908ae-9', NULL, NULL, NULL),
(213, NULL, NULL, 'Products/Product16223/76b8a2.jpg', 16223, NULL, 'Product', '3a048ec984-2', NULL, NULL, NULL),
(214, NULL, NULL, 'Products/Product16223/5f1640.jpg', 16223, NULL, 'Product', '9a61342a28-3', NULL, NULL, NULL),
(215, NULL, NULL, 'Products/Product16223/4fa2b7.jpg', 16223, NULL, 'Product', 'a88cf24cef-4', NULL, NULL, NULL),
(216, NULL, NULL, 'Products/Product16223/241cbe.jpg', 16223, NULL, 'Product', '6e0b5f48f1-5', NULL, NULL, NULL),
(217, NULL, NULL, 'Products/Product16223/cecb01.jpg', 16223, NULL, 'Product', 'c8ce2dfc50-6', NULL, NULL, NULL),
(218, NULL, NULL, 'Products/Product16223/155477.jpg', 16223, NULL, 'Product', '0086222bfb-7', NULL, NULL, NULL),
(219, NULL, NULL, 'Products/Product16223/38fd8a.jpg', 16223, NULL, 'Product', 'b0e2b1f6f9-8', NULL, NULL, NULL),
(220, NULL, NULL, 'Products/Product16223/e54a1e.jpg', 16223, NULL, 'Product', '4c2208d3ba-9', NULL, NULL, NULL),
(221, NULL, NULL, 'Products/Product16223/d5224f.jpg', 16223, NULL, 'Product', '09af33e70b-10', NULL, NULL, NULL),
(222, NULL, NULL, 'Products/Product16231/94ecbc.jpg', 16231, NULL, 'Product', '6b1125e4be-2', NULL, NULL, NULL),
(223, NULL, NULL, 'Products/Product16231/e2f551.jpg', 16231, NULL, 'Product', 'cdf060e065-3', NULL, NULL, NULL),
(224, NULL, NULL, 'Products/Product16231/fea795.jpg', 16231, NULL, 'Product', 'da4de4ec53-4', NULL, NULL, NULL),
(225, NULL, NULL, 'Products/Product16231/0351e3.jpg', 16231, NULL, 'Product', '32a0c3c60d-5', NULL, NULL, NULL),
(226, NULL, NULL, 'Products/Product16231/98b2d8.jpg', 16231, NULL, 'Product', 'f8a1462991-6', NULL, NULL, NULL),
(227, NULL, NULL, 'Products/Product16231/017f88.jpg', 16231, NULL, 'Product', '3f03fdf65f-7', NULL, NULL, NULL),
(228, NULL, NULL, 'Products/Product16231/1d830c.jpg', 16231, NULL, 'Product', '4bac44594d-8', NULL, NULL, NULL),
(229, NULL, NULL, 'Products/Product16231/90c2fd.jpg', 16231, NULL, 'Product', '807293c670-9', NULL, NULL, NULL),
(230, NULL, NULL, 'Products/Product16231/fc1b89.jpg', 16231, NULL, 'Product', '54056e29c3-10', NULL, NULL, NULL),
(231, NULL, NULL, 'Products/Product16222/609ff0.jpg', 16222, NULL, 'Product', '9d70074332-2', NULL, NULL, NULL),
(232, NULL, NULL, 'Products/Product16222/e43bea.jpg', 16222, NULL, 'Product', 'a884c43559-3', NULL, NULL, NULL),
(233, NULL, NULL, 'Products/Product16222/51d29a.jpg', 16222, NULL, 'Product', '3a5201cab1-4', NULL, NULL, NULL),
(234, NULL, NULL, 'Products/Product16222/45637a.jpg', 16222, NULL, 'Product', 'ce77701603-5', NULL, NULL, NULL),
(235, NULL, NULL, 'Products/Product16222/02efba.jpg', 16222, NULL, 'Product', 'b64de01793-6', NULL, NULL, NULL),
(236, NULL, NULL, 'Products/Product16222/baf860.jpg', 16222, NULL, 'Product', 'd1ee806b5e-7', NULL, NULL, NULL),
(237, NULL, NULL, 'Products/Product16222/e0bb3a.jpg', 16222, NULL, 'Product', 'e8ef6fd658-8', NULL, NULL, NULL),
(238, NULL, NULL, 'Products/Product16222/c26e15.jpg', 16222, NULL, 'Product', 'a6f0df1209-9', NULL, NULL, NULL),
(239, NULL, NULL, 'Products/Product16222/8a0876.jpg', 16222, NULL, 'Product', 'ab34163fe8-10', NULL, NULL, NULL),
(240, NULL, NULL, 'Products/Product16217/ca7572.jpg', 16217, NULL, 'Product', 'd2c7f20fda-2', NULL, NULL, NULL),
(241, NULL, NULL, 'Products/Product16217/23198f.jpg', 16217, NULL, 'Product', '8692215db5-3', NULL, NULL, NULL),
(242, NULL, NULL, 'Products/Product16217/5a0c56.jpg', 16217, NULL, 'Product', 'f9079c89e2-4', NULL, NULL, NULL),
(243, NULL, NULL, 'Products/Product16217/c198d3.jpg', 16217, NULL, 'Product', '943869769a-5', NULL, NULL, NULL),
(244, NULL, NULL, 'Products/Product16217/6957cc.jpg', 16217, NULL, 'Product', '9204e9387a-6', NULL, NULL, NULL),
(245, NULL, NULL, 'Products/Product16217/c54f92.jpg', 16217, NULL, 'Product', '346274af59-7', NULL, NULL, NULL),
(246, NULL, NULL, 'Products/Product16217/91896c.jpg', 16217, NULL, 'Product', '6c7df7a9d0-8', NULL, NULL, NULL),
(247, NULL, NULL, 'Products/Product16217/a34ea4.jpg', 16217, NULL, 'Product', 'dbd5e3a015-9', NULL, NULL, NULL),
(248, NULL, NULL, 'Products/Product16217/8b54fe.jpg', 16217, NULL, 'Product', 'd880a33f3e-10', NULL, NULL, NULL),
(249, NULL, NULL, 'Products/Product16218/fa0e9a.jpg', 16218, NULL, 'Product', 'd563062e94-2', NULL, NULL, NULL),
(250, NULL, NULL, 'Products/Product16218/daa804.jpg', 16218, NULL, 'Product', '7865fa0655-3', NULL, NULL, NULL),
(251, NULL, NULL, 'Products/Product16218/e1da5d.jpg', 16218, NULL, 'Product', '7b281cd638-4', NULL, NULL, NULL),
(252, NULL, NULL, 'Products/Product16218/43b39c.jpg', 16218, NULL, 'Product', '060a85b428-5', NULL, NULL, NULL),
(253, NULL, NULL, 'Products/Product16218/56559d.jpg', 16218, NULL, 'Product', '701ab6058a-6', NULL, NULL, NULL),
(254, NULL, NULL, 'Products/Product16218/6a1c22.jpg', 16218, NULL, 'Product', '0f02fa5ccd-7', NULL, NULL, NULL),
(255, NULL, NULL, 'Products/Product16218/0cf242.jpg', 16218, NULL, 'Product', '7cb5d09905-8', NULL, NULL, NULL),
(256, NULL, NULL, 'Products/Product16218/23395e.jpg', 16218, NULL, 'Product', '19f7dfd9a1-9', NULL, NULL, NULL),
(257, NULL, NULL, 'Products/Product16218/ef66db.jpg', 16218, NULL, 'Product', 'a9b6dc6b18-10', NULL, NULL, NULL),
(258, NULL, NULL, 'Products/Product16202/3210d2.jpg', 16202, NULL, 'Product', '161b1e5d58-2', NULL, NULL, NULL),
(259, NULL, NULL, 'Products/Product16202/40c0b1.jpg', 16202, NULL, 'Product', '6bff9aa7a3-3', NULL, NULL, NULL),
(260, NULL, NULL, 'Products/Product16202/1f710a.jpg', 16202, NULL, 'Product', '18acf957cc-4', NULL, NULL, NULL),
(261, NULL, NULL, 'Products/Product16202/e4504e.jpg', 16202, NULL, 'Product', 'a556904c70-5', NULL, NULL, NULL),
(262, NULL, NULL, 'Products/Product16202/bbc755.jpg', 16202, NULL, 'Product', 'e4f079951f-6', NULL, NULL, NULL),
(263, NULL, NULL, 'Products/Product16202/c66ccc.jpg', 16202, NULL, 'Product', '7f29b3661f-7', NULL, NULL, NULL),
(264, NULL, NULL, 'Products/Product16202/d212b2.jpg', 16202, NULL, 'Product', '822cf7ad3e-8', NULL, NULL, NULL),
(265, NULL, NULL, 'Products/Product16202/beca99.jpg', 16202, NULL, 'Product', '3764278ff8-9', NULL, NULL, NULL),
(266, NULL, NULL, 'Products/Product16197/4822de.jpg', 16197, NULL, 'Product', '70ed532cb4-2', NULL, NULL, NULL),
(267, NULL, NULL, 'Products/Product16197/6de566.jpg', 16197, NULL, 'Product', '45ca35bd2c-3', NULL, NULL, NULL),
(268, NULL, NULL, 'Products/Product16197/fb4064.jpg', 16197, NULL, 'Product', 'b64c6df60e-4', NULL, NULL, NULL),
(269, NULL, NULL, 'Products/Product16197/a5f1b9.jpg', 16197, NULL, 'Product', '7d19ec0f07-5', NULL, NULL, NULL),
(270, NULL, NULL, 'Products/Product16197/34c73b.jpg', 16197, NULL, 'Product', 'df51576343-6', NULL, NULL, NULL),
(271, NULL, NULL, 'Products/Product16197/f4116d.jpg', 16197, NULL, 'Product', '381e4f9217-7', NULL, NULL, NULL),
(272, NULL, NULL, 'Products/Product16197/11844b.jpg', 16197, NULL, 'Product', '01c123b547-8', NULL, NULL, NULL),
(273, NULL, NULL, 'Products/Product16197/bf61bd.jpg', 16197, NULL, 'Product', '591cc8f40b-9', NULL, NULL, NULL),
(274, NULL, NULL, 'Products/Product16197/b31537.jpg', 16197, NULL, 'Product', '2e0ddb421c-10', NULL, NULL, NULL),
(275, NULL, NULL, 'Products/Product16191/a49f14.jpg', 16191, NULL, 'Product', 'fa0dbf4ac5-2', NULL, NULL, NULL),
(276, NULL, NULL, 'Products/Product16191/9e2191.jpg', 16191, NULL, 'Product', '08455cb69c-3', NULL, NULL, NULL),
(277, NULL, NULL, 'Products/Product16191/946cfc.jpg', 16191, NULL, 'Product', 'bfaf22e990-4', NULL, NULL, NULL),
(278, NULL, NULL, 'Products/Product16191/07c46e.jpg', 16191, NULL, 'Product', 'a072fbbb1d-5', NULL, NULL, NULL),
(279, NULL, NULL, 'Products/Product16191/ff08c5.jpg', 16191, NULL, 'Product', '3b6c02ec48-6', NULL, NULL, NULL),
(280, NULL, NULL, 'Products/Product16191/dfc2a6.jpg', 16191, NULL, 'Product', 'c43b1e8545-7', NULL, NULL, NULL),
(281, NULL, NULL, 'Products/Product16191/3fd5ae.jpg', 16191, NULL, 'Product', 'b40902b8da-8', NULL, NULL, NULL),
(282, NULL, NULL, 'Products/Product16191/378aa8.jpg', 16191, NULL, 'Product', 'c94cd1331d-9', NULL, NULL, NULL),
(283, NULL, NULL, 'Products/Product16191/f42451.jpg', 16191, NULL, 'Product', '439b0b40f6-10', NULL, NULL, NULL),
(284, NULL, NULL, 'Products/Product16215/672553.jpg', 16215, NULL, 'Product', 'fe687e734d-2', NULL, NULL, NULL),
(285, NULL, NULL, 'Products/Product16215/c857ef.jpg', 16215, NULL, 'Product', 'eb58026b5b-3', NULL, NULL, NULL),
(286, NULL, NULL, 'Products/Product16215/00ca4d.jpg', 16215, NULL, 'Product', 'bd44328738-4', NULL, NULL, NULL),
(287, NULL, NULL, 'Products/Product16215/fad6c5.jpg', 16215, NULL, 'Product', 'a13d039032-5', NULL, NULL, NULL),
(288, NULL, NULL, 'Products/Product16215/605448.jpg', 16215, NULL, 'Product', 'ac5059aa80-6', NULL, NULL, NULL),
(289, NULL, NULL, 'Products/Product16215/584e6a.jpg', 16215, NULL, 'Product', '9ec27ce703-7', NULL, NULL, NULL),
(290, NULL, NULL, 'Products/Product16215/c7f7be.jpg', 16215, NULL, 'Product', '4c3bf13688-8', NULL, NULL, NULL),
(291, NULL, NULL, 'Products/Product16215/1f1af8.jpg', 16215, NULL, 'Product', '45ed8f9713-9', NULL, NULL, NULL),
(292, NULL, NULL, 'Products/Product16215/6521d0.jpg', 16215, NULL, 'Product', 'cc18a1a5f9-10', NULL, NULL, NULL),
(293, NULL, NULL, 'Products/Product16206/e0b5f8.jpg', 16206, NULL, 'Product', 'a2a60d44c9-2', NULL, NULL, NULL),
(294, NULL, NULL, 'Products/Product16206/8e0e28.jpg', 16206, NULL, 'Product', '78c71f05fb-3', NULL, NULL, NULL),
(295, NULL, NULL, 'Products/Product16206/c130be.jpg', 16206, NULL, 'Product', 'a2dc0ef38d-4', NULL, NULL, NULL),
(296, NULL, NULL, 'Products/Product16206/c9241b.jpg', 16206, NULL, 'Product', '7dde99081b-5', NULL, NULL, NULL),
(297, NULL, NULL, 'Products/Product16206/a120c1.jpg', 16206, NULL, 'Product', '6c71cab195-6', NULL, NULL, NULL),
(298, NULL, NULL, 'Products/Product16206/0d839f.jpg', 16206, NULL, 'Product', '99e00953e9-7', NULL, NULL, NULL),
(299, NULL, NULL, 'Products/Product16206/4413f5.jpg', 16206, NULL, 'Product', '7988741750-8', NULL, NULL, NULL),
(300, NULL, NULL, 'Products/Product16206/b07076.jpg', 16206, NULL, 'Product', '4cfe0a1cc8-9', NULL, NULL, NULL),
(301, NULL, NULL, 'Products/Product16206/e4042b.jpg', 16206, NULL, 'Product', '55aab992f5-10', NULL, NULL, NULL),
(302, NULL, NULL, 'Products/Product16187/130f56.jpg', 16187, NULL, 'Product', '74f737d311-2', NULL, NULL, NULL),
(303, NULL, NULL, 'Products/Product16187/8ebc06.jpg', 16187, NULL, 'Product', '9bdf2b02f0-3', NULL, NULL, NULL),
(304, NULL, NULL, 'Products/Product16187/91c2fd.jpg', 16187, NULL, 'Product', '20c21ab0f2-4', NULL, NULL, NULL),
(305, NULL, NULL, 'Products/Product16187/b13cd2.jpg', 16187, NULL, 'Product', '3d09a071b9-5', NULL, NULL, NULL),
(306, NULL, NULL, 'Products/Product16187/dde5b4.jpg', 16187, NULL, 'Product', '54ba71c131-6', NULL, NULL, NULL),
(307, NULL, NULL, 'Products/Product16187/949789.jpg', 16187, NULL, 'Product', 'f5f6f796a0-7', NULL, NULL, NULL),
(308, NULL, NULL, 'Products/Product16187/7b740f.jpg', 16187, NULL, 'Product', '48a27f382e-8', NULL, NULL, NULL),
(309, NULL, NULL, 'Products/Product16187/5c333c.jpg', 16187, NULL, 'Product', 'fa41ab5572-9', NULL, NULL, NULL),
(310, NULL, NULL, 'Products/Product16187/ddf6ff.jpg', 16187, NULL, 'Product', '68ed7251fb-10', NULL, NULL, NULL),
(311, NULL, NULL, 'Products/Product16177/04b685.jpg', 16177, NULL, 'Product', 'bd70e1b4dc-2', NULL, NULL, NULL),
(312, NULL, NULL, 'Products/Product16177/6e364a.jpg', 16177, NULL, 'Product', 'c08f45cea0-3', NULL, NULL, NULL),
(313, NULL, NULL, 'Products/Product16177/6a6083.jpg', 16177, NULL, 'Product', '1ca4e5dd28-4', NULL, NULL, NULL),
(314, NULL, NULL, 'Products/Product16177/17d56b.jpg', 16177, NULL, 'Product', 'ba59023076-5', NULL, NULL, NULL),
(315, NULL, NULL, 'Products/Product16177/4af4a4.jpg', 16177, NULL, 'Product', '71c4a2ff2d-6', NULL, NULL, NULL),
(316, NULL, NULL, 'Products/Product16177/7a3d08.jpg', 16177, NULL, 'Product', 'c6d86888de-7', NULL, NULL, NULL),
(317, NULL, NULL, 'Products/Product16177/79f04c.jpg', 16177, NULL, 'Product', 'ad24659b7b-8', NULL, NULL, NULL),
(318, NULL, NULL, 'Products/Product16177/3dd317.jpg', 16177, NULL, 'Product', 'a52a464407-9', NULL, NULL, NULL),
(319, NULL, NULL, 'Products/Product16177/c16b78.jpg', 16177, NULL, 'Product', '9e0a40de64-10', NULL, NULL, NULL),
(320, NULL, NULL, 'Products/Product16125/63549f.jpg', 16125, NULL, 'Product', '4e27518f7d-2', NULL, NULL, NULL),
(321, NULL, NULL, 'Products/Product16125/d9e069.jpg', 16125, NULL, 'Product', '2fad234d0a-3', NULL, NULL, NULL),
(322, NULL, NULL, 'Products/Product16125/62738a.jpg', 16125, NULL, 'Product', 'a42906d9d0-4', NULL, NULL, NULL),
(323, NULL, NULL, 'Products/Product16125/67d124.jpg', 16125, NULL, 'Product', 'ea28339e7e-5', NULL, NULL, NULL),
(324, NULL, NULL, 'Products/Product16125/f4eeb8.jpg', 16125, NULL, 'Product', 'f53fe45920-6', NULL, NULL, NULL),
(325, NULL, NULL, 'Products/Product16124/79fd24.jpg', 16124, NULL, 'Product', '949dbfabc9-2', NULL, NULL, NULL),
(326, NULL, NULL, 'Products/Product16124/9b05fd.jpg', 16124, NULL, 'Product', '90379666be-3', NULL, NULL, NULL),
(327, NULL, NULL, 'Products/Product16124/264514.jpg', 16124, NULL, 'Product', 'efc842e8fb-4', NULL, NULL, NULL),
(328, NULL, NULL, 'Products/Product16124/5d6bcb.jpg', 16124, NULL, 'Product', 'ea7e161b20-5', NULL, NULL, NULL),
(329, NULL, NULL, 'Products/Product16124/81740e.jpg', 16124, NULL, 'Product', '287c2e2ce4-6', NULL, NULL, NULL),
(330, NULL, NULL, 'Products/Product16168/f170ae.jpg', 16168, NULL, 'Product', 'a335a333d4-2', NULL, NULL, NULL),
(331, NULL, NULL, 'Products/Product16168/40bbcc.jpg', 16168, NULL, 'Product', '5fbfaaae75-3', NULL, NULL, NULL),
(332, NULL, NULL, 'Products/Product16168/3a072b.jpg', 16168, NULL, 'Product', '7bb185352e-4', NULL, NULL, NULL),
(333, NULL, NULL, 'Products/Product16168/e59852.jpg', 16168, NULL, 'Product', 'e32aad724e-5', NULL, NULL, NULL),
(334, NULL, NULL, 'Products/Product16168/f0c6d0.jpg', 16168, NULL, 'Product', 'fe703408a9-6', NULL, NULL, NULL),
(335, NULL, NULL, 'Products/Product16168/1ec5d1.jpg', 16168, NULL, 'Product', 'cce98c467e-7', NULL, NULL, NULL),
(336, NULL, NULL, 'Products/Product16168/503509.jpg', 16168, NULL, 'Product', 'a64f9035af-8', NULL, NULL, NULL),
(337, NULL, NULL, 'Products/Product16168/2c56dc.jpg', 16168, NULL, 'Product', '9bf3fe1471-9', NULL, NULL, NULL),
(338, NULL, NULL, 'Products/Product16168/bb0626.jpg', 16168, NULL, 'Product', 'e003671f79-10', NULL, NULL, NULL),
(339, NULL, NULL, 'Products/Product16123/d5659a.jpg', 16123, NULL, 'Product', '7cf757a697-2', NULL, NULL, NULL),
(340, NULL, NULL, 'Products/Product16123/299b85.jpg', 16123, NULL, 'Product', '847a6fe546-3', NULL, NULL, NULL),
(341, NULL, NULL, 'Products/Product16123/0d4db5.jpg', 16123, NULL, 'Product', '16c42379ea-4', NULL, NULL, NULL),
(342, NULL, NULL, 'Products/Product16123/b057f5.jpg', 16123, NULL, 'Product', '1e14985885-5', NULL, NULL, NULL),
(343, NULL, NULL, 'Products/Product16120/df05e5.jpg', 16120, NULL, 'Product', '4bafa391a2-2', NULL, NULL, NULL),
(344, NULL, NULL, 'Products/Product16120/c60f36.jpg', 16120, NULL, 'Product', '5c15f6b5e7-3', NULL, NULL, NULL),
(345, NULL, NULL, 'Products/Product16120/80d229.jpg', 16120, NULL, 'Product', '69bb6e2e01-4', NULL, NULL, NULL),
(346, NULL, NULL, 'Products/Product16120/e5263d.jpg', 16120, NULL, 'Product', '114128ec3f-5', NULL, NULL, NULL),
(347, NULL, NULL, 'Products/Product16120/c7cc15.jpg', 16120, NULL, 'Product', 'd0e764aee5-6', NULL, NULL, NULL),
(348, NULL, NULL, 'Products/Product16121/75e024.jpg', 16121, NULL, 'Product', '8ca63ffd69-2', NULL, NULL, NULL),
(349, NULL, NULL, 'Products/Product16121/d00b9f.jpg', 16121, NULL, 'Product', 'e8c1fcaadf-3', NULL, NULL, NULL),
(350, NULL, NULL, 'Products/Product16121/139ce9.jpg', 16121, NULL, 'Product', '1d42c4f2bf-4', NULL, NULL, NULL),
(351, NULL, NULL, 'Products/Product16121/a4355d.jpg', 16121, NULL, 'Product', 'a88c4c2c56-5', NULL, NULL, NULL),
(352, NULL, NULL, 'Products/Product16121/1554c8.jpg', 16121, NULL, 'Product', '53d848f21e-6', NULL, NULL, NULL),
(353, NULL, NULL, 'Products/Product16119/94fcae.jpg', 16119, NULL, 'Product', 'c46869e87f-2', NULL, NULL, NULL),
(354, NULL, NULL, 'Products/Product16119/7dd812.jpg', 16119, NULL, 'Product', '292bbab601-3', NULL, NULL, NULL),
(355, NULL, NULL, 'Products/Product16119/c106b0.jpg', 16119, NULL, 'Product', '7b7863acba-4', NULL, NULL, NULL),
(356, NULL, NULL, 'Products/Product16119/71a742.jpg', 16119, NULL, 'Product', 'e0a0b89d3f-5', NULL, NULL, NULL),
(357, NULL, NULL, 'Products/Product16119/7b8b9c.jpg', 16119, NULL, 'Product', '2c109c295b-6', NULL, NULL, NULL),
(358, NULL, NULL, 'Products/Product16095/d10ef2.jpg', 16095, NULL, 'Product', 'fbcc5819a3-2', NULL, NULL, NULL),
(359, NULL, NULL, 'Products/Product16095/e1b72a.jpg', 16095, NULL, 'Product', '4f11023d03-3', NULL, NULL, NULL),
(360, NULL, NULL, 'Products/Product16095/61848c.jpg', 16095, NULL, 'Product', '4fdae82dc1-4', NULL, NULL, NULL),
(361, NULL, NULL, 'Products/Product16095/005b9c.jpg', 16095, NULL, 'Product', '7d60715d80-5', NULL, NULL, NULL),
(362, NULL, NULL, 'Products/Product16095/a60c25.jpg', 16095, NULL, 'Product', 'c39ad47cbf-6', NULL, NULL, NULL),
(363, NULL, NULL, 'Products/Product16122/822961.jpg', 16122, NULL, 'Product', '495faef9d4-2', NULL, NULL, NULL),
(364, NULL, NULL, 'Products/Product16122/766578.jpg', 16122, NULL, 'Product', '6ca71945cc-3', NULL, NULL, NULL),
(365, NULL, NULL, 'Products/Product16122/e9d72f.jpg', 16122, NULL, 'Product', '13039435b4-4', NULL, NULL, NULL),
(366, NULL, NULL, 'Products/Product16122/d81d35.jpg', 16122, NULL, 'Product', 'a2c6c679d9-5', NULL, NULL, NULL),
(367, NULL, NULL, 'Products/Product16122/e01ea4.jpg', 16122, NULL, 'Product', 'babab7873c-6', NULL, NULL, NULL),
(368, NULL, NULL, 'Products/Product16094/c62771.jpg', 16094, NULL, 'Product', '06ff2e4c17-2', NULL, NULL, NULL),
(369, NULL, NULL, 'Products/Product16094/f0cab1.jpg', 16094, NULL, 'Product', '70d296c444-3', NULL, NULL, NULL),
(370, NULL, NULL, 'Products/Product16094/c8b6c3.jpg', 16094, NULL, 'Product', 'e84970feae-4', NULL, NULL, NULL),
(371, NULL, NULL, 'Products/Product16094/a84d22.jpg', 16094, NULL, 'Product', 'fe41e95f20-5', NULL, NULL, NULL),
(372, NULL, NULL, 'Products/Product16094/831823.jpg', 16094, NULL, 'Product', 'b8a8f16060-6', NULL, NULL, NULL),
(373, NULL, NULL, 'Products/Product16093/2369ad.jpg', 16093, NULL, 'Product', 'e1ca9bf33c-2', NULL, NULL, NULL),
(374, NULL, NULL, 'Products/Product16093/5193fa.jpg', 16093, NULL, 'Product', '634d005c7d-3', NULL, NULL, NULL),
(375, NULL, NULL, 'Products/Product16093/d8ae2d.jpg', 16093, NULL, 'Product', '81bc7b1f01-4', NULL, NULL, NULL),
(376, NULL, NULL, 'Products/Product16093/ee62f2.jpg', 16093, NULL, 'Product', '4db5be602b-5', NULL, NULL, NULL),
(377, NULL, NULL, 'Products/Product16091/f3b32d.jpg', 16091, NULL, 'Product', '6da58497d1-2', NULL, NULL, NULL),
(378, NULL, NULL, 'Products/Product16091/8fa8f6.jpg', 16091, NULL, 'Product', '18d52625d4-3', NULL, NULL, NULL),
(379, NULL, NULL, 'Products/Product16091/e1ea1c.jpg', 16091, NULL, 'Product', '01c3c5f629-4', NULL, NULL, NULL),
(380, NULL, NULL, 'Products/Product16091/33a3d1.jpg', 16091, NULL, 'Product', '72e25b6cf4-5', NULL, NULL, NULL),
(381, NULL, NULL, 'Products/Product16091/763613.jpg', 16091, NULL, 'Product', '41e8b41c0b-6', NULL, NULL, NULL),
(382, NULL, NULL, 'Products/Product16092/00c42d.jpg', 16092, NULL, 'Product', '9475ee5e80-2', NULL, NULL, NULL),
(383, NULL, NULL, 'Products/Product16092/cecb42.jpg', 16092, NULL, 'Product', '079e3e648f-3', NULL, NULL, NULL),
(384, NULL, NULL, 'Products/Product16092/48d10d.jpg', 16092, NULL, 'Product', '86918e0959-4', NULL, NULL, NULL),
(385, NULL, NULL, 'Products/Product16092/6de1ff.jpg', 16092, NULL, 'Product', 'e61818f249-5', NULL, NULL, NULL),
(386, NULL, NULL, 'Products/Product16092/8c7be3.jpg', 16092, NULL, 'Product', '0c7abbc113-6', NULL, NULL, NULL),
(387, NULL, NULL, 'Products/Product16090/4de1bf.jpg', 16090, NULL, 'Product', '0a022bbde9-2', NULL, NULL, NULL),
(388, NULL, NULL, 'Products/Product16090/be59e1.jpg', 16090, NULL, 'Product', 'bada9cabe0-3', NULL, NULL, NULL),
(389, NULL, NULL, 'Products/Product16090/6f3855.jpg', 16090, NULL, 'Product', '878bbc9977-4', NULL, NULL, NULL),
(390, NULL, NULL, 'Products/Product16090/729601.jpg', 16090, NULL, 'Product', 'fc391dcdf8-5', NULL, NULL, NULL),
(391, NULL, NULL, 'Products/Product16090/c9761c.jpg', 16090, NULL, 'Product', '141e2b580d-6', NULL, NULL, NULL),
(392, NULL, NULL, 'Products/Product16089/35eaaa.jpg', 16089, NULL, 'Product', '368dfc7ab2-2', NULL, NULL, NULL),
(393, NULL, NULL, 'Products/Product16089/745ec7.jpg', 16089, NULL, 'Product', 'd2a47fd51a-3', NULL, NULL, NULL),
(394, NULL, NULL, 'Products/Product16089/12863e.jpg', 16089, NULL, 'Product', '13407b25ce-4', NULL, NULL, NULL),
(395, NULL, NULL, 'Products/Product16089/f20e24.jpg', 16089, NULL, 'Product', '657fe9de60-5', NULL, NULL, NULL),
(396, NULL, NULL, 'Products/Product16089/61f07b.jpg', 16089, NULL, 'Product', 'b156dadbe8-6', NULL, NULL, NULL),
(397, NULL, NULL, 'Products/Product16042/e420ad.jpg', 16042, NULL, 'Product', 'e3a26c95cc-2', NULL, NULL, NULL),
(398, NULL, NULL, 'Products/Product16042/d11470.jpg', 16042, NULL, 'Product', '029c6a42d3-3', NULL, NULL, NULL),
(399, NULL, NULL, 'Products/Product16042/ae1c1a.jpg', 16042, NULL, 'Product', '51110901e8-4', NULL, NULL, NULL),
(400, NULL, NULL, 'Products/Product16042/d607e2.jpg', 16042, NULL, 'Product', 'c1d10a198b-5', NULL, NULL, NULL),
(401, NULL, NULL, 'Products/Product16042/b9e6f4.jpg', 16042, NULL, 'Product', '0f0a8644d4-6', NULL, NULL, NULL),
(402, NULL, NULL, 'Products/Product16039/d96427.jpg', 16039, NULL, 'Product', 'a4e088d3ca-2', NULL, NULL, NULL),
(403, NULL, NULL, 'Products/Product16039/90c3f7.jpg', 16039, NULL, 'Product', 'c3d047e0ea-3', NULL, NULL, NULL),
(404, NULL, NULL, 'Products/Product16039/8f182a.jpg', 16039, NULL, 'Product', '907a4cf2b3-4', NULL, NULL, NULL),
(405, NULL, NULL, 'Products/Product16039/20b480.jpg', 16039, NULL, 'Product', 'e9b4323d97-5', NULL, NULL, NULL),
(406, NULL, NULL, 'Products/Product16039/d31fdd.jpg', 16039, NULL, 'Product', '9c8599abdb-6', NULL, NULL, NULL),
(407, NULL, NULL, 'Products/Product16041/ee6b38.jpg', 16041, NULL, 'Product', '755380fe90-2', NULL, NULL, NULL),
(408, NULL, NULL, 'Products/Product16041/df7860.jpg', 16041, NULL, 'Product', '8d2404ea53-3', NULL, NULL, NULL),
(409, NULL, NULL, 'Products/Product16041/128406.jpg', 16041, NULL, 'Product', '3322b46486-4', NULL, NULL, NULL),
(410, NULL, NULL, 'Products/Product16041/de739b.jpg', 16041, NULL, 'Product', '233f1b99b0-5', NULL, NULL, NULL),
(411, NULL, NULL, 'Products/Product16041/529b20.jpg', 16041, NULL, 'Product', '0021a627bb-6', NULL, NULL, NULL),
(412, NULL, NULL, 'Products/Product16040/fede45.jpg', 16040, NULL, 'Product', '6d4bae8f73-2', NULL, NULL, NULL),
(413, NULL, NULL, 'Products/Product16040/6c63d8.jpg', 16040, NULL, 'Product', '7d80422499-3', NULL, NULL, NULL),
(414, NULL, NULL, 'Products/Product16040/7fa4af.jpg', 16040, NULL, 'Product', '955d003c0c-4', NULL, NULL, NULL),
(415, NULL, NULL, 'Products/Product16040/6cde7f.jpg', 16040, NULL, 'Product', '28cab00254-5', NULL, NULL, NULL),
(416, NULL, NULL, 'Products/Product16040/595915.jpg', 16040, NULL, 'Product', 'cef5cfd631-6', NULL, NULL, NULL),
(417, NULL, NULL, 'Products/Product16043/73606d.jpg', 16043, NULL, 'Product', 'c6e61f1e17-2', NULL, NULL, NULL),
(418, NULL, NULL, 'Products/Product16043/f4d247.jpg', 16043, NULL, 'Product', '5decb08d2c-3', NULL, NULL, NULL),
(419, NULL, NULL, 'Products/Product16043/b70a2f.jpg', 16043, NULL, 'Product', 'c159ea1846-4', NULL, NULL, NULL),
(420, NULL, NULL, 'Products/Product16043/a593c6.jpg', 16043, NULL, 'Product', '94a02f3b4b-5', NULL, NULL, NULL),
(421, NULL, NULL, 'Products/Product16045/a6e972.jpg', 16045, NULL, 'Product', '518bc23f79-2', NULL, NULL, NULL),
(422, NULL, NULL, 'Products/Product16045/6dee61.jpg', 16045, NULL, 'Product', 'fb181cf8f2-3', NULL, NULL, NULL),
(423, NULL, NULL, 'Products/Product16045/8ac98e.jpg', 16045, NULL, 'Product', '07c8778f7b-4', NULL, NULL, NULL),
(424, NULL, NULL, 'Products/Product16045/15bdcf.jpg', 16045, NULL, 'Product', '58be9d232d-5', NULL, NULL, NULL),
(425, NULL, NULL, 'Products/Product16045/e1252b.jpg', 16045, NULL, 'Product', '1239f3b5de-6', NULL, NULL, NULL),
(426, NULL, NULL, 'Products/Product16044/9e7925.jpg', 16044, NULL, 'Product', '397c9d94b5-2', NULL, NULL, NULL),
(427, NULL, NULL, 'Products/Product16044/14a070.jpg', 16044, NULL, 'Product', '0aa1cbe3d8-3', NULL, NULL, NULL),
(428, NULL, NULL, 'Products/Product16044/237abd.jpg', 16044, NULL, 'Product', 'ce38e53769-4', NULL, NULL, NULL),
(429, NULL, NULL, 'Products/Product16044/534ccf.jpg', 16044, NULL, 'Product', 'b25486ed4c-5', NULL, NULL, NULL),
(430, NULL, NULL, 'Products/Product16044/1236c4.jpg', 16044, NULL, 'Product', 'c97f40946c-6', NULL, NULL, NULL),
(431, NULL, NULL, 'Products/Product15105/da2f3f.jpg', 15105, NULL, 'Product', '09aa959b03-2', NULL, NULL, NULL),
(432, NULL, NULL, 'Products/Product15105/2312c6.jpg', 15105, NULL, 'Product', '4a8783e896-3', NULL, NULL, NULL),
(433, NULL, NULL, 'Products/Product15105/c8ebfb.jpg', 15105, NULL, 'Product', 'd4cbe3738e-4', NULL, NULL, NULL),
(434, NULL, NULL, 'Products/Product15099/5012cc.jpg', 15099, NULL, 'Product', 'ded7120ab4-2', NULL, NULL, NULL),
(435, NULL, NULL, 'Products/Product15099/ca5002.jpg', 15099, NULL, 'Product', '256e209a9f-3', NULL, NULL, NULL),
(436, NULL, NULL, 'Products/Product15099/ec8146.jpg', 15099, NULL, 'Product', 'e3455d2e1f-4', NULL, NULL, NULL),
(437, NULL, NULL, 'Products/Product15099/69b715.jpg', 15099, NULL, 'Product', '6b5db37cee-5', NULL, NULL, NULL),
(438, NULL, NULL, 'Products/Product15079/d7aac3.jpg', 15079, NULL, 'Product', 'f05eb81bb8-2', NULL, NULL, NULL),
(439, NULL, NULL, 'Products/Product15079/32638c.jpg', 15079, NULL, 'Product', '7cdaebfb73-3', NULL, NULL, NULL),
(440, NULL, NULL, 'Products/Product15079/de232e.jpg', 15079, NULL, 'Product', 'c5bdf7bf5f-4', NULL, NULL, NULL),
(441, NULL, NULL, 'Products/Product15078/d09b51.jpg', 15078, NULL, 'Product', 'd16320e331-2', NULL, NULL, NULL),
(442, NULL, NULL, 'Products/Product15078/d96319.jpg', 15078, NULL, 'Product', '3684b263c4-3', NULL, NULL, NULL),
(443, NULL, NULL, 'Products/Product15078/182bec.jpg', 15078, NULL, 'Product', '75d6a7858c-4', NULL, NULL, NULL),
(444, NULL, NULL, 'Products/Product15078/434eb0.jpg', 15078, NULL, 'Product', '5fc2cbbd7f-5', NULL, NULL, NULL),
(445, NULL, NULL, 'Products/Product15077/98fba2.jpg', 15077, NULL, 'Product', '8940f4ca72-2', NULL, NULL, NULL),
(446, NULL, NULL, 'Products/Product15077/f878ac.jpg', 15077, NULL, 'Product', '084ad70910-3', NULL, NULL, NULL),
(447, NULL, NULL, 'Products/Product15077/6cde16.jpg', 15077, NULL, 'Product', 'eb86e8168a-4', NULL, NULL, NULL),
(448, NULL, NULL, 'Products/Product15076/44c3ec.jpg', 15076, NULL, 'Product', 'bb33ecfcc9-2', NULL, NULL, NULL),
(449, NULL, NULL, 'Products/Product15076/bd39bd.jpg', 15076, NULL, 'Product', '82e0763636-3', NULL, NULL, NULL),
(450, NULL, NULL, 'Products/Product15076/d84b4c.jpg', 15076, NULL, 'Product', '26c9c1e233-4', NULL, NULL, NULL),
(451, NULL, NULL, 'Products/Product15051/6e8773.jpg', 15051, NULL, 'Product', '707880b20a-2', NULL, NULL, NULL),
(452, NULL, NULL, 'Products/Product15051/6b5bc4.jpg', 15051, NULL, 'Product', '53acc95616-3', NULL, NULL, NULL),
(453, NULL, NULL, 'Products/Product15051/0a6f83.jpg', 15051, NULL, 'Product', 'c703862b09-4', NULL, NULL, NULL),
(454, NULL, NULL, 'Products/Product12119/aaa971.jpg', 12119, NULL, 'Product', '48969ca5b9-2', NULL, NULL, NULL),
(455, NULL, NULL, 'Products/Product12119/c51245.jpg', 12119, NULL, 'Product', '1fef0395df-3', NULL, NULL, NULL);
INSERT INTO `image` (`id`, `title`, `alt`, `filePath`, `itemId`, `isMain`, `modelName`, `urlAlias`, `description`, `gallery_id`, `sort`) VALUES
(456, NULL, NULL, 'Products/Product12119/328a8b.jpg', 12119, NULL, 'Product', '398ade0699-4', NULL, NULL, NULL),
(457, NULL, NULL, 'Products/Product12119/d9a568.jpg', 12119, NULL, 'Product', '6bc1d91d90-5', NULL, NULL, NULL),
(458, NULL, NULL, 'Products/Product12119/8d9d16.jpg', 12119, NULL, 'Product', '6897757746-6', NULL, NULL, NULL),
(459, NULL, NULL, 'Products/Product15053/a0d32c.jpg', 15053, NULL, 'Product', 'ed579cad2e-2', NULL, NULL, NULL),
(460, NULL, NULL, 'Products/Product15053/612781.jpg', 15053, NULL, 'Product', '358eaaabf3-3', NULL, NULL, NULL),
(461, NULL, NULL, 'Products/Product15053/6c9eb4.jpg', 15053, NULL, 'Product', '48d956e041-4', NULL, NULL, NULL),
(462, NULL, NULL, 'Products/Product14726/39f636.jpg', 14726, NULL, 'Product', 'fa7f5fcb70-2', NULL, NULL, NULL),
(463, NULL, NULL, 'Products/Product14726/ab9715.jpg', 14726, NULL, 'Product', '2b56425427-3', NULL, NULL, NULL),
(464, NULL, NULL, 'Products/Product14726/caf0d9.jpg', 14726, NULL, 'Product', '578731e0ae-4', NULL, NULL, NULL),
(465, NULL, NULL, 'Products/Product15050/ae49f4.jpg', 15050, NULL, 'Product', '208c2b3fc1-2', NULL, NULL, NULL),
(466, NULL, NULL, 'Products/Product15050/3e80bf.jpg', 15050, NULL, 'Product', '2a1da2fc3d-3', NULL, NULL, NULL),
(467, NULL, NULL, 'Products/Product15050/c04f67.jpg', 15050, NULL, 'Product', '342ddb5f68-4', NULL, NULL, NULL),
(468, NULL, NULL, 'Products/Product9518/ec2457.jpg', 9518, NULL, 'Product', '2b7c281a52-2', NULL, NULL, NULL),
(469, NULL, NULL, 'Products/Product9518/6318a5.jpg', 9518, NULL, 'Product', '56477a750f-3', NULL, NULL, NULL),
(470, NULL, NULL, 'Products/Product9518/787cbe.jpg', 9518, NULL, 'Product', '3c4032239c-4', NULL, NULL, NULL),
(471, NULL, NULL, 'Products/Product9518/9f91cd.jpg', 9518, NULL, 'Product', 'ce9e27dc70-5', NULL, NULL, NULL),
(472, NULL, NULL, 'Products/Product9518/6c4e26.jpg', 9518, NULL, 'Product', '09e4336e13-6', NULL, NULL, NULL),
(473, NULL, NULL, 'Products/Product2446/11825b.jpg', 2446, NULL, 'Product', '6c287beef1-2', NULL, NULL, NULL),
(474, NULL, NULL, 'Products/Product2446/0742ad.jpg', 2446, NULL, 'Product', '19abc6d372-3', NULL, NULL, NULL),
(475, NULL, NULL, 'Products/Product2446/f2f41f.jpg', 2446, NULL, 'Product', '6d9db88dcf-4', NULL, NULL, NULL),
(476, NULL, NULL, 'Products/Product18863/91700d.jpg', 18863, 1, 'Product', 'a5e695c3ba-1', NULL, NULL, NULL),
(477, NULL, NULL, 'Products/Product18863/b7b0b8.jpg', 18863, NULL, 'Product', 'a0498432c0-4', NULL, NULL, NULL),
(478, NULL, NULL, 'Products/Product20406/0343bb.jpg', 20406, 1, 'Product', '801567f975-1', NULL, NULL, NULL),
(479, NULL, NULL, 'Products/Product20406/eb3ee0.jpg', 20406, NULL, 'Product', 'ee5a0cbf85-4', NULL, NULL, NULL),
(480, NULL, NULL, 'Products/Product20435/7bce03.jpg', 20435, 1, 'Product', '9dd88c0319-1', NULL, NULL, NULL),
(481, NULL, NULL, 'Products/Product20435/cb205d.jpg', 20435, NULL, 'Product', '9d675542c0-4', NULL, NULL, NULL),
(482, NULL, NULL, 'Products/Product20364/7b3ff2.jpg', 20364, 1, 'Product', 'bbe77f283c-1', NULL, NULL, NULL),
(483, NULL, NULL, 'Products/Product20364/73905e.jpg', 20364, NULL, 'Product', '73a94786c8-4', NULL, NULL, NULL),
(484, NULL, NULL, 'Products/Product20346/7adc0c.jpg', 20346, 1, 'Product', 'fd444c9002-1', NULL, NULL, NULL),
(485, NULL, NULL, 'Products/Product20346/cbe037.jpg', 20346, NULL, 'Product', '37129e9912-6', NULL, NULL, NULL),
(486, NULL, NULL, 'Products/Product20346/0dbefb.jpg', 20346, NULL, 'Product', '4f592197f4-7', NULL, NULL, NULL),
(487, NULL, NULL, 'Products/Product20346/92453d.jpg', 20346, NULL, 'Product', '6637a977ab-8', NULL, NULL, NULL),
(488, NULL, NULL, 'Products/Product20331/15fac0.jpg', 20331, 1, 'Product', 'a72b76ee3d-1', NULL, NULL, NULL),
(489, NULL, NULL, 'Products/Product20331/910d11.jpg', 20331, NULL, 'Product', '02ec6fa7ee-5', NULL, NULL, NULL),
(490, NULL, NULL, 'Products/Product20331/ffbcfa.jpg', 20331, NULL, 'Product', 'e028c53d4d-6', NULL, NULL, NULL),
(491, NULL, NULL, 'Products/Product20343/db5114.jpg', 20343, 1, 'Product', 'ba1add94de-1', NULL, NULL, NULL),
(492, NULL, NULL, 'Products/Product20343/25423e.jpg', 20343, NULL, 'Product', '9f549c4962-5', NULL, NULL, NULL),
(493, NULL, NULL, 'Products/Product20343/0ea324.jpg', 20343, NULL, 'Product', '400fd8634b-6', NULL, NULL, NULL),
(494, NULL, NULL, 'Products/Product19916/60cffd.jpg', 19916, 1, 'Product', '9620aebc14-1', NULL, NULL, NULL),
(495, NULL, NULL, 'Products/Product19916/83fa0e.jpg', 19916, NULL, 'Product', '278d110f55-4', NULL, NULL, NULL),
(496, NULL, NULL, 'Products/Product19897/77211b.jpg', 19897, 1, 'Product', '8f89cafc7b-1', NULL, NULL, NULL),
(497, NULL, NULL, 'Products/Product19897/9efbe9.jpg', 19897, NULL, 'Product', '3874fd1e2d-4', NULL, NULL, NULL),
(498, NULL, NULL, 'Products/Product19891/264f4c.jpg', 19891, 1, 'Product', 'bd01926bbd-1', NULL, NULL, NULL),
(499, NULL, NULL, 'Products/Product19891/dcc4af.jpg', 19891, NULL, 'Product', 'b22b3310f5-4', NULL, NULL, NULL),
(500, NULL, NULL, 'Products/Product19887/31d91b.jpg', 19887, 1, 'Product', '4ace2fb297-1', NULL, NULL, NULL),
(501, NULL, NULL, 'Products/Product19887/9a480a.jpg', 19887, NULL, 'Product', 'cb40419113-5', NULL, NULL, NULL),
(502, NULL, NULL, 'Products/Product19887/03e920.jpg', 19887, NULL, 'Product', '0561d3dca6-6', NULL, NULL, NULL),
(503, NULL, NULL, 'Products/Product19886/39b57f.jpg', 19886, 1, 'Product', '732f1419bf-1', NULL, NULL, NULL),
(504, NULL, NULL, 'Products/Product19886/a0682c.jpg', 19886, NULL, 'Product', 'e5aefef34c-5', NULL, NULL, NULL),
(505, NULL, NULL, 'Products/Product19886/2d9c64.jpg', 19886, NULL, 'Product', '670f43861b-6', NULL, NULL, NULL),
(506, NULL, NULL, 'Products/Product19678/708a9d.jpg', 19678, 1, 'Product', 'a70182ccb7-1', NULL, NULL, NULL),
(507, NULL, NULL, 'Products/Product19678/2d8951.jpg', 19678, NULL, 'Product', '4ef737a5b9-6', NULL, NULL, NULL),
(508, NULL, NULL, 'Products/Product19678/7398d2.jpg', 19678, NULL, 'Product', 'e122e0c51e-7', NULL, NULL, NULL),
(509, NULL, NULL, 'Products/Product19678/28db63.jpg', 19678, NULL, 'Product', '6e51fc096b-8', NULL, NULL, NULL),
(510, NULL, NULL, 'Products/Product19425/234536.jpg', 19425, 1, 'Product', '493092c655-1', NULL, NULL, NULL),
(511, NULL, NULL, 'Products/Product19425/7d0763.jpg', 19425, NULL, 'Product', '1d2a1a3b53-8', NULL, NULL, NULL),
(512, NULL, NULL, 'Products/Product19425/27076c.jpg', 19425, NULL, 'Product', '96e9eac897-9', NULL, NULL, NULL),
(513, NULL, NULL, 'Products/Product19425/9d4166.jpg', 19425, NULL, 'Product', 'ee4b066629-10', NULL, NULL, NULL),
(514, NULL, NULL, 'Products/Product19425/4ec673.jpg', 19425, NULL, 'Product', '9f2a102e47-11', NULL, NULL, NULL),
(515, NULL, NULL, 'Products/Product19425/8e23e4.jpg', 19425, NULL, 'Product', '82a6f0048e-12', NULL, NULL, NULL),
(516, NULL, NULL, 'Products/Product19732/048c6c.jpg', 19732, 1, 'Product', '6d224be7ff-1', NULL, NULL, NULL),
(517, NULL, NULL, 'Products/Product19732/fe113c.jpg', 19732, NULL, 'Product', '8c60362384-8', NULL, NULL, NULL),
(518, NULL, NULL, 'Products/Product19732/c901e8.jpg', 19732, NULL, 'Product', '12bbfc55ae-9', NULL, NULL, NULL),
(519, NULL, NULL, 'Products/Product19732/04eab6.jpg', 19732, NULL, 'Product', 'b8a0ea6daf-10', NULL, NULL, NULL),
(520, NULL, NULL, 'Products/Product19732/8d3771.jpg', 19732, NULL, 'Product', '8191c8df9a-11', NULL, NULL, NULL),
(521, NULL, NULL, 'Products/Product19732/282545.jpg', 19732, NULL, 'Product', '8795f9553f-12', NULL, NULL, NULL),
(522, NULL, NULL, 'Products/Product19426/82d2d4.jpg', 19426, 1, 'Product', 'b5f6fd5c58-1', NULL, NULL, NULL),
(523, NULL, NULL, 'Products/Product19426/f2a78e.jpg', 19426, NULL, 'Product', '18da1d319b-8', NULL, NULL, NULL),
(524, NULL, NULL, 'Products/Product19426/198deb.jpg', 19426, NULL, 'Product', '0d59c489a8-9', NULL, NULL, NULL),
(525, NULL, NULL, 'Products/Product19426/3d6482.jpg', 19426, NULL, 'Product', 'b5f75b26a6-10', NULL, NULL, NULL),
(526, NULL, NULL, 'Products/Product19426/ca1a89.jpg', 19426, NULL, 'Product', '3f8c617fc7-11', NULL, NULL, NULL),
(527, NULL, NULL, 'Products/Product19426/c353ac.jpg', 19426, NULL, 'Product', '930a113568-12', NULL, NULL, NULL),
(528, NULL, NULL, 'Products/Product19663/224d24.jpg', 19663, 1, 'Product', 'a4fe19c798-1', NULL, NULL, NULL),
(529, NULL, NULL, 'Products/Product19663/9b9cd4.jpg', 19663, NULL, 'Product', 'baeede5fd8-8', NULL, NULL, NULL),
(530, NULL, NULL, 'Products/Product19663/ed3051.jpg', 19663, NULL, 'Product', '950e346193-9', NULL, NULL, NULL),
(531, NULL, NULL, 'Products/Product19663/a462ee.jpg', 19663, NULL, 'Product', '335a94da8f-10', NULL, NULL, NULL),
(532, NULL, NULL, 'Products/Product19663/0a9847.jpg', 19663, NULL, 'Product', '25509963f1-11', NULL, NULL, NULL),
(533, NULL, NULL, 'Products/Product19663/73272d.jpg', 19663, NULL, 'Product', 'ddb6d0dc51-12', NULL, NULL, NULL),
(534, NULL, NULL, 'Products/Product19121/dde34f.jpg', 19121, 1, 'Product', '8dac670aec-1', NULL, NULL, NULL),
(535, NULL, NULL, 'Products/Product19121/f1e75d.jpg', 19121, NULL, 'Product', '002bdaa373-4', NULL, NULL, NULL),
(536, NULL, NULL, 'Products/Product19118/3d84d1.jpg', 19118, 1, 'Product', 'b4381c3a02-1', NULL, NULL, NULL),
(537, NULL, NULL, 'Products/Product19118/30354c.jpg', 19118, NULL, 'Product', '52941e1ae9-4', NULL, NULL, NULL),
(538, NULL, NULL, 'Products/Product19120/56cda3.jpg', 19120, 1, 'Product', 'bc9d5afca1-1', NULL, NULL, NULL),
(539, NULL, NULL, 'Products/Product19120/bde7af.jpg', 19120, NULL, 'Product', 'da44ef5df6-4', NULL, NULL, NULL),
(540, NULL, NULL, 'Products/Product19117/0e1dab.jpg', 19117, 1, 'Product', '4527127b40-1', NULL, NULL, NULL),
(541, NULL, NULL, 'Products/Product19117/6856f5.jpg', 19117, NULL, 'Product', 'dca74cf30e-4', NULL, NULL, NULL),
(542, NULL, NULL, 'Products/Product19100/7da2e5.jpg', 19100, 1, 'Product', '401110af1e-1', NULL, NULL, NULL),
(543, NULL, NULL, 'Products/Product19100/7ff610.jpg', 19100, NULL, 'Product', '2fef3e78b2-4', NULL, NULL, NULL),
(544, NULL, NULL, 'Products/Product18865/286806.jpg', 18865, 1, 'Product', '07b88ed068-1', NULL, NULL, NULL),
(545, NULL, NULL, 'Products/Product18865/524942.jpg', 18865, NULL, 'Product', '5976d0228a-4', NULL, NULL, NULL),
(546, NULL, NULL, 'Products/Product18866/df800f.jpg', 18866, 1, 'Product', '4156ea6360-1', NULL, NULL, NULL),
(547, NULL, NULL, 'Products/Product18866/5dac7e.jpg', 18866, NULL, 'Product', 'a37180f443-4', NULL, NULL, NULL),
(548, NULL, NULL, 'Products/Product18862/0ab283.jpg', 18862, 1, 'Product', '592aecacfb-1', NULL, NULL, NULL),
(549, NULL, NULL, 'Products/Product18862/0e1cdc.jpg', 18862, NULL, 'Product', 'e72cbe102f-4', NULL, NULL, NULL),
(550, NULL, NULL, 'Products/Product18861/4ec36d.jpg', 18861, 1, 'Product', '7d6d8c7d56-1', NULL, NULL, NULL),
(551, NULL, NULL, 'Products/Product18861/61814e.jpg', 18861, NULL, 'Product', '37b46483f8-4', NULL, NULL, NULL),
(552, NULL, NULL, 'Products/Product18864/d4ddba.jpg', 18864, 1, 'Product', 'eee027d813-1', NULL, NULL, NULL),
(553, NULL, NULL, 'Products/Product18864/dc45bf.jpg', 18864, NULL, 'Product', '732c9b36f1-4', NULL, NULL, NULL),
(554, NULL, NULL, 'Products/Product18860/bd3d52.jpg', 18860, 1, 'Product', '3ac6fede5d-1', NULL, NULL, NULL),
(555, NULL, NULL, 'Products/Product18860/a80889.jpg', 18860, NULL, 'Product', 'a7e22504c8-4', NULL, NULL, NULL),
(556, NULL, NULL, 'Products/Product18859/ca7eff.jpg', 18859, 1, 'Product', 'b9e8d3e904-1', NULL, NULL, NULL),
(557, NULL, NULL, 'Products/Product18859/4dfc61.jpg', 18859, NULL, 'Product', 'ba19019c8e-4', NULL, NULL, NULL),
(558, NULL, NULL, 'Products/Product18857/00c894.jpg', 18857, 1, 'Product', '244503f41f-1', NULL, NULL, NULL),
(559, NULL, NULL, 'Products/Product18857/611649.jpg', 18857, NULL, 'Product', 'c70da0c05b-4', NULL, NULL, NULL),
(560, NULL, NULL, 'Products/Product18858/7305c2.jpg', 18858, 1, 'Product', 'cf716f3924-1', NULL, NULL, NULL),
(561, NULL, NULL, 'Products/Product18858/46c176.jpg', 18858, NULL, 'Product', 'f9ee7a6ff9-4', NULL, NULL, NULL),
(562, NULL, NULL, 'Products/Product18592/ae570f.jpg', 18592, 1, 'Product', '71f953bd56-1', NULL, NULL, NULL),
(563, NULL, NULL, 'Products/Product18592/b6cfb5.jpg', 18592, NULL, 'Product', '1f172a1feb-4', NULL, NULL, NULL),
(564, NULL, NULL, 'Products/Product18487/9dd9ff.jpg', 18487, 1, 'Product', 'ecc8f5c8b5-1', NULL, NULL, NULL),
(565, NULL, NULL, 'Products/Product18487/bd8787.jpg', 18487, NULL, 'Product', 'c8f47eeff9-4', NULL, NULL, NULL),
(566, NULL, NULL, 'Products/Product18490/08508b.jpg', 18490, 1, 'Product', 'd011a09d4d-1', NULL, NULL, NULL),
(567, NULL, NULL, 'Products/Product18490/52669e.jpg', 18490, NULL, 'Product', 'ab115cdea9-4', NULL, NULL, NULL),
(568, NULL, NULL, 'Products/Product18439/d4c76a.jpg', 18439, 1, 'Product', '2d0f8a9ba6-1', NULL, NULL, NULL),
(569, NULL, NULL, 'Products/Product18439/bb9e03.jpg', 18439, NULL, 'Product', '3953457637-4', NULL, NULL, NULL),
(570, NULL, NULL, 'Products/Product18441/cb3b70.jpg', 18441, 1, 'Product', 'aee578c6a6-1', NULL, NULL, NULL),
(571, NULL, NULL, 'Products/Product18441/38f198.jpg', 18441, NULL, 'Product', 'c2562ce01e-4', NULL, NULL, NULL),
(572, NULL, NULL, 'Products/Product18488/c0d23e.jpg', 18488, 1, 'Product', 'be67a61317-1', NULL, NULL, NULL),
(573, NULL, NULL, 'Products/Product18488/3252b2.jpg', 18488, NULL, 'Product', '21883edbce-4', NULL, NULL, NULL),
(574, NULL, NULL, 'Products/Product18472/ffce19.jpg', 18472, 1, 'Product', 'e04b29e65a-1', NULL, NULL, NULL),
(575, NULL, NULL, 'Products/Product18472/1b0c4d.jpg', 18472, NULL, 'Product', 'e336e0fa34-4', NULL, NULL, NULL),
(576, NULL, NULL, 'Products/Product18440/acd478.jpg', 18440, 1, 'Product', 'e4c6aa7aac-1', NULL, NULL, NULL),
(577, NULL, NULL, 'Products/Product18440/20035d.jpg', 18440, NULL, 'Product', '1c67ae5d89-4', NULL, NULL, NULL),
(578, NULL, NULL, 'Products/Product18489/7eb9d3.jpg', 18489, 1, 'Product', '5823bc5a34-1', NULL, NULL, NULL),
(579, NULL, NULL, 'Products/Product18489/5467fc.jpg', 18489, NULL, 'Product', '4505648b36-4', NULL, NULL, NULL),
(580, NULL, NULL, 'Products/Product18438/1dbbbc.jpg', 18438, 1, 'Product', '56ad671e95-1', NULL, NULL, NULL),
(581, NULL, NULL, 'Products/Product18438/20de22.jpg', 18438, NULL, 'Product', '18d84cc8c4-4', NULL, NULL, NULL),
(582, NULL, NULL, 'Products/Product18473/99ad9f.jpg', 18473, 1, 'Product', '382c04bb62-1', NULL, NULL, NULL),
(583, NULL, NULL, 'Products/Product18473/c221cc.jpg', 18473, NULL, 'Product', '4cdf22b5ce-6', NULL, NULL, NULL),
(584, NULL, NULL, 'Products/Product18473/1f93e7.jpg', 18473, NULL, 'Product', 'f97b860b45-7', NULL, NULL, NULL),
(585, NULL, NULL, 'Products/Product18473/72c25f.jpg', 18473, NULL, 'Product', 'ae6e3c0bf4-8', NULL, NULL, NULL),
(586, NULL, NULL, 'Products/Product18305/f14ac4.jpg', 18305, 1, 'Product', 'a094bfb9fa-1', NULL, NULL, NULL),
(587, NULL, NULL, 'Products/Product18305/b021f1.jpg', 18305, NULL, 'Product', 'd470359f8f-4', NULL, NULL, NULL),
(588, NULL, NULL, 'Products/Product18091/003ba5.jpg', 18091, 1, 'Product', '7a1f61230c-1', NULL, NULL, NULL),
(589, NULL, NULL, 'Products/Product18091/2f21e3.jpg', 18091, NULL, 'Product', '7c3f8587bf-5', NULL, NULL, NULL),
(590, NULL, NULL, 'Products/Product18091/7e573e.jpg', 18091, NULL, 'Product', 'd34df12f35-6', NULL, NULL, NULL),
(591, NULL, NULL, 'Products/Product18306/feec2b.jpg', 18306, 1, 'Product', '0d2826b1a4-1', NULL, NULL, NULL),
(592, NULL, NULL, 'Products/Product18306/745d62.jpg', 18306, NULL, 'Product', 'f3af1e6fd5-4', NULL, NULL, NULL),
(593, NULL, NULL, 'Products/Product18364/11a59a.jpg', 18364, 1, 'Product', 'bad287f9f9-1', NULL, NULL, NULL),
(594, NULL, NULL, 'Products/Product18364/56abe7.jpg', 18364, NULL, 'Product', '7df44681f1-5', NULL, NULL, NULL),
(595, NULL, NULL, 'Products/Product18364/1b1ddd.jpg', 18364, NULL, 'Product', 'b37f205345-6', NULL, NULL, NULL),
(596, NULL, NULL, 'Products/Product18090/d9b6aa.jpg', 18090, 1, 'Product', '2bdc71aa12-1', NULL, NULL, NULL),
(597, NULL, NULL, 'Products/Product18090/b1836a.jpg', 18090, NULL, 'Product', '3f99e37e05-5', NULL, NULL, NULL),
(598, NULL, NULL, 'Products/Product18090/0c1f7f.jpg', 18090, NULL, 'Product', 'dc626ee24f-6', NULL, NULL, NULL),
(599, NULL, NULL, 'Products/Product18304/9ee500.jpg', 18304, 1, 'Product', '6c66fde178-1', NULL, NULL, NULL),
(600, NULL, NULL, 'Products/Product18304/d323dd.jpg', 18304, NULL, 'Product', '84277b9d6d-4', NULL, NULL, NULL),
(601, NULL, NULL, 'Products/Product18363/74838c.jpg', 18363, 1, 'Product', 'c4fabeec07-1', NULL, NULL, NULL),
(602, NULL, NULL, 'Products/Product18363/adac72.jpg', 18363, NULL, 'Product', '8d2f2c7b04-5', NULL, NULL, NULL),
(603, NULL, NULL, 'Products/Product18363/b1c63f.jpg', 18363, NULL, 'Product', 'b269c553d4-6', NULL, NULL, NULL),
(604, NULL, NULL, 'Products/Product18303/e09f37.jpg', 18303, 1, 'Product', '178de03c93-1', NULL, NULL, NULL),
(605, NULL, NULL, 'Products/Product18303/fd3ad5.jpg', 18303, NULL, 'Product', '325665b43e-4', NULL, NULL, NULL),
(606, NULL, NULL, 'Products/Product18071/b8a736.jpg', 18071, 1, 'Product', '639d7b483b-1', NULL, NULL, NULL),
(607, NULL, NULL, 'Products/Product18071/d64c23.jpg', 18071, NULL, 'Product', '469c788d0f-6', NULL, NULL, NULL),
(608, NULL, NULL, 'Products/Product18071/16154c.jpg', 18071, NULL, 'Product', '9a2566564a-7', NULL, NULL, NULL),
(609, NULL, NULL, 'Products/Product18071/3f388f.jpg', 18071, NULL, 'Product', 'f6db920ca8-8', NULL, NULL, NULL),
(610, NULL, NULL, 'Products/Product18081/a48e49.jpg', 18081, 1, 'Product', '38a3d089c4-1', NULL, NULL, NULL),
(611, NULL, NULL, 'Products/Product18081/cd9f5f.jpg', 18081, NULL, 'Product', '0b04071e8d-5', NULL, NULL, NULL),
(612, NULL, NULL, 'Products/Product18081/4db536.jpg', 18081, NULL, 'Product', '5241d49c1a-6', NULL, NULL, NULL),
(613, NULL, NULL, 'Products/Product18052/ac0c7b.jpg', 18052, 1, 'Product', 'edf1377395-1', NULL, NULL, NULL),
(614, NULL, NULL, 'Products/Product18052/a80f9c.jpg', 18052, NULL, 'Product', 'f23581bfe0-5', NULL, NULL, NULL),
(615, NULL, NULL, 'Products/Product18052/70415a.jpg', 18052, NULL, 'Product', '002dab00ef-6', NULL, NULL, NULL),
(616, NULL, NULL, 'Products/Product18032/0ead51.jpg', 18032, 1, 'Product', '9558f9693b-1', NULL, NULL, NULL),
(617, NULL, NULL, 'Products/Product18032/3d7360.jpg', 18032, NULL, 'Product', '46cc9333ff-5', NULL, NULL, NULL),
(618, NULL, NULL, 'Products/Product18032/e00791.jpg', 18032, NULL, 'Product', '24821e7c1c-6', NULL, NULL, NULL),
(619, NULL, NULL, 'Products/Product18030/027d15.jpg', 18030, 1, 'Product', '95d1feead7-1', NULL, NULL, NULL),
(620, NULL, NULL, 'Products/Product18030/abecc7.jpg', 18030, NULL, 'Product', '1c2d249bf8-5', NULL, NULL, NULL),
(621, NULL, NULL, 'Products/Product18030/1fa860.jpg', 18030, NULL, 'Product', 'e5bd337b05-6', NULL, NULL, NULL),
(622, NULL, NULL, 'Products/Product18051/f40c4f.jpg', 18051, 1, 'Product', '3c285ca26d-1', NULL, NULL, NULL),
(623, NULL, NULL, 'Products/Product18051/2cabbc.jpg', 18051, NULL, 'Product', 'afa9f7bcba-6', NULL, NULL, NULL),
(624, NULL, NULL, 'Products/Product18051/33fff6.jpg', 18051, NULL, 'Product', 'affe678a20-7', NULL, NULL, NULL),
(625, NULL, NULL, 'Products/Product18051/731d26.jpg', 18051, NULL, 'Product', '470f698f25-8', NULL, NULL, NULL),
(626, NULL, NULL, 'Products/Product18302/df9823.jpg', 18302, 1, 'Product', '6c99a3bacc-1', NULL, NULL, NULL),
(627, NULL, NULL, 'Products/Product18302/8ccd59.jpg', 18302, NULL, 'Product', '50d5a57210-7', NULL, NULL, NULL),
(628, NULL, NULL, 'Products/Product18302/0b38f3.jpg', 18302, NULL, 'Product', '302af0d1e3-8', NULL, NULL, NULL),
(629, NULL, NULL, 'Products/Product18302/d03d1e.jpg', 18302, NULL, 'Product', '423e3778d9-9', NULL, NULL, NULL),
(630, NULL, NULL, 'Products/Product18302/ebdbdb.jpg', 18302, NULL, 'Product', '147497ecae-10', NULL, NULL, NULL),
(631, NULL, NULL, 'Products/Product18028/cb9472.jpg', 18028, 1, 'Product', 'ab8567c664-1', NULL, NULL, NULL),
(632, NULL, NULL, 'Products/Product18028/c919cf.jpg', 18028, NULL, 'Product', 'f7e349d6b1-4', NULL, NULL, NULL),
(633, NULL, NULL, 'Products/Product18025/8c5e75.jpg', 18025, 1, 'Product', '4a62c8c8aa-1', NULL, NULL, NULL),
(634, NULL, NULL, 'Products/Product18025/226321.jpg', 18025, NULL, 'Product', '089d400b4f-4', NULL, NULL, NULL),
(635, NULL, NULL, 'Products/Product18031/4ddc0d.jpg', 18031, 1, 'Product', '2de7323436-1', NULL, NULL, NULL),
(636, NULL, NULL, 'Products/Product18031/e6632c.jpg', 18031, NULL, 'Product', '3f28ba062f-5', NULL, NULL, NULL),
(637, NULL, NULL, 'Products/Product18031/a658bb.jpg', 18031, NULL, 'Product', '61ec9d76cf-6', NULL, NULL, NULL),
(638, NULL, NULL, 'Products/Product18029/4f17d6.jpg', 18029, 1, 'Product', '2d3cb0ea2e-1', NULL, NULL, NULL),
(639, NULL, NULL, 'Products/Product18029/a60a0c.jpg', 18029, NULL, 'Product', 'b672de3fda-5', NULL, NULL, NULL),
(640, NULL, NULL, 'Products/Product18029/fb37d9.jpg', 18029, NULL, 'Product', '7ab4e8114b-6', NULL, NULL, NULL),
(641, NULL, NULL, 'Products/Product18018/bd52d4.jpg', 18018, 1, 'Product', 'd3730320d5-1', NULL, NULL, NULL),
(642, NULL, NULL, 'Products/Product18018/e3a3ee.jpg', 18018, NULL, 'Product', '4124db4733-4', NULL, NULL, NULL),
(643, NULL, NULL, 'Products/Product18016/49d73f.jpg', 18016, 1, 'Product', '89adfc9c10-1', NULL, NULL, NULL),
(644, NULL, NULL, 'Products/Product18016/1f9846.jpg', 18016, NULL, 'Product', '734e281b07-5', NULL, NULL, NULL),
(645, NULL, NULL, 'Products/Product18016/e54168.jpg', 18016, NULL, 'Product', '0b8c62c23d-6', NULL, NULL, NULL),
(646, NULL, NULL, 'Products/Product18015/c5a6ac.jpg', 18015, 1, 'Product', 'aa9e537c2c-1', NULL, NULL, NULL),
(647, NULL, NULL, 'Products/Product18015/e8bdb3.jpg', 18015, NULL, 'Product', '39b8f3c786-4', NULL, NULL, NULL),
(648, NULL, NULL, 'Products/Product16228/c3da36.jpg', 16228, 1, 'Product', '11521b82f0-1', NULL, NULL, NULL),
(649, NULL, NULL, 'Products/Product16228/c84e84.jpg', 16228, NULL, 'Product', 'de19535e0c-10', NULL, NULL, NULL),
(650, NULL, NULL, 'Products/Product16228/6fe19b.jpg', 16228, NULL, 'Product', '18d84437bc-11', NULL, NULL, NULL),
(651, NULL, NULL, 'Products/Product16228/55b182.jpg', 16228, NULL, 'Product', '3c7b4a81dd-12', NULL, NULL, NULL),
(652, NULL, NULL, 'Products/Product16228/1c2e45.jpg', 16228, NULL, 'Product', '6ae47c2d0f-13', NULL, NULL, NULL),
(653, NULL, NULL, 'Products/Product16228/e837f2.jpg', 16228, NULL, 'Product', '52768914aa-14', NULL, NULL, NULL),
(654, NULL, NULL, 'Products/Product16228/c2781b.jpg', 16228, NULL, 'Product', '894166355c-15', NULL, NULL, NULL),
(655, NULL, NULL, 'Products/Product16228/1f211c.jpg', 16228, NULL, 'Product', 'db2a63aef2-16', NULL, NULL, NULL),
(656, NULL, NULL, 'Products/Product16223/379d9e.jpg', 16223, 1, 'Product', 'ade8656b1b-1', NULL, NULL, NULL),
(657, NULL, NULL, 'Products/Product16223/ed139b.jpg', 16223, NULL, 'Product', 'd0cfc8155b-11', NULL, NULL, NULL),
(658, NULL, NULL, 'Products/Product16223/485f96.jpg', 16223, NULL, 'Product', '8f3a7298e6-12', NULL, NULL, NULL),
(659, NULL, NULL, 'Products/Product16223/a91a44.jpg', 16223, NULL, 'Product', '3c9eae7775-13', NULL, NULL, NULL),
(660, NULL, NULL, 'Products/Product16223/40af49.jpg', 16223, NULL, 'Product', 'e461d4a8b6-14', NULL, NULL, NULL),
(661, NULL, NULL, 'Products/Product16223/9e225e.jpg', 16223, NULL, 'Product', '9276cc247f-15', NULL, NULL, NULL),
(662, NULL, NULL, 'Products/Product16223/3ebbb5.jpg', 16223, NULL, 'Product', 'aeb5dfd4d4-16', NULL, NULL, NULL),
(663, NULL, NULL, 'Products/Product16223/3709f7.jpg', 16223, NULL, 'Product', 'a247f38e8d-17', NULL, NULL, NULL),
(664, NULL, NULL, 'Products/Product16223/957d50.jpg', 16223, NULL, 'Product', '90eb1a7de5-18', NULL, NULL, NULL),
(665, NULL, NULL, 'Products/Product16231/7bde75.jpg', 16231, 1, 'Product', '995cb027dd-1', NULL, NULL, NULL),
(666, NULL, NULL, 'Products/Product16231/8a60d3.jpg', 16231, NULL, 'Product', '2c3b3518a1-11', NULL, NULL, NULL),
(667, NULL, NULL, 'Products/Product16231/21ab04.jpg', 16231, NULL, 'Product', '308b93100d-12', NULL, NULL, NULL),
(668, NULL, NULL, 'Products/Product16231/d48d1c.jpg', 16231, NULL, 'Product', 'fdf844612b-13', NULL, NULL, NULL),
(669, NULL, NULL, 'Products/Product16231/8adb75.jpg', 16231, NULL, 'Product', 'd8dc92523f-14', NULL, NULL, NULL),
(670, NULL, NULL, 'Products/Product16231/2e5b10.jpg', 16231, NULL, 'Product', 'b5077ffc51-15', NULL, NULL, NULL),
(671, NULL, NULL, 'Products/Product16231/6227ee.jpg', 16231, NULL, 'Product', '8ac6e9bb3d-16', NULL, NULL, NULL),
(672, NULL, NULL, 'Products/Product16231/6f0287.jpg', 16231, NULL, 'Product', '5cde9b8eae-17', NULL, NULL, NULL),
(673, NULL, NULL, 'Products/Product16231/bb8aea.jpg', 16231, NULL, 'Product', '1d116fea70-18', NULL, NULL, NULL),
(674, NULL, NULL, 'Products/Product16222/9eac92.jpg', 16222, 1, 'Product', '43a1b80007-1', NULL, NULL, NULL),
(675, NULL, NULL, 'Products/Product16222/9451a9.jpg', 16222, NULL, 'Product', '30e0ad950f-11', NULL, NULL, NULL),
(676, NULL, NULL, 'Products/Product16222/343db8.jpg', 16222, NULL, 'Product', '3b08296129-12', NULL, NULL, NULL),
(677, NULL, NULL, 'Products/Product16222/91cf1f.jpg', 16222, NULL, 'Product', 'a1a8723deb-13', NULL, NULL, NULL),
(678, NULL, NULL, 'Products/Product16222/cfa70c.jpg', 16222, NULL, 'Product', 'bf9ad032a2-14', NULL, NULL, NULL),
(679, NULL, NULL, 'Products/Product16222/63cb6f.jpg', 16222, NULL, 'Product', '4877218420-15', NULL, NULL, NULL),
(680, NULL, NULL, 'Products/Product16222/a744bf.jpg', 16222, NULL, 'Product', 'c7c75192b4-16', NULL, NULL, NULL),
(681, NULL, NULL, 'Products/Product16222/3b80d0.jpg', 16222, NULL, 'Product', '250cc21f84-17', NULL, NULL, NULL),
(682, NULL, NULL, 'Products/Product16222/b61df5.jpg', 16222, NULL, 'Product', '95883ae0a5-18', NULL, NULL, NULL),
(683, NULL, NULL, 'Products/Product16217/029228.jpg', 16217, 1, 'Product', '215ce2361d-1', NULL, NULL, NULL),
(684, NULL, NULL, 'Products/Product16217/2b0eb5.jpg', 16217, NULL, 'Product', '4055caa5d6-11', NULL, NULL, NULL),
(685, NULL, NULL, 'Products/Product16217/15b74c.jpg', 16217, NULL, 'Product', 'b3c4c31375-12', NULL, NULL, NULL),
(686, NULL, NULL, 'Products/Product16217/6966ec.jpg', 16217, NULL, 'Product', 'fa77778b14-13', NULL, NULL, NULL),
(687, NULL, NULL, 'Products/Product16217/91ede3.jpg', 16217, NULL, 'Product', '632c251162-14', NULL, NULL, NULL),
(688, NULL, NULL, 'Products/Product16217/bd0616.jpg', 16217, NULL, 'Product', '5ff143113f-15', NULL, NULL, NULL),
(689, NULL, NULL, 'Products/Product16217/a41021.jpg', 16217, NULL, 'Product', 'a2a6de816c-16', NULL, NULL, NULL),
(690, NULL, NULL, 'Products/Product16217/399ef0.jpg', 16217, NULL, 'Product', '28811e3609-17', NULL, NULL, NULL),
(691, NULL, NULL, 'Products/Product16217/d5b459.jpg', 16217, NULL, 'Product', 'f2b9fb68c1-18', NULL, NULL, NULL),
(692, NULL, NULL, 'Products/Product16218/102083.jpg', 16218, 1, 'Product', 'a29b0c6c93-1', NULL, NULL, NULL),
(693, NULL, NULL, 'Products/Product16218/a23dfc.jpg', 16218, NULL, 'Product', 'adb06eb1c2-11', NULL, NULL, NULL),
(694, NULL, NULL, 'Products/Product16218/85ccee.jpg', 16218, NULL, 'Product', '3841fb80ee-12', NULL, NULL, NULL),
(695, NULL, NULL, 'Products/Product16218/044f0e.jpg', 16218, NULL, 'Product', '7215771874-13', NULL, NULL, NULL),
(696, NULL, NULL, 'Products/Product16218/a9889b.jpg', 16218, NULL, 'Product', '538236971d-14', NULL, NULL, NULL),
(697, NULL, NULL, 'Products/Product16218/99be00.jpg', 16218, NULL, 'Product', 'f7cceff1f9-15', NULL, NULL, NULL),
(698, NULL, NULL, 'Products/Product16218/541599.jpg', 16218, NULL, 'Product', 'c2c3eb8ef4-16', NULL, NULL, NULL),
(699, NULL, NULL, 'Products/Product16218/b8dbf8.jpg', 16218, NULL, 'Product', '4372238cb8-17', NULL, NULL, NULL),
(700, NULL, NULL, 'Products/Product16218/934690.jpg', 16218, NULL, 'Product', 'a0ade743f2-18', NULL, NULL, NULL),
(701, NULL, NULL, 'Products/Product16202/233131.jpg', 16202, 1, 'Product', '8c66b8c8bf-1', NULL, NULL, NULL),
(702, NULL, NULL, 'Products/Product16202/59d23f.jpg', 16202, NULL, 'Product', 'fb8330d830-10', NULL, NULL, NULL),
(703, NULL, NULL, 'Products/Product16202/895309.jpg', 16202, NULL, 'Product', '4df5f11f45-11', NULL, NULL, NULL),
(704, NULL, NULL, 'Products/Product16202/a9b22d.jpg', 16202, NULL, 'Product', 'f23dc10ff6-12', NULL, NULL, NULL),
(705, NULL, NULL, 'Products/Product16202/064468.jpg', 16202, NULL, 'Product', '5a393f4954-13', NULL, NULL, NULL),
(706, NULL, NULL, 'Products/Product16202/2b0e70.jpg', 16202, NULL, 'Product', 'a4445009a6-14', NULL, NULL, NULL),
(707, NULL, NULL, 'Products/Product16202/3d4783.jpg', 16202, NULL, 'Product', 'c05a5e1b4b-15', NULL, NULL, NULL),
(708, NULL, NULL, 'Products/Product16202/c3ab78.jpg', 16202, NULL, 'Product', '3d56095a2f-16', NULL, NULL, NULL),
(709, NULL, NULL, 'Products/Product16197/0f1bdb.jpg', 16197, 1, 'Product', '044be5a8ab-1', NULL, NULL, NULL),
(710, NULL, NULL, 'Products/Product16197/c49e95.jpg', 16197, NULL, 'Product', '37209ce605-11', NULL, NULL, NULL),
(711, NULL, NULL, 'Products/Product16197/fa0eb7.jpg', 16197, NULL, 'Product', '51df7d9206-12', NULL, NULL, NULL),
(712, NULL, NULL, 'Products/Product16197/60adaa.jpg', 16197, NULL, 'Product', '3cecc2ff10-13', NULL, NULL, NULL),
(713, NULL, NULL, 'Products/Product16197/7eee59.jpg', 16197, NULL, 'Product', '69fb9f6666-14', NULL, NULL, NULL),
(714, NULL, NULL, 'Products/Product16197/2efff3.jpg', 16197, NULL, 'Product', 'bcb4912b0c-15', NULL, NULL, NULL),
(715, NULL, NULL, 'Products/Product16197/3d13e8.jpg', 16197, NULL, 'Product', '7299df30c1-16', NULL, NULL, NULL),
(716, NULL, NULL, 'Products/Product16197/4b41ed.jpg', 16197, NULL, 'Product', 'ab5f2a363b-17', NULL, NULL, NULL),
(717, NULL, NULL, 'Products/Product16197/c4b9f3.jpg', 16197, NULL, 'Product', '5935ccd384-18', NULL, NULL, NULL),
(718, NULL, NULL, 'Products/Product16191/8a8422.jpg', 16191, 1, 'Product', '6fb77e34f4-1', NULL, NULL, NULL),
(719, NULL, NULL, 'Products/Product16191/c72cbc.jpg', 16191, NULL, 'Product', '85fcde6292-11', NULL, NULL, NULL),
(720, NULL, NULL, 'Products/Product16191/ae4ed6.jpg', 16191, NULL, 'Product', 'a267d64da0-12', NULL, NULL, NULL),
(721, NULL, NULL, 'Products/Product16191/962c84.jpg', 16191, NULL, 'Product', 'c81e60d5c2-13', NULL, NULL, NULL),
(722, NULL, NULL, 'Products/Product16191/cdb687.jpg', 16191, NULL, 'Product', 'f5adb6d615-14', NULL, NULL, NULL),
(723, NULL, NULL, 'Products/Product16191/681ebe.jpg', 16191, NULL, 'Product', '4cfff55a43-15', NULL, NULL, NULL),
(724, NULL, NULL, 'Products/Product16191/b29468.jpg', 16191, NULL, 'Product', '38bcec8874-16', NULL, NULL, NULL),
(725, NULL, NULL, 'Products/Product16191/c012e3.jpg', 16191, NULL, 'Product', '1e979201bf-17', NULL, NULL, NULL),
(726, NULL, NULL, 'Products/Product16191/2308a6.jpg', 16191, NULL, 'Product', '7e6c60e37d-18', NULL, NULL, NULL),
(727, NULL, NULL, 'Products/Product16215/790336.jpg', 16215, 1, 'Product', '87301b1636-1', NULL, NULL, NULL),
(728, NULL, NULL, 'Products/Product16215/379138.jpg', 16215, NULL, 'Product', 'b71977a241-11', NULL, NULL, NULL),
(729, NULL, NULL, 'Products/Product16215/a12125.jpg', 16215, NULL, 'Product', '891f7f622e-12', NULL, NULL, NULL),
(730, NULL, NULL, 'Products/Product16215/9c0b8d.jpg', 16215, NULL, 'Product', 'fcc22f06b0-13', NULL, NULL, NULL),
(731, NULL, NULL, 'Products/Product16215/97d7c0.jpg', 16215, NULL, 'Product', '8a2581b4b1-14', NULL, NULL, NULL),
(732, NULL, NULL, 'Products/Product16215/38f37c.jpg', 16215, NULL, 'Product', 'a7ae40241f-15', NULL, NULL, NULL),
(733, NULL, NULL, 'Products/Product16215/7389c6.jpg', 16215, NULL, 'Product', 'dd3de59fe3-16', NULL, NULL, NULL),
(734, NULL, NULL, 'Products/Product16215/bba9ab.jpg', 16215, NULL, 'Product', '8f82d3ff39-17', NULL, NULL, NULL),
(735, NULL, NULL, 'Products/Product16215/664b25.jpg', 16215, NULL, 'Product', '420330f0b0-18', NULL, NULL, NULL),
(736, NULL, NULL, 'Products/Product16206/1c7bb4.jpg', 16206, 1, 'Product', '3aa752b191-1', NULL, NULL, NULL),
(737, NULL, NULL, 'Products/Product16206/4f49a3.jpg', 16206, NULL, 'Product', '5e5472af39-11', NULL, NULL, NULL),
(738, NULL, NULL, 'Products/Product16206/a6eaf8.jpg', 16206, NULL, 'Product', 'a977211856-12', NULL, NULL, NULL),
(739, NULL, NULL, 'Products/Product16206/baaf63.jpg', 16206, NULL, 'Product', 'f5c7c0769e-13', NULL, NULL, NULL),
(740, NULL, NULL, 'Products/Product16206/732e26.jpg', 16206, NULL, 'Product', 'f44023b07f-14', NULL, NULL, NULL),
(741, NULL, NULL, 'Products/Product16206/3aed71.jpg', 16206, NULL, 'Product', 'a500e952cb-15', NULL, NULL, NULL),
(742, NULL, NULL, 'Products/Product16206/6a8942.jpg', 16206, NULL, 'Product', '5969a103a6-16', NULL, NULL, NULL),
(743, NULL, NULL, 'Products/Product16206/06daa0.jpg', 16206, NULL, 'Product', '2d1c35239d-17', NULL, NULL, NULL),
(744, NULL, NULL, 'Products/Product16206/9b5b2b.jpg', 16206, NULL, 'Product', '7f99f53b33-18', NULL, NULL, NULL),
(745, NULL, NULL, 'Products/Product16187/984636.jpg', 16187, 1, 'Product', '35671ec813-1', NULL, NULL, NULL),
(746, NULL, NULL, 'Products/Product16187/dfd525.jpg', 16187, NULL, 'Product', '1ce221cb8f-11', NULL, NULL, NULL),
(747, NULL, NULL, 'Products/Product16187/049075.jpg', 16187, NULL, 'Product', 'a6cd813c8d-12', NULL, NULL, NULL),
(748, NULL, NULL, 'Products/Product16187/8cc65b.jpg', 16187, NULL, 'Product', '571834a448-13', NULL, NULL, NULL),
(749, NULL, NULL, 'Products/Product16187/c0e0e5.jpg', 16187, NULL, 'Product', '679ee96ce4-14', NULL, NULL, NULL),
(750, NULL, NULL, 'Products/Product16187/9e6cd3.jpg', 16187, NULL, 'Product', '0ce2d3c0ad-15', NULL, NULL, NULL),
(751, NULL, NULL, 'Products/Product16187/0d5ac0.jpg', 16187, NULL, 'Product', '1c9f576d93-16', NULL, NULL, NULL),
(752, NULL, NULL, 'Products/Product16187/53737c.jpg', 16187, NULL, 'Product', '6376deb36d-17', NULL, NULL, NULL),
(753, NULL, NULL, 'Products/Product16187/a37825.jpg', 16187, NULL, 'Product', 'f375c43ce1-18', NULL, NULL, NULL),
(754, NULL, NULL, 'Products/Product16177/e25d25.jpg', 16177, 1, 'Product', '24840c4cb5-1', NULL, NULL, NULL),
(755, NULL, NULL, 'Products/Product16177/b5284f.jpg', 16177, NULL, 'Product', '063d0b942a-11', NULL, NULL, NULL),
(756, NULL, NULL, 'Products/Product16177/0066d8.jpg', 16177, NULL, 'Product', 'd02d62dc2c-12', NULL, NULL, NULL),
(757, NULL, NULL, 'Products/Product16177/e8e809.jpg', 16177, NULL, 'Product', '0e8d41161d-13', NULL, NULL, NULL),
(758, NULL, NULL, 'Products/Product16177/832c2d.jpg', 16177, NULL, 'Product', 'ad9480f976-14', NULL, NULL, NULL),
(759, NULL, NULL, 'Products/Product16177/e0ca7d.jpg', 16177, NULL, 'Product', '6a3c66f5f0-15', NULL, NULL, NULL),
(760, NULL, NULL, 'Products/Product16177/28b50a.jpg', 16177, NULL, 'Product', 'c5d091245b-16', NULL, NULL, NULL),
(761, NULL, NULL, 'Products/Product16177/29ec49.jpg', 16177, NULL, 'Product', '9bf1ddec46-17', NULL, NULL, NULL),
(762, NULL, NULL, 'Products/Product16177/c7b631.jpg', 16177, NULL, 'Product', '270b5441f0-18', NULL, NULL, NULL),
(763, NULL, NULL, 'Products/Product16125/99fc54.jpg', 16125, 1, 'Product', '86f2b12d2f-1', NULL, NULL, NULL),
(764, NULL, NULL, 'Products/Product16125/00768c.jpg', 16125, NULL, 'Product', 'befd3fbea9-7', NULL, NULL, NULL),
(765, NULL, NULL, 'Products/Product16125/86d42b.jpg', 16125, NULL, 'Product', 'ec27e3fd66-8', NULL, NULL, NULL),
(766, NULL, NULL, 'Products/Product16125/09f1b4.jpg', 16125, NULL, 'Product', '6797776b5e-9', NULL, NULL, NULL),
(767, NULL, NULL, 'Products/Product16125/2d962a.jpg', 16125, NULL, 'Product', '4ca833b371-10', NULL, NULL, NULL),
(768, NULL, NULL, 'Products/Product16124/b1abb4.jpg', 16124, 1, 'Product', '2820dfd3e9-1', NULL, NULL, NULL),
(769, NULL, NULL, 'Products/Product16124/01daed.jpg', 16124, NULL, 'Product', 'efe400e960-7', NULL, NULL, NULL),
(770, NULL, NULL, 'Products/Product16124/4127d1.jpg', 16124, NULL, 'Product', '256f8844a3-8', NULL, NULL, NULL),
(771, NULL, NULL, 'Products/Product16124/eb10de.jpg', 16124, NULL, 'Product', 'a364509016-9', NULL, NULL, NULL),
(772, NULL, NULL, 'Products/Product16124/356214.jpg', 16124, NULL, 'Product', '1708494b28-10', NULL, NULL, NULL),
(773, NULL, NULL, 'Products/Product16168/9596a4.jpg', 16168, 1, 'Product', '4f7309fb7d-1', NULL, NULL, NULL),
(774, NULL, NULL, 'Products/Product16168/5b9062.jpg', 16168, NULL, 'Product', '48cd3b2a03-11', NULL, NULL, NULL),
(775, NULL, NULL, 'Products/Product16168/2cb6e7.jpg', 16168, NULL, 'Product', '90ad66ba52-12', NULL, NULL, NULL),
(776, NULL, NULL, 'Products/Product16168/71d092.jpg', 16168, NULL, 'Product', 'e93cd461b8-13', NULL, NULL, NULL),
(777, NULL, NULL, 'Products/Product16168/8d065f.jpg', 16168, NULL, 'Product', 'dec89d9b84-14', NULL, NULL, NULL),
(778, NULL, NULL, 'Products/Product16168/264e79.jpg', 16168, NULL, 'Product', '50b63b80b1-15', NULL, NULL, NULL),
(779, NULL, NULL, 'Products/Product16168/9174d2.jpg', 16168, NULL, 'Product', '9ae0128556-16', NULL, NULL, NULL),
(780, NULL, NULL, 'Products/Product16168/8879c4.jpg', 16168, NULL, 'Product', '29542973c0-17', NULL, NULL, NULL),
(781, NULL, NULL, 'Products/Product16168/0853da.jpg', 16168, NULL, 'Product', '7bf2476504-18', NULL, NULL, NULL),
(782, NULL, NULL, 'Products/Product16123/ffcfb9.jpg', 16123, 1, 'Product', '48e91e58d9-1', NULL, NULL, NULL),
(783, NULL, NULL, 'Products/Product16123/5bd642.jpg', 16123, NULL, 'Product', 'c9c541556c-6', NULL, NULL, NULL),
(784, NULL, NULL, 'Products/Product16123/1377f9.jpg', 16123, NULL, 'Product', '4286b99449-7', NULL, NULL, NULL),
(785, NULL, NULL, 'Products/Product16123/da3577.jpg', 16123, NULL, 'Product', '0bf6d12d78-8', NULL, NULL, NULL),
(786, NULL, NULL, 'Products/Product16120/f3e4b1.jpg', 16120, 1, 'Product', '2b99e03b04-1', NULL, NULL, NULL),
(787, NULL, NULL, 'Products/Product16120/a8a964.jpg', 16120, NULL, 'Product', 'ed2369d896-7', NULL, NULL, NULL),
(788, NULL, NULL, 'Products/Product16120/d60899.jpg', 16120, NULL, 'Product', '10ee44d22f-8', NULL, NULL, NULL),
(789, NULL, NULL, 'Products/Product16120/a14ecf.jpg', 16120, NULL, 'Product', 'd6bd805993-9', NULL, NULL, NULL),
(790, NULL, NULL, 'Products/Product16120/e9427f.jpg', 16120, NULL, 'Product', '01babbbb69-10', NULL, NULL, NULL),
(791, NULL, NULL, 'Products/Product16121/f2f07e.jpg', 16121, 1, 'Product', 'a519db1b6f-1', NULL, NULL, NULL),
(792, NULL, NULL, 'Products/Product16121/572d06.jpg', 16121, NULL, 'Product', 'a7ac49fc86-7', NULL, NULL, NULL),
(793, NULL, NULL, 'Products/Product16121/029b52.jpg', 16121, NULL, 'Product', '823569fc3f-8', NULL, NULL, NULL),
(794, NULL, NULL, 'Products/Product16121/47236a.jpg', 16121, NULL, 'Product', 'e3aafc8339-9', NULL, NULL, NULL),
(795, NULL, NULL, 'Products/Product16121/16bb0a.jpg', 16121, NULL, 'Product', 'bc1fbcf6d1-10', NULL, NULL, NULL),
(796, NULL, NULL, 'Products/Product16119/f2d611.jpg', 16119, 1, 'Product', '70553c2153-1', NULL, NULL, NULL),
(797, NULL, NULL, 'Products/Product16119/3cf3a0.jpg', 16119, NULL, 'Product', '99fdb28584-7', NULL, NULL, NULL),
(798, NULL, NULL, 'Products/Product16119/15b0e8.jpg', 16119, NULL, 'Product', '37446acdeb-8', NULL, NULL, NULL),
(799, NULL, NULL, 'Products/Product16119/43e5f2.jpg', 16119, NULL, 'Product', '01d90a75b4-9', NULL, NULL, NULL),
(800, NULL, NULL, 'Products/Product16119/80966b.jpg', 16119, NULL, 'Product', 'b12aad1b66-10', NULL, NULL, NULL),
(801, NULL, NULL, 'Products/Product16095/2ac71c.jpg', 16095, 1, 'Product', '4a753a11d5-1', NULL, NULL, NULL),
(802, NULL, NULL, 'Products/Product16095/1c5362.jpg', 16095, NULL, 'Product', '627df4999b-7', NULL, NULL, NULL),
(803, NULL, NULL, 'Products/Product16095/adab19.jpg', 16095, NULL, 'Product', '95c9e863da-8', NULL, NULL, NULL),
(804, NULL, NULL, 'Products/Product16095/b71c40.jpg', 16095, NULL, 'Product', '11be2096f4-9', NULL, NULL, NULL),
(805, NULL, NULL, 'Products/Product16095/81add6.jpg', 16095, NULL, 'Product', '11f1ef816c-10', NULL, NULL, NULL),
(806, NULL, NULL, 'Products/Product16122/76b569.jpg', 16122, 1, 'Product', '8abe8c763b-1', NULL, NULL, NULL),
(807, NULL, NULL, 'Products/Product16122/de6c29.jpg', 16122, NULL, 'Product', '2581f653b1-7', NULL, NULL, NULL),
(808, NULL, NULL, 'Products/Product16122/2a28fe.jpg', 16122, NULL, 'Product', '8d74bd2655-8', NULL, NULL, NULL),
(809, NULL, NULL, 'Products/Product16122/4c4f08.jpg', 16122, NULL, 'Product', '23b9c025b3-9', NULL, NULL, NULL),
(810, NULL, NULL, 'Products/Product16122/227c83.jpg', 16122, NULL, 'Product', '584b41243e-10', NULL, NULL, NULL),
(811, NULL, NULL, 'Products/Product16094/d625d0.jpg', 16094, 1, 'Product', '5c6496eb7e-1', NULL, NULL, NULL),
(812, NULL, NULL, 'Products/Product16094/4bf287.jpg', 16094, NULL, 'Product', '4140ec8111-7', NULL, NULL, NULL),
(813, NULL, NULL, 'Products/Product16094/b9b29b.jpg', 16094, NULL, 'Product', '41f1c7c7b5-8', NULL, NULL, NULL),
(814, NULL, NULL, 'Products/Product16094/85ad9d.jpg', 16094, NULL, 'Product', '11d208c069-9', NULL, NULL, NULL),
(815, NULL, NULL, 'Products/Product16094/851174.jpg', 16094, NULL, 'Product', '764bd36f25-10', NULL, NULL, NULL),
(816, NULL, NULL, 'Products/Product16093/145c48.jpg', 16093, 1, 'Product', '6d1690c1e0-1', NULL, NULL, NULL),
(817, NULL, NULL, 'Products/Product16093/438c5b.jpg', 16093, NULL, 'Product', 'd22da8cb93-6', NULL, NULL, NULL),
(818, NULL, NULL, 'Products/Product16093/25fac0.jpg', 16093, NULL, 'Product', 'f39660aa7a-7', NULL, NULL, NULL),
(819, NULL, NULL, 'Products/Product16093/793b88.jpg', 16093, NULL, 'Product', 'e7099d51f2-8', NULL, NULL, NULL),
(820, NULL, NULL, 'Products/Product16091/203969.jpg', 16091, 1, 'Product', 'e094d9f56a-1', NULL, NULL, NULL),
(821, NULL, NULL, 'Products/Product16091/3b2de4.jpg', 16091, NULL, 'Product', '4b29881cdc-7', NULL, NULL, NULL),
(822, NULL, NULL, 'Products/Product16091/d5463c.jpg', 16091, NULL, 'Product', '6ba445d3c2-8', NULL, NULL, NULL),
(823, NULL, NULL, 'Products/Product16091/ab375b.jpg', 16091, NULL, 'Product', '4de14163b5-9', NULL, NULL, NULL),
(824, NULL, NULL, 'Products/Product16091/78fa1c.jpg', 16091, NULL, 'Product', '3b3b5679ea-10', NULL, NULL, NULL),
(825, NULL, NULL, 'Products/Product16092/3228bb.jpg', 16092, 1, 'Product', '84193804d4-1', NULL, NULL, NULL),
(826, NULL, NULL, 'Products/Product16092/7cc545.jpg', 16092, NULL, 'Product', '6f66a05679-7', NULL, NULL, NULL),
(827, NULL, NULL, 'Products/Product16092/035e90.jpg', 16092, NULL, 'Product', '11532b8a42-8', NULL, NULL, NULL),
(828, NULL, NULL, 'Products/Product16092/794398.jpg', 16092, NULL, 'Product', '2eece2bd55-9', NULL, NULL, NULL),
(829, NULL, NULL, 'Products/Product16092/445739.jpg', 16092, NULL, 'Product', '19c810821a-10', NULL, NULL, NULL),
(830, NULL, NULL, 'Products/Product16090/51cf8c.jpg', 16090, 1, 'Product', 'c5d888dca0-1', NULL, NULL, NULL),
(831, NULL, NULL, 'Products/Product16090/b2782d.jpg', 16090, NULL, 'Product', 'c9d7e22313-7', NULL, NULL, NULL),
(832, NULL, NULL, 'Products/Product16090/c9d4a0.jpg', 16090, NULL, 'Product', '6ec752c042-8', NULL, NULL, NULL),
(833, NULL, NULL, 'Products/Product16090/b6ca51.jpg', 16090, NULL, 'Product', 'd81daa944a-9', NULL, NULL, NULL),
(834, NULL, NULL, 'Products/Product16090/d61244.jpg', 16090, NULL, 'Product', '67b01c60e3-10', NULL, NULL, NULL),
(835, NULL, NULL, 'Products/Product16089/19ed34.jpg', 16089, 1, 'Product', 'c86d869aca-1', NULL, NULL, NULL),
(836, NULL, NULL, 'Products/Product16089/457db4.jpg', 16089, NULL, 'Product', '3e9cb10a00-7', NULL, NULL, NULL),
(837, NULL, NULL, 'Products/Product16089/4d2c5f.jpg', 16089, NULL, 'Product', 'a0c36ceb79-8', NULL, NULL, NULL),
(838, NULL, NULL, 'Products/Product16089/e7355d.jpg', 16089, NULL, 'Product', 'e9e1690c5a-9', NULL, NULL, NULL),
(839, NULL, NULL, 'Products/Product16089/d36a0a.jpg', 16089, NULL, 'Product', '85bf355635-10', NULL, NULL, NULL),
(840, NULL, NULL, 'Products/Product16042/a561ac.jpg', 16042, 1, 'Product', '88448dda30-1', NULL, NULL, NULL),
(841, NULL, NULL, 'Products/Product16042/d80c04.jpg', 16042, NULL, 'Product', '52f78bd95d-7', NULL, NULL, NULL),
(842, NULL, NULL, 'Products/Product16042/e5d9f7.jpg', 16042, NULL, 'Product', 'ae321fafe1-8', NULL, NULL, NULL),
(843, NULL, NULL, 'Products/Product16042/8ad437.jpg', 16042, NULL, 'Product', 'e002dfd490-9', NULL, NULL, NULL),
(844, NULL, NULL, 'Products/Product16042/82d6b9.jpg', 16042, NULL, 'Product', 'd4ec40e46c-10', NULL, NULL, NULL),
(845, NULL, NULL, 'Products/Product16039/c7db36.jpg', 16039, 1, 'Product', '16b8eb7cbd-1', NULL, NULL, NULL),
(846, NULL, NULL, 'Products/Product16039/eecbcf.jpg', 16039, NULL, 'Product', '5de09d9bf0-7', NULL, NULL, NULL),
(847, NULL, NULL, 'Products/Product16039/46636d.jpg', 16039, NULL, 'Product', '78e1150940-8', NULL, NULL, NULL),
(848, NULL, NULL, 'Products/Product16039/2fe742.jpg', 16039, NULL, 'Product', '5d6b17a8b0-9', NULL, NULL, NULL),
(849, NULL, NULL, 'Products/Product16039/100579.jpg', 16039, NULL, 'Product', '4069b6fe57-10', NULL, NULL, NULL),
(850, NULL, NULL, 'Products/Product16041/d7869e.jpg', 16041, 1, 'Product', '27d173f1b3-1', NULL, NULL, NULL),
(851, NULL, NULL, 'Products/Product16041/e65d1d.jpg', 16041, NULL, 'Product', '80e67c554d-7', NULL, NULL, NULL),
(852, NULL, NULL, 'Products/Product16041/c82ea7.jpg', 16041, NULL, 'Product', 'c024738d55-8', NULL, NULL, NULL),
(853, NULL, NULL, 'Products/Product16041/398c82.jpg', 16041, NULL, 'Product', 'be0d88030f-9', NULL, NULL, NULL),
(854, NULL, NULL, 'Products/Product16041/026dd4.jpg', 16041, NULL, 'Product', 'd73bd6372e-10', NULL, NULL, NULL),
(855, NULL, NULL, 'Products/Product16040/5f7059.jpg', 16040, 1, 'Product', '5592760899-1', NULL, NULL, NULL),
(856, NULL, NULL, 'Products/Product16040/f61f8e.jpg', 16040, NULL, 'Product', '121e2396ad-7', NULL, NULL, NULL),
(857, NULL, NULL, 'Products/Product16040/b88595.jpg', 16040, NULL, 'Product', '603b78a8d4-8', NULL, NULL, NULL),
(858, NULL, NULL, 'Products/Product16040/04ba65.jpg', 16040, NULL, 'Product', '5a737494a6-9', NULL, NULL, NULL),
(859, NULL, NULL, 'Products/Product16040/957a9d.jpg', 16040, NULL, 'Product', 'a79531ab5b-10', NULL, NULL, NULL),
(860, NULL, NULL, 'Products/Product16043/743163.jpg', 16043, 1, 'Product', '5b5502d2ff-1', NULL, NULL, NULL),
(861, NULL, NULL, 'Products/Product16043/e9156d.jpg', 16043, NULL, 'Product', '57a2fdfc32-6', NULL, NULL, NULL),
(862, NULL, NULL, 'Products/Product16043/55bc96.jpg', 16043, NULL, 'Product', '28a779a10c-7', NULL, NULL, NULL),
(863, NULL, NULL, 'Products/Product16043/b31557.jpg', 16043, NULL, 'Product', '4920bf6340-8', NULL, NULL, NULL),
(864, NULL, NULL, 'Products/Product16045/743692.jpg', 16045, 1, 'Product', 'dc57928a4f-1', NULL, NULL, NULL),
(865, NULL, NULL, 'Products/Product16045/020845.jpg', 16045, NULL, 'Product', 'a6652857f8-7', NULL, NULL, NULL),
(866, NULL, NULL, 'Products/Product16045/1b1a93.jpg', 16045, NULL, 'Product', 'c88015a36f-8', NULL, NULL, NULL),
(867, NULL, NULL, 'Products/Product16045/462443.jpg', 16045, NULL, 'Product', '1fa3dfe665-9', NULL, NULL, NULL),
(868, NULL, NULL, 'Products/Product16045/cb30e3.jpg', 16045, NULL, 'Product', '981ffcc748-10', NULL, NULL, NULL),
(869, NULL, NULL, 'Products/Product16044/d1ce68.jpg', 16044, 1, 'Product', 'bb7c522c01-1', NULL, NULL, NULL),
(870, NULL, NULL, 'Products/Product16044/0c08f2.jpg', 16044, NULL, 'Product', '737cb57802-7', NULL, NULL, NULL),
(871, NULL, NULL, 'Products/Product16044/ec772b.jpg', 16044, NULL, 'Product', 'baeb54bec0-8', NULL, NULL, NULL),
(872, NULL, NULL, 'Products/Product16044/c76e18.jpg', 16044, NULL, 'Product', 'ee93242aaa-9', NULL, NULL, NULL),
(873, NULL, NULL, 'Products/Product16044/52fea0.jpg', 16044, NULL, 'Product', '1e82ad2ba3-10', NULL, NULL, NULL),
(874, NULL, NULL, 'Products/Product15105/10f16b.jpg', 15105, 1, 'Product', 'd2062c24d7-1', NULL, NULL, NULL),
(875, NULL, NULL, 'Products/Product15105/023de3.jpg', 15105, NULL, 'Product', 'b129484f02-5', NULL, NULL, NULL),
(876, NULL, NULL, 'Products/Product15105/9e0824.jpg', 15105, NULL, 'Product', '5989a6e62c-6', NULL, NULL, NULL),
(877, NULL, NULL, 'Products/Product15099/580b3d.jpg', 15099, 1, 'Product', '436013acc6-1', NULL, NULL, NULL),
(878, NULL, NULL, 'Products/Product15099/a39f18.jpg', 15099, NULL, 'Product', '86900d2f8b-6', NULL, NULL, NULL),
(879, NULL, NULL, 'Products/Product15099/b3dc7b.jpg', 15099, NULL, 'Product', 'bec08629c5-7', NULL, NULL, NULL),
(880, NULL, NULL, 'Products/Product15099/8e7456.jpg', 15099, NULL, 'Product', '3cfab83ad4-8', NULL, NULL, NULL),
(881, NULL, NULL, 'Products/Product15079/576878.jpg', 15079, 1, 'Product', '3d12cc9a3e-1', NULL, NULL, NULL),
(882, NULL, NULL, 'Products/Product15079/659794.jpg', 15079, NULL, 'Product', 'e2c66d03a0-5', NULL, NULL, NULL),
(883, NULL, NULL, 'Products/Product15079/cf3f4f.jpg', 15079, NULL, 'Product', '9034d1feb4-6', NULL, NULL, NULL),
(884, NULL, NULL, 'Products/Product15078/98ce8f.jpg', 15078, 1, 'Product', 'e036fa5e26-1', NULL, NULL, NULL),
(885, NULL, NULL, 'Products/Product15078/7a46fd.jpg', 15078, NULL, 'Product', 'f97a4903d0-6', NULL, NULL, NULL),
(886, NULL, NULL, 'Products/Product15078/2a337b.jpg', 15078, NULL, 'Product', '4a8965f0ea-7', NULL, NULL, NULL),
(887, NULL, NULL, 'Products/Product15078/46a4cf.jpg', 15078, NULL, 'Product', '3f09c23ecc-8', NULL, NULL, NULL),
(888, NULL, NULL, 'Products/Product15077/8c60ff.jpg', 15077, 1, 'Product', 'fc5b00bd02-1', NULL, NULL, NULL),
(889, NULL, NULL, 'Products/Product15077/3c0c8c.jpg', 15077, NULL, 'Product', '56de452ec2-5', NULL, NULL, NULL),
(890, NULL, NULL, 'Products/Product15077/e03a9f.jpg', 15077, NULL, 'Product', '9fac6ebc59-6', NULL, NULL, NULL),
(891, NULL, NULL, 'Products/Product15076/835fda.jpg', 15076, 1, 'Product', '1e0e10a0bc-1', NULL, NULL, NULL),
(892, NULL, NULL, 'Products/Product15076/d4f862.jpg', 15076, NULL, 'Product', '398f4d932f-5', NULL, NULL, NULL),
(893, NULL, NULL, 'Products/Product15076/c26db9.jpg', 15076, NULL, 'Product', 'bbfac38135-6', NULL, NULL, NULL),
(894, NULL, NULL, 'Products/Product15051/9f3e6c.jpg', 15051, 1, 'Product', '26256ee789-1', NULL, NULL, NULL),
(895, NULL, NULL, 'Products/Product15051/2e942c.jpg', 15051, NULL, 'Product', 'dd51e0ce3e-5', NULL, NULL, NULL),
(896, NULL, NULL, 'Products/Product15051/94ffd7.jpg', 15051, NULL, 'Product', 'b513474611-6', NULL, NULL, NULL),
(897, NULL, NULL, 'Products/Product12119/149184.jpg', 12119, 1, 'Product', '5614fd6042-1', NULL, NULL, NULL),
(898, NULL, NULL, 'Products/Product12119/294ad6.jpg', 12119, NULL, 'Product', '7a2672bf3f-7', NULL, NULL, NULL),
(899, NULL, NULL, 'Products/Product12119/63a86c.jpg', 12119, NULL, 'Product', '766989cb74-8', NULL, NULL, NULL),
(900, NULL, NULL, 'Products/Product12119/9054db.jpg', 12119, NULL, 'Product', 'dd64304334-9', NULL, NULL, NULL),
(901, NULL, NULL, 'Products/Product12119/4cb747.jpg', 12119, NULL, 'Product', 'be7a322089-10', NULL, NULL, NULL),
(902, NULL, NULL, 'Products/Product15053/101dba.jpg', 15053, 1, 'Product', '8555b99e59-1', NULL, NULL, NULL),
(903, NULL, NULL, 'Products/Product15053/7675bc.jpg', 15053, NULL, 'Product', '1074ae8370-5', NULL, NULL, NULL),
(904, NULL, NULL, 'Products/Product15053/c9c259.jpg', 15053, NULL, 'Product', 'a758ffce17-6', NULL, NULL, NULL),
(905, NULL, NULL, 'Products/Product14726/439e2c.jpg', 14726, 1, 'Product', '747185a266-1', NULL, NULL, NULL);
INSERT INTO `image` (`id`, `title`, `alt`, `filePath`, `itemId`, `isMain`, `modelName`, `urlAlias`, `description`, `gallery_id`, `sort`) VALUES
(906, NULL, NULL, 'Products/Product14726/ffde28.jpg', 14726, NULL, 'Product', 'becd00ab10-5', NULL, NULL, NULL),
(907, NULL, NULL, 'Products/Product14726/acc839.jpg', 14726, NULL, 'Product', '56c05e7eaa-6', NULL, NULL, NULL),
(908, NULL, NULL, 'Products/Product15050/57ef1d.jpg', 15050, 1, 'Product', '3867463062-1', NULL, NULL, NULL),
(909, NULL, NULL, 'Products/Product15050/4c6860.jpg', 15050, NULL, 'Product', '110ced0595-5', NULL, NULL, NULL),
(910, NULL, NULL, 'Products/Product15050/c9056a.jpg', 15050, NULL, 'Product', '7cf46ca9d3-6', NULL, NULL, NULL),
(911, NULL, NULL, 'Products/Product9518/ca797a.jpg', 9518, 1, 'Product', 'bfc6599be2-1', NULL, NULL, NULL),
(912, NULL, NULL, 'Products/Product9518/e6eaa5.jpg', 9518, NULL, 'Product', 'cdc0a7e00e-7', NULL, NULL, NULL),
(913, NULL, NULL, 'Products/Product9518/c20c49.jpg', 9518, NULL, 'Product', '7150b5a946-8', NULL, NULL, NULL),
(914, NULL, NULL, 'Products/Product9518/967c92.jpg', 9518, NULL, 'Product', '3f6d39c95b-9', NULL, NULL, NULL),
(915, NULL, NULL, 'Products/Product9518/cbc298.jpg', 9518, NULL, 'Product', '6f1a12c8c1-10', NULL, NULL, NULL),
(916, NULL, NULL, 'Products/Product2446/e98dc0.jpg', 2446, 1, 'Product', '708851c529-1', NULL, NULL, NULL),
(917, NULL, NULL, 'Products/Product2446/a9ed88.jpg', 2446, NULL, 'Product', '1fc3f3aacc-5', NULL, NULL, NULL),
(918, NULL, NULL, 'Products/Product2446/6d0599.jpg', 2446, NULL, 'Product', '393542bcff-6', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблиці `language`
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
-- Дамп даних таблиці `language`
--

INSERT INTO `language` (`language_id`, `language`, `country`, `url`, `name`, `name_ascii`, `status`) VALUES
('be-BY', 'be', 'by', 'by', 'Беларуская', 'Belarusian', 0),
('en-US', 'en', 'us', 'en', 'English (US)', 'English (US)', 0),
('ru-RU', 'ru', 'ru', 'ru', 'Русский', 'Russian', 1),
('uk-UA', 'uk', 'ua', 'ua', 'Українська', 'Ukrainian', 0);

-- --------------------------------------------------------

--
-- Структура таблиці `language_source`
--

CREATE TABLE `language_source` (
  `id` int(11) NOT NULL,
  `category` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `language_source`
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
(23, 'admin', 'Admin panel'),
(24, 'shop', 'Shop'),
(25, 'field', 'Fields'),
(26, 'filter', 'Filter'),
(27, 'user', 'Users'),
(28, 'admin', 'Save & Create new');

-- --------------------------------------------------------

--
-- Структура таблиці `language_translate`
--

CREATE TABLE `language_translate` (
  `id` int(11) NOT NULL DEFAULT '0',
  `language` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `translation` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `language_translate`
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
(23, 'ru-RU', 'Администрирование'),
(24, 'ru-RU', 'Магазин'),
(25, 'ru-RU', 'Дополнительные поля'),
(26, 'ru-RU', 'Фильтр'),
(27, 'ru-RU', 'Пользователи'),
(28, 'ru-RU', 'Сохранить и создать');

-- --------------------------------------------------------

--
-- Структура таблиці `medias`
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
-- Структура таблиці `menu_item`
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
  `metatitle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
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

--
-- Дамп даних таблиці `menu_item`
--

INSERT INTO `menu_item` (`id`, `menu_type_id`, `parent_id`, `translation_id`, `status`, `language`, `title`, `alias`, `path`, `note`, `link`, `link_type`, `link_weight`, `link_params`, `layout_path`, `access_rule`, `metatitle`, `metakey`, `metadesc`, `robots`, `secure`, `created_at`, `updated_at`, `created_by`, `updated_by`, `lft`, `rgt`, `level`, `ordering`, `hits`, `lock`) VALUES
(1, 0, NULL, NULL, 1, '', 'Root', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, 1476552518, NULL, 1, 1, 1, 40, 1, 1, NULL, 1),
(2, 1, 1, 2, 1, 'ru-RU', 'Компьютерние столы', 'komputernie-stoly', 'komputernie-stoly', '', 'shop/category/view?slug=komp-uternye-stoly', 1, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1481588437, 1486839337, 1, 1, 6, 7, 2, 3, NULL, 26),
(3, 1, 1, 3, 1, 'ru-RU', 'Шкафы купе', 'komputernie-stoly-2', 'komputernie-stoly-2', '', 'shop/category/view?slug=skafy-kupe', 1, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1481588632, 1483094567, 1, 1, 24, 25, 2, 12, NULL, 26),
(4, 2, 1, 4, 1, 'ru-RU', 'Кухня', 'kuhnya', 'kuhnya', '', 'shop/category/view?slug=kuhna', 1, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1481659473, 1483125724, 1, 1, 8, 9, 2, 4, NULL, 11),
(5, 2, 1, 5, 1, 'ru-RU', 'Cпальня', 'cpalna', 'cpalna', '', 'shop/category/view?slug=spal-na', 1, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1481659667, 1483125307, 1, 1, 10, 11, 2, 5, NULL, 2),
(6, 2, 1, 6, 1, 'ru-RU', 'гостиння', 'gostinna', 'gostinna', '', 'shop/category/view?slug=gostinna', 1, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1481659724, 1483125326, 1, 1, 12, 13, 2, 6, NULL, 2),
(7, 2, 1, 7, 1, 'ru-RU', 'Прихожая', 'prihozaa', 'prihozaa', '', 'shop/category/view?slug=prihozaa', 1, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1481659761, 1483125380, 1, 1, 14, 15, 2, 7, NULL, 2),
(8, 2, 1, 8, 1, 'ru-RU', 'Детская', 'detskaa', 'detskaa', '', 'shop/category/view?slug=detskaa', 1, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1481659777, 1483125663, 1, 1, 16, 17, 2, 8, NULL, 3),
(9, 2, 1, 9, 1, 'ru-RU', 'Подростковая', 'podrostkovaa', 'podrostkovaa', '', 'shop/category/view?slug=podrostkovaa', 1, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1481659801, 1483125675, 1, 1, 18, 19, 2, 9, NULL, 2),
(10, 2, 1, 10, 1, 'ru-RU', 'Кабинет', 'kabinet', 'kabinet', '', 'shop/category/view?slug=kabinet', 1, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1481659814, 1483125689, 1, 1, 20, 21, 2, 10, NULL, 2),
(11, 2, 1, 11, 1, 'ru-RU', 'Акции', 'akcii', 'akcii', '', '#', 2, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1481659859, 1481659859, 1, 1, 22, 23, 2, 11, NULL, 1),
(13, 1, 1, 13, 1, 'ru-RU', 'Диваны', 'divany', 'divany', '', 'shop/category/view?slug=divany', 1, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1483094788, 1483094788, 1, 1, 26, 27, 2, 13, NULL, 1),
(14, 1, 1, 14, 1, 'ru-RU', 'Матрасы', 'matrasy', 'matrasy', '', 'shop/category/view?slug=matrasy', 1, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1483094821, 1483094821, 1, 1, 28, 29, 2, 14, NULL, 1),
(15, 1, 1, 15, 1, 'ru-RU', 'Журнальные столы', 'zurnalnye-stoly', 'zurnalnye-stoly', '', 'shop/category/view?slug=zurnal-nye-stoly', 1, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1483095552, 1483095552, 1, 1, 30, 31, 2, 15, NULL, 1),
(16, 1, 1, 16, 1, 'ru-RU', 'Тумбы', 'tumby', 'tumby', '', 'shop/category/view?slug=tumby', 1, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1483096194, 1483096194, 1, 1, 32, 33, 2, 16, NULL, 1),
(17, 1, 1, 17, 1, 'ru-RU', 'Стулья', 'stula', 'stula', '', 'shop/category/view?slug=stul-a', 1, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1483096243, 1483096243, 1, 1, 34, 35, 2, 17, NULL, 1),
(18, 1, 1, 18, 1, 'ru-RU', 'Светильники', 'svetilniki', 'svetilniki', '', 'shop/category/view?slug=svetil-niki', 1, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1483096266, 1483096266, 1, 1, 36, 37, 2, 18, NULL, 1),
(19, 1, 1, 19, 1, 'ru-RU', 'Кровати', 'krovati', 'krovati', '', 'shop/category/view?slug=krovati', 1, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1483096283, 1483096283, 1, 1, 38, 39, 2, 19, NULL, 1),
(20, 3, 1, 20, 2, 'ru-RU', 'Главная', 'glavnaa', 'glavnaa', '', 'content/page/view?slug=home', 1, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1483476708, 1486659517, 1, 1, 4, 5, 2, 2, NULL, 8),
(21, 3, 1, 20, 2, 'en-US', 'Home', 'glavnaa', 'glavnaa', '', 'content/page/view?slug=home', 1, 0, '{"title":"","class":"","style":"","target":"","onclick":"","rel":""}', '', '', '', '', '', '', NULL, 1488312626, 1488312626, 1, 1, 2, 3, 2, 1, NULL, 1);

-- --------------------------------------------------------

--
-- Структура таблиці `menu_type`
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
-- Дамп даних таблиці `menu_type`
--

INSERT INTO `menu_type` (`id`, `title`, `alias`, `path`, `note`, `created_at`, `updated_at`, `created_by`, `updated_by`, `lock`) VALUES
(1, 'Дополнительное меню', 'dopolnitelnoe-menu', NULL, '', 1476543986, 1481654728, 1, 1, 2),
(2, 'Основное меню', 'osnovnoe-menu', NULL, '', 1481654682, 1481654682, 1, 1, 1),
(3, 'Перелинковка', 'perelinkovka', NULL, 'Тут хранятся ссылки которые не выдны на сайте в меню', 1483475363, 1483475363, 1, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблиці `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `language` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `translation` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `migration`
--

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
-- Структура таблиці `modules_modules`
--

CREATE TABLE `modules_modules` (
  `module_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `class` varchar(128) NOT NULL,
  `bootstrapClass` varchar(128) NOT NULL DEFAULT '',
  `isFrontend` tinyint(1) NOT NULL,
  `controllerNamespace` varchar(500) NOT NULL DEFAULT '',
  `viewPath` varchar(500) NOT NULL DEFAULT '',
  `isAdmin` tinyint(1) NOT NULL,
  `AdminControllerNamespace` varchar(500) NOT NULL DEFAULT '',
  `AdminViewPath` varchar(500) NOT NULL DEFAULT '',
  `title` varchar(128) NOT NULL,
  `icon` varchar(32) NOT NULL,
  `settings` text NOT NULL,
  `order` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `modules_modules`
--

INSERT INTO `modules_modules` (`module_id`, `name`, `class`, `bootstrapClass`, `isFrontend`, `controllerNamespace`, `viewPath`, `isAdmin`, `AdminControllerNamespace`, `AdminViewPath`, `title`, `icon`, `settings`, `order`, `status`) VALUES
(1, 'seo', 'app\\modules\\seo\\Module', '', 0, '', '', 1, '', '', 'Seo', '', '{"title":{"type":"textInput","value":"OAKCMS"}}', 10, 1),
(2, 'content', 'app\\modules\\content\\Module', '', 1, '', '', 1, '', '', 'Content', '', '{"show_title":{"type":"checkbox","value":false},"link_titles":{"type":"checkbox","value":false},"show_intro":{"type":"checkbox","value":false},"show_category":{"type":"checkbox","value":false},"link_category":{"type":"checkbox","value":false},"show_parent_category":{"type":"checkbox","value":false},"link_parent_category":{"type":"checkbox","value":false},"show_author":{"type":"checkbox","value":false},"link_author":{"type":"checkbox","value":false},"show_create_date":{"type":"checkbox","value":false},"show_modify_date":{"type":"checkbox","value":false},"show_publish_date":{"type":"checkbox","value":false},"show_hits":{"type":"checkbox","value":false},"categoryThumb":{"type":"checkbox","value":false},"category_items_order":{"type":"select","value":0,"items":{"rdate":"Latest First","date":"Latest Last","rmodified":"Modified First","modified":"Modified Last","alpha":"Alphabetical","ralpha":"Alphabetical Reversed","hits":"Most Hits","rhits":"Least Hits","random":"Random"}}}', 2, 1),
(3, 'user', 'app\\modules\\user\\Module', '', 1, '', '', 1, '', '', 'User manager', 'fa fa-user', '{"title":{"type":"textInput","value":"OAKCMS"}}', 6, 1),
(4, 'language', 'app\\modules\\language\\Module', '', 0, '', '', 1, '', '', 'Language', 'fa fa-flag', '[]', 7, 1),
(5, 'text', 'app\\modules\\text\\Module', '', 0, '', '', 1, '', '', 'Text', '', '[]', 8, 1),
(6, 'shop', 'app\\modules\\shop\\Module', '', 1, '', '', 1, '', '', 'Shop', '', '[]', 3, 1),
(7, 'menu', 'app\\modules\\menu\\Module', 'app\\modules\\menu\\Bootstrap', 1, '', '', 1, '', '', 'Menu', '', '[]', 1, 1),
(8, 'widgets', 'app\\modules\\widgets\\Module', '', 1, '', '', 1, '', '', 'Widgets', '', '{"googlemapseapikey":{"value":"","type":"textInput"},"disable_frontend_style":{"value":"0","type":"checkbox"}}', 9, 1),
(9, 'system', 'app\\modules\\system\\Module', '', 1, '', '', 1, '', '', 'System', '', '{"BackCallEmail":{"type":"textInput","value":"script@email.ua"},"BackCallSubject":{"type":"textInput","value":"\\u041d\\u043e\\u0432\\u0430\\u044f \\u0437\\u0430\\u044f\\u0432\\u043a\\u0430 \\u0437 \\u0441\\u0430\\u0439\\u0442\\u0430 falconcity.kz"},"BackCallSuccessText":{"type":"textInput","value":"\\u0412\\u0430\\u0448 \\u0437\\u0430\\u043f\\u0440\\u043e\\u0441 \\u043f\\u043e\\u043b\\u0443\\u0447\\u0435\\u043d!<br>\\u0412 \\u0431\\u043b\\u0438\\u0436\\u0430\\u0439\\u0448\\u0435\\u0435 \\u0432\\u0440\\u0435\\u043c\\u044f \\u043d\\u0430\\u0448 \\u043c\\u0435\\u043d\\u0435\\u0434\\u0436\\u0435\\u0440 \\u0441\\u0432\\u044f\\u0436\\u0438\\u0442\\u0441\\u044f \\u0441 \\u0412\\u0430\\u043c\\u0438!"},"SocialInstagramLink":{"type":"textInput","value":"#"},"SocialTwitterLink":{"type":"textInput","value":"#"},"SocialFacebookLink":{"type":"textInput","value":"#"},"FrequentlyAskedQuestionsLink":{"type":"textInput","value":"#"}}', 0, 1),
(10, 'cart', 'app\\modules\\cart\\Module', 'app\\modules\\cart\\Bootstrap', 1, '', '', 1, '', '', 'Cart', '', '[]', 11, 1),
(11, 'gallery', 'app\\modules\\gallery\\Module', '', 1, '', '', 1, '', '', 'Gallery', '', '[]', 12, 1),
(14, 'relations', 'app\\modules\\relations\\Module', '', 1, '', '', 1, '', '', 'Relations', '', '[]', 14, 1),
(13, 'packagist', 'app\\modules\\packagist\\Module', '', 0, '', '', 1, '', '', 'Packagist', '', '{"git_hub_username":{"type":"textInput","value":""},"git_hub_password":{"type":"textInput","value":""}}', 13, 0),
(15, 'field', 'app\\modules\\field\\Module', '', 1, '', '', 1, '', '', 'Fields', '', '[]', 4, 1),
(16, 'filter', 'app\\modules\\filter\\Module', '', 1, '', '', 1, '', '', '1', '', '[]', 5, 1),
(17, 'importmebel', 'app\\modules\\importmebel\\Module', '', 0, '', '', 1, '', '', 'importmebel', '', '[]', 15, 0),
(18, 'akeebabackup', 'app\\modules\\akeebabackup\\Module', '', 0, '', '', 1, '', '', 'Akeeba Backup', '', '[]', 16, 0);

-- --------------------------------------------------------

--
-- Структура таблиці `seo_items`
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
-- Дамп даних таблиці `seo_items`
--

INSERT INTO `seo_items` (`id`, `link`, `title`, `keywords`, `description`, `canonical`, `status`) VALUES
(1, '/', 'Home', '1', '', '', 1);

-- --------------------------------------------------------

--
-- Структура таблиці `shop_category`
--

CREATE TABLE `shop_category` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `title_h1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `code` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `text` text COLLATE utf8_unicode_ci,
  `image` text COLLATE utf8_unicode_ci,
  `sort` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `shop_incoming`
--

CREATE TABLE `shop_incoming` (
  `id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `content` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `shop_outcoming`
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
-- Структура таблиці `shop_price`
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
-- Структура таблиці `shop_price_type`
--

CREATE TABLE `shop_price_type` (
  `id` int(11) NOT NULL,
  `name` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `sort` int(11) DEFAULT NULL,
  `condition` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `shop_price_type`
--

INSERT INTO `shop_price_type` (`id`, `name`, `sort`, `condition`) VALUES
(1, 'Основная цена', NULL, NULL),
(2, 'Акционная цена', NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблиці `shop_producer`
--

CREATE TABLE `shop_producer` (
  `id` int(11) NOT NULL,
  `code` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image` text COLLATE utf8_unicode_ci,
  `text` text COLLATE utf8_unicode_ci,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `shop_producer`
--

INSERT INTO `shop_producer` (`id`, `code`, `name`, `image`, `text`, `slug`) VALUES
(1, NULL, 'Techlink', NULL, '', 'techlink'),
(2, NULL, 'Szynaka Meble', NULL, '', 'szynaka-meble');

-- --------------------------------------------------------

--
-- Структура таблиці `shop_product`
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

-- --------------------------------------------------------

--
-- Структура таблиці `shop_product_modification`
--

CREATE TABLE `shop_product_modification` (
  `id` int(11) NOT NULL,
  `price` float NOT NULL,
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
-- Структура таблиці `shop_product_to_category`
--

CREATE TABLE `shop_product_to_category` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `shop_stock`
--

CREATE TABLE `shop_stock` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `shop_stock`
--

INSERT INTO `shop_stock` (`id`, `name`, `address`, `text`) VALUES
(1, '13', '123', '<p>123312</p>\r\n');

-- --------------------------------------------------------

--
-- Структура таблиці `shop_stock_to_product`
--

CREATE TABLE `shop_stock_to_product` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `shop_stock_to_user`
--

CREATE TABLE `shop_stock_to_user` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `solo_ak_params`
--

CREATE TABLE `solo_ak_params` (
  `tag` varchar(255) NOT NULL,
  `data` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `solo_ak_params`
--

INSERT INTO `solo_ak_params` (`tag`, `data`) VALUES
('update_version', '2.1.0.b1');

-- --------------------------------------------------------

--
-- Структура таблиці `solo_ak_profiles`
--

CREATE TABLE `solo_ak_profiles` (
  `id` int(10) UNSIGNED NOT NULL,
  `description` varchar(255) NOT NULL,
  `configuration` longtext,
  `filters` longtext,
  `quickicon` tinyint(3) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `solo_ak_profiles`
--

INSERT INTO `solo_ak_profiles` (`id`, `description`, `configuration`, `filters`, `quickicon`) VALUES
(1, 'Default Backup Profile', '[global]\n[akeeba]\ntuning.min_exec_time="2000"\ntuning.max_exec_time="14"\ntuning.run_time_bias="75"\ntuning.nobreak.beforelargefile="0"\ntuning.nobreak.afterlargefile="0"\ntuning.nobreak.proactive="0"\ntuning.nobreak.domains="0"\ntuning.nobreak.finalization="0"\ntuning.settimelimit="0"\nadvanced.autoresume="1"\nadvanced.autoresume_timeout="10"\nadvanced.autoresume_maxretries="3"\nadvanced.dump_engine="native"\nadvanced.scan_engine="large"\nadvanced.archiver_engine="jpa"\nadvanced.postproc_engine="none"\nadvanced.embedded_installer="angie-wordpress"\nadvanced.uploadkickstart="0"\nadvanced.integritycheck="0"\nadvanced.virtual_folder="external_files"\nquota.remote="0"\nquota.maxage.enable="0"\nquota.maxage.maxdays="31"\nquota.maxage.keepday="1"\nquota.obsolete_quota="50"\nquota.enable_size_quota="0"\nquota.size_quota="15728640"\nquota.enable_count_quota="1"\nquota.count_quota="3"\nbasic.output_directory="/home/volodumur/PhpstormProjects/oakcms/application/modules/akeebabackup/app/backups"\nbasic.log_level="4"\nbasic.archive_name="site-[HOST]-[DATE]-[TIME]"\nbasic.backup_type="full"\nbasic.clientsidewait="0"\nbasic.useiframe="0"\ncore.usedbstorage="0"\nplatform.scripttype="wordpress"\nflag.confwiz="1"\n[engine]\ninstaller.angie.key=""\narchiver.common.dereference_symlinks="0"\narchiver.common.part_size="0"\narchiver.common.chunk_size="1048576"\narchiver.common.big_file_threshold="1048576"\narchiver.zip.cd_glue_chunk_size="1048576"\ndump.divider.common="0"\ndump.divider.mysql="0"\ndump.divider.reverse="0"\ndump.common.blankoutpass="0"\ndump.common.extended_inserts="1"\ndump.common.packet_size="131072"\ndump.common.splitsize="524288"\ndump.common.batchsize="1000"\ndump.native.advanced_entitites="0"\ndump.native.nodependencies="0"\ndump.native.nobtree="1"\nscan.smart.large_dir_threshold="100"\nscan.common.largefile="10485760"\nscan.large.dir_threshold="100"\nscan.large.file_threshold="50"\n[core]\nfilters.errorlogs.enabled="1"\nfilters.hoststats.enabled="1"\nfilters.dateconditional.enabled="0"\nfilters.dateconditional.start="1981-02-20 12:15 GMT+2"\n', '', 1);

-- --------------------------------------------------------

--
-- Структура таблиці `solo_ak_stats`
--

CREATE TABLE `solo_ak_stats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) NOT NULL,
  `comment` longtext,
  `backupstart` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `backupend` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('run','fail','complete') NOT NULL DEFAULT 'run',
  `origin` varchar(30) NOT NULL DEFAULT 'backend',
  `type` varchar(30) NOT NULL DEFAULT 'full',
  `profile_id` bigint(20) NOT NULL DEFAULT '1',
  `archivename` longtext,
  `absolute_path` longtext,
  `multipart` int(11) NOT NULL DEFAULT '0',
  `tag` varchar(255) DEFAULT NULL,
  `backupid` varchar(255) DEFAULT NULL,
  `filesexist` tinyint(3) NOT NULL DEFAULT '1',
  `remote_filename` varchar(1000) DEFAULT NULL,
  `total_size` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `solo_ak_storage`
--

CREATE TABLE `solo_ak_storage` (
  `tag` varchar(255) NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `data` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `solo_ak_storage`
--

INSERT INTO `solo_ak_storage` (`tag`, `lastupdate`, `data`) VALUES
('liveupdate', '2017-02-28 09:29:00', '{"stuck":0,"software":"Akeeba Backup for WordPress","version":"2.1.0.b1","link":"http:\\/\\/cdn.akeebabackup.com\\/downloads\\/backupwp\\/2.1.0.b1\\/akeebabackupwp-2.1.0.b1-core.zip","date":"2017-02-22","releasenotes":"<h3>What\'s new<\\/h3><p>    <strong>Rewritten Javascript<\\/strong>. We have rewritten all the client-side code to use native Javascript instead    of jQuery. One less hard dependency means easier code to maintain. As it\'s been the case since 2012, our software    only supports Internet Explorer 9 or better and all modern browsers (Chrome, Firefox, Opera, Safari, ...).<\\/p><p>    <strong>More secure JPS 2.0 backup format<\\/strong>. We have upgraded the security of the JPS archive format by using    PBKDF2 to create an encryption key from your password instead of the old method of password self-encryption. This    greatly increases the security of shorter passwords. Moreover, passwords over 16 bytes are now fully taken    into account (previous versions internally truncated passwords at 16 bytes). You will need Kickstart 5.3.0 or later    to extract these archives. A new version of Akeeba eXtract Wizard will also be released soon as the current version    is not compatible with the new JPS archives.<\\/p><p>    <strong>Supporting additional S3 regions<\\/strong>. Canada and London regions are now supported.<\\/p><p>    <strong>Better support for sites using different database technologies<\\/strong>. If you have a site with two or more    databases using different database server technologies (MySQL, PostgreSQL, SQL Server) you will now be able to back    them up even if you choose the Native database dump engine.<\\/p><h3>Updating Akeeba Backup for WordPress to version 2.1.x<\\/h3><p>    Due to changes in the packaging format and \\/ or issues in the updater, you cannot update automatically from Akeeba    Backup for WordPress versions 1.0 through 1.8.2 (inclusive) to version 1.9.0 and beyond. You will have to do that    manually.<\\/p><p>    <strong>Heads up!<\\/strong> You must NOT uninstall or deactivate the plugin before the update. Doing so may result in    loss of your backup settings and \\/ or your backup archives. Instead, here\'s what to do:<\\/p><ul>    <li>        Download the ZIP file for Akeeba Backup for WordPress 2.1 and extract it locally. You will see an extracted        folder named <code>akeebabackupwp<\\/code>.    <\\/li>    <li>        Upload the files from the extracted <code>akeebabackupwp<\\/code> folder into your site\'s        <code>wp-content\\/plugins\\/akeebabackupwp<\\/code>        folder, overwriting your existing files, using FTP or SFTP.        Please note that the name of the folder on your site <em>may<\\/em> be different than <code>akeebabackupwp<\\/code>,        e.g. <code>akeebabackupwpcore<\\/code>, <code>akeebabackupwp (1)<\\/code> or something similar. It depends on how        you installed the plugin.    <\\/li>    <li>        Log in to WordPress\' wp-admin and access Akeeba Backup for WordPress to automatically complete the update        process. There is no message when the process completes. You just see the main page of Akeeba Backup for        WordPress (this means the update succeeded).    <\\/li><\\/ul><p>    You will only need to do this once, upgrading to version 1.9 or later for the first time.<\\/p><h3>PHP 5.3.3 or later or PHP 7 is required<\\/h3><p>    Akeeba Solo and Akeeba Backup for WordPress 1.9 are compatible with PHP 5.3.04 and later versions, including 5.4,    5.5, 5.6 and the newest versions of PHP, 7.0 and 7.1.    We\'d like to remind you that most third party software which can be backed up by our software do not support    PHP 7 yet. As a result we can\'t guarantee a trouble-free restoration or tha the restored site will work on    PHP 7 as this depends entirely on the software powering your site.<\\/p><h3>Changelog<\\/h3><h4>Bug fixes<\\/h4><ul>\\t<li>[MEDIUM] Infinite recursion if the current profile doesn\'t exist<\\/li>\\t<li>[MEDIUM] Views defined against fully qualified tables (i.e. including the database name) could not be restored on a database with a different name<\\/li><\\/ul><h4>New features<\\/h4><ul>\\t<li>Add support for Canada (Montreal) Amazon S3 region<\\/li>\\t<li>Add support for EU (London) Amazon S3 region<\\/li>\\t<li>Hide action icons based on the user\'s permissions<\\/li>\\t<li>Support for JPS format v2.0 with improved password security<\\/li><\\/ul><h4>Miscellaneous changes<\\/h4><ul>\\t<li>Now using the Reverse Engineering database dump engine when a Native database dump engine is not available (PostgreSQL, Microsoft SQL Server, SQLite)<\\/li>\\t<li>Permissions are now more reasonably assigned to different views<\\/li><\\/ul>","infourl":"https:\\/\\/www.akeebabackup.com\\/download\\/backupwp\\/2-1-0-b1.html","md5":"86746599792e05f240b1d4c46cd7d2a4","sha1":"7bd2089f3ff371226c2030368d46912094b10901","platforms":"php\\/5.3,php\\/5.4,php\\/5.5,php\\/5.6,php\\/7.0,php\\/7.1,wordpress\\/3.8+"}'),
('liveupdate_lastcheck', '2017-02-28 09:28:59', '1488274139');

-- --------------------------------------------------------

--
-- Структура таблиці `solo_ak_users`
--

CREATE TABLE `solo_ak_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `parameters` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `system_log`
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
-- Структура таблиці `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `param_name` varchar(100) NOT NULL,
  `param_value` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `system_settings`
--

INSERT INTO `system_settings` (`id`, `param_name`, `param_value`, `type`) VALUES
(1, 'indexing', '1', 'checkbox'),
(2, 'siteName', 'OakCMS', 'textInput'),
(3, 'googleAuthenticator', '0', 'checkbox'),
(4, 'language', 'ru-RU', 'language'),
(5, 'themeFrontend', 'base', 'getThemeFrontend'),
(6, 'themeBackend', 'base', 'getThemeBackend');

-- --------------------------------------------------------

--
-- Структура таблиці `texts`
--

CREATE TABLE `texts` (
  `id` int(11) NOT NULL,
  `layout` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `where_to_place` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `links` text COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `order` int(11) NOT NULL,
  `published_at` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `texts`
--

INSERT INTO `texts` (`id`, `layout`, `slug`, `where_to_place`, `links`, `status`, `order`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 'default', 'position_top_1', '0', '2', 0, 1, 0, 0, 1486569967);

-- --------------------------------------------------------

--
-- Структура таблиці `texts_lang`
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

--
-- Дамп даних таблиці `texts_lang`
--

INSERT INTO `texts_lang` (`id`, `texts_id`, `title`, `subtitle`, `text`, `settings`, `language`) VALUES
(1, 1, 'Title', 'SubTitle', '<p>1</p>', '{"cssClass":{"value":"clear","type":"textInput"},"id":{"value":"id","type":"textInput"},"hideTitle":{"value":"","type":"checkbox"}}', 'ru-ru');

-- --------------------------------------------------------

--
-- Структура таблиці `user`
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
  `googleAuthenticatorSecret` varchar(255) NOT NULL DEFAULT '',
  `status` smallint(6) NOT NULL DEFAULT '0',
  `role` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `user`
--

INSERT INTO `user` (`id`, `created_at`, `updated_at`, `username`, `auth_key`, `email_confirm_token`, `password_hash`, `password_reset_token`, `email`, `googleAuthenticator`, `googleAuthenticatorSecret`, `status`, `role`) VALUES
(1, 1459981535, 1488464536, 'admin', '-ZSUE3afc2rCB0UXa08ymzhpWsPSHEfk', 'Ht3SjXJ0MUJOXt4P0gwsltk5B0eKJFOH', '$2y$13$UZ9rZpxE0F6lhN5/PYaC0.0wwOI1hra62hNQ7JGGaAzRjj3eH5An2', NULL, 'legionerblack@yandex.ru', 0, '', 1, 'administrator');

-- --------------------------------------------------------

--
-- Структура таблиці `user_profile`
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
-- Дамп даних таблиці `user_profile`
--

INSERT INTO `user_profile` (`user_id`, `firstname`, `middlename`, `lastname`, `avatar`, `locale`, `gender`) VALUES
(1, 'Володимир', 'Ігорович', 'Гривінський', '5740816e01325.jpg', 'ru-RU', 2);

-- --------------------------------------------------------

--
-- Структура таблиці `widgetkit`
--

CREATE TABLE `widgetkit` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `data` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `widgetkit`
--

INSERT INTO `widgetkit` (`id`, `name`, `type`, `data`) VALUES
(1, 'Проверка инстаграм', 'instagram', '{"_widget":{"name":"slideshow","data":{"nav":"dotnav","nav_overlay":true,"nav_align":"center","thumbnail_width":"70","thumbnail_height":"70","thumbnail_alt":false,"slidenav":"default","nav_contrast":true,"animation":"fade","slices":"15","duration":"500","autoplay":false,"interval":"3000","autoplay_pause":true,"kenburns":false,"kenburns_animation":"","kenburns_duration":"15","fullscreen":false,"min_height":"300","media":true,"image_width":"auto","image_height":"auto","overlay":"none","overlay_animation":"fade","overlay_background":true,"title":true,"content":true,"title_size":"h3","content_size":"","link":true,"link_style":"button","link_text":"Read more","badge":true,"badge_style":"badge","link_target":false,"class":"","link_media":false}},"limit":10,"title":"fullname","username":"vizhital","prepared":"[{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u041a\\\\u0443\\\\u0440\\\\u0442\\\\u043a\\\\u0430 \\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0430\\\\u044f\\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 5750 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0430\\\\u044f\\\\u043a\\\\u0443\\\\u0440\\\\u0442\\\\u043a\\\\u0430\\\\\\">#\\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0430\\\\u044f\\\\u043a\\\\u0443\\\\u0440\\\\u0442\\\\u043a\\\\u0430<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0443\\\\u044e\\\\u043a\\\\u0443\\\\u0440\\\\u0442\\\\u043a\\\\u0443\\\\\\">#\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0443\\\\u044e\\\\u043a\\\\u0443\\\\u0440\\\\u0442\\\\u043a\\\\u0443<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0432\\\\u0435\\\\u0440\\\\u0445\\\\u043d\\\\u044f\\\\u044f\\\\u043e\\\\u0434\\\\u0435\\\\u0436\\\\u0434\\\\u0430\\\\u043a\\\\u0438\\\\u0435\\\\u0432\\\\\\">#\\\\u0432\\\\u0435\\\\u0440\\\\u0445\\\\u043d\\\\u044f\\\\u044f\\\\u043e\\\\u0434\\\\u0435\\\\u0436\\\\u0434\\\\u0430\\\\u043a\\\\u0438\\\\u0435\\\\u0432<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/style\\\\\\">#style<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:44:58 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB17O0GQdDg\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/12748247_1693409820948269_231409638_n.jpg?ig_cache_key=MTE4NjExNDU3OTYzODM3NDYyNA%3D%3D.2\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}},{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u041f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e \\\\u0438\\\\u0437 \\\\u0432\\\\u0430\\\\u0440\\\\u0435\\\\u043d\\\\u043e\\\\u0439 \\\\u0448\\\\u0435\\\\u0440\\\\u0441\\\\u0442\\\\u0438\\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 2350 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e\\\\\\">#\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438\\\\\\">#\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0448\\\\u0435\\\\u0440\\\\u0441\\\\u0442\\\\u044f\\\\u043d\\\\u043e\\\\u0435\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e\\\\\\">#\\\\u0448\\\\u0435\\\\u0440\\\\u0441\\\\u0442\\\\u044f\\\\u043d\\\\u043e\\\\u0435\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/oversize\\\\\\">#oversize<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/stylish\\\\\\">#stylish<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:37:14 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB16WLoQdCl\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/12717112_1505445679765394_2100472763_n.jpg?ig_cache_key=MTE4NjExMDY4Nzg5NDY4NzkwOQ%3D%3D.2\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}},{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u041f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e \\\\u0448\\\\u0435\\\\u0440\\\\u0441\\\\u0442\\\\u044f\\\\u043d\\\\u043e\\\\u0435\\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 2880 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438\\\\\\">#\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0448\\\\u0435\\\\u0440\\\\u0441\\\\u0442\\\\u044f\\\\u043d\\\\u043e\\\\u0435\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e\\\\\\">#\\\\u0448\\\\u0435\\\\u0440\\\\u0441\\\\u0442\\\\u044f\\\\u043d\\\\u043e\\\\u0435\\\\u043f\\\\u0430\\\\u043b\\\\u044c\\\\u0442\\\\u043e<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/oversize\\\\\\">#oversize<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/cute\\\\\\">#cute<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:34:01 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB15-unwdB6\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/12717097_193531387674982_1018250677_n.jpg?ig_cache_key=MTE4NjEwOTA3NjE5OTgyMTQzNA%3D%3D.2\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}},{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u0414\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430 \\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0430\\\\u044f\\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 10500 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430\\\\\\">#\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432\\\\\\">#\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430\\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0430\\\\u044f\\\\\\">#\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430\\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0430\\\\u044f<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/style\\\\\\">#style<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/look\\\\\\">#look<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:30:21 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB15jxjwdBa\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/12747667_1038265569578441_215959711_n.jpg?ig_cache_key=MTE4NjEwNzIyMzkyODA2NjEzOA%3D%3D.2\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}},{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u0414\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430 \\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0430\\\\u044f \\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 12500 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430\\\\u043d\\\\u0430\\\\u0442\\\\u0443\\\\u0440\\\\u0430\\\\u043b\\\\u044c\\\\u043d\\\\u0430\\\\u044f\\\\\\">#\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430\\\\u043d\\\\u0430\\\\u0442\\\\u0443\\\\u0440\\\\u0430\\\\u043b\\\\u044c\\\\u043d\\\\u0430\\\\u044f<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432\\\\\\">#\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/cute\\\\\\">#cute<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/stylish\\\\\\">#stylish<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:26:48 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB15J2fwdA8\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/12599056_1672280749694684_1324694833_n.jpg?ig_cache_key=MTE4NjEwNTQ0MjUyMzI3MTIyOA%3D%3D.2\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}},{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u0414\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430 \\\\u043a\\\\u043e\\\\u0436\\\\u0430\\\\u043d\\\\u0430\\\\u044f\\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 9800 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430\\\\\\">#\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432\\\\\\">#\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430\\\\u043d\\\\u0430\\\\u0442\\\\u0443\\\\u0440\\\\u0430\\\\u043b\\\\u044c\\\\u043d\\\\u0430\\\\u044f\\\\\\">#\\\\u0434\\\\u0443\\\\u0431\\\\u043b\\\\u0435\\\\u043d\\\\u043a\\\\u0430\\\\u043d\\\\u0430\\\\u0442\\\\u0443\\\\u0440\\\\u0430\\\\u043b\\\\u044c\\\\u043d\\\\u0430\\\\u044f<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/style\\\\\\">#style<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:22:24 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB14pkXQdAR\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/10632360_1507229832918278_654945332_n.jpg?ig_cache_key=MTE4NjEwMzIyNDAzMDA1NjQ2NQ%3D%3D.2\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}},{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u0428\\\\u0443\\\\u0431\\\\u0430 \\\\u043d\\\\u043e\\\\u0440\\\\u043a\\\\u043e\\\\u0432\\\\u0430\\\\u044f \\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 39500 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438\\\\\\">#\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0448\\\\u0443\\\\u0431\\\\u0443\\\\\\">#\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0448\\\\u0443\\\\u0431\\\\u0443<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u043d\\\\u043e\\\\u0440\\\\u043a\\\\u0430\\\\\\">#\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u043d\\\\u043e\\\\u0440\\\\u043a\\\\u0430<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/style\\\\\\">#style<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/look\\\\\\">#look<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:17:34 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB14GLMwdPa\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/12728551_239872913012570_976925889_n.jpg?ig_cache_key=MTE4NjEwMDc5MTgyODY2NTMwNg%3D%3D.2\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}},{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u0428\\\\u0443\\\\u0431\\\\u0430 \\\\u043d\\\\u043e\\\\u0440\\\\u043a\\\\u043e\\\\u0432\\\\u0430\\\\u044f\\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 27900 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u043d\\\\u043e\\\\u0440\\\\u043a\\\\u0430\\\\\\">#\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u043d\\\\u043e\\\\u0440\\\\u043a\\\\u0430<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0448\\\\u0443\\\\u0431\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432\\\\\\">#\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0448\\\\u0443\\\\u0431\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438\\\\\\">#\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/style\\\\\\">#style<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/cute\\\\\\">#cute<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:12:58 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB13kjjQdO0\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/12748207_1060157127340088_389350033_n.jpg?ig_cache_key=MTE4NjA5ODQ4MTUxMzc0NzM4MA%3D%3D.2.l\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}},{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u0428\\\\u0443\\\\u0431\\\\u0430 \\\\u043a\\\\u043e\\\\u0436\\\\u0430+\\\\u043c\\\\u0435\\\\u0445 \\\\u0447\\\\u0435\\\\u0440\\\\u043d\\\\u043e\\\\u0431\\\\u0443\\\\u0440\\\\u043a\\\\u0438\\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 9800 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0448\\\\u0443\\\\u0431\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432\\\\\\">#\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0448\\\\u0443\\\\u0431\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438\\\\\\">#\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u0432\\\\u043d\\\\u0430\\\\u043b\\\\u0438\\\\u0447\\\\u0438\\\\u0438<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/beautiful\\\\\\">#beautiful<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/stylish\\\\\\">#stylish<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:05:26 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB12tXZQdN6\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/1389450_952919778123648_1346572496_n.jpg?ig_cache_key=MTE4NjA5NDY4ODg4OTg1Mjc5NA%3D%3D.2\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}},{\\"title\\":\\"\\\\u0412\\\\u0438\\\\u0436\\\\u0438\\\\u0442\\\\u0430\\\\u043b\\\\u044c\\",\\"content\\":\\"\\\\u0428\\\\u0443\\\\u0431\\\\u0430 \\\\u043c\\\\u0443\\\\u0442\\\\u043e\\\\u043d\\\\u043e\\\\u0432\\\\u0430\\\\u044f\\\\n\\\\u0426\\\\u0435\\\\u043d\\\\u0430: 12700 \\\\u0433\\\\u0440\\\\u043d. \\\\n <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u043a\\\\u0438\\\\u0435\\\\u0432\\\\\\">#\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u043a\\\\u0438\\\\u0435\\\\u0432<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u043c\\\\u0443\\\\u0442\\\\u043e\\\\u043d\\\\\\">#\\\\u0448\\\\u0443\\\\u0431\\\\u0430\\\\u043c\\\\u0443\\\\u0442\\\\u043e\\\\u043d<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0448\\\\u0443\\\\u0431\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432\\\\\\">#\\\\u043a\\\\u0443\\\\u043f\\\\u0438\\\\u0442\\\\u044c\\\\u0448\\\\u0443\\\\u0431\\\\u0443\\\\u043a\\\\u0438\\\\u0435\\\\u0432<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/fashion\\\\\\">#fashion<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/look\\\\\\">#look<\\\\\\/a>  <a href=\\\\\\"https:\\\\\\/\\\\\\/instagram.com\\\\\\/explore\\\\\\/tags\\\\\\/style\\\\\\">#style<\\\\\\/a>\\",\\"date\\":\\"16-02-2016 10:00:01 +0100\\",\\"link\\":\\"https:\\\\\\/\\\\\\/www.instagram.com\\\\\\/p\\\\\\/BB12FmdwdNB\\\\\\/\\",\\"location\\":null,\\"media\\":\\"https:\\\\\\/\\\\\\/scontent-waw1-1.cdninstagram.com\\\\\\/t51.2885-15\\\\\\/s640x640\\\\\\/sh0.08\\\\\\/e35\\\\\\/12724749_458481994348884_184489370_n.jpg?ig_cache_key=MTE4NjA5MTk1NjI5MjQwODEyOQ%3D%3D.2\\",\\"options\\":{\\"media\\":{\\"width\\":640,\\"height\\":640}}}]","hash":"ad4b720dd7cd8aca7e066ec994a863fb","hashed":1476912118}'),
(2, '1234567', 'custom', '{"_widget":{"name":"slideshow","data":{"nav":"dotnav","nav_overlay":true,"nav_align":"center","thumbnail_width":"70","thumbnail_height":"70","thumbnail_alt":false,"slidenav":"default","nav_contrast":true,"animation":"fade","slices":"15","duration":"500","autoplay":false,"interval":"3000","autoplay_pause":true,"kenburns":false,"kenburns_animation":"","kenburns_duration":"15","fullscreen":false,"min_height":"300","media":true,"image_width":"auto","image_height":"auto","overlay":"none","overlay_animation":"fade","overlay_background":true,"title":true,"content":true,"title_size":"h3","content_size":"","link":true,"link_style":"button","link_text":"Read more","badge":true,"badge_style":"badge","link_target":false,"class":""}},"items":[{"media":"uploads\\/user\\/66.jpg","options":{"media":{"width":500,"height":334,"type":"image"}},"title":"Undefined"},{"media":"uploads\\/user\\/_1\\/5740816e01325.jpg","options":{"media":{"width":342,"height":604,"type":"image"}},"title":"Undefined"}],"random":false,"_fields":[]}');

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD PRIMARY KEY (`item_name`,`user_id`);

--
-- Індекси таблиці `auth_item`
--
ALTER TABLE `auth_item`
  ADD PRIMARY KEY (`name`),
  ADD KEY `rule_name` (`rule_name`),
  ADD KEY `idx-auth_item-type` (`type`);

--
-- Індекси таблиці `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD PRIMARY KEY (`parent`,`child`),
  ADD KEY `child` (`child`);

--
-- Індекси таблиці `auth_rule`
--
ALTER TABLE `auth_rule`
  ADD PRIMARY KEY (`name`);

--
-- Індекси таблиці `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Індекси таблиці `cart_element`
--
ALTER TABLE `cart_element`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`);

--
-- Індекси таблиці `content_articles`
--
ALTER TABLE `content_articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `create_user_id` (`create_user_id`),
  ADD KEY `update_user_id` (`update_user_id`),
  ADD KEY `status` (`status`),
  ADD KEY `access_type` (`access_type`),
  ADD KEY `comment_status` (`comment_status`),
  ADD KEY `publish_date` (`updated_at`),
  ADD KEY `category_id` (`category_id`);

--
-- Індекси таблиці `content_articles_lang`
--
ALTER TABLE `content_articles_lang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slug` (`slug`);

--
-- Індекси таблиці `content_articles_medias`
--
ALTER TABLE `content_articles_medias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `i_virtuemart_product_id` (`content_articles_id`,`media_id`),
  ADD KEY `i_ordering` (`ordering`);

--
-- Індекси таблиці `content_category`
--
ALTER TABLE `content_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_tree_NK1` (`tree`),
  ADD KEY `tbl_tree_NK2` (`lft`),
  ADD KEY `tbl_tree_NK3` (`rgt`),
  ADD KEY `tbl_tree_NK4` (`depth`),
  ADD KEY `tbl_tree_NK5` (`active`);

--
-- Індекси таблиці `content_category_lang`
--
ALTER TABLE `content_category_lang`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `content_pages`
--
ALTER TABLE `content_pages`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `content_pages_lang`
--
ALTER TABLE `content_pages_lang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_pages_id` (`content_pages_id`);

--
-- Індекси таблиці `content_tags`
--
ALTER TABLE `content_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- Індекси таблиці `db_state`
--
ALTER TABLE `db_state`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `field`
--
ALTER TABLE `field`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Індекси таблиці `field_category`
--
ALTER TABLE `field_category`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `field_value`
--
ALTER TABLE `field_value`
  ADD PRIMARY KEY (`id`),
  ADD KEY `field_id` (`field_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Індекси таблиці `field_variant`
--
ALTER TABLE `field_variant`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_field` (`field_id`);

--
-- Індекси таблиці `filter`
--
ALTER TABLE `filter`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `filter_relation_value`
--
ALTER TABLE `filter_relation_value`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `filter_value`
--
ALTER TABLE `filter_value`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `variant_item` (`variant_id`,`item_id`);

--
-- Індекси таблиці `filter_variant`
--
ALTER TABLE `filter_variant`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_filter` (`filter_id`);

--
-- Індекси таблиці `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`language_id`),
  ADD KEY `url` (`url`);

--
-- Індекси таблиці `language_source`
--
ALTER TABLE `language_source`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `language_translate`
--
ALTER TABLE `language_translate`
  ADD PRIMARY KEY (`id`,`language`);

--
-- Індекси таблиці `medias`
--
ALTER TABLE `medias`
  ADD PRIMARY KEY (`media_id`),
  ADD KEY `i_published` (`status`);

--
-- Індекси таблиці `menu_item`
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
-- Індекси таблиці `menu_type`
--
ALTER TABLE `menu_type`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`,`language`);

--
-- Індекси таблиці `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Індекси таблиці `modules_modules`
--
ALTER TABLE `modules_modules`
  ADD PRIMARY KEY (`module_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Індекси таблиці `seo_items`
--
ALTER TABLE `seo_items`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `shop_category`
--
ALTER TABLE `shop_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`,`parent_id`);

--
-- Індекси таблиці `shop_incoming`
--
ALTER TABLE `shop_incoming`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `shop_outcoming`
--
ALTER TABLE `shop_outcoming`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `shop_price`
--
ALTER TABLE `shop_price`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_type` (`type_id`);

--
-- Індекси таблиці `shop_price_type`
--
ALTER TABLE `shop_price_type`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `shop_producer`
--
ALTER TABLE `shop_producer`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `shop_product`
--
ALTER TABLE `shop_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `producer_id` (`producer_id`);

--
-- Індекси таблиці `shop_product_modification`
--
ALTER TABLE `shop_product_modification`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `shop_product_to_category`
--
ALTER TABLE `shop_product_to_category`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `shop_stock`
--
ALTER TABLE `shop_stock`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `shop_stock_to_product`
--
ALTER TABLE `shop_stock_to_product`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `shop_stock_to_user`
--
ALTER TABLE `shop_stock_to_user`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `solo_ak_params`
--
ALTER TABLE `solo_ak_params`
  ADD PRIMARY KEY (`tag`);

--
-- Індекси таблиці `solo_ak_profiles`
--
ALTER TABLE `solo_ak_profiles`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `solo_ak_stats`
--
ALTER TABLE `solo_ak_stats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fullstatus` (`filesexist`,`status`),
  ADD KEY `idx_stale` (`status`,`origin`);

--
-- Індекси таблиці `solo_ak_storage`
--
ALTER TABLE `solo_ak_storage`
  ADD PRIMARY KEY (`tag`);

--
-- Індекси таблиці `solo_ak_users`
--
ALTER TABLE `solo_ak_users`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `system_log`
--
ALTER TABLE `system_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_log_level` (`level`),
  ADD KEY `idx_log_category` (`category`);

--
-- Індекси таблиці `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ux_yupe_yupe_settings_module_id_param_name_user_id` (`param_name`),
  ADD KEY `ix_yupe_yupe_settings_param_name` (`param_name`);

--
-- Індекси таблиці `texts`
--
ALTER TABLE `texts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slug` (`slug`);

--
-- Індекси таблиці `texts_lang`
--
ALTER TABLE `texts_lang`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-user-username` (`username`),
  ADD KEY `idx-user-email` (`email`),
  ADD KEY `idx-user-status` (`status`);

--
-- Індекси таблиці `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`user_id`);

--
-- Індекси таблиці `widgetkit`
--
ALTER TABLE `widgetkit`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для збережених таблиць
--

--
-- AUTO_INCREMENT для таблиці `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблиці `cart_element`
--
ALTER TABLE `cart_element`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблиці `content_articles`
--
ALTER TABLE `content_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблиці `content_articles_lang`
--
ALTER TABLE `content_articles_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблиці `content_articles_medias`
--
ALTER TABLE `content_articles_medias`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблиці `content_category`
--
ALTER TABLE `content_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique tree node identifier', AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблиці `content_category_lang`
--
ALTER TABLE `content_category_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблиці `content_pages`
--
ALTER TABLE `content_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблиці `content_pages_lang`
--
ALTER TABLE `content_pages_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблиці `content_tags`
--
ALTER TABLE `content_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблиці `db_state`
--
ALTER TABLE `db_state`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблиці `field`
--
ALTER TABLE `field`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT для таблиці `field_category`
--
ALTER TABLE `field_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблиці `field_value`
--
ALTER TABLE `field_value`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT для таблиці `field_variant`
--
ALTER TABLE `field_variant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблиці `filter`
--
ALTER TABLE `filter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT для таблиці `filter_relation_value`
--
ALTER TABLE `filter_relation_value`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
--
-- AUTO_INCREMENT для таблиці `filter_value`
--
ALTER TABLE `filter_value`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT для таблиці `filter_variant`
--
ALTER TABLE `filter_variant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
--
-- AUTO_INCREMENT для таблиці `image`
--
ALTER TABLE `image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=919;
--
-- AUTO_INCREMENT для таблиці `language_source`
--
ALTER TABLE `language_source`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
--
-- AUTO_INCREMENT для таблиці `medias`
--
ALTER TABLE `medias`
  MODIFY `media_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблиці `menu_item`
--
ALTER TABLE `menu_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT для таблиці `menu_type`
--
ALTER TABLE `menu_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблиці `modules_modules`
--
ALTER TABLE `modules_modules`
  MODIFY `module_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT для таблиці `seo_items`
--
ALTER TABLE `seo_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблиці `shop_category`
--
ALTER TABLE `shop_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1861;
--
-- AUTO_INCREMENT для таблиці `shop_incoming`
--
ALTER TABLE `shop_incoming`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблиці `shop_outcoming`
--
ALTER TABLE `shop_outcoming`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблиці `shop_price`
--
ALTER TABLE `shop_price`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблиці `shop_price_type`
--
ALTER TABLE `shop_price_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблиці `shop_producer`
--
ALTER TABLE `shop_producer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблиці `shop_product`
--
ALTER TABLE `shop_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблиці `shop_product_modification`
--
ALTER TABLE `shop_product_modification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблиці `shop_product_to_category`
--
ALTER TABLE `shop_product_to_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблиці `shop_stock`
--
ALTER TABLE `shop_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблиці `shop_stock_to_product`
--
ALTER TABLE `shop_stock_to_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблиці `shop_stock_to_user`
--
ALTER TABLE `shop_stock_to_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблиці `solo_ak_profiles`
--
ALTER TABLE `solo_ak_profiles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблиці `solo_ak_stats`
--
ALTER TABLE `solo_ak_stats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблиці `solo_ak_users`
--
ALTER TABLE `solo_ak_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблиці `system_log`
--
ALTER TABLE `system_log`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблиці `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT для таблиці `texts`
--
ALTER TABLE `texts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблиці `texts_lang`
--
ALTER TABLE `texts_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблиці `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблиці `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблиці `widgetkit`
--
ALTER TABLE `widgetkit`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Обмеження зовнішнього ключа збережених таблиць
--

--
-- Обмеження зовнішнього ключа таблиці `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `auth_item`
--
ALTER TABLE `auth_item`
  ADD CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `field`
--
ALTER TABLE `field`
  ADD CONSTRAINT `fk_field_category_id` FOREIGN KEY (`category_id`) REFERENCES `field_category` (`id`);

--
-- Обмеження зовнішнього ключа таблиці `field_value`
--
ALTER TABLE `field_value`
  ADD CONSTRAINT `fk_field_value_field_id` FOREIGN KEY (`field_id`) REFERENCES `field` (`id`);

--
-- Обмеження зовнішнього ключа таблиці `field_variant`
--
ALTER TABLE `field_variant`
  ADD CONSTRAINT `fk_field_variant_field_id` FOREIGN KEY (`field_id`) REFERENCES `field` (`id`);

--
-- Обмеження зовнішнього ключа таблиці `filter_value`
--
ALTER TABLE `filter_value`
  ADD CONSTRAINT `fk_variant` FOREIGN KEY (`variant_id`) REFERENCES `filter_variant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `filter_variant`
--
ALTER TABLE `filter_variant`
  ADD CONSTRAINT `fk_filter` FOREIGN KEY (`filter_id`) REFERENCES `filter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `shop_price`
--
ALTER TABLE `shop_price`
  ADD CONSTRAINT `fk_product` FOREIGN KEY (`product_id`) REFERENCES `shop_product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_type` FOREIGN KEY (`type_id`) REFERENCES `shop_price_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `shop_product`
--
ALTER TABLE `shop_product`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `shop_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_producer` FOREIGN KEY (`producer_id`) REFERENCES `shop_producer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
