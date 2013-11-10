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

INSERT INTO `user` (`id`, `username`, `email`, `password_hash`, `auth_key`, `create_time`, `update_time`) VALUES
(1, 'user', 'user@example.com', '$2y$13$qY.ImaYBppt66qez6B31QO92jc5DYVRzo5NxM1ivItkW74WsSG6Ui', '39HU0m5lpjWtqstFVGFjj6lFb7UZDeRq', 1383494773, 1383494773);