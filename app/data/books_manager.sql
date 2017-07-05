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

CREATE TABLE IF NOT EXISTS roles (
		`role_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` varchar(128) not null,
         primary key(`role_id`),
		 UNIQUE INDEX `UQ_roles_1` (`name` ASC)
)ENGINE = InnoDB; 

CREATE TABLE IF NOT EXISTS `users` (
		`user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `login` varchar(45) not null,
        `password` varchar(255) not null,
        `role_id` INT UNSIGNED NOT NULL,
         primary key(`user_id`),
         INDEX `IX_users_1` (`role_id` ASC),
		 UNIQUE INDEX `UQ_users_1` (`login` ASC),
		 CONSTRAINT `FK_users_1`
			FOREIGN KEY (`role_id`)
			REFERENCES `roles` (`role_id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION
)ENGINE = InnoDB; 

CREATE TABLE IF NOT EXISTS `users_data` (
		`user_id` INT UNSIGNED NOT NULL,
        `firstname` varchar(45),
        `surname` varchar(45),
        `email` varchar(45),
         primary key(`user_id`)
)ENGINE = InnoDB; 

CREATE TABLE IF NOT EXISTS `photos` (
  `photo_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `book_id` INT UNSIGNED NOT NULL,
  `photo` VARCHAR(128) NOT NULL,
  PRIMARY KEY (`photo_id`)
  )ENGINE = InnoDB; 
  
  CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `book_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `matter` VARCHAR(128) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT `(getdate())`,
  PRIMARY KEY (`comment_id`)
  )ENGINE = InnoDB; 