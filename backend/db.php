<?php

// connect to the database
// root -> no password
// mysql.exe -u root -p
$db = mysqli_connect('localhost', 'root', '', 'hululu');

if (!$db) die("Connection failed: " . mysqli_connect_error());
