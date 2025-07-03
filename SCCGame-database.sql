CREATE DATABASE IF NOT EXISTS sccgame;
USE sccgame;

DROP TABLE IF EXISTS languages;
CREATE TABLE languages (
    code VARCHAR(5) PRIMARY KEY,  -- Ví dụ: 'en', 'vi', 'ja'
    name VARCHAR(30) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE     -- Bật/tắt ngôn ngữ
);

INSERT INTO languages (`code`,`name`,`is_active`) Values 
('vi','Tiếng Việt',TRUE),
('en','English',TRUE);

DROP TABLE IF EXISTS users;
CREATE TABLE users (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username varchar(20) UNIQUE,
    avatar varchar(100),
    password varchar(255),
    language varchar(5),
    email varchar(255),
    xp BIGINT UNSIGNED,
    
    FOREIGN KEY (language) REFERENCES languages(code)
        ON UPDATE CASCADE
        ON DELETE SET NULL
)


