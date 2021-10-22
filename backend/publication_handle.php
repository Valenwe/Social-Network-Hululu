<?php
include "../backend/db.php";
session_start();

if (isset($_POST["delete"])) {
    $post_id = $_POST["post_id"];
    $query = "DELETE FROM publications WHERE post_id=$post_id";
    $result = mysqli_query($db, $query);
    array_push($_SESSION["success"], "Publication deleted");
}

if (isset($_POST["like"])) {
    $post_id = $_POST["post_id"];
    $id = $_SESSION["id"];
    $query = "UPDATE publications SET likes = CONCAT(likes, '$id ') WHERE post_id=$post_id";
    mysqli_query($db, $query);

    $result = mysqli_query($db, "SELECT * FROM publications WHERE post_id=$post_id");
    $post = mysqli_fetch_array($result);
    if (!empty($post['likes'])) {
        $array_likes = explode(" ", $post['likes']);
        if (empty(end($array_likes)))
            unset($array_likes[count($array_likes) - 1]);
            
        $n = count($array_likes);
    } else
        $n = 0;

    $db_id = mysqli_thread_id($db);
    mysqli_kill($db, $db_id);

    echo $n;
}

if (isset($_POST["dislike"])) {
    $post_id = $_POST["post_id"];
    $id = $_SESSION["id"];
    $query = "UPDATE publications SET likes = CONCAT(LEFT(likes, LOCATE('$id', likes) - 1), RIGHT(likes, LOCATE('$id', likes) - 2)) WHERE post_id=$post_id";
    mysqli_query($db, $query);

    $result = mysqli_query($db, "SELECT * FROM publications WHERE post_id=$post_id");
    $post = mysqli_fetch_array($result);
    if (!empty($post['likes'])) {
        $array_likes = explode(" ", $post['likes']);
        if (empty(end($array_likes)))
            unset($array_likes[count($array_likes) - 1]);

        $n = count($array_likes);
    } else
        $n = 0;

    $db_id = mysqli_thread_id($db);
    mysqli_kill($db, $db_id);

    echo $n;
}