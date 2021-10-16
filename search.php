<?php include('server.php') ?>
<!DOCTYPE html>
<html>

<head>
    <title>Search</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <div class="header">
        <h2>Search result</h2>
    </div>
    <div class="content">
        <?php

        if (isset($_POST['search'])) {
            $search = $_POST['search'];
            $query = "SELECT * FROM users WHERE username LIKE '%$search%'";
            $result = mysqli_query($db, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $display = "Username: " . $row["username"];
                    if (!empty($row["firstname"]) || !empty($row["lastname"])) {
                        $display .= " - Name: ";
                        if (!empty($row["firstname"]))
                            $display .= $row["firstname"] . " ";

                        if (!empty($row["lastname"]))
                            $display .= $row["lastname"];
                    }
                    echo $display . "<br>";
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