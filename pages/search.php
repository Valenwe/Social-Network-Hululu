<?php
include('../backend/server.php');

if (!isset($_SESSION['username'])) {
    array_push($errors, "You must be logged in first");
    $_SESSION["errors"] = $errors;
    header('location: /login');
}

foreach ($_GET as $key => $value) {
    if (strpos($value, "user_") !== false) {
        $_SESSION['target_search'] = str_replace_first("user_", "", $value);

        // au cas où de mauvais caractères sont présents
        $_SESSION['target_search'] = get_valid_str($_SESSION['target_search']);
        // si on clique sur notre compte
        if ($_SESSION["username"] == $_SESSION["target_search"]) {
            header("location: /me");
        } else {
            header("location: /user=" . $_SESSION['target_search']);
        }
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Search</title>
    <link rel="stylesheet" type="text/css" href="../sn/style.css">
</head>

<body>
    <div class="header">
        <h2>Search results</h2>
    </div>
    <div class="content">
        <p><a class="btn" href="/home">Back</a></p>
        <?php

        if (isset($_SESSION['search'])) {
            $target = $_SESSION['search'];
            $query = "SELECT * FROM users WHERE username LIKE '%$target%'";
            $results = mysqli_query($db, $query);

            if (mysqli_num_rows($results) > 0) {
                echo mysqli_num_rows($results) . " results found" . "<br><br>";
                while ($row = mysqli_fetch_assoc($results)) {
                    $display = "Username: " . $row["username"];
                    if (!empty($row["firstname"]) || !empty($row["lastname"])) {
                        $display .= " - Name: ";
                        if (!empty($row["firstname"]))
                            $display .= $row["firstname"] . " ";

                        if (!empty($row["lastname"]))
                            $display .= $row["lastname"];
                    }
                    $search_username = $row['username'];
                    echo "<p><a href='/search?target=user_$search_username'>" . $display . "</a></p><br>";
                }
            } else {
                echo "No results";
            }
        } else {
            echo "No results";
        }

        ?>
    </div>
</body>

</html>