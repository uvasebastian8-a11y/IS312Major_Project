-- ============================================================
--  Library System with Book Reviews — database.sql
--  IS312 AT3 | Team: Jasmine, Sebastian & Joseph
--  Divine Word University
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ── Create & select database ──────────────────────────────────
CREATE DATABASE IF NOT EXISTS `library_system`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `library_system`;

-- ============================================================
--  TABLE: users
-- ============================================================
CREATE TABLE `users` (
  `UserID`           INT(11)      NOT NULL AUTO_INCREMENT,
  `FirstName`        VARCHAR(50)  NOT NULL,
  `LastName`         VARCHAR(50)  NOT NULL,
  `Email`            VARCHAR(100) NOT NULL,
  `Password`         VARCHAR(255) NOT NULL,
  `Gender`           ENUM('Male','Female','Other') NOT NULL,
  `Address`          VARCHAR(255) NOT NULL,
  `PostalCode`       VARCHAR(20)  DEFAULT NULL,
  `PostOfficeNumber` VARCHAR(50)  DEFAULT NULL,
  `Contact`          VARCHAR(20)  NOT NULL,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
--  TABLE: books
-- ============================================================
CREATE TABLE `books` (
  `BookID`      INT(11)      NOT NULL AUTO_INCREMENT,
  `Title`       VARCHAR(150) NOT NULL,
  `Author`      VARCHAR(100) NOT NULL,
  `Category`    VARCHAR(100) NOT NULL,
  `Description` TEXT         DEFAULT NULL,
  `Image`       VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`BookID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
--  TABLE: reviews
-- ============================================================
CREATE TABLE `reviews` (
  `ReviewID`  INT(11)   NOT NULL AUTO_INCREMENT,
  `UserID`    INT(11)   NOT NULL,
  `BookID`    INT(11)   NOT NULL,
  `Rating`    TINYINT   NOT NULL,
  `Comment`   TEXT      NOT NULL,
  `CreatedAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`ReviewID`),
  -- One review per user per book (spec requirement)
  UNIQUE KEY `unique_user_book` (`UserID`, `BookID`),
  CONSTRAINT `chk_rating`
    CHECK (`Rating` BETWEEN 1 AND 5),
  CONSTRAINT `reviews_ibfk_1`
    FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reviews_ibfk_2`
    FOREIGN KEY (`BookID`) REFERENCES `books` (`BookID`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
--  SAMPLE DATA — Books
-- ============================================================
INSERT INTO `books`
  (`BookID`, `Title`, `Author`, `Category`, `Description`, `Image`)
VALUES
(1,  'The Great Gatsby',
     'F. Scott Fitzgerald', 'Fiction',
     'A story of wealth, obsession and love in the 1920s American Dream era.',
     'gatsby.jpg'),
(2,  'To Kill a Mockingbird',
     'Harper Lee', 'Classic',
     'A powerful novel about racial injustice and moral growth in the Deep South.',
     'mockingbird.jpg'),
(3,  '1984',
     'George Orwell', 'Dystopian',
     'A chilling depiction of a totalitarian society under constant surveillance.',
     '1984.jpg'),
(4,  'The Alchemist',
     'Paulo Coelho', 'Philosophy',
     'A journey of self-discovery, dreams and following one\'s personal legend.',
     'alchemist.jpg'),
(5,  'Atomic Habits',
     'James Clear', 'Self-Help',
     'A practical guide to building good habits and breaking bad ones.',
     'atomic.jpg'),
(6,  'Pride and Prejudice',
     'Jane Austen', 'Classic',
     'A timeless romantic novel exploring love, class and societal expectations.',
     'pride_prejudice.jpg'),
(7,  'The Hobbit',
     'J.R.R. Tolkien', 'Fantasy',
     'Bilbo Baggins journeys with dwarves to reclaim their homeland from a dragon.',
     'hobbit.jpg'),
(8,  'Harry Potter and the Sorcerer\'s Stone',
     'J.K. Rowling', 'Fantasy',
     'A young boy discovers he is a wizard and begins his education at Hogwarts.',
     'harry_potter.jpg'),
(9,  'The Da Vinci Code',
     'Dan Brown', 'Mystery',
     'A gripping mystery involving secret societies and hidden messages in art.',
     'davinci_code.jpg'),
(10, 'Becoming',
     'Michelle Obama', 'Biography',
     'An intimate memoir by former First Lady Michelle Obama.',
     'becoming.jpg');

-- ============================================================
--  SAMPLE DATA — Users
--  All passwords are: Password@1
--  Hash generated with password_hash('Password@1', PASSWORD_BCRYPT)
-- ============================================================
INSERT INTO `users`
  (`UserID`, `FirstName`, `LastName`, `Email`, `Password`,
   `Gender`, `Address`, `PostalCode`, `PostOfficeNumber`, `Contact`)
VALUES
(1, 'John',  'Doe',
    'john@example.com',
    '$2y$10$TKh8H1.PfunDKObEkkFaGOe8a8Dxe/v5RCmFp6nAiRmQ1bMbEhH5i',
    'Male',   'Madang Town',      '111', 'PO Box 123', '70000001'),
(2, 'Mary',  'Kila',
    'mary@example.com',
    '$2y$10$TKh8H1.PfunDKObEkkFaGOe8a8Dxe/v5RCmFp6nAiRmQ1bMbEhH5i',
    'Female', 'Lae City',         '211', 'PO Box 456', '70000002'),
(3, 'Peter', 'Uva',
    'peter@example.com',
    '$2y$10$TKh8H1.PfunDKObEkkFaGOe8a8Dxe/v5RCmFp6nAiRmQ1bMbEhH5i',
    'Male',   'Port Moresby',     '311', 'PO Box 789', '70000003'),
(4, 'Anna',  'James',
    'anna@example.com',
    '$2y$10$TKh8H1.PfunDKObEkkFaGOe8a8Dxe/v5RCmFp6nAiRmQ1bMbEhH5i',
    'Female', 'Goroka Town',      '411', 'PO Box 321', '70000004'),
(5, 'Grace', 'Teine',
    'grace@example.com',
    '$2y$10$TKh8H1.PfunDKObEkkFaGOe8a8Dxe/v5RCmFp6nAiRmQ1bMbEhH5i',
    'Female', 'Madang Province',  '511', 'PO Box 61',  '70000005');

-- ============================================================
--  SAMPLE DATA — Reviews
-- ============================================================
INSERT INTO `reviews`
  (`ReviewID`, `UserID`, `BookID`, `Rating`, `Comment`, `CreatedAt`)
VALUES
(1, 1, 1,  5, 'Amazing book, highly recommended!',
             '2026-05-12 05:36:58'),
(2, 2, 1,  4, 'Very interesting read about the American Dream.',
             '2026-05-12 05:40:00'),
(3, 3, 2,  5, 'A must-read classic. Powerful and emotional.',
             '2026-05-12 05:45:00'),
(4, 4, 3,  4, 'Thought-provoking and intense. Very relevant today.',
             '2026-05-12 05:50:00'),
(5, 1, 4,  5, 'Inspirational and uplifting. Changed my perspective.',
             '2026-05-12 06:00:00'),
(6, 2, 5,  3, 'Good concepts but some parts felt repetitive.',
             '2026-05-12 07:48:58'),
(7, 3, 7,  5, 'A fantastic fantasy adventure from start to finish!',
             '2026-05-13 08:00:00'),
(8, 4, 10, 4, 'Interesting memoir. Very inspiring life story.',
             '2026-05-13 15:00:11');

COMMIT;