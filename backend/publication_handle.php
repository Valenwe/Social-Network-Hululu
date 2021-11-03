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
    $title = get_valid_str($_POST["title"]);
    $content = get_valid_str($_POST["content"]);

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
    $content = get_valid_str($_POST["content"]);

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
            $query = "INSERT INTO comments (post_id, id, content) VALUES ('$post_id', '$author_id', '" . $content . "')";
            mysqli_query($db, $query);

            // on récupère les informations du nouveau commentaire
            $query = "SELECT comment_id, modified, id FROM comments WHERE post_id=$post_id AND id=$author_id ORDER BY creation_date DESC LIMIT 1";
            $comment = mysqli_fetch_assoc(mysqli_query($db, $query));
            $comment_id = $comment["comment_id"];

            $currentDate = new DateTime();

            $response = "<div class='comment_section' id=$comment_id>";
            $response .= "<p class='comment_header'>By " . $author["username"] . " | " . $currentDate->format('Y-m-d H:i:s') . "</p>";

            if ($comment["modified"] == 1) $response .= "<p>Modified</p>";

            if ($comment["id"] == $_SESSION["id"]) {
                $response .= "<span class='delete_comment interactable' data-id=$comment_id>Delete </span>";
                $response .= "<span class='edit_comment interactable' data-id=$comment_id>Edit</span>";
            }

            $response .= "</br><p class='comment_content'>$content</p>";
            $response .= "</div>";

            // partie éditable du commentaire
            if ($comment["id"] == $_SESSION["id"]) {

                $response .= "<form method='post' class='edit_comment_form hide' id=$comment_id>
                        <div class='input-group'>
                            <input type='text' name='edit_content' placeholder='Text' value='$content'>
                        </div>
                        <div class='input-group'>
                            <button type='button' class='btn edit_comment_cancel'>Cancel</button>
                        </div>
                        <div class='input-group'>
                            <button type='submit' class='btn' name='edit'>Save</button>
                        </div>
                        </form>";
            }

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
    $content = get_valid_str($_POST["content"]);

    $query = "SELECT * FROM comments WHERE comment_id=$comment_id";
    $result = mysqli_query($db, $query);

    if ($result) {
        $comment = mysqli_fetch_assoc($result);
        if ($comment["id"] == $id) {
            $query = "UPDATE comments SET content ='" . $content . "', modified=1 WHERE comment_id=$comment_id";
            mysqli_query($db, $query);
        }
    }
}

$db_id = mysqli_thread_id($db);
mysqli_kill($db, $db_id);
