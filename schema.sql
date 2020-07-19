-- Создаем БД
CREATE DATABASE doingsdone DEFAULT CHARACTER
SET utf8 DEFAULT COLLATE utf8_general_ci;
-- Делаем ее активной для работы
USE doingsdone;
-- Создаем табллицу для сущности Пользователь
CREATE TABLE user_reg (
  user_id INT(11) UNSIGNED AUTO_INCREMENT NOT NULL,
  date_reg TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  email CHAR (64) NOT NULL UNIQUE,
  us_name VARCHAR (128) NOT NULL,
  pass CHAR (64) NOT NULL,
  PRIMARY KEY (user_id)
) ENGINE = InnoDB;
-- Создаем табллицу для сущности Проект
CREATE TABLE project (
  proj_id INT(11) UNSIGNED AUTO_INCREMENT NOT NULL,
  user_id INT (11) UNSIGNED NOT NULL,
  proj_name VARCHAR (128) NOT NULL,
  PRIMARY KEY (proj_id),
  INDEX idxProject (user_id),
  CONSTRAINT user_project FOREIGN KEY (user_id) REFERENCES user_reg (user_id) ON
    DELETE CASCADE ON
    UPDATE CASCADE
) ENGINE = InnoDB;
-- Создаем табллицу для сущности Задача
CREATE TABLE task (
  task_id INT(11) UNSIGNED AUTO_INCREMENT NOT NULL,
  proj_id INT (11) UNSIGNED NOT NULL,
  user_id INT (11) UNSIGNED NOT NULL,
  date_task TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status_task BIT (1) NOT NULL DEFAULT 0,
  title_task VARCHAR (255) NOT NULL,
  link_file VARCHAR (255) NULL,
  date_task_end TIMESTAMP NULL,
  PRIMARY KEY (task_id),
  INDEX idxTaskProject (proj_id),
  INDEX idxTaskUser (user_id),
  CONSTRAINT project_task FOREIGN KEY (proj_id) REFERENCES project (proj_id) ON
      DELETE CASCADE ON
      UPDATE CASCADE,
  CONSTRAINT user_task FOREIGN KEY (user_id) REFERENCES user_reg (user_id) ON
      DELETE CASCADE ON
      UPDATE CASCADE
) ENGINE = InnoDB;
-- Создаем полнотекстовый поиск таблицы task для поля title_task
ALTER TABLE task
ADD FULLTEXT (title_task);
-- Составляем запрос полнотекстового поиска
SELECT *
FROM task
WHERE MATCH (title_task) AGAINST ('часть слова*' IN BOOLEAN MODE);
