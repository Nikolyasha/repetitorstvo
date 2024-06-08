-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июл 15 2021 г., 00:20
-- Версия сервера: 8.0.19
-- Версия PHP: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `site`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cities`
--

CREATE TABLE `cities` (
  `id` int NOT NULL,
  `city_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `cities`
--

INSERT INTO `cities` (`id`, `city_name`) VALUES
(1, 'Москва'),
(2, 'СПБ');

-- --------------------------------------------------------

--
-- Структура таблицы `companies`
--

CREATE TABLE `companies` (
  `id` int NOT NULL,
  `company_owner_id` int NOT NULL,
  `company_name` varchar(50) NOT NULL,
  `company_type` int NOT NULL,
  `company_status` int NOT NULL DEFAULT '1' COMMENT '0 - скрыта \r\n1 - активна',
  `city` int NOT NULL,
  `office_adress` varchar(50) NOT NULL,
  `company_desc` varchar(3000) NOT NULL,
  `company_contacts` varchar(1000) NOT NULL,
  `owner_name` varchar(50) NOT NULL,
  `owner_phone` varchar(50) NOT NULL,
  `owner_status` varchar(50) NOT NULL,
  `logo` text NOT NULL,
  `vacancy_avaiable` int NOT NULL DEFAULT '0',
  `favorite` json DEFAULT NULL,
  `extra_params` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `companies`
--

INSERT INTO `companies` (`id`, `company_owner_id`, `company_name`, `company_type`, `company_status`, `city`, `office_adress`, `company_desc`, `company_contacts`, `owner_name`, `owner_phone`, `owner_status`, `logo`, `vacancy_avaiable`, `favorite`, `extra_params`) VALUES
(1, 1, 'M', 0, 1, 1, 'Крылатская ул., 17, стр. 1Е', 'Одна из крупнейших транснациональных компаний по производству проприетарного программного обеспечения для различного рода вычислительной техники - персональных компьютеров, игровых приставок, КПК, мобильных телефонов и прочего. Разработчик наиболее широко распространённой на данный момент в мире программной платформы - семейства операционных систем Windows.', '+7 (495) 916-71-71 (для звонков из Москвы)\r\n8 (800) 200-80-01 (номер для звонков из России)-', 'Максим Михалин', '+70000000000', 'Владелец', '1.jpg', 1, '[\"2\"]', '[{\"name\": \"official_work\", \"value\": 1}, {\"name\": \"select_test\", \"value\": 1}]'),
(2, 2, 'Яндекс', 0, 1, 1, 'Описание', '123', '456', 'Happy Thoughts', '+70000000000', 'HR', 'ya.png', 0, 'null', '[{\"name\": \"official_work\", \"value\": 1}, {\"name\": \"select_test\", \"value\": 1}]');

-- --------------------------------------------------------

--
-- Структура таблицы `company_types`
--

CREATE TABLE `company_types` (
  `id` int NOT NULL,
  `company_type_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `company_types`
--

INSERT INTO `company_types` (`id`, `company_type_name`) VALUES
(0, 'Рекрутинговая компания'),
(1, 'Модельное агентство'),
(2, 'Рекламное агентство'),
(3, 'Маркетинговое агентство'),
(4, 'Ивент агентство'),
(5, 'Прямой заказчик'),
(6, 'Частное лицо'),
(7, 'Новый элемент'),
(8, 'Новый элемент'),
(9, 'Новый элемент'),
(10, 'Новый элемент'),
(11, 'Новый элемент'),
(12, 'Новый элемент'),
(13, 'Новый элемент'),
(14, 'Новый элемент');

-- --------------------------------------------------------

--
-- Структура таблицы `extra_filters`
--

CREATE TABLE `extra_filters` (
  `id` int NOT NULL,
  `object_type` int NOT NULL COMMENT '0 - вакансия\r\n1 - анкета\r\n2 - компания',
  `name` varchar(50) NOT NULL,
  `display` varchar(50) NOT NULL,
  `type` int NOT NULL COMMENT '0 - checkbox\r\n1 - radiobutton\r\n2 - select',
  `options` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

--
-- Дамп данных таблицы `extra_filters`
--

INSERT INTO `extra_filters` (`id`, `object_type`, `name`, `display`, `type`, `options`) VALUES
(2, 0, 'need_medical_book', 'Мед. книжка', 2, 'Не требуется;Требуется'),
(13, 2, 'official_work', 'Официальное трудоустройство', 0, 'Нет;Да'),
(14, 2, 'select_test', 'Тестовый селектор', 2, 'Вариант 1;Вариант 2;Вариант 3'),
(15, 0, 'work_type', 'Тип трудоустройства', 1, 'Официальный;Самозанятый;Неофициальный'),
(16, 0, 'test_select_vacancy', 'Тестовый селектор', 2, 'Селектор 1;Селектор 2;Селектор 3'),
(17, 1, 'have_auto', 'Есть свое авто', 0, 'Нет;Да');

-- --------------------------------------------------------

--
-- Структура таблицы `logs`
--

CREATE TABLE `logs` (
  `id` int NOT NULL,
  `event` text NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

--
-- Дамп данных таблицы `logs`
--

INSERT INTO `logs` (`id`, `event`, `date`) VALUES
(1, 'Тестовая запись', '2021-06-16 18:03:31'),
(3, 'Пополнение баланса #7 пользователем id1 на 100 монет', '2021-06-16 18:47:25'),
(4, 'Разблокировка пользователя #10', '2021-06-18 02:43:10'),
(5, 'Изменение баланса пользователя id4 +34 на монет', '2021-06-18 03:09:13'),
(6, 'Изменение баланса пользователя id4 0 на монет', '2021-06-18 03:09:55'),
(7, 'Изменение баланса пользователя id4 +3 на монет', '2021-06-18 03:10:00'),
(8, 'Изменение баланса пользователя id4 -3 на монет', '2021-06-18 03:10:21');

-- --------------------------------------------------------

--
-- Структура таблицы `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `owner_id` int NOT NULL,
  `amount` int NOT NULL,
  `payment_id` int NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

--
-- Дамп данных таблицы `payments`
--

INSERT INTO `payments` (`id`, `owner_id`, `amount`, `payment_id`, `date`) VALUES
(1, 1, 100, 473073445, '2021-06-18 16:57:50'),
(2, 1, 1000, 473073946, '2021-06-21 16:57:50'),
(3, 1, 100, 0, '2021-06-20 16:57:50'),
(4, 1, 100, 490216501, '2021-06-21 16:57:50'),
(5, 1, 100, 490217574, '2021-06-22 16:57:50'),
(6, 11, 100, 490218704, '2021-06-22 16:57:50'),
(7, 1, 100, 490232090, '2021-06-23 16:57:50'),
(8, 1, 100, 0, '2021-06-25 13:41:01');

-- --------------------------------------------------------

--
-- Структура таблицы `purchases`
--

CREATE TABLE `purchases` (
  `id` int NOT NULL,
  `owner_id` int NOT NULL,
  `object_type` int NOT NULL COMMENT '0 - контакты пользователя\r\n1 - контакты компании',
  `object_id` int NOT NULL,
  `purchase_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

--
-- Дамп данных таблицы `purchases`
--

INSERT INTO `purchases` (`id`, `owner_id`, `object_type`, `object_id`, `purchase_date`) VALUES
(1, 1, 0, 4, '2021-06-25 16:44:00'),
(2, 2, 0, 4, '2021-06-25 16:44:00'),
(3, 1, 0, 2, '2021-06-25 16:44:00'),
(5, 1, 0, 41, '2021-06-25 16:44:00'),
(7, 4, 1, 1, '2021-06-25 16:44:00'),
(8, 1, 0, 10, '2021-06-25 16:44:00'),
(9, 4, 1, 1, '2021-06-25 16:44:00'),
(10, 4, 1, 1, '2021-06-25 16:44:00');

-- --------------------------------------------------------

--
-- Структура таблицы `responses`
--

CREATE TABLE `responses` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `vacancy_id` int NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `offer` varchar(1000) NOT NULL DEFAULT 'Этот пользователь прислал вам предложение',
  `status` int NOT NULL DEFAULT '0' COMMENT '0 - новый, 1 - принят, 2 - отклонен',
  `response` varchar(1000) NOT NULL,
  `remove` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `responses`
--

INSERT INTO `responses` (`id`, `user_id`, `vacancy_id`, `date`, `offer`, `status`, `response`, `remove`) VALUES
(5, 4, 1, '2021-01-07 00:00:00', 'Этот пользователь прислал вам предложение', 1, 'Ты принят', 2),
(6, 4, 2, '2021-01-07 00:00:00', 'Этот пользователь прислал вам предложение', 2, 'Извини, но ты нам не подоходишь', 0),
(7, 2, 1, '2021-01-07 00:00:00', 'Этот пользователь прислал вам предложение', 2, '', 2),
(12, 4, 3, '2021-04-17 00:45:10', 'Этот пользователь прислал вам предложение', 2, '', 0),
(13, 4, 14, '2021-05-03 16:41:52', 'Этот пользователь прислал вам предложение', 0, '', 0),
(14, 8, 14, '2021-05-07 20:10:59', 'Этот пользователь прислал вам предложение', 0, '', 0),
(15, 4, 4, '2021-07-14 20:01:22', 'Этот пользователь прислал вам предложение', 0, '', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `site_pages`
--

CREATE TABLE `site_pages` (
  `id` int NOT NULL,
  `name` text NOT NULL,
  `link` text NOT NULL,
  `html` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

--
-- Дамп данных таблицы `site_pages`
--

INSERT INTO `site_pages` (`id`, `name`, `link`, `html`) VALUES
(0, 'Preview', '_preview', '<script>swal(\"Ошибка предпросмотра\", \"Открывайте эту страницу только из админ панели\", \"error\").then(() => {window.location=\"/admin/edit_page.php\"});</script> '),
(1, 'О сайте', 'about', '<script>swal(\"Страница еще не создана\", \"Отредактируйте ее в админ панели\", \"info\");</script>'),
(3, 'Реклама на сайте', 'ads', '<script>swal(\"Страница еще не создана\", \"Отредактируйте ее в админ панели\", \"info\");</script>'),
(4, 'Условия пользования сайтом', 'terms', '<script>swal(\"Страница еще не создана\", \"Отредактируйте ее в админ панели\", \"info\");</script>'),
(5, 'Контактная информация', 'contacts', '<script>swal(\"Страница еще не создана\", \"Отредактируйте ее в админ панели\", \"info\");</script>'),
(6, 'Правила сайта', 'rules', '<script>swal(\"Страница еще не создана\", \"Отредактируйте ее в админ панели\", \"info\");</script>'),
(7, 'Платные услуги', 'paid-services', '<script>swal(\"Страница еще не создана\", \"Отредактируйте ее в админ панели\", \"info\");</script>'),
(8, 'Советы новичкам', 'welcome', '<script>swal(\"Страница еще не создана\", \"Отредактируйте ее в админ панели\", \"info\");</script>'),
(9, 'Полезные статьи', 'articles', '<script>swal(\"Страница еще не создана\", \"Отредактируйте ее в админ панели\", \"info\");</script>'),
(10, 'Промо-Словарь', 'dictionary', '<script>swal(\"Страница еще не создана\", \"Отредактируйте ее в админ панели\", \"info\");</script>'),
(11, 'Написать в службу поддержки', 'support', '<script>swal(\"Страница еще не создана\", \"Отредактируйте ее в админ панели\", \"info\");</script>'),
(12, 'Работодателям', 'employers', '<script>swal(\"Страница еще не создана\", \"Отредактируйте ее в админ панели\", \"info\");</script>'),
(13, 'Соискателям', 'applicants', '<script>swal(\"Страница еще не создана\", \"Отредактируйте ее в админ панели\", \"info\");</script>'),
(14, 'Помощь', 'help', '<script>swal(\"Страница еще не создана\", \"Отредактируйте ее в админ панели\", \"info\");</script>'),
(15, 'Рекламный фрейм в правом меню', 'right_banner', '<p style=\"text-align:center\"><strong>Зайди на сайт партнера</strong></p>\r\n\r\n<p style=\"text-align:center\"><a href=\"https://learn.javascript.ru\" target=\"_blank\"><img alt=\"\" src=\"https://learn.javascript.ru/img/sitetoolbar__logo_ru.svg\" /></a></p>\r\n'),
(16, 'Рекламный фрейм в верхней части', 'top_banner', '<b>ads top</b>');

-- --------------------------------------------------------

--
-- Структура таблицы `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int NOT NULL,
  `name` text NOT NULL,
  `display_name` text NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

--
-- Дамп данных таблицы `site_settings`
--

INSERT INTO `site_settings` (`id`, `name`, `display_name`, `value`) VALUES
(0, 'coin_price', 'Цена 1 монеты (₸)', '1'),
(1, 'vacancy_price', 'Создание вакансии', '10'),
(2, 'worker_contact_price', 'Контакты анкеты', '10'),
(3, 'send_offer_price', 'Отправка отклика', '3'),
(4, 'company_contact_price', 'Контакты компании', '10'),
(5, 'site_name_option', 'Имя сайта', 'Promobank.ru'),
(6, 'anket_create_price', 'Создание анкеты', '0'),
(7, 'primay_color_option', 'Цвет сайта (HEX)', '#833ea3'),
(8, 'support_mail_option', 'Почта техподдержки', 'support@mail.ru'),
(9, 'support_phone_option', 'Телефон техподдержки', '8 (800) 555-35-36'),
(10, 'social_insta_option', 'Ссылка на Instagram', 'https://instagram.com/123'),
(11, 'social_vk_option', 'Ссылка на ВКонтакте', 'https://vk.com/club123'),
(12, 'social_whatsapp_option', 'Ссылка на WhatsApp', 'https://whatsapp.com/123'),
(13, 'max_profile_photos_option', 'Макс. кол-во фото профиля', '10'),
(90, 'paybox_id_option', 'ID магазина PayBox', '521476'),
(91, 'paybox_pay_key_option', 'Ключ от PayBox', 'y7DvdFrG5S5sms1O'),
(92, 'paybox_public_key_option', 'Публичный ключ PayBox', '6LdFgYYaAAAAAIRPVXdnYPi8QxGB8V70shN7i63j'),
(93, 'paybox_private_key_option', 'Секретный ключ PayBox', '6LdFgYYaAAAAAJIWmqLb5uhQABTzuc2bMAezAyhW'),
(97, 'captcha_public_option', 'reCaptcha Public', '6LfT2zUbAAAAALmeq0Eea-BOKlqj3s5qD4o3Mnac'),
(98, 'captcha_private_option', 'reCaptcha Private', '6LfT2zUbAAAAAGFfd0lS5boeoQdNsWgwuf0-b5L5'),
(99, 'company_types_list', 'Типы компаний (работодателей)', 'company_types'),
(100, 'cities_list', 'Список городов', 'cities'),
(101, 'vacancy_types_list', 'Типы вакансий', 'vacancy_types');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `passwd` varchar(32) NOT NULL,
  `auth_token` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `admin` int NOT NULL DEFAULT '0',
  `activation` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `recovery_token` varchar(32) NOT NULL DEFAULT '',
  `type` int NOT NULL DEFAULT '0' COMMENT '0 - скрытый, \r\n1 - работник, \r\n2 - работодатель',
  `base_type` int NOT NULL COMMENT 'Тип аккаунта для бана',
  `balance` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `email`, `passwd`, `auth_token`, `name`, `admin`, `activation`, `recovery_token`, `type`, `base_type`, `balance`) VALUES
(1, 'admin@admin.ru', '202cb962ac59075b964b07152d234b70', '13e781fbc2efc59ba8ed27eba3457a64', 'Максим Михалин', 1, '', '7aa973c970c06a13a0529834f44855e2', 2, 2, 1492),
(2, 'test@gmail.com', '202cb962ac59075b964b07152d234b70', '', 'Happy Thoughts', 0, '', '', 2, 2, 1),
(4, 'work@mail.ru', '202cb962ac59075b964b07152d234b70', '', 'Павел Павлов', 0, '', '', 1, 1, 11),
(8, 'some@mail.ru', '43dd1802f53a629bbd25221d7439334b', '', 'Иван Сафонов', 0, '', '', 1, 1, 0),
(10, 'avebhs.ru@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '', 'Some Body', 0, '', '', 1, 1, 0),
(11, 'test@mail.ru', '25d55ad283aa400af464c76d713c07ad', '', 'Иванов Иван', 0, '', '', 1, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `vacancies`
--

CREATE TABLE `vacancies` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `owner_id` int NOT NULL,
  `city_id` int NOT NULL,
  `type_id` int NOT NULL,
  `sex` int NOT NULL DEFAULT '0',
  `age_min` int NOT NULL DEFAULT '0',
  `age_max` int NOT NULL DEFAULT '0',
  `experience` int NOT NULL,
  `time_type` int NOT NULL,
  `time_from` varchar(5) NOT NULL,
  `time_to` varchar(5) NOT NULL,
  `days` varchar(20) NOT NULL,
  `payment_type` int NOT NULL,
  `salary_per_hour` int NOT NULL DEFAULT '0',
  `salary_per_day` int NOT NULL DEFAULT '0',
  `salary_per_month` int NOT NULL DEFAULT '0',
  `description` varchar(2500) NOT NULL,
  `desc_min` varchar(100) NOT NULL,
  `workplace_count` int NOT NULL,
  `public_date` datetime NOT NULL,
  `contact_info` varchar(500) NOT NULL,
  `views` int NOT NULL DEFAULT '0',
  `extra_params` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `vacancies`
--

INSERT INTO `vacancies` (`id`, `name`, `owner_id`, `city_id`, `type_id`, `sex`, `age_min`, `age_max`, `experience`, `time_type`, `time_from`, `time_to`, `days`, `payment_type`, `salary_per_hour`, `salary_per_day`, `salary_per_month`, `description`, `desc_min`, `workplace_count`, `public_date`, `contact_info`, `views`, `extra_params`) VALUES
(1, 'Сварщик', 2, 2, 2, 1, 18, 50, 0, 0, '5:00', '18:00', '1,2,3,4,5', 0, 36000, 0, 0, 'Нужен сварщик, парень работящий ', 'Нужен сварщик, парень работящий', 1, '2021-02-10 07:16:22', '+700000000000', 1, '[{\"name\": \"need_auto\", \"value\": 0}, {\"name\": \"need_medical_book\", \"value\": 0}, {\"name\": \"work_type\", \"value\": 2}, {\"name\": \"test_select_vacancy\", \"value\": 0}]'),
(3, 'Разработчик С++', 1, 1, 17, 2, 18, 45, 3, 0, '8:00', '18:00', '6,7', 2, 0, 5000, 0, 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 1, '2021-02-04 06:24:33', '', 3, '[{\"name\": \"need_auto\", \"value\": 1}, {\"name\": \"need_medical_book\", \"value\": 1}, {\"name\": \"need_test\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"work_type\", \"value\": 0}, {\"name\": \"test_select_vacancy\", \"value\": 2}]'),
(4, 'Разработчик С++', 2, 1, 17, 2, 18, 45, 3, 0, '8:00', '18:00', '6,7', 2, 0, 5000, 0, 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 1, '2021-02-04 06:24:33', '', 1, '[{\"name\": \"need_auto\", \"value\": 1}, {\"name\": \"need_medical_book\", \"value\": 1}, {\"name\": \"need_test\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"work_type\", \"value\": 0}, {\"name\": \"test_select_vacancy\", \"value\": 2}]'),
(5, 'Разработчик С++', 1, 1, 17, 2, 18, 45, 3, 0, '8:00', '18:00', '6,7', 2, 0, 5000, 0, 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 1, '2021-02-04 06:24:33', '', 0, '[{\"name\": \"need_auto\", \"value\": 1}, {\"name\": \"need_medical_book\", \"value\": 1}, {\"name\": \"need_test\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"work_type\", \"value\": 0}, {\"name\": \"test_select_vacancy\", \"value\": 2}]'),
(6, 'Разработчик С++', 1, 1, 17, 2, 18, 45, 3, 0, '8:00', '18:00', '6,7', 2, 0, 5000, 0, 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 1, '2021-02-04 06:24:33', '', 1, '[{\"name\": \"need_auto\", \"value\": 1}, {\"name\": \"need_medical_book\", \"value\": 1}, {\"name\": \"need_test\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"work_type\", \"value\": 0}, {\"name\": \"test_select_vacancy\", \"value\": 2}]'),
(7, 'Разработчик С++', 1, 1, 17, 2, 18, 45, 3, 0, '8:00', '18:00', '6,7', 2, 0, 5000, 0, 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 1, '2021-02-04 06:24:33', '', 0, '[{\"name\": \"need_auto\", \"value\": 1}, {\"name\": \"need_medical_book\", \"value\": 1}, {\"name\": \"need_test\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"work_type\", \"value\": 0}, {\"name\": \"test_select_vacancy\", \"value\": 2}]'),
(8, 'Разработчик С++', 1, 1, 17, 2, 18, 45, 3, 0, '8:00', '18:00', '6,7', 2, 0, 5000, 0, 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 1, '2021-02-04 06:24:33', '', 1, '[{\"name\": \"need_auto\", \"value\": 1}, {\"name\": \"need_medical_book\", \"value\": 1}, {\"name\": \"need_test\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"work_type\", \"value\": 0}, {\"name\": \"test_select_vacancy\", \"value\": 2}]'),
(9, 'Разработчик С++', 1, 1, 17, 2, 18, 45, 3, 0, '8:00', '18:00', '6,7', 2, 0, 5000, 0, 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 1, '2021-02-04 06:24:33', '', 1, '[{\"name\": \"need_auto\", \"value\": 1}, {\"name\": \"need_medical_book\", \"value\": 1}, {\"name\": \"need_test\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"work_type\", \"value\": 0}, {\"name\": \"test_select_vacancy\", \"value\": 2}]'),
(10, 'Разработчик С++', 1, 1, 17, 2, 18, 45, 3, 0, '8:00', '18:00', '6,7', 2, 0, 5000, 0, 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 1, '2021-02-04 06:24:33', '', 1, '[{\"name\": \"need_auto\", \"value\": 1}, {\"name\": \"need_medical_book\", \"value\": 1}, {\"name\": \"need_test\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"work_type\", \"value\": 0}, {\"name\": \"test_select_vacancy\", \"value\": 2}]'),
(11, 'Разработчик С++', 1, 1, 17, 2, 18, 45, 3, 0, '8:00', '18:00', '6,7', 2, 0, 5000, 0, 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 1, '2021-02-04 06:24:33', '', 0, '[{\"name\": \"need_auto\", \"value\": 1}, {\"name\": \"need_medical_book\", \"value\": 1}, {\"name\": \"need_test\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"work_type\", \"value\": 0}, {\"name\": \"test_select_vacancy\", \"value\": 2}]'),
(12, 'Разработчик С++', 1, 1, 17, 2, 18, 45, 3, 0, '8:00', '18:00', '6,7', 2, 0, 5000, 0, 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 1, '2021-02-04 06:24:33', '', 0, '[{\"name\": \"need_auto\", \"value\": 1}, {\"name\": \"need_medical_book\", \"value\": 1}, {\"name\": \"need_test\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"work_type\", \"value\": 0}, {\"name\": \"test_select_vacancy\", \"value\": 2}]'),
(13, 'Разработчик С++', 1, 1, 17, 2, 18, 45, 3, 0, '8:00', '18:00', '6,7', 2, 0, 5000, 0, 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 'Разработчик С++ в проект поисковика Яндекса, опыт работы от 3 лет', 1, '2021-02-04 06:24:33', '', 1, '[{\"name\": \"need_auto\", \"value\": 1}, {\"name\": \"need_medical_book\", \"value\": 1}, {\"name\": \"need_test\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"qwe\", \"value\": 0}, {\"name\": \"speak_english\", \"value\": 0}, {\"name\": \"work_type\", \"value\": 0}, {\"name\": \"test_select_vacancy\", \"value\": 2}]'),
(14, 'Сварщик 12', 1, 2, 5, 1, 0, 0, 3, 2, '8:40', '9:00', '2021-05-12', 1, 0, 1000, 0, '5644654654', '46546545', 1, '2021-05-02 10:43:11', '4665465', 4, '[{\"name\": \"need_auto\", \"value\": 1}, {\"name\": \"need_medical_book\", \"value\": 1}, {\"name\": \"work_type\", \"value\": 2}, {\"name\": \"test_select_vacancy\", \"value\": 1}]');

-- --------------------------------------------------------

--
-- Структура таблицы `vacancy_types`
--

CREATE TABLE `vacancy_types` (
  `id` int NOT NULL,
  `vacancy_type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `vacancy_types`
--

INSERT INTO `vacancy_types` (`id`, `vacancy_type_name`) VALUES
(1, 'Промоутер'),
(2, 'Супервайзер'),
(3, 'Промо-модель'),
(4, 'Модель     '),
(5, 'Интервьюер'),
(6, 'Аудитор/чекер'),
(7, 'Тайный покупалеть'),
(8, 'Хостес'),
(9, 'Официант'),
(10, 'Бармен'),
(11, 'Курьер'),
(12, 'Ростовая кукла'),
(13, 'Мерчендайзер'),
(14, 'Разнорабочий'),
(15, 'Стажёр'),
(16, 'Аниматор'),
(17, 'IT-сфера');

-- --------------------------------------------------------

--
-- Структура таблицы `workers`
--

CREATE TABLE `workers` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `status` int NOT NULL COMMENT '0 - неактив\r\n1 - актив',
  `activation` int NOT NULL DEFAULT '0' COMMENT '0 - не куплен\r\n1 - куплен',
  `age` int NOT NULL,
  `first_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `sex` int NOT NULL COMMENT '0 - мужчина, 1 - женщина',
  `phone` varchar(20) NOT NULL,
  `viber` varchar(32) NOT NULL DEFAULT '',
  `telegram` varchar(32) NOT NULL DEFAULT '',
  `whatsapp` varchar(32) NOT NULL DEFAULT '',
  `job_types` text NOT NULL,
  `min_salary` int NOT NULL DEFAULT '100' COMMENT 'В час ',
  `week_days` varchar(14) NOT NULL DEFAULT '1,2,3,4,5',
  `time_range` varchar(11) NOT NULL DEFAULT '08:00-18:00',
  `experience` varchar(500) NOT NULL,
  `special` varchar(2500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `about` varchar(2500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `view` varchar(2500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `city` varchar(100) NOT NULL,
  `birthday` date NOT NULL,
  `photos` text NOT NULL,
  `extra_fields` json DEFAULT NULL,
  `reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_online` int NOT NULL,
  `views` int NOT NULL DEFAULT '0',
  `favorite` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `workers`
--

INSERT INTO `workers` (`id`, `user_id`, `status`, `activation`, `age`, `first_name`, `last_name`, `sex`, `phone`, `viber`, `telegram`, `whatsapp`, `job_types`, `min_salary`, `week_days`, `time_range`, `experience`, `special`, `about`, `view`, `city`, `birthday`, `photos`, `extra_fields`, `reg_date`, `last_online`, `views`, `favorite`) VALUES
(1, 2, 1, 1, 34, 'Павел', 'Павлов', 1, '89000000000', '', '', '', '8,13,14,15,17', 434543, '1,2,4,7', '08:01-18:02', 'e1', 's3', 'a1', 'v2', '2', '2021-04-15', '1.jpg', '[{\"name\": \"have_auto\", \"value\": 1}]', '1999-12-31 21:00:00', 1617980825, 6, '[\"2\"]'),
(2, 4, 1, 1, 45, 'Павел', 'Павлов', 1, '89000000000', '89000000000', '@pavelkdtr', '89000000000', '4,8,13,14,15,17', 434543, '1,2,4,7', '08:01-18:02', 'e1', 's3', 'a1', 'v2', '2', '2021-04-15', '1.jpg', '[{\"name\": \"have_auto\", \"value\": 1}]', '2021-04-09 14:42:16', 1626297634, 2, '[\"13\", \"1\", \"3\", \"14\", \"12\"]'),
(4, 8, 0, 0, 18, 'Иван', 'Сафонов', 0, '', '', '', '', '', 0, '', '08:00-18:00', '', '', '', '', '1', '2000-01-01', '1.jpg', NULL, '2021-05-07 17:07:29', 1620407579, 0, '[\"14\"]'),
(6, 10, 0, 1, 18, 'Some', 'Body', 0, '234234', '', '', '', '3,4,5', 0, '1,2,5', '08:00-18:00', '', '', '', '', '1', '2000-01-01', '1.jpg', '[{\"name\": \"have_auto\", \"value\": 0}]', '2021-05-20 15:37:09', 1624314491, 0, '[\"14\", \"13\"]'),
(7, 11, 0, 1, 18, 'Иванов', 'Иван', 0, '', '', '', '', '', 0, '', '08:00-18:00', '', '', '', '', '1', '2000-01-01', '', NULL, '2021-06-17 15:23:42', 0, 0, NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `companies_ibfk_1` (`city`),
  ADD KEY `companies_ibfk_2` (`company_owner_id`);

--
-- Индексы таблицы `company_types`
--
ALTER TABLE `company_types`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `extra_filters`
--
ALTER TABLE `extra_filters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `responses`
--
ALTER TABLE `responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `site_pages`
--
ALTER TABLE `site_pages`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `vacancies`
--
ALTER TABLE `vacancies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vacancies_ibfk_2` (`owner_id`),
  ADD KEY `vacancies_ibfk_3` (`city_id`),
  ADD KEY `vacancies_ibfk_4` (`type_id`);

--
-- Индексы таблицы `vacancy_types`
--
ALTER TABLE `vacancy_types`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `workers`
--
ALTER TABLE `workers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `company_types`
--
ALTER TABLE `company_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `extra_filters`
--
ALTER TABLE `extra_filters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблицы `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `responses`
--
ALTER TABLE `responses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `site_pages`
--
ALTER TABLE `site_pages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `vacancies`
--
ALTER TABLE `vacancies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `vacancy_types`
--
ALTER TABLE `vacancy_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT для таблицы `workers`
--
ALTER TABLE `workers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`city`) REFERENCES `cities` (`id`) ON UPDATE RESTRICT,
  ADD CONSTRAINT `companies_ibfk_2` FOREIGN KEY (`company_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `responses`
--
ALTER TABLE `responses`
  ADD CONSTRAINT `responses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `workers` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `vacancies`
--
ALTER TABLE `vacancies`
  ADD CONSTRAINT `vacancies_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `vacancies_ibfk_3` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON UPDATE RESTRICT,
  ADD CONSTRAINT `vacancies_ibfk_4` FOREIGN KEY (`type_id`) REFERENCES `vacancy_types` (`id`) ON UPDATE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `workers`
--
ALTER TABLE `workers`
  ADD CONSTRAINT `workers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
