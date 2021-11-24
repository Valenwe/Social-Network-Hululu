<?php
include "../backend/functions.php";
session_start();

if (isset($_POST["delete_post"])) {
    $id = $_SESSION["id"];
    $post_id = $_POST["post_id"];

    $pub = find("publications", array("post_id" => $post_id), 1);

    if ($pub["id"] == $id) {
        delete("publications", array("post_id" => $post_id));
        // array_push($_SESSION["success"], "Publication deleted");
    }
}

if (isset($_POST["like"])) {
    $id = $_SESSION["id"];
    $post_id = $_POST["post_id"];

    $pub = find("publications", array("post_id" => $post_id), 1);

    if (strpos($pub["likes"], $id) == false) {

        $query = "UPDATE publications SET likes = CONCAT(likes, '$id ') WHERE post_id=$post_id";
        special_query($query);

        $post = find("publications", array("post_id" => $post_id), 1);
        if (!empty($post['likes'])) {
            $array_likes = explode(" ", $post['likes']);
            if (empty(end($array_likes)))
                unset($array_likes[count($array_likes) - 1]);

            $n = count($array_likes);
        } else
            $n = 0;

        echo $n;
    }
}

if (isset($_POST["dislike"])) {
    $post_id = $_POST["post_id"];
    $id = $_SESSION["id"];

    $pub = find("publications", array("post_id" => $post_id), 1);

    if (strpos($pub["likes"], $id) == false) {
        $query = "UPDATE publications SET likes = CONCAT(LEFT(likes, LOCATE('$id', likes) - 1), RIGHT(likes, LOCATE('$id', likes) - 2)) WHERE post_id=$post_id";
        special_query($query);

        $post = find("publications", array("post_id" => $post_id), 1);
        if (!empty($post['likes'])) {
            $array_likes = explode(" ", $post['likes']);
            if (empty(end($array_likes)))
                unset($array_likes[count($array_likes) - 1]);

            $n = count($array_likes);
        } else
            $n = 0;

        echo $n;
    }
}

if (isset($_POST["edit_post"])) {
    $id = $_SESSION["id"];
    $post_id = $_POST["post_id"];
    $title = $_POST["title"];
    $content = $_POST["content"];

    $post = find("publications", array("post_id" => $post_id), 1);

    if ($post["id"] == $id) {
        update("publications", array("title" => $title, "content" => $content, "modified" => 1), array("post_id" => $post_id));
    }

    $post = find("publications", array("post_id" => $post_id), 1);

    $publications = array();
    array_push($publications, $post);
    echo display_publications($publications, true);
}

if (isset($_POST["add_comment"])) {
    $author_id = $_SESSION["id"];
    $post_id = $_POST["post_id"];
    $content = $_POST["content"];

    $post = find("publications", array("post_id" => $post_id), 1);

    // checker si l'utilisateur est autorisé à commenter
    $author = find("users", array("id" => $author_id), 1);

    $followers = explode(" ", $author["follower"]);
    // dans le cas où on commente sa propre publication
    array_push($followers, $author["id"]);

    // si l'utilisateur est un follower de l'auteur
    if (in_array($author_id, $followers)) {
        create("comments", array("post_id" => $post_id, "id" => $author_id, "content" => $content));

        // on récupère les informations du nouveau commentaire
        $comment = find("comments", array("post_id" => $post_id, "id" => $author_id), 1, array(), "AND", true);

        $response = display_comment($comment);
        echo $response;
    }
}

if (isset($_POST["delete_comment"])) {
    $id = $_SESSION["id"];
    $comment_id = $_POST["comment_id"];

    $comment = find("comments", array("comment_id" => $comment_id), 1);

    if ($comment["id"] == $id) {
        delete("comments", array("comment_id" => $comment_id));
    }
}

if (isset($_POST["edit_comment"])) {
    $id = $_SESSION["id"];
    $comment_id = $_POST["comment_id"];
    $content = $_POST["content"];

    $comment = find("comments", array("comment_id" => $comment_id), 1);
    if ($comment["id"] == $id) {
        update("comments", array("content" => $content, "modified" => 1), array("comment_id" => $comment_id));
    }

    $comment = find("comments", array("comment_id" => $comment_id), 1);
    echo display_comment($comment);
}
