# Web project
# social-network

## Members:
* Victor
* Cedric
* Michael
* Baptiste
* Louis

## How to install the project:

* You first need to install XAMPP, 
* Then go to the Admin page of MySQL (`localhost/phpmyadmin/`), and execute those SQL requests:
```
CREATE DATABASE `hululu`;
```
And inside that new database, these aswell:
```
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) CHARACTER SET latin1 NOT NULL,
  `email` varchar(100) CHARACTER SET latin1 NOT NULL,
  `password` varchar(100) CHARACTER SET latin1 NOT NULL,
  `firstname` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `lastname` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `following` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `follower` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `publications` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `likes` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `modified` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`post_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `modified` int(11) NOT NULL DEFAULT 0,
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

* Then go in its root folder, and create a new directory inside `/htdocs/`, called `sn`
* After that, paste the ZIP content of the repository inside that new directory.

* Then, go in `/apache/conf`, and change the file `httpd.conf`:
--> Remove the '#' from `#LoadModule rewrite_module modules/mod_rewrite.so`
--> Change all `AllowOverride None` to `AllowOverride All`

* Also, create a .htaccess file in the htdocs folder, and add the following content:
```
RewriteEngine on
RewriteBase /sn/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ url.php [QSA,L]
```

The website should now be available at `localhost/register`.

## To-Do list:
* Avatar image system for each user
* Auto refresh pages (Ajax query?) for new notifications
* Friend system
* Private messaging system
* Publication system
* Admin remote control panel
