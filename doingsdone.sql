-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июл 18 2020 г., 13:48
-- Версия сервера: 10.3.13-MariaDB-log
-- Версия PHP: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `doingsdone`
--

-- --------------------------------------------------------

--
-- Структура таблицы `project`
--

CREATE TABLE `project` (
  `proj_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `proj_name` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `project`
--

INSERT INTO `project` (`proj_id`, `user_id`, `proj_name`) VALUES
(9, 7, 'Входящие'),
(10, 7, 'Авто'),
(11, 8, 'Входящие'),
(12, 8, 'Компьютер'),
(43, 7, 'Ольга');

-- --------------------------------------------------------

--
-- Структура таблицы `task`
--

CREATE TABLE `task` (
  `task_id` int(11) UNSIGNED NOT NULL,
  `proj_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `date_task` timestamp NULL DEFAULT current_timestamp(),
  `status_task` bit(1) NOT NULL DEFAULT b'0',
  `title_task` varchar(255) NOT NULL,
  `link_file` varchar(255) DEFAULT NULL,
  `date_task_end` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `task`
--

INSERT INTO `task` (`task_id`, `proj_id`, `user_id`, `date_task`, `status_task`, `title_task`, `link_file`, `date_task_end`) VALUES
(65, 11, 8, '2020-07-08 16:43:13', b'0', 'Задача ух', NULL, '2020-07-09 21:00:00'),
(66, 12, 8, '2020-07-09 15:41:52', b'0', 'Милена', NULL, '2020-07-09 21:00:00'),
(68, 11, 8, '2020-07-09 15:52:21', b'0', 'Привет ромашки', NULL, '2020-07-08 21:00:00'),
(75, 9, 7, '2020-07-10 13:00:52', b'0', 'Сегодня', NULL, '2020-07-09 21:00:00'),
(76, 9, 7, '2020-07-10 13:01:14', b'0', 'Завтра', NULL, '2020-07-10 21:00:00'),
(77, 9, 7, '2020-07-10 13:01:35', b'0', 'Время нет', NULL, NULL),
(78, 10, 7, '2020-07-10 13:02:15', b'0', 'С файлом', 'http://1496641-doingsdone-12/uploads/1594386135_7.jpg', '2020-07-10 21:00:00'),
(79, 10, 7, '2020-07-10 13:02:45', b'0', 'Время нет 2', 'http://1496641-doingsdone-12/uploads/1594386165_7.jpg', NULL),
(80, 10, 7, '2020-07-10 16:06:39', b'0', 'Задача к черту', NULL, '2020-07-10 21:00:00'),
(82, 43, 7, '2020-07-18 05:56:23', b'0', 'Купить кофе', NULL, '2020-07-18 21:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `user_reg`
--

CREATE TABLE `user_reg` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `date_reg` timestamp NULL DEFAULT current_timestamp(),
  `email` char(64) NOT NULL,
  `us_name` varchar(128) NOT NULL,
  `pass` char(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user_reg`
--

INSERT INTO `user_reg` (`user_id`, `date_reg`, `email`, `us_name`, `pass`) VALUES
(7, '2020-07-08 16:19:10', 'denisgerc178@gmail.com', 'denisgerc178', '$2y$10$QC1rTmPwIoeUc.EJT608duPCZsyvdr6Z9jWnC/XmGblyyGdADQC22'),
(8, '2020-07-08 16:21:29', 'gerc@gerc.ru', 'gerc', '$2y$10$Jsd/M1DBHymd2JrpNluHyuShVmG2r4sVwvZvIZIMHX/JML2iSc33q'),
(18, '2020-07-15 15:30:15', '12345@12345.ru', '12345', '$2y$10$m4gkRJ5kt4bnP2YduTz0o.00uKYS3Cm1Iw5hO62XoqQlvId4hqPK2');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`proj_id`),
  ADD KEY `idxProject` (`user_id`);

--
-- Индексы таблицы `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `idxTaskProject` (`proj_id`),
  ADD KEY `idxTaskUser` (`user_id`);
ALTER TABLE `task` ADD FULLTEXT KEY `title_task` (`title_task`);

--
-- Индексы таблицы `user_reg`
--
ALTER TABLE `user_reg`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `project`
--
ALTER TABLE `project`
  MODIFY `proj_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT для таблицы `task`
--
ALTER TABLE `task`
  MODIFY `task_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT для таблицы `user_reg`
--
ALTER TABLE `user_reg`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `user_project` FOREIGN KEY (`user_id`) REFERENCES `user_reg` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `project_task` FOREIGN KEY (`proj_id`) REFERENCES `project` (`proj_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_task` FOREIGN KEY (`user_id`) REFERENCES `user_reg` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
