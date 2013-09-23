SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE categories (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  added_by int(10) unsigned NOT NULL,
  title varchar(255) NOT NULL,
  description text NOT NULL,
  available_from int(10) unsigned NOT NULL DEFAULT '0',
  available_until int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE challenges (
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
  num_attempts_allowed tinyint(3) unsigned NOT NULL DEFAULT '5',
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE files (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  added_by int(10) unsigned NOT NULL,
  title varchar(255) NOT NULL,
  size int(10) unsigned NOT NULL,
  challenge int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE hints (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  challenge int(10) unsigned NOT NULL,
  added int(10) unsigned NOT NULL,
  added_by int(10) unsigned NOT NULL,
  body text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE ip_log (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(10) unsigned NOT NULL,
  added int(10) unsigned NOT NULL,
  last_used int(10) unsigned NOT NULL,
  ip int(10) unsigned NOT NULL,
  times_used int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (id),
  UNIQUE KEY user_ip (user_id,ip)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE news (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  added_by int(10) unsigned NOT NULL,
  title varchar(255) NOT NULL,
  body text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE restrict_email (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  added_by int(11) NOT NULL,
  rule varchar(255) NOT NULL,
  enabled tinyint(1) NOT NULL DEFAULT '1',
  white tinyint(1) NOT NULL DEFAULT '1',
  priority int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE submissions (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  challenge int(10) unsigned NOT NULL,
  user_id int(10) unsigned NOT NULL,
  flag varchar(255) NOT NULL,
  correct tinyint(1) NOT NULL,
  pos int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE users (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  email varchar(255) NOT NULL,
  team_name varchar(255) NOT NULL,
  added int(10) unsigned NOT NULL,
  passhash char(64) NOT NULL,
  salt char(64) NOT NULL,
  class tinyint(4) NOT NULL DEFAULT '0',
  enabled tinyint(1) NOT NULL DEFAULT '1',
  `type` enum('uni','hs','tafe') NOT NULL DEFAULT 'uni',
  PRIMARY KEY (id),
  UNIQUE KEY username (email),
  UNIQUE KEY team_name (team_name)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
