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

// va retirer uniquement le premier patern trouvé
function str_replace_first($from, $to, $content)
{
    $from = '/' . preg_quote($from, '/') . '/';
    return preg_replace($from, $to, $content, 1);
}

function is_following($friend_id)
{
    if (!empty($_SESSION["following"])) {
        return in_array($friend_id, $_SESSION["following"]);
    }
    return false;
}

function is_follower($friend_id)
{
    if (!empty($_SESSION["follower"])) {
        return in_array($friend_id, $_SESSION["follower"]);
    }
    return false;
}

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
