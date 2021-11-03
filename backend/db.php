<?php

// connect to the database
// root -> password
// mysql.exe -u root --password
$db = mysqli_connect('localhost', 'root', 'root', 'hululu');

if (!$db) die("Connection failed: " . mysqli_connect_error());
