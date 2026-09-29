CREATE DATABASE IF NOT EXISTS ci3_crud CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ci3_crud;

CREATE TABLE IF NOT EXISTS users (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100) NOT NULL,
  email       VARCHAR(150) NOT NULL UNIQUE,
  phone       VARCHAR(20)  NULL,
  gender      ENUM('male','female','other') NOT NULL DEFAULT 'male',
  profile_pic VARCHAR(255) NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS hobbies (
  id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS user_hobbies (
  user_id  INT UNSIGNED NOT NULL,
  hobby_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (user_id, hobby_id),
  FOREIGN KEY (user_id)  REFERENCES users(id)   ON DELETE CASCADE,
  FOREIGN KEY (hobby_id) REFERENCES hobbies(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT IGNORE INTO hobbies (name) VALUES
('Reading'),('Cricket'),('Music'),('Traveling'),('Cooking'),('Gaming'),('Photography'),('Painting');
