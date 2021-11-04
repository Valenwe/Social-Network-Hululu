<?php
session_start();

include "functions.php";

if (!empty($_POST['function'])) {
    $function2call = $_POST['function'];
    switch ($function2call) {
        case 'get_and_display_publications':
            $results = get_most_recent_publication($_POST["row"], true);
            display_publications($results);
            break;

        case "get_and_display_comments":
            echo display_comments($_POST["post_id"], $_POST["row"], false);
            break;

        case "get_and_display_messages":
            echo display_conversation($_POST["target_id"], $_POST["row"], $_POST["hidden"]);
            break;
    }

    unset($_POST["function"]);
}
