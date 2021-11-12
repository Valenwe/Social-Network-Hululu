<?php
require "../backend/basic_functions.php";

// on attribue toutes les valeurs de la session (pour du local)
function set_session_value($user)
{
    session_unset();
    $_SESSION['username'] = $user['username'];
    $_SESSION['id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['avatar'] = $user['avatar'];

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
            $new_follower = explode(" ", $friend["follower"]);
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

    if (is_following($friend_id)) {
        // on change le user target
        if (!empty($friend["follower"])) {
            $new_follower = explode(" ", $friend["follower"]);
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

    // strip_tags remove any existing HTML tag
    $title = addslashes(strip_tags($title));
    $content = addslashes(strip_tags($content));

    $query = "INSERT INTO publications (id, title, content, likes) VALUES('$id', '" . $title . "', '" . $content . "', '')";
    mysqli_query($db, $query);

    // get process id
    $db_id = mysqli_thread_id($db);
    // Kill connection
    mysqli_kill($db, $db_id);
}

// récupère les plus récentes publications
function get_most_recent_publication($limit, $in_home)
{
    check_session_variables();
    include "../backend/db.php";
    $following = array();

    if ($in_home) {
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

function display_comment($comment, $db)
{
    $author_id = $comment["id"];
    $comment_id = $comment["comment_id"];

    // on récupère username
    $query = "SELECT username FROM users WHERE id='$author_id'";
    $result = mysqli_query($db, $query);
    if ($result) {
        $author = mysqli_fetch_assoc($result);
        $username = $author["username"];
    }

    $content = stripslashes($comment["content"]);
    $date = $comment["creation_date"];

    $html = "<div class='comment_section' id=$comment_id>";
    $html .= "<p class='comment_header'>By <a href='/search?target=user_$username'>$username</a> | $date</p>";

    if ($comment["modified"] == 1) $html .= "<p>Modified</p>";

    if ($comment["id"] == $_SESSION["id"]) {
        $html .= "<span class='delete_comment interactable' data-id=$comment_id>Delete </span>";
        $html .= "<span class='edit_comment interactable' data-id=$comment_id>Edit</span>";
    }

    $html .= "</br><p class='comment_content'>" . enrich_content($content) . "</p>";
    $html .= "</div>";

    // partie éditable du commentaire
    if ($comment["id"] == $_SESSION["id"]) {

        $html .= "<form method='post' class='edit_comment_form hide' id=$comment_id>
                <div class='input-group'>
                    <textarea type='text' name='edit_content' placeholder='Text'>$content</textarea>
                </div>
                <div class='input-group'>
                    <button type='button' class='btn edit_comment_cancel'>Cancel</button>
                </div>
                <div class='input-group'>
                    <button type='submit' class='btn' name='edit'>Save</button>
                </div>
                </form>";
    }

    return $html;
}

function display_comments($post_id, $limit, $hidden)
{
    include "../backend/db.php";

    // comment section
    if ($hidden) {
        $html = "<span class='show_comments interactable' data-id=$post_id>Show comments</span>";
        $html .= "<span class='hide hide_comments interactable' data-id=$post_id>Hide comments</span>";
        $html .= "<div class='comments hide'>";
    } else {
        $html = "<span class='show_comments interactable hide' data-id=$post_id>Show comments</span>";
        $html .= "<span class='hide_comments interactable' data-id=$post_id>Hide comments</span>";
        $html .= "<div class='comments'>";
    }

    $html .= "<div data-id='$post_id'> <textarea class='add_comment_content' placeholder='Comment'></textarea>";
    $html .= "<button type='button' class='btn add_comment'>Add</button> </div>";

    // on récupère les commentaires
    $query = "SELECT * FROM comments WHERE post_id='$post_id' ORDER BY creation_date DESC LIMIT $limit";
    $comments_result = mysqli_query($db, $query);

    if (mysqli_num_rows($comments_result) > 0) {
        while ($comment = mysqli_fetch_assoc($comments_result)) {
            $html .= display_comment($comment, $db);
        }

        $count = mysqli_num_rows($comments_result);
        if ($count % 5 == 0) {
            $html .= "<span class='interactable show_more_comments'>Show more</span>";
            $html .= "<input class='comment_counter' type='hidden' id='$post_id' value=$count>";
        } else
            $html .= "<p>No more comments available</p>";
    } else {
        $html .= "<p>No comments yet</p>";
    }

    // end comment section
    $html .= "</div>";

    return $html;
}

// print une liste de publications données
function display_publications($publications, $edited)
{
    include "../backend/db.php";
    $html = "";
    if ($publications != null && count($publications) > 0) {
        foreach ($publications as $post) {
            $title = stripslashes($post["title"]);
            $content = stripslashes($post["content"]);
            $post_id = $post["post_id"];

            // id est nécessaire pour localiser le post si on le delete
            $html .= "<div class='content post' id=$post_id>";

            $likes = array();
            if (!empty($post["likes"])) {
                $likes = explode(" ", $post["likes"]);
                // on retire la dernière valeur si elle est vide
                if (empty(end($likes)))
                    unset($likes[array_search(end($likes), $likes)]);
            }

            $author = get_user($post["id"]);
            $author_name = $author["username"];
            $avatar = $author["avatar"];

            $date_time = new DateTime($post['creation_date']);
            $date = $date_time->format('d/m/y H:i');

            $html .= "<a href='/search?target=user_$author_name'><img class='lil_avatar' src='$avatar'></a>";
            $html .= "<h3 class='post_title'>$title</h3>";

            if ($post["modified"]) $html .= "<p>Modified</p>";

            $html .= "<p>$date</p>";

            if ($post["id"] == $_SESSION["id"]) {
                $html .= "<span class='delete_post interactable' data-id=$post_id>Delete </span>";
                $html .= "<span class='edit_post interactable' data-id=$post_id>Edit</span>";
            }

            $html .= "<p>From <a href='/search?target=user_$author_name'>$author_name</a></p> </br>";

            $html .= "<p class='post_content'>" . enrich_content($content) . "</p>";

            $html .= "<span class='likes_count'> " . count($likes) . " likes  </span>";

            if (in_array($_SESSION["id"], $likes)) {
                $html .= "<span class='like interactable hide' data-id=$post_id>Like</span>";
                $html .= "<span class='dislike interactable' data-id=$post_id>Dislike</span>";
            } else {
                $html .= "<span class='like interactable' data-id=$post_id>Like</span>";
                $html .= "<span class='dislike interactable hide' data-id=$post_id>Dislike</span>";
            }

            // partie commentaires
            $html .= "<br>" . display_comments($post_id, 5, true);

            // end post
            $html .= " </div>";


            // partie éditable du post s'il appartient à l'utilisateur
            if ($post["id"] == $_SESSION["id"]) {

                $html .= "<form method='post' class='edit_post_form hide' id=$post_id>
                <div class='input-group'>
                    <input type='text' name='edit_title' placeholder='Title' value='$title'>
                </div>
                <div class='input-group'>
                    <textarea type='text' name='edit_content' placeholder='Text'>$content</textarea>
                </div>
                <div class='input-group'>
                    <button type='button' class='btn edit_post_cancel'>Cancel</button>
                </div>
                <div class='input-group'>
                    <button type='submit' class='btn' name='edit'>Save</button>
                </div>
                </form>";
            }
        }
        if (count($publications) % 5 != 0 && !$edited)
            $html .= "<p>End of the publications</p>";
    } else {
        $html .= "<div class='content'> <p> No publications yet </p> </div>";
    }

    if (!$edited) {
        $count = count($publications);
        $html .= "<input type='hidden' class='post_counter' value=$count>";
    }

    echo $html;
}

// affiche un message privé
function display_message($pm, $target_username)
{
    $id = $_SESSION["id"];
    $response = "<div class='message'>";

    if ($pm["id1"] == $id) {
        $response .= "<p class='message_header'>You " . $pm["creation_date"] . "</p> <p class='message_content'>" . enrich_content($pm["content"]) . "</p>";
    } else if ($pm["id2"] == $id) {
        $response .= "<p class='message_header'>" . $target_username . " " . $pm["creation_date"] . "</p> <p class='message_content'>" . enrich_content($pm["content"]) . "</p>";
    }

    $response .= "</div>";

    return $response;
}

// affiche une conversation
function display_conversation($target_id, $limit, $hidden)
{
    include "../backend/db.php";
    $id = $_SESSION["id"];

    // on récupère tous les pms qui concernent l'id
    $query = "SELECT * FROM private_messages WHERE (id1=$id AND id2=$target_id) OR (id1=$target_id AND id2=$id) ORDER BY creation_date DESC LIMIT $limit";
    $results = mysqli_query($db, $query);

    $pms = array();
    if ($results) {
        if (mysqli_num_rows($results) > 0) {
            while ($row = mysqli_fetch_assoc($results)) {
                $row["content"] = stripslashes($row["content"]);
                array_push($pms, $row);
            }
        }
    }

    // on reverse pour afficher les messages moins récents en premiers
    $pms = array_reverse($pms);

    $query = "SELECT username FROM users WHERE id='$target_id'";
    $target_username = mysqli_fetch_assoc(mysqli_query($db, $query))["username"];

    // title
    $response = "<div class='content' id=$target_id>";
    $response .= "<h3>$target_username</h3>";

    if ($hidden) {
        $response .= "<span class='show_conversation interactable'>Expand</span>";
        $response .= "<span class='hide_conversation interactable hide'>Hide</span>";

        // messages
        $response .= "<div class='conversation hide'>";
    } else {
        $response .= "<span class='show_conversation interactable hide'>Expand</span>";
        $response .= "<span class='hide_conversation interactable'>Hide</span>";

        // messages
        $response .= "<div class='conversation'>";
    }


    $nb_pm = 0;
    for ($i = 0; $i < count($pms); $i++) {
        $pm = $pms[$i];

        if ($pms[$i]["id1"] == $id || $pms[$i]["id2"] == $id) {

            // partie pour accéder aux messages plus anciens
            if ($nb_pm == 0 && $limit % 5 == 0) {
                $response .= "<span class='show_more_messages interactable'>Show more</span>";
            }

            $nb_pm++;
            $response .= display_message($pm, $target_username);
        }
    }

    if ($nb_pm == 0) {
        $response .= "<p>No messages yet</p>";
    } else {
        $response .= "<input type='hidden' class='message_counter' value=$nb_pm>";
    }

    $response .= "<textarea class='new_message_content' placeholder='New message'></textarea>";
    $response .= "<span class='send_message interactable'>Send</span>";
    $response .= "</div> </div>";

    return $response;
}

// affiche les onglets des messages privés
function display_pms($id, $target_id)
{
    include "../backend/db.php";

    // on récupère tous les pms qui concernent l'id dans l'ordre du plus récent
    $query = "SELECT * FROM private_messages WHERE id1=$id OR id2=$id ORDER BY creation_date DESC";
    $results = mysqli_query($db, $query);

    $pms = array();
    if ($results) {
        if (mysqli_num_rows($results) > 0) {
            while ($row = mysqli_fetch_assoc($results)) {
                $row["content"] = stripslashes($row["content"]);
                array_push($pms, $row);
            }
        }
    }

    // on définit tous les onglets de pms à créer
    $target_pms = array();
    foreach ($pms as $pm) {
        if ($pm["id1"] != $id) {
            if (!in_array($pm["id1"], $target_pms)) array_push($target_pms, $pm["id1"]);
        }

        if ($pm["id2"] != $id) {
            if (!in_array($pm["id2"], $target_pms)) array_push($target_pms, $pm["id2"]);
        }
    }

    // si on veut créer un nouveau message, on ajoute un onglet
    if (!in_array($target_id, $target_pms) && $target_id != -1) array_push($target_pms, $target_id);

    $response = "";
    foreach ($target_pms as $target_id) {
        $response .= display_conversation($target_id, 5, 1);
    }

    if (count($target_pms) == 0) $response .= "<p>No messages yet</p>";

    return $response;
}

// permet d'ajouter des couleurs / changement de police pour tout contenu
function enrich_content($content)
{
    // remove any existing HTML tag
    $rich_content = $content;

    // mise en place du sautage de ligne
    $rich_content = str_replace("\n", "<br>", $rich_content);
    $rich_content = str_replace("\r", "<br>", $rich_content);

    // code appending
    while (strpos($rich_content, "```", strpos($rich_content, "```") + 1) != strpos($rich_content, "```") && gettype(strpos($rich_content, "```", strpos($rich_content, "```") + 1)) != "boolean") {
        $rich_content = str_replace_first("```", "<span class='content_code'>", $rich_content);
        $rich_content = str_replace_first("```", "</span>", $rich_content);
    }

    // add hashtag colors
    if (gettype(strpos($rich_content, "#", 0)) != "boolean") {
        $offset = 0;
        // tant qu'il y a un #, et qu'il est soit au début, soit possède un ' ' avant lui-même
        while (gettype(strpos($rich_content, "#", $offset)) != "boolean" && (strpos($rich_content, "#", $offset) > 0 && $rich_content[strpos($rich_content, "#", $offset) - 1] == ' ') || strpos($rich_content, "#", $offset) == 0) {
            $rich_content = str_replace_first("#", "<span class='hashtag'>#", $rich_content, $offset);
            $offset = strpos($rich_content, "#", $offset) + 1;
    
            if (strpos($rich_content, " ", $offset) != 0) {
                $rich_content = str_replace_first(" ", "</span> ", $rich_content, $offset);
            } else {
                $rich_content .= "</span>";
            }
        }
    }

    return $rich_content;
}
