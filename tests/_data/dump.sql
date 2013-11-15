CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(60) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `registration_ip` int(11) unsigned DEFAULT NULL,
  `login_ip` int(11) unsigned DEFAULT NULL,
  `login_time` int(11) DEFAULT NULL,
  `confirmation_token` varchar(32) DEFAULT NULL,
  `confirmation_time` int(11) DEFAULT NULL,
  `confirmation_sent_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_unique` (`username`),
  UNIQUE KEY `email_unique` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `user` (`id`, `username`, `email`, `password_hash`, `auth_key`, `create_time`, `update_time`, `registration_ip`, `login_ip`, `login_time`, `confirmation_token`, `confirmation_time`, `confirmation_sent_time`) VALUES
(1, 'user', 'user@example.com', '$2y$13$qY.ImaYBppt66qez6B31QO92jc5DYVRzo5NxM1ivItkW74WsSG6Ui', '39HU0m5lpjWtqstFVGFjj6lFb7UZDeRq', 1383494773, 1383494773, NULL, NULL, NULL, NULL, 1383494773, NULL),
(2, 'unconfirmed', 'unconfirmed@example.com', '$2y$13$CIH1LSMPzU9xDCywt3QO8uovAu2axp8hwuXVa72oI.1G/USsGyMBS', 'mhh1A6KfqQLmHP-MiWN0WB0M90Q2u5OE', 1384517855, 1384517855, NULL, NULL, NULL, 'NO2aCmBIjFQX624xmAc3VBu7Th3NJoa6', NULL, 1384517855);