<?php

include_once ("./utilities/database.php");
include_once ("./utilities/functions.php");
include_once ("./utilities/security.php");
if (check_login()) {
    header("Location: ./");
    die;
} else {
    if (isset($_POST["pass"]) && isset($_POST["account"])) {
        //? Login activities
        $pass = $_POST["pass"];
        $account = $_POST["account"];

        $conn = connect_to_mysql();
        $datas = get_data($conn, "SELECT * FROM admins WHERE email='$account' OR name='$account'");

        if (count($datas) == 0) {
            alert("user not found!");
        } else {
            foreach ($datas as $admin_datas) {
                if (password_verify($pass, $admin_datas["password"])) {
                    $data_pass = $admin_datas["password"];
                    $current_date_time = date("Y-m-d H:i:s");
                    $result = update_data($conn, "admins", "password='$data_pass'", "last_login_datetime='$current_date_time'", "Login Attempt To ($account)");

                    if ($result) {
                        //? Successfully Login!
                        $hashed_name = password_hash($admin_datas["name"], PASSWORD_DEFAULT);
                        $hashed_email = password_hash($admin_datas["email"], PASSWORD_DEFAULT);

                        $result_data = $hashed_name . $user_login_delimiter . $hashed_email . $user_login_delimiter . $pass;

                        if (isset($_POST["remember_me"])) {
                            setcookie("li", $result_data, time() + (60 * 60 * 24 * 30), "/");
                        } else {
                            if (session_status() == PHP_SESSION_NONE) {
                                session_start();
                            }
                            $_SESSION["li"] = $result_data;
                        }
                        alert("successfully login!", "./");
                    } else {
                        //? Unsuccessfully Login!
                        $error = $conn->error;
                        alert("can't update to database.. error: $error");
                    }
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/login.css">
</head>

<body>
    <div class="content-wrapper">
        <form action="" method="post">
            <h1 class="title">Login</h1>

            <div class="input_field">
                <label for="account">Name/Email :</label>
                <input type="text" name="account" id="account" required>
            </div>

            <div class="input_field">
                <label for="pass">Password :</label>
                <input type="password" name="pass" id="pass" required>
            </div>

            <div class="input_field checkbox">
                <input type="checkbox" name="remember_me" id="remember_me" value="true">
                <label for="remember_me">Remember Me</label>
            </div>

            <button class="submit" type="submit">Submit</button>
        </form>
    </div>
</body>

</html>