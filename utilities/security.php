<?php

include_once ("./database.php");

function check_login()
{
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

        $splitted_login_info = explode("/", $login_info);
        $hashed_name = $splitted_login_info[0];
        $hashed_email = $splitted_login_info[1];
        $hashed_password = $splitted_login_info[2];

        $conn = connect_to_mysql();
        $result = get_data($conn, "SELECT * FROM 'admin'");

        foreach ($result as $index => $data) {
            if (
                password_verify($data["name"], $hashed_name) &&
                password_verify($data["email"], $hashed_email) &&
                password_verify($data["password"], $hashed_password)
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