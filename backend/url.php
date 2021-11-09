<?php
// https://programmation.surleweb-france.fr/php-url-rewriting/
// url => new_url
$rules = array(
    "index"      => "home",
    "account" => "me",
    "login" => "login",
    "register" => "register",
    "search" => "search",
    "settings" => "settings",
    "target_account" => "user=",
    "user_list" => "friends",
    "post" => "post",
    "private_messages" => "pm",
    "avatars" => "avatars"
);

$uri = rtrim(dirname($_SERVER["SCRIPT_NAME"]), '/');
$uri = '/' . trim(str_replace($uri, '', $_SERVER['REQUEST_URI']), '/');
$uri = urldecode($uri);

// echo $uri;
foreach ($rules as $url => $new_url) {
    if (preg_match('/' . $new_url . '/', $uri)) {
        // dans le cas de 'avatars', on n'applique aucune règle
        if ($url != "avatars")
            require("../pages/" . $url . '.php');
        else
            require("../" . $uri);
        exit();
    }
}
// Si rien n'est trouvé, on affiche une page 404.php
include('../pages/404.php');
