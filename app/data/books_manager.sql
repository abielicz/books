CREATE TABLE IF NOT EXISTS books (
         `book_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
         `title` varchar(128) not null,
         `author` varchar(128) not null,
         primary key(`book_id`),
          UNIQUE INDEX `UQ_books_1` (`title`, `author` ASC)
  )ENGINE = InnoDB;
  
  CREATE TABLE IF NOT EXISTS tags (
		`tag_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` varchar(128) not null,
         primary key(`tag_id`),
		 UNIQUE INDEX `UQ_tags_1` (`name` ASC)
   )ENGINE = InnoDB;   

  CREATE TABLE IF NOT EXISTS books_tags (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `book_id` INT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `FK_books_tags_1` (`book_id` ASC),
  INDEX `FK_books_tags_2` (`tag_id` ASC),
  UNIQUE INDEX `UQ_books_tags_1` (`book_id` ASC, `tag_id` ASC),
  CONSTRAINT `FK_books_tags_1`
    FOREIGN KEY (`book_id`)
    REFERENCES `books` (`book_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_books_tags_2`
    FOREIGN KEY (`tag_id`)
    REFERENCES `tags` (`tag_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;