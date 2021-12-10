<?php
include "db.php";
include "functions.php";
session_start();

if (isset($_POST["send_message"])) {
    $id = $_SESSION["id"];
    $target_id = $_POST["target_id"];
    $content = $_POST["content"];

    // si l'utilisateur suit la personne et est suivi par la personne
    if (in_array($target_id, $_SESSION["following"]) && in_array($target_id, $_SESSION["follower"]) && !empty($content)) {
        create("private_messages", array("id1" => $id, "id2" => $target_id, "content" => $content));

        $currentDate = new DateTime();
        $content = strip_tags($content);

        // on crée une variable $pm en fonction de ce qu'on a reçu
        $pm = array("id1" => $id, "id2" => $target_id, "content" => $content, "creation_date" => $currentDate->format('Y-m-d H:i:s'));
        echo display_message($pm, "none");
    }
}
