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
