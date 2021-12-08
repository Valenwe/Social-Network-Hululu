<?php
include "../backend/functions.php";
session_start();

// initializing variables
$username = "";
$email    = "";
$errors = array();
$success = array();

if (!empty($_SESSION["errors"])) {
    $errors = $_SESSION["errors"];
    unset($_SESSION["errors"]);
}

// REGISTER USER
if (isset($_POST['reg_user'])) {
    // form validation: ensure that the form is correctly filled ...
    // by adding (array_push()) corresponding error unto $errors array
    if (empty($_POST['reg_username']))
        array_push($errors, "Username is required");

    if (empty($_POST['reg_email']))
        array_push($errors, "Email is required");

    if (empty($_POST['reg_password_1']))
        array_push($errors, "Password is required");


    if (!empty($_POST['reg_username']) && !empty($_POST['reg_email']) && !empty($_POST['reg_password_1']) && !empty($_POST['reg_password_2'])) {
        // receive all input values from the form
        $username = $_POST['reg_username'];
        $email = $_POST['reg_email'];
        $password_1 = $_POST['reg_password_1'];
        $password_2 = $_POST['reg_password_2'];

        if (!is_str_valid($username))
            array_push($errors, "Invalid username (characters allowed are letters, numbers and '_')");

        if (strlen($password_1) <= 5)
            array_push($errors, "The password has to be at least 6 characters long");

        if (!preg_match('~[0-9]+~', $password_1))
            array_push($errors, "The password must contain at least one digit number");

        if ($password_1 != $password_2)
            array_push($errors, "The two passwords do not match");

        // first check the database to make sure 
        // a user does not already exist with the same username and/or email
        $user = find("users", array("username" => $username, "email" => $email), 1, array("username", "email"), "OR");
        if ($user) {
            if ($user['username'] === $username)
                array_push($errors, "Username already exists");

            if ($user['email'] === $email)
                array_push($errors, "Email already exists");
        }

        // Finally, register user if there are no errors in the form
        if (count($errors) == 0) {
            $password = password_hash($password_1, PASSWORD_BCRYPT); //encrypt the password before saving in the database

            // '".$password."' -> au cas où il y a des espaces
            create("users", array("username" => $username, "email" => $email, "password" => $password, "follower" => "", "following" => ""));

            set_session_value(find("users", array("username" => $username), 1));
            if (isset($_SESSION['username'])) {
                array_push($success, "You are now logged in");
                $_SESSION["success"] = $success;
                header('location: /home');
            } else {
                array_push($errors, "Error trying to create a new user");
            }
        }
    }
}


// LOGIN USER
if (isset($_POST['log_user'])) {
    $username = $_POST['log_username'];
    $password = $_POST['log_password'];

    if (empty($username))
        array_push($errors, "Username is required");

    if (empty($password))
        array_push($errors, "Password is required");

    if (count($errors) == 0) {
        $user = find("users", array("username" => $username), 1);
        if ($user && password_verify($password, $user['password'])) {
            set_session_value($user);
            array_push($success, "You are now logged in");
            $_SESSION["success"] = $success;
            header('location: /home');
        } else {
            array_push($errors, "Wrong username/password combination");
        }
    }
}


// CHANGE SETTINGS
if (isset($_POST['set_change'])) {
    $username = $_SESSION['username'];
    $id = $_SESSION['id'];

    $new_username = $_POST['set_username'];
    $new_email = $_POST['set_email'];
    $old_password = $_POST['set_old_password'];
    $new_password = $_POST['set_new_password'];

    $new_firstname = $_POST['set_firstname'];
    $new_lastname = $_POST['set_lastname'];

    // changement de mot de passe tenté
    if (!empty($old_password) || !empty($new_password)) {
        if (empty($old_password) || empty($new_password)) {
            array_push($errors, "You have to enter both the old and the new password to change it");
        }

        $new_password = password_hash($new_password, PASSWORD_BCRYPT);
        $user = find("users", array("username" => $username), 1);

        if ($user && !password_verify($old_password, $user['password'])) {
            array_push($errors, "Error, the old password is incorrect");
        }

        if (count($errors) == 0) {
            update("users", array("password" => $new_password), array("username" => $username));
        }
    }

    if (!empty($new_username)) {
        if (!is_str_valid($username)) {
            array_push($errors, "Invalid username (characters allowed are letters, numbers and '_', '-')");
        }

        $user = find("users", array("username" => $new_username), 1);

        if ($user) {
            array_push($errors, "That username already exists");
        }

        if (count($errors) == 0) {
            update("users", array("username" => $new_username), array("id" => $id));
            $_SESSION['username'] = $new_username;
        }
    }

    if (!empty($new_email)) {
        $user = find("users", array("email" => $new_email), 1, array("username"));

        if ($user) {
            array_push($errors, "This email is already used");
        }

        if (count($errors) == 0) {
            update("users", array("email" => $new_email), array("id" => $id));
        }
    }

    if (!empty($new_firstname)) {
        if (!is_str_valid($username)) {
            array_push($errors, "Invalid name (characters allowed are letters, numbers and '_', '-')");
        } else {
            update("users", array("firstname" => $new_firstname), array("id" => $id));
            $_SESSION['firstname'] = $new_firstname;
        }
    }

    if (!empty($new_lastname)) {
        if (!is_str_valid($username)) {
            array_push($errors, "Invalid name (characters allowed are letters, numbers and '_', '-')");
        } else {
            update("users", array("lastname" => $new_lastname), array("id" => $id));
            $_SESSION['lastname'] = $new_lastname;
        }
    }

    if (count($errors) == 0) {
        array_push($success, "Changes saved successfully");
        $_SESSION["success"] = $success;
        header("location: /home");
    }
}

// RESET NAME
if (isset($_POST['set_reset_name'])) {
    $id = $_SESSION['id'];

    update("users", array("firstname" => null, "lastname" => null), array("id" => $id));

    unset($_SESSION['firstname']);
    unset($_SESSION['lastname']);

    array_push($success, "Name reset");
}

// UPLOAD AVATAR
if (isset($_POST['set_avatar']) && isset($_FILES["avatar_file"])) {
    $id = $_SESSION['id'];
    $image = $_FILES["avatar_file"]["tmp_name"];
    $type = $_FILES["avatar_file"]["type"];
    $size = $_FILES["avatar_file"]["size"];

    if ($type != "image/png") {
        array_push($errors, "The image has to be a .png file");
    }

    if ($size > 2000000) {
        array_push($errors, "The image has to be less than 2 Mo");
    }

    $image_info = getimagesize($image);

    // vérifie si le fichier est bien une image
    if (!is_array($image_info)) {
        array_push($errors, "This file is not an image");
    } else {
        $image_width = $image_info[0];
        $image_height = $image_info[1];

        if ($image_width != $image_height || $image_width != 64 || $image_height != 64) {
            array_push($errors, "The image has to be a 64x64 square");
        }
    }

    if (count($errors) == 0) {
        $target = "../avatars/" . $id . ".png";

        if (move_uploaded_file($image, $target)) {
            update("users", array("avatar" => $target), array("id" => $id));

            $_SESSION['avatar'] = $target;
            array_push($success, "Image uploaded successfully");
        } else {
            array_push($errors, "Failed to upload image");
        }
    }
}

// RESET AVATAR
if (isset($_POST['reset_avatar'])) {
    $id = $_SESSION['id'];
    $reset_image = '../avatars/0.png';

    update("users", array("avatar" => $reset_image), array("id" => $id));

    $_SESSION['avatar'] = $reset_image;
    array_push($success, "Avatar reset successfully");
}
