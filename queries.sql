-- Добавляем пользователей
INSERT INTO user_reg (date_reg, email, us_name, pass) 
VALUES  (DEFAULT, 'ivanov@yandex.ru', 'Иван', 'qwerty'),
        (DEFAULT, 'petrov@mail.ru', 'Петр', 'asdfgh'),
        (DEFAULT, 'sidorov@mail.ru', 'Сидор', 'zxcvbnm'),
        (DEFAULT, 'pupkin@mail.ru', 'Пупкин', 'ytrewq');

-- Добавляем список проектов
INSERT INTO project (user_id, proj_name) 
VALUES (1, 'Входящие'), (2, 'Учеба'), (3, 'Работа'), (4, 'Домашние дела'), (1, 'Авто');

-- Добавляем список задач
-- 01
INSERT INTO task VALUES (DEFAULT, 1, 1, DEFAULT, DEFAULT, 'Собеседование в IT компании', 'http//download/file01.jpg', '2020-01-05');
-- 02
INSERT INTO task VALUES (DEFAULT, 2, 2, DEFAULT, DEFAULT, 'Выполнить тестовое задание', 'http//download/file02.jpg', '2020-02-15');
-- 03
INSERT INTO task VALUES (DEFAULT, 3, 3, DEFAULT, 1, 'Сделать задание первого раздела', 'http//download/file03.jpg', '2020-03-25');
-- 04
INSERT INTO task VALUES (DEFAULT, 4, 4, DEFAULT, DEFAULT, 'Встреча с другом', NULL, '2020-04-05');

-- query #1. Получить список из всех проектов для одного пользователя
SELECT us_name, proj_name  FROM user_reg u, project p WHERE u.user_id = p.user_id  AND u.user_id = 1;

-- query #2. Получить список из всех задач для одного проекта
SELECT proj_name, title_task FROM project p, task t WHERE p.proj_id = t.proj_id AND proj_name = 'Учеба';

-- query #3. Пометить задачу как выполненную
UPDATE task SET status_task = 1 WHERE user_id = 1;

-- query #4. Обновить название задачи по её идентификатору
UPDATE task SET title_task = 'Новая задача' WHERE task_id = 1;
