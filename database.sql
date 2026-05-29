CREATE DATABASE IF NOT EXISTS game_tracker;

USE game_tracker;

CREATE TABLE
    IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
        username VARCHAR(255) UNIQUE NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM ('user', 'admin') NOT NULL DEFAULT 'user',
        created_at DATETIME DEFAULT NOW (),
        updated_at DATETIME DEFAULT NOW () ON UPDATE NOW ()
    );

CREATE TABLE
    IF NOT EXISTS games (
        id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        genre VARCHAR(100) NOT NULL,
        release_year YEAR NOT NULL,
        image_url VARCHAR(255) NOT NULL,
        created_at DATETIME DEFAULT NOW (),
        updated_at DATETIME DEFAULT NOW () ON UPDATE NOW ()
    );

CREATE TABLE
    IF NOT EXISTS game_entries (
        id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
        user_id INT NOT NULL,
        game_id INT NOT NULL,
        status ENUM ('playing', 'finished', 'quit', 'waitlist') NOT NULL,
        rating TINYINT NULL CHECK (rating BETWEEN 1 AND 5),
        notes TEXT NULL,
        started_at DATE NULL,
        finished_at DATE NULL,
        created_at DATETIME DEFAULT NOW (),
        updated_at DATETIME DEFAULT NOW () ON UPDATE NOW (),
        UNIQUE (user_id, game_id),
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
        FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE
    );

CREATE TABLE
    IF NOT EXISTS sessions (
        id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
        entry_id INT NOT NULL,
        played_at DATE NOT NULL,
        duration_minutes INT NOT NULL,
        notes TEXT NULL,
        FOREIGN KEY (entry_id) REFERENCES game_entries (id) ON DELETE CASCADE
    );