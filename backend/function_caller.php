<?php
session_start();

include "functions.php";

if (!empty($_POST['function'])) {
    $function2call = $_POST['function'];
    switch ($function2call) {
        case 'get_and_display_publications':
            $result = get_most_recent_publication($_POST["row"]);
            display_publications($result);
            break;
    }

    unset($_POST["function"]);
}
