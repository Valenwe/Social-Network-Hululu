<?php
include "../backend/db.php";
include "../backend/functions.php";
session_start();

if (isset($_POST["send_message"])) {
    $id = $_SESSION["id"];
    $target_id = $_POST["target_id"];
    $content = get_valid_str($_POST["content"]);

    // si l'utilisateur suit la personne
    if (in_array($target_id, $_SESSION["following"]) && !empty($content)) {
        $query = "INSERT INTO private_messages (id1, id2, content) VALUES ('$id', '$target_id', '$content')";
        mysqli_query($db, $query);

        $currentDate = new DateTime();
        echo "<div class='message'> <p>You " . $currentDate->format('Y-m-d H:i:s') . "</p> <p>" . $content . "</p> </div>";
    }
}