# social-network
## Web project

## Members:
Victor
Cedric
Michael
Baptiste
Louis

## How to install the project:

You first need to install XAMPP, 
Then go to the Admin page of MySQL (localhost/phpmyadmin/), and create a new database names 'registration', and a new table with that SQL request:
'
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `creation_date` date NOT NULL DEFAULT current_timestamp(),
  `following` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `followers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1
'

Then go in its root folder, and create a new directory inside /htdocs/
After that, paste the ZIP content of the repository, and then the website will be available at localhost/%your_directory_name%/register.php
