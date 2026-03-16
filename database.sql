CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `activiteiten` (
  `activiteit_id` int(11) NOT NULL AUTO_INCREMENT,
  `activiteit_titel` varchar(255) NOT NULL,
  `activiteit_beschrijving` text NOT NULL,
  `activiteit_datum` date NOT NULL,
  `activiteit_tijd` time NOT NULL,
  `activiteit_locatie` varchar(255) NOT NULL,
  `soort_activiteit` enum('binnen','buiten') NOT NULL,
  `activiteit_status` enum('gepland','geannuleerd','voltooid') NOT NULL DEFAULT 'gepland',
  `activiteit_opmerkingen` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`activiteit_id`),
  KEY `fk_activiteiten_user` (`user_id`),
  CONSTRAINT `fk_activiteiten_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `weer` (
  `weer_id` int(11) NOT NULL AUTO_INCREMENT,
  `activiteit_id` int(11) NOT NULL,
  `temperatuur` decimal(4,1) DEFAULT NULL,
  `weersomschrijving` varchar(255) DEFAULT NULL,
  `wind` decimal(4,1) DEFAULT NULL,
  `neerslag_kans` int(3) DEFAULT NULL,
  PRIMARY KEY (`weer_id`),
  KEY `fk_weer_activiteit` (`activiteit_id`),
  CONSTRAINT `fk_weer_activiteit`
    FOREIGN KEY (`activiteit_id`) REFERENCES `activiteiten` (`activiteit_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
