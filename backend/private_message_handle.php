<?php
include "../backend/db.php";
include "../backend/functions.php";
session_start();

if (isset($_POST["send_message"])) {
    $id = $_SESSION["id"];
    $target_id = $_POST["target_id"];
    $content = addslashes($_POST["content"]);

    // si l'utilisateur suit la personne ou est suivi par la personne
    if ((in_array($target_id, $_SESSION["following"]) || in_array($target_id, $_SESSION["follower"])) && !empty($content)) {
        $query = "INSERT INTO private_messages (id1, id2, content) VALUES ('$id', '$target_id', '" . $content . "')";
        mysqli_query($db, $query);

        $currentDate = new DateTime();
        $content = stripslashes(strip_tags($content));

        // on crée une variable $pm en fonction de ce qu'on a reçu
        $pm = array("id1" => $id, "id2" => $target_id, "content" => $content, "creation_date" => $currentDate->format('Y-m-d H:i:s'));
        echo display_message($pm, "none");
    }
}
