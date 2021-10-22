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

// on attribue toutes les valeurs de la session (pour du local)
function set_session_value($user)
{
    $_SESSION['username'] = $user['username'];
    $_SESSION['id'] = $user['id'];
    $_SESSION['email'] = $user['email'];

    $_SESSION['follower'] = array();
    $_SESSION['following'] = array();

    if (!is_null($user['firstname'])) {
        $_SESSION['firstname'] = $user['firstname'];
    }
    if (!is_null($user['lastname'])) {
        $_SESSION['lastname'] = $user['lastname'];
    }
    if (!empty($user['follower'])) {
        $_SESSION['follower'] = explode(" ", $user['follower']);
    }
    if (!empty($user['following'])) {
        $_SESSION['following'] = explode(" ", $user['following']);
    }
}

// check si quelqu'un ne possède pas toutes les variables de session
function check_session_variables()
{
    if (!isset($_SESSION["username"]) || !isset($_SESSION["id"]) || !isset($_SESSION["email"]) || !isset($_SESSION["following"]) || !isset($_SESSION["follower"])) {

        $_SESSION["errors"] = array();
        array_push($_SESSION["errors"], "You must be logged in first");
        header('location: /login');
    }
}

// va retirer uniquement le premier patern trouvé
function str_replace_first($from, $to, $content)
{
    $from = '/' . preg_quote($from, '/') . '/';
    return preg_replace($from, $to, $content, 1);
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

// ajoute un follow d'un user
function add_follow($db, $friend)
{
    $new_following = array();
    $new_follower = array();
    if (!empty($_SESSION["following"])) {
        $new_following = $_SESSION["following"];
    }
    $friend_id = $friend["id"];
    if (!is_following($friend_id)) {
        // on change le user target
        if (!empty($friend["follower"])) {
            $new_follower = $friend["follower"];
        }
        array_push($new_follower, $_SESSION["id"]);
        $str_new_follower = implode(" ", $new_follower);
        $query = "UPDATE users SET follower = '$str_new_follower' WHERE id='$friend_id'";
        mysqli_query($db, $query);

        // on change notre compte user
        array_push($new_following, $friend_id);
        $str_new_following = implode(" ", $new_following);
        $id = $_SESSION["id"];
        $query = "UPDATE users SET following = '$str_new_following' WHERE id='$id'";
        mysqli_query($db, $query);
        $_SESSION["following"] = $new_following;
        return true;
    }
    return false;
}

// retire un follow
function remove_follow($db, $friend)
{
    $new_following = array();
    $new_follower = array();
    if (!empty($_SESSION["following"])) {
        $new_following = $_SESSION["following"];
    }
    $friend_id = $friend["id"];
    if (!is_following($friend_id)) {
        // on change le user target
        if (!empty($friend["follower"])) {
            $new_follower = $friend["follower"];
        }
        unset($new_follower[array_search($_SESSION["id"], $new_follower)]);
        $str_new_follower = implode(" ", $new_follower);
        $query = "UPDATE users SET follower = '$str_new_follower' WHERE id='$friend_id'";
        mysqli_query($db, $query);

        // on change notre compte user
        unset($new_following[array_search($friend_id, $new_following)]);
        $str_new_following = implode(" ", $new_following);
        $id = $_SESSION["id"];
        $query = "UPDATE users SET following = '$str_new_following' WHERE id='$id'";
        mysqli_query($db, $query);
        $_SESSION["following"] = $new_following;
        return true;
    }
    return false;
}

// actualise les valeurs d'un utilisateur
function refresh_user($db, $user)
{
    $id = $user["id"];
    $query = "SELECT * FROM users WHERE id='$id'";
    $result = mysqli_query($db, $query);

    if ($result) {
        $target =  mysqli_fetch_assoc($result);
        return $target;
    }
}

// poste une publication
// '" . $title . "' pour prendre en compte les espaces
// addslashes permet de gérer les caractères problématiques comme ' \ " 
function post($id, $title, $content)
{
    include "../backend/db.php";

    $title = addslashes($title);
    $content = addslashes($content);

    $query = "INSERT INTO publications (id, title, content, comments, likes) VALUES('$id', '" . $title . "', '" . $content . "', '', '')";
    mysqli_query($db, $query);

    // get process id
    $db_id = mysqli_thread_id($db);
    // Kill connection
    mysqli_kill($db, $db_id);
}

// récupère les plus récentes publications
function get_most_recent_publication($limit)
{
    check_session_variables();
    include "../backend/db.php";
    $following = array();

    if (strpos($_SERVER['REQUEST_URI'], "home")) {
        $following = $_SESSION["following"];
    }


    // on ajoute aussi l'id de la session
    array_push($following, $_SESSION["id"]);
    $following_str = "(" . implode(", ", $following) . ")";

    $query = "SELECT * FROM publications WHERE id IN $following_str ORDER BY creation_date DESC LIMIT $limit";
    $results = mysqli_query($db, $query);

    $db_id = mysqli_thread_id($db);
    mysqli_kill($db, $db_id);

    if ($results) {
        $publications = array();
        if (mysqli_num_rows($results) > 0) {
            while ($row = mysqli_fetch_assoc($results)) {
                $row["title"] = stripslashes($row["title"]);
                $row["content"] = stripslashes($row["content"]);
                array_push($publications, $row);
            }
        }
        return $publications;
    } else {
        return null;
    }
}

// récupère un utilisateur avec un id
function get_user($id)
{
    include "../backend/db.php";

    $query = "SELECT * FROM users WHERE id='$id'";
    $result = mysqli_query($db, $query);

    $db_id = mysqli_thread_id($db);
    mysqli_kill($db, $db_id);

    if ($result) {
        $user = mysqli_fetch_assoc($result);
        if ($user)
            return $user;
        else
            return null;
    } else
        return null;
}

// print une liste de publications données
function display_publications($publications)
{
    $html = "";
    if ($publications != null && count($publications) > 0) {
        foreach ($publications as $post) {
            $title = $post["title"];
            $content = $post["content"];
            $post_id = $post["post_id"];

            // id est nécessaire pour localiser le post si on le delete
            $html .= "<div class='content post' id=$post_id> ";

            $likes = array();
            if (!empty($post["likes"])) {
                $likes = explode(" ", $post["likes"]);
                // on retire la dernière valeur si elle est vide
                if (empty(end($likes)))
                    unset($likes[array_search(end($likes), $likes)]);
            }

            $author = get_user($post["id"]);
            $author_name = $author["username"];

            $date_time = new DateTime($post['creation_date']);
            $date = $date_time->format('d/m/y H:i');

            $html .= "<h3>$title</h3> <p>$date</p>";

            if ($post["id"] == $_SESSION["id"])
                $html .= "<span class='delete' data-id=$post_id>Delete</span>";

            $html .= "<p>From $author_name</p> </br>";
            $html .= "<p>$content</p>";

            $html .= "<span class='likes_count'> " . count($likes) . " likes  </span>";

            if (in_array($_SESSION["id"], $likes)) {
                $html .= "<span class='like hide' data-id=$post_id>Like</span>";
                $html .= "<span class='dislike' data-id=$post_id>Dislike</span>";
            } else {
                $html .= "<span class='like' data-id=$post_id>Like</span>";
                $html .= "<span class='dislike hide' data-id=$post_id>Dislike</span>";
            }

            $html .= " </div>";
        }
        if (count($publications) % 5 != 0)
            $html .= "<p>End of the publications</p>";
    } else {
        $html .= "<div class='content'> <p> No publications yet </p> </div>";
    }

    echo $html;
}
