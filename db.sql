SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS categories (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  added_by int(10) unsigned NOT NULL,
  title varchar(255) NOT NULL,
  description text NOT NULL,
  available_from int(10) unsigned NOT NULL DEFAULT '0',
  available_until int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS challenges (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  added_by int(10) unsigned NOT NULL,
  title varchar(255) NOT NULL,
  category smallint(5) unsigned NOT NULL,
  description text NOT NULL,
  available_from int(10) unsigned NOT NULL DEFAULT '0',
  available_until int(10) unsigned NOT NULL DEFAULT '0',
  flag varchar(255) NOT NULL,
  points int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS files (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  added_by int(10) unsigned NOT NULL,
  title varchar(255) NOT NULL,
  size int(10) unsigned NOT NULL,
  challenge int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS news (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  added_by int(10) unsigned NOT NULL,
  title varchar(255) NOT NULL,
  body text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS submissions (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  challenge int(10) unsigned NOT NULL,
  `user` int(10) unsigned NOT NULL,
  flag varchar(255) NOT NULL,
  correct tinyint(1) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS users (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  username varchar(128) NOT NULL,
  team_name varchar(255) NOT NULL,
  added int(10) unsigned NOT NULL,
  passhash char(64) NOT NULL,
  salt char(64) NOT NULL,
  class tinyint(4) NOT NULL DEFAULT '0',
  enabled tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (id),
  UNIQUE KEY username (username),
  UNIQUE KEY team_name (team_name)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
