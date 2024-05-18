<?php

include_once ("./utilities/database.php");
include_once ("./utilities/functions.php");
$user_login_delimiter = ";;;";

function check_login($information = "")
{
    global $user_login_delimiter;

    session_start();
    if (isset($_SESSION["li"]) || isset($_COOKIE["li"])) {
        $login_info = "";
        if (isset($_SESSION["li"])) {
            $login_info = $_SESSION["li"];
        } else {
            $login_info = $_COOKIE["li"];
        }

        if (!str_contains($login_info, $user_login_delimiter)) {
            return;
        }

        $splitted_login_info = explode($user_login_delimiter, $login_info);
        $user_hashed_name = $splitted_login_info[0];
        $user_hashed_email = $splitted_login_info[1];
        $user_password = $splitted_login_info[2];

        $conn = connect_to_mysql();
        $result = get_data($conn, "SELECT * FROM admins", $information);

        foreach ($result as $index => $data) {
            if (
                password_verify($data["name"], $user_hashed_name) &&
                password_verify($data["email"], $user_hashed_email) &&
                password_verify($user_password, $data["password"])
            ) {
                return true;
            }
        }
    }
    return false;
}

function login_or_redirect(string $url = "./login.php")
{
    if (!check_login()) {
        header("Location: " . $url);
    }
}

function logout()
{
    global $user_login_delimiter;

    session_start();
    if (isset($_SESSION["li"]) || isset($_COOKIE["li"])) {
        $login_info = "";
        if (isset($_SESSION["li"])) {
            $login_info = $_SESSION["li"];
        } else {
            $login_info = $_COOKIE["li"];
        }

        if (!str_contains($login_info, "/")) {
            return;
        }

        $splitted_login_info = explode($user_login_delimiter, $login_info);
        $user_hashed_name = $splitted_login_info[0];
        $user_hashed_email = $splitted_login_info[1];
        $user_password = $splitted_login_info[2];

        $conn = connect_to_mysql();
        $result = get_data($conn, "SELECT * FROM admins");
        foreach ($result as $index => $data) {
            if (
                password_verify($data["name"], $user_hashed_name) &&
                password_verify($data["email"], $user_hashed_email) &&
                password_verify($user_password, $data["password"])
            ) {
                $name = $data["name"];
                $result_timeline = insert_timeline($conn, "$name logout!", "");
                if ($result_timeline) {
                    if (isset($_SESSION["li"])) {
                        unset($_SESSION["li"]);
                    }
                    if (isset($_COOKIE["li"])) {
                        setcookie("li", "", time() - 3600, "/");
                    }
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
    return false;
}

function get_user_data(): array
{
    global $user_login_delimiter;
    $user_data = array();

    session_start();

    $login_info = "";
    if (isset($_COOKIE["li"])) {
        $login_info = $_COOKIE["li"];
    } else if (isset($_SESSION["li"])) {
        $login_info = $_SESSION["li"];
    } else {
        return $user_data;
    }

    $login_info = explode($user_login_delimiter, $login_info, 3);
    $user_data["username"] = $login_info[0];
    $user_data["email"] = $login_info[1];
    $user_data["password"] = $login_info[2];

    return $user_data;
}