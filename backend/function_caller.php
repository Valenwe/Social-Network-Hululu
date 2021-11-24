<?php
session_start();

include "functions.php";

if (!empty($_POST['function'])) {
    $function2call = $_POST['function'];
    switch ($function2call) {
        case 'get_and_display_publications':
            $results = get_most_recent_publication($_POST["row"], true);
            display_publications($results, false);
            break;

        case "get_and_display_comments":
            echo display_comments($_POST["post_id"], $_POST["row"], false);
            break;

        case "get_and_display_messages":
            echo display_conversation($_POST["target_id"], $_POST["row"], $_POST["hidden"]);
            break;

        case "has_new_messages":
            $id = $_SESSION["id"];
            $target_id = $_POST["target_id"];
            $last_message_id = $_POST["last_message_id"];
            $query = "SELECT pm_id FROM private_messages WHERE (id1=$id AND id2=$target_id) OR (id1=$target_id AND id2=$id) ORDER BY creation_date DESC LIMIT 1";
            $last_msg = special_find_query($query)[0];
            
            if ($last_msg["pm_id"] != $last_message_id)
                echo 1;
            else
                echo 0;
            break;
    }

    unset($_POST["function"]);
}
