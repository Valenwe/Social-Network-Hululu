<?php
include "../backend/db.php";
session_start();

if (isset($_POST["delete"])) {
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
        $post = mysqli_fetch_array($result);
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
        $post = mysqli_fetch_array($result);
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

if (isset($_POST["edit"])) {
    $id = $_SESSION["id"];
    $post_id = $_POST["post_id"];
    $title = $_POST["title"];
    $content = $_POST["content"];

    $query = "SELECT * FROM publications WHERE post_id='$post_id'";
    $result = mysqli_query($db, $query);

    if ($result) {
        $post = mysqli_fetch_assoc($result);
        if ($post["id"] == $id) {
            $query = "UPDATE publications SET title='" . $title . "', content ='" . $content . "', modified=1 WHERE post_id = '$post_id'";
            mysqli_query($db, $query);
        }
    }
}

if (isset($_POST["add_comment"])) {
    $id = $_SESSION["id"];
    $post_id = $_POST["post_id"];
    $content = $_POST["content"];

    $query = "SELECT * FROM publications WHERE post_id='$post_id'";
    $result = mysqli_query($db, $query);

    if ($result) {
        $post = mysqli_fetch_assoc($result);
        $post_id = $post["post_id"];
        $author_id = $post["id"];

        // checker si l'utilisateur est autorisé à commenter
        $query = "SELECT * FROM users WHERE id='$author_id'";
        $author = mysqli_fetch_assoc(mysqli_query($db, $query));
        $followers = explode(" ", $author["follower"]);
        // dans le cas où on commente sa propre publication
        array_push($followers, $author["id"]);

        // si l'utilisateur est un follower de l'auteur
        if (in_array($id, $followers)) {
            $query = "INSERT INTO comments (post_id, id, content, likes) VALUES ($post_id, $author_id, '" . $content . "', '')";
            // mysqli_query($db, $query);

            $query = "SELECT comment_id FROM comments WHERE post_id=$post_id AND id=$author_id ORDER BY creation_date DESC LIMIT 1";
            $comment_id = 1; //mysqli_fetch_assoc(mysqli_query($db, $query))["comment_id"];

            $currentDate = new DateTime();
            $response = "<div class='comment_section' id=$comment_id>";
            $response .= "<p>By " . $author["username"] . " | " . $currentDate->format('Y-m-d H:i:s') . "</br>$content</p>";
            $response .= "</div>";

            echo $response;
        }
    }
}

$db_id = mysqli_thread_id($db);
mysqli_kill($db, $db_id);
