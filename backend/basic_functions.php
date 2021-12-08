<?php

// https://www.writephponline.com/
function debug($msg, $exit)
{
    echo '<pre>';
    print_r($msg);
    if ($exit) {
        exit;
    }
}

// va retirer uniquement le premier patern trouvé
function str_replace_first($from, $to, $content, $offset = 0)
{
    $second_half = substr($content, $offset);
    $from = '/' . preg_quote($from, '/') . '/';

    $first_half = substr($content, 0, strlen($content) - strlen($second_half));

    return $first_half . preg_replace($from, $to, $second_half, 1);
}

// vérifie si on follow un user
function is_following($friend_id)
{
    if (!empty($_SESSION["following"])) {
        return in_array($friend_id, $_SESSION["following"]);
    }
    return false;
}

// vérifie si un user est un follower
function is_follower($friend_id)
{
    if (!empty($_SESSION["follower"])) {
        return in_array($friend_id, $_SESSION["follower"]);
    }
    return false;
}


// detecte si un string n'est pas utilisable pour une query sql
function is_str_valid($str)
{
    return preg_match("/^[a-zA-Z0-9-_-]*$/", $str) && $str == strip_tags($str);
}

// transforme un string en un string valide pour une requête sql
function get_valid_str($str)
{
    return preg_replace("~[^a-zA-Z0-9-_:]~i", "", $str);
}

// CRUDS functions
function find($table, $list_values, $limit = -1, $specific_column = array(), $comparator = "AND", $order = false, $value_in = false)
{
    require('../backend/db.php');

    $columns = "*";
    if (count($specific_column) > 0) {
        $columns = "";
        for ($i = 0; $i < count($specific_column) - 1; $i++) {
            $columns .= $specific_column[$i] . ", ";
        }
        $columns .= end($specific_column);
    }

    $query = "SELECT " . $columns . " FROM " . $table . " WHERE "; // Create query
    $n = 0;
    foreach ($list_values as $key => $value) {
        $value = addslashes($value);

        if ($n == 0)
            $n = 1;
        else
            $query = $query . " " . $comparator . " ";

        if (!$value_in)
            $query = $query . $key . " = '" . $value . "'";
        else
            $query = $query . $key . " IN " . $value;
    }
    if ($order)
        $query .= " ORDER BY creation_date DESC";

    if ($limit > 0)
        $query .= " LIMIT " . $limit;

    $result = mysqli_query($db, $query); // Execute query to database

    if (!$result) // Process result / error
        return null;

    $list = array();
    while ($row = mysqli_fetch_array($result)) {
        foreach ($row as $key => $value) {
            $value = stripslashes($value);
        }
        array_push($list, $row);
    }

    // get process id
    $db_id = mysqli_thread_id($db);
    // Kill connection
    mysqli_kill($db, $db_id);

    if ($limit == 1 && sizeof($list) > 0)
        return $list[0];
    else
        return $list;
}

function update($table, $list_values_to_update, $list_values)
{
    require('../backend/db.php');
    $query = "UPDATE " . $table . " SET "; // Create query
    $n = 0;
    foreach ($list_values_to_update as $key => $value) {
        // strip_tags remove any existing HTML tag
        $value = addslashes($value);
        $value = strip_tags($value);

        if ($n == 0) {
            $n = 1;
        } else {
            $query = $query . ",";
        }

        if ($key != null)
            $query = $query . $key . "= '" . $value . "'";
        else
            $query = $query . $key . "= null";
    }
    $query = $query . " WHERE ";
    $n = 0;
    foreach ($list_values as $key => $value) {
        $value = addslashes($value);
        if ($n == 0) {
            $n = 1;
        } else {
            $query = $query . " AND ";
        }
        $query = $query . $key . "='" . $value . "'";
    }
    //echo $query;
    $result = mysqli_query($db, $query);

    $db_id = mysqli_thread_id($db);
    mysqli_kill($db, $db_id);

    return $result;
}

function create($table, $list_values)
{
    require('../backend/db.php');
    $query = "INSERT INTO " . $table; // Create query
    $args = $values = "";
    foreach ($list_values as $key => $value) {
        // strip_tags remove any existing HTML tag
        $value = addslashes($value);
        $value = strip_tags($value);
        if ($args != "") {
            $args = $args . ",";
            $values = $values . ",";
        }
        $args = $args . $key;
        $values = $values . "'" . $value . "'";
    }
    $query = $query . " (" . $args . ") VALUES (" . $values . ")";
    // echo $query;
    $result = mysqli_query($db, $query);

    $db_id = mysqli_thread_id($db);
    mysqli_kill($db, $db_id);

    return $result;
}

function delete($table, $list_values)
{
    require('../backend/db.php');

    $query = "DELETE FROM " . $table . " WHERE "; // Create query
    $n = 0;
    foreach ($list_values as $key => $value) {
        $value = addslashes($value);

        if ($n == 0)
            $n = 1;
        else
            $query = $query . " AND ";

        $query = $query . $key . " = '" . $value . "'";
    }

    // echo $query;
    mysqli_query($db, $query); // Execute query to database

    $db_id = mysqli_thread_id($db);
    mysqli_kill($db, $db_id);
}

function special_find_query($query)
{
    require('../backend/db.php');

    $result = mysqli_query($db, $query); // Execute query to database

    if (!$result) // Process result / error
        return null;
    

    $list = array();
    while ($row = mysqli_fetch_array($result)) {
        foreach ($row as $key => $value) {
            $value = stripslashes($value);
        }
        array_push($list, $row);
    }

    $db_id = mysqli_thread_id($db);
    mysqli_kill($db, $db_id);

    return $list;
}

function special_query($query) {
    require('../backend/db.php');

    mysqli_query($db, $query);
    
    $db_id = mysqli_thread_id($db);
    mysqli_kill($db, $db_id);
}