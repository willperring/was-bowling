-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema bowling
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema bowling
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `bowling` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `bowling` ;

-- -----------------------------------------------------
-- Table `bowling`.`player`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bowling`.`player` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `handle` VARCHAR(15) NOT NULL,
  `gamecount` INT UNSIGNED NOT NULL DEFAULT 1,
  `modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `handle_UNIQUE` (`handle` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bowling`.`game`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bowling`.`game` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bowling`.`bowl`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bowling`.`bowl` (
  `gameid` INT UNSIGNED NOT NULL,
  `playerid` INT UNSIGNED NOT NULL,
  `frame` INT UNSIGNED NOT NULL,
  `bowl` INT UNSIGNED NOT NULL,
  `count` INT NULL,
  `created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gameid`, `playerid`, `frame`, `bowl`),
  INDEX `fk_bowlUser_idx` (`playerid` ASC),
  INDEX `fk_bowlGame_idx` (`gameid` ASC),
  CONSTRAINT `fk_bowlPlayer`
    FOREIGN KEY (`playerid`)
    REFERENCES `bowling`.`player` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_bowlGame`
    FOREIGN KEY (`gameid`)
    REFERENCES `bowling`.`game` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
