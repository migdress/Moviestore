SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `moviestore` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `moviestore` ;

-- -----------------------------------------------------
-- Table `moviestore`.`Movie`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `moviestore`.`Movie` (
  `movie_id` INT NOT NULL AUTO_INCREMENT,
  `movie_name` VARCHAR(30) NOT NULL,
  `movie_desc` VARCHAR(30) NOT NULL,
  `movie_price` INT NOT NULL,
  `movie_imagePath` VARCHAR(30) NULL,
  PRIMARY KEY (`movie_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `moviestore`.`Genre`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `moviestore`.`Genre` (
  `genre_id` INT NOT NULL AUTO_INCREMENT,
  `genre_name` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`genre_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `moviestore`.`Movie_has_Genre`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `moviestore`.`Movie_has_Genre` (
  `Movie_movie_id` INT NOT NULL,
  `Genre_genre_id` INT NOT NULL,
  PRIMARY KEY (`Movie_movie_id`, `Genre_genre_id`),
  INDEX `fk_Movie_has_Genre_Genre1_idx` (`Genre_genre_id` ASC),
  INDEX `fk_Movie_has_Genre_Movie_idx` (`Movie_movie_id` ASC),
  CONSTRAINT `fk_Movie_has_Genre_Movie`
    FOREIGN KEY (`Movie_movie_id`)
    REFERENCES `moviestore`.`Movie` (`movie_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Movie_has_Genre_Genre1`
    FOREIGN KEY (`Genre_genre_id`)
    REFERENCES `moviestore`.`Genre` (`genre_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `moviestore`.`User`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `moviestore`.`User` (
  `user_id` INT NOT NULL,
  `user_name` VARCHAR(30) NOT NULL,
  `user_lastname` VARCHAR(30) NOT NULL,
  `user_username` VARCHAR(30) NOT NULL,
  `user_password` VARCHAR(80) NOT NULL,
  `user_email` VARCHAR(45) NOT NULL,
  `user_isActive` TINYINT(1) NOT NULL,
  PRIMARY KEY (`user_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `moviestore`.`Purchase`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `moviestore`.`Purchase` (
  `purchase_id` INT NOT NULL,
  `User_user_id` INT NOT NULL,
  `Movie_movie_id` INT NOT NULL,
  `purchase_date` DATE NOT NULL,
  PRIMARY KEY (`purchase_id`, `User_user_id`, `Movie_movie_id`),
  INDEX `fk_User_has_Movie_Movie1_idx` (`Movie_movie_id` ASC),
  INDEX `fk_User_has_Movie_User1_idx` (`User_user_id` ASC),
  CONSTRAINT `fk_User_has_Movie_User1`
    FOREIGN KEY (`User_user_id`)
    REFERENCES `moviestore`.`User` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_User_has_Movie_Movie1`
    FOREIGN KEY (`Movie_movie_id`)
    REFERENCES `moviestore`.`Movie` (`movie_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `moviestore`.`Role`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `moviestore`.`Role` (
  `role_id` INT NOT NULL,
  `role_string` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`role_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `moviestore`.`User_has_Role`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `moviestore`.`User_has_Role` (
  `User_user_id` INT NOT NULL,
  `Role_role_id` INT NOT NULL,
  PRIMARY KEY (`User_user_id`, `Role_role_id`),
  INDEX `fk_User_has_Role_Role1_idx` (`Role_role_id` ASC),
  INDEX `fk_User_has_Role_User1_idx` (`User_user_id` ASC),
  CONSTRAINT `fk_User_has_Role_User1`
    FOREIGN KEY (`User_user_id`)
    REFERENCES `moviestore`.`User` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_User_has_Role_Role1`
    FOREIGN KEY (`Role_role_id`)
    REFERENCES `moviestore`.`Role` (`role_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;



-- Inserting Admin User --
insert into Role(role_id, role_string) values(1, "ROLE_ADMIN");
insert into Role(role_id, role_string) values(2, "ROLE_USER");

insert into User(user_id, user_name, user_lastname, user_username, user_password, user_email, user_isActive)
	values(1, "admin", "", "admin", "$2y$13$xF9cg5JbK46pED0GH415Fe6BGc89sraHM7RNfeOJeMa...", "", true);

insert into User(user_id, user_name, user_lastname, user_username, user_password, user_email, user_isActive)
  values(2, "Jhon", "Doe", "jdoe", "$2y$13$F6d6c89xJlKu.9AnCvHI4OD.N34rtWqXfDl3WDUDuh8g6.dPtX/x.", "", true);

insert into User_has_Role(User_user_id, Role_role_id) values(1,1);
insert into User_has_Role(User_user_id, Role_role_id) values(2,2);


