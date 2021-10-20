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
    if (!empty($user['followers'])) {
        $_SESSION['followers'] = $user['followers'];
    }
    if (!empty($user['following'])) {
        $_SESSION['following'] = $user['following'];
    }
}

// va retirer uniquement le premier patern trouvé
function str_replace_first($from, $to, $content)
{
    $from = '/' . preg_quote($from, '/') . '/';
    return preg_replace($from, $to, $content, 1);
}