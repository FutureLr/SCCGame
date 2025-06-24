CREATE DATABASE IF NOT EXISTS sccgame;
USE sccgame;

DROP TABLE IF EXISTS languages;
CREATE TABLE languages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,  -- Ví dụ: 'en', 'vi', 'ja'
    is_active BOOLEAN DEFAULT TRUE     -- Bật/tắt ngôn ngữ
);

DROP TABLE IF EXISTS users;
CREATE TABLE users (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username varchar(20) UNIQUE,
    avatar varchar(100),
    password varchar(255),
    language varchar(2),
    email varchar(50),
    xp BIGINT UNSIGNED
)


