<?php

include_once ("./utilities/database.php");
$user_login_delimiter = ";;;";

function check_login()
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