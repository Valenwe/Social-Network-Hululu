<?php
include "../backend/db.php";
include "../backend/functions.php";
session_start();

if (isset($_POST["delete_post"])) {
    $id = $_SESSION["id"];
    $post_id = $_POST["post_id"];

    $query = "SELECT * FROM publications WHERE post_id=$post_id";
    $pub = mysqli_fetch_assoc(mysqli_query($db, $query));

    if ($pub["id"] == $id) {
        $query = "DELETE FROM publications WHERE post_id=$post_id";
        $result = mysqli_query($db, $query);
        // array_push($_SESSION["success"], "Publication deleted");
    }
}

if (isset($_POST["like"])) {
    $id = $_SESSION["id"];
    $post_id = $_POST["post_id"];

    $query = "SELECT * FROM publications WHERE post_id=$post_id";
    $pub = mysqli_fetch_assoc(mysqli_query($db, $query));

    if (strpos($pub["likes"], $id) == false) {


        $query = "UPDATE publications SET likes = CONCAT(likes, '$id ') WHERE post_id=$post_id";
        mysqli_query($db, $query);

        $result = mysqli_query($db, "SELECT * FROM publications WHERE post_id=$post_id");
        $post = mysqli_fetch_assoc($result);
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

    $query = "SELECT * FROM publications WHERE post_id=$post_id";
    $pub = mysqli_fetch_assoc(mysqli_query($db, $query));


    if (strpos($pub["likes"], $id) == false) {
        $query = "UPDATE publications SET likes = CONCAT(LEFT(likes, LOCATE('$id', likes) - 1), RIGHT(likes, LOCATE('$id', likes) - 2)) WHERE post_id=$post_id";
        mysqli_query($db, $query);

        $result = mysqli_query($db, "SELECT * FROM publications WHERE post_id=$post_id");
        $post = mysqli_fetch_assoc($result);
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
    $title = addslashes($_POST["title"]);
    $content = addslashes($_POST["content"]);

    $query = "SELECT * FROM publications WHERE post_id='$post_id'";
    $result = mysqli_query($db, $query);

    if ($result) {
        $post = mysqli_fetch_assoc($result);
        if ($post["id"] == $id) {
            $query = "UPDATE publications SET title='" . $title . "', content ='" . $content . "', modified=1 WHERE post_id = '$post_id'";
            mysqli_query($db, $query);
        }

        $query = "SELECT * FROM publications WHERE post_id='$post_id'";
        $post = mysqli_fetch_assoc(mysqli_query($db, $query));

        $publications = array();
        array_push($publications, $post);
        echo display_publications($publications, true);
    }
}

if (isset($_POST["add_comment"])) {
    $author_id = $_SESSION["id"];
    $post_id = $_POST["post_id"];
    $content = addslashes(strip_tags($_POST["content"]));

    $query = "SELECT * FROM publications WHERE post_id='$post_id'";
    $result = mysqli_query($db, $query);

    if ($result) {
        $post = mysqli_fetch_assoc($result);
        $post_id = $post["post_id"];

        // checker si l'utilisateur est autorisé à commenter
        $query = "SELECT * FROM users WHERE id='$author_id'";
        $author = mysqli_fetch_assoc(mysqli_query($db, $query));
        $followers = explode(" ", $author["follower"]);
        // dans le cas où on commente sa propre publication
        array_push($followers, $author["id"]);

        // si l'utilisateur est un follower de l'auteur
        if (in_array($author_id, $followers)) {
            $query = "INSERT INTO comments (post_id, id, content) VALUES ('$post_id', '$author_id', '" . $content . "')";
            mysqli_query($db, $query);

            // on récupère les informations du nouveau commentaire
            $query = "SELECT * FROM comments WHERE post_id=$post_id AND id=$author_id ORDER BY creation_date DESC LIMIT 1";
            $comment = mysqli_fetch_assoc(mysqli_query($db, $query));

            $response = display_comment($comment, $db);
            echo $response;
        }
    }
}

if (isset($_POST["delete_comment"])) {
    $id = $_SESSION["id"];
    $comment_id = $_POST["comment_id"];

    $query = "SELECT * FROM comments WHERE comment_id=$comment_id";
    $comment = mysqli_fetch_assoc(mysqli_query($db, $query));

    if ($comment["id"] == $id) {
        $query = "DELETE FROM comments WHERE comment_id=$comment_id";
        $result = mysqli_query($db, $query);
    }
}

if (isset($_POST["edit_comment"])) {
    $id = $_SESSION["id"];
    $comment_id = $_POST["comment_id"];
    $content = addslashes($_POST["content"]);

    $query = "SELECT * FROM comments WHERE comment_id=$comment_id";
    $result = mysqli_query($db, $query);

    if ($result) {
        $comment = mysqli_fetch_assoc($result);
        if ($comment["id"] == $id) {
            $query = "UPDATE comments SET content ='" . $content . "', modified=1 WHERE comment_id=$comment_id";
            mysqli_query($db, $query);
        }

        $query = "SELECT * FROM comments WHERE comment_id=$comment_id";
        $comment = mysqli_fetch_assoc(mysqli_query($db, $query));
        echo display_comment($comment, $db);
    }
}

$db_id = mysqli_thread_id($db);
mysqli_kill($db, $db_id);
