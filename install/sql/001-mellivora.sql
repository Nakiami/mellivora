SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE categories (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  added_by int(10) unsigned NOT NULL,
  title varchar(255) NOT NULL,
  description text NOT NULL,
  exposed tinyint(1) NOT NULL DEFAULT '1',
  available_from int(10) unsigned NOT NULL DEFAULT '0',
  available_until int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE challenges (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  added_by int(10) unsigned NOT NULL,
  title varchar(255) NOT NULL,
  category smallint(5) unsigned NOT NULL,
  description text NOT NULL,
  exposed tinyint(1) NOT NULL DEFAULT '1',
  available_from int(10) unsigned NOT NULL DEFAULT '0',
  available_until int(10) unsigned NOT NULL DEFAULT '0',
  flag text NOT NULL,
  case_insensitive tinyint(1) NOT NULL DEFAULT '1',
  automark tinyint(1) NOT NULL DEFAULT '1',
  points int(10) unsigned NOT NULL DEFAULT '0',
  num_attempts_allowed tinyint(3) unsigned NOT NULL DEFAULT '0',
  min_seconds_between_submissions smallint(5) unsigned NOT NULL DEFAULT '0',
  relies_on int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (id),
  KEY category (category)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE cookie_tokens (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  user_id int(10) unsigned NOT NULL,
  token_series char(16) NOT NULL,
  token char(64) NOT NULL,
  ip_created int(10) unsigned NOT NULL,
  ip_last int(10) unsigned NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY user_t_ts (user_id,token,token_series)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE countries (
  id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  country_name varchar(50) NOT NULL DEFAULT '',
  country_code char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (id),
  UNIQUE KEY short (country_code)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE dynamic_pages (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(255) NOT NULL,
  body text NOT NULL,
  visibility enum('public','private','both') NOT NULL DEFAULT 'public',
  min_user_class tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE dynamic_menu (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(255) NOT NULL,
  permalink varchar(255) NOT NULL,
  internal_page int(10) unsigned NOT NULL,
  url varchar(255) NOT NULL,
  visibility enum('public','private','both') NOT NULL,
  min_user_class tinyint(4) NOT NULL DEFAULT '0',
  priority smallint(5) unsigned NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY permalink (permalink)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE exceptions (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  added_by int(10) unsigned NOT NULL,
  message varchar(255) NOT NULL,
  `code` varchar(10) NOT NULL,
  trace text NOT NULL,
  `file` varchar(255) NOT NULL,
  line int(10) unsigned NOT NULL,
  user_ip int(10) unsigned NOT NULL,
  user_agent text NOT NULL,
  unread BOOLEAN NOT NULL DEFAULT TRUE,
PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE files (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  added_by int(10) unsigned NOT NULL,
  title varchar(255) NOT NULL,
  size int(10) unsigned NOT NULL,
  md5 char(32) NOT NULL,
  download_key char(64) NOT NULL,
  challenge int(10) unsigned NOT NULL,
  file_type enum('local','remote') NOT NULL DEFAULT 'local',
  PRIMARY KEY (id),
  KEY challenge (challenge),
  UNIQUE KEY (download_key)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE hints (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  challenge int(10) unsigned NOT NULL,
  added int(10) unsigned NOT NULL,
  added_by int(10) unsigned NOT NULL,
  visible tinyint(1) NOT NULL DEFAULT '0',
  body text NOT NULL,
  PRIMARY KEY (id),
  KEY challenge (challenge)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE interest (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  secret char(40) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY email (email)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE ip_log (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(10) unsigned NOT NULL,
  added int(10) unsigned NOT NULL,
  last_used int(10) unsigned NOT NULL,
  ip int(10) unsigned NOT NULL,
  times_used int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (id),
  UNIQUE KEY user_ip (user_id,ip)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE news (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  added_by int(10) unsigned NOT NULL,
  title varchar(255) NOT NULL,
  body text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE recruit (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  user_id int(10) unsigned NOT NULL,
  email varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  city varchar(255) NOT NULL,
  country char(2) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE reset_password (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  user_id int(10) unsigned NOT NULL,
  ip int(10) unsigned NOT NULL,
  auth_key char(64) NOT NULL,
  PRIMARY KEY (id),
  KEY user_key (user_id,auth_key)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE restrict_email (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  added_by int(11) NOT NULL,
  rule varchar(255) NOT NULL,
  enabled tinyint(1) NOT NULL DEFAULT '1',
  white tinyint(1) NOT NULL DEFAULT '1',
  priority int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE submissions (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  added int(10) unsigned NOT NULL,
  challenge int(10) unsigned NOT NULL,
  user_id int(10) unsigned NOT NULL,
  flag text NOT NULL,
  correct tinyint(1) NOT NULL DEFAULT '0',
  marked tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY challenge (challenge),
  KEY user_id (user_id),
  KEY challenge_user_id (challenge,user_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE two_factor_auth (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(10) unsigned NOT NULL,
  secret char(32) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY user_id (user_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE users (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  email varchar(255) NOT NULL,
  team_name varchar(255) NOT NULL,
  added int(10) unsigned NOT NULL,
  last_active int(10) unsigned NOT NULL DEFAULT '0',
  passhash varchar(255) NOT NULL,
  download_key char(64) NOT NULL,
  class tinyint(4) NOT NULL DEFAULT '0',
  enabled tinyint(1) NOT NULL DEFAULT '1',
  user_type tinyint(3) unsigned NOT NULL DEFAULT '0',
  competing tinyint(1) NOT NULL DEFAULT '1',
  country_id smallint(5) unsigned NOT NULL,
  2fa_status enum('disabled','generated','enabled') NOT NULL DEFAULT 'disabled',
  PRIMARY KEY (id),
  UNIQUE KEY email (email),
  UNIQUE KEY team_name (team_name),
  UNIQUE KEY (download_key)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE user_types (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(255) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
