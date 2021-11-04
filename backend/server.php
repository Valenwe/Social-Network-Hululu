<?php
include "../backend/functions.php";
include "../backend/db.php";
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
    if (empty($_POST['reg_username'])) {
        array_push($errors, "Username is required");
    }
    if (empty($_POST['reg_email'])) {
        array_push($errors, "Email is required");
    }
    if (empty($_POST['reg_password_1'])) {
        array_push($errors, "Password is required");
    }

    if (!empty($_POST['reg_username']) && !empty($_POST['reg_email']) && !empty($_POST['reg_password_1']) && !empty($_POST['reg_password_2'])) {
        // receive all input values from the form
        $username = mysqli_real_escape_string($db, $_POST['reg_username']);
        $email = mysqli_real_escape_string($db, $_POST['reg_email']);
        $password_1 = mysqli_real_escape_string($db, $_POST['reg_password_1']);
        $password_2 = mysqli_real_escape_string($db, $_POST['reg_password_2']);

        if (!is_str_valid($username)) {
            array_push($errors, "Invalid username (characters allowed are letters, numbers and '_')");
        }

        if ($password_1 != $password_2) {
            array_push($errors, "The two passwords do not match");
        }

        // first check the database to make sure 
        // a user does not already exist with the same username and/or email
        $user_check_query = "SELECT username, email FROM users WHERE username=$username OR email=$email";
        $result = mysqli_query($db, $user_check_query);
        if (!empty($result)) {
            $user = mysqli_fetch_assoc($result);
            if ($user) {
                if ($user['username'] === $username) {
                    array_push($errors, "Username already exists");
                }

                if ($user['email'] === $email) {
                    array_push($errors, "Email already exists");
                }
            }
        }

        // Finally, register user if there are no errors in the form
        if (count($errors) == 0) {
            $password = password_hash($password_1, PASSWORD_BCRYPT); //encrypt the password before saving in the database

            // '".$password."' -> au cas où il y a des espaces
            $query = "INSERT INTO users (username, email, password, following, follower) VALUES('$username', '$email', '" . $password . "', '', '')";
            $result = mysqli_query($db, $query);

            $query = "SELECT * FROM users WHERE username='" . $username . "'";
            $result = mysqli_query($db, $query);

            set_session_value(mysqli_fetch_assoc($result));
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
    $username = mysqli_real_escape_string($db, $_POST['log_username']);
    $password = mysqli_real_escape_string($db, $_POST['log_password']);

    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    if (count($errors) == 0) {
        $query = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($db, $query);
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
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

    $new_username = mysqli_real_escape_string($db, $_POST['set_username']);
    $new_email = mysqli_real_escape_string($db, $_POST['set_email']);
    $old_password = mysqli_real_escape_string($db, $_POST['set_old_password']);
    $new_password = mysqli_real_escape_string($db, $_POST['set_new_password']);

    $new_firstname = mysqli_real_escape_string($db, $_POST['set_firstname']);
    $new_lastname = mysqli_real_escape_string($db, $_POST['set_lastname']);

    // changement de mot de passe tenté
    if (!empty($old_password) || !empty($new_password)) {
        if (empty($old_password) || empty($new_password)) {
            array_push($errors, "You have to enter both the old and the new password to change it");
        }

        $new_password = password_hash($new_password, PASSWORD_BCRYPT);
        $query = "SELECT password FROM users WHERE username='$username'";
        $result = mysqli_query($db, $query);
        $user = mysqli_fetch_assoc($result);

        if (!password_verify($old_password, $user['password'])) {
            array_push($errors, "Error, the old password is incorrect");
        }

        if (count($errors) == 0) {
            $query = "UPDATE users SET password = '$new_password' WHERE username = '$username'";
            mysqli_query($db, $query);
        }
    }

    if (!empty($new_username)) {
        if (!is_str_valid($username)) {
            array_push($errors, "Invalid username (characters allowed are letters, numbers and '_', '-')");
        }

        $query = "SELECT username FROM users WHERE username='$new_username'";
        $result = mysqli_query($db, $query);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            array_push($errors, "That username already exists");
        }

        if (count($errors) == 0) {
            $query = "UPDATE users SET username='$new_username' WHERE id=$id";
            mysqli_query($db, $query);
            $_SESSION['username'] = $new_username;
        }
    }

    if (!empty($new_email)) {
        $query = "SELECT username FROM users WHERE email='$new_email'";
        $result = mysqli_query($db, $query);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            array_push($errors, "This email is already used");
        }

        if (count($errors) == 0) {
            $query = "UPDATE users SET email='$new_email' WHERE id=$id";
            mysqli_query($db, $query);
        }
    }

    if (!empty($new_firstname)) {
        if (!is_str_valid($username)) {
            array_push($errors, "Invalid name (characters allowed are letters, numbers and '_', '-')");
        } else {
            $query = "UPDATE users SET firstname='" . $new_firstname . "' WHERE id=$id";
            mysqli_query($db, $query);
            $_SESSION['firstname'] = $new_firstname;
        }
    }

    if (!empty($new_lastname)) {
        if (!is_str_valid($username)) {
            array_push($errors, "Invalid name (characters allowed are letters, numbers and '_', '-')");
        } else {
            $query = "UPDATE users SET lastname='" . $new_lastname . "' WHERE id=$id";
            mysqli_query($db, $query);
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

    $query = "UPDATE users SET firstname=null, lastname=null WHERE id=$id";
    mysqli_query($db, $query);

    unset($_SESSION['firstname']);
    unset($_SESSION['lastname']);

    array_push($success, "Name reset");
}
