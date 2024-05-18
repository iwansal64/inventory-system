<?php

include_once ("./utilities/functions.php");
include_once ("./utilities/database.php");
include_once ("./utilities/security.php");

login_or_redirect();

$conn = connect_to_mysql();

$message = "";
$message_duration = 1500;
if (isset($_POST["i_want_to"])) {
    $i_want_to = $_POST["i_want_to"];
    $borrowers_id = $_POST["borrowers_id"];

    if ($i_want_to == "edit") {
        $new_borrowers_name = $_POST["borrowers_name"];
        $result = update_data($conn, "borrow", "id=$borrowers_id", "borrowers_name='$new_borrowers_name'");

        if ($result) {
            $message = "Successfully edit borrow data!";
        } else {
            $error = $conn->error;
            $message = "Unsuccessfully delete data! error = $error";
            $message_duration = 5000;
        }
    } else if ($i_want_to == "delete") {
        $result = delete_data($conn, "borrow", "id=$borrowers_id");

        if ($result) {
            alert("Successfully delete data!", "./borrowers.php");
        } else {
            $error = $conn->error;
            $message = "Unsuccessfully delete data! error = $error";
            $message_duration = 5000;
        }
    }
}


if (!isset($_GET["id"])) {
    alert("id parameter not specified!", "./borrowers.php");
}

$borrowers_id = "";
try {
    $borrowers_id = $_GET["id"];
} catch (Exception $e) {
    header("Location: ./borrowers.php");
}

$borrowers_data = get_data($conn, "SELECT * FROM borrow WHERE id=$borrowers_id")[0];


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $borrowers_data["borrowers_name"] ?> borrowing '<?= $borrowers_data["item_name"] ?>' data</title>
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/borrower_info.css">
    <link rel="stylesheet" href="styles/item.css">
</head>

<body>
    <div class="content-wrapper">
        <h1 class="title" style="font-size: 2.4rem"><?= $borrowers_data["borrowers_name"] ?> borrowing '<?= $borrowers_data["item_name"] ?>' data</h1>

        <div class="item-info">
            <?php foreach ($borrowers_data as $key => $value): ?>
                <div>
                    <h2 class="key">
                        <?= underscore_uppercase($key) ?>
                    </h2>
                    <h2 class="value">
                        <?= underscore_uppercase($value) ?>
                    </h2>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="action-buttons">
            <button class="back" onclick="window.location.href='./borrowers.php'">Back</button>
            <button class="edit" onclick="document.getElementById('edit_ui').classList.add('active')">Edit</button>
            <button class="delete" onclick="document.getElementById('delete_ui').classList.add('active')">Delete</button>
        </div>

        <div id="edit_ui" class="ui">
            <form action="" method="post">
                <div>
                    <label for="borrowers_name">Borrower's name :</label>
                    <input type="text" name="borrowers_name" id="borrowers_name">
                </div>

                <input type="hidden" name="i_want_to" value="edit">
                <input type="hidden" name="borrowers_id" , value="<?= $borrowers_id ?>">
                <div class="btn">
                    <button class="submit" type="submit">Submit</button>
                    <button class="back" type="button" onclick="document.getElementById('edit_ui').classList.remove('active')">Back</button>
                </div>
            </form>
        </div>

        <div id="delete_ui" class="ui">
            <form action="" method="post">

                <h1>Are you sure?</h1>

                <input type="hidden" name="i_want_to" value="delete">
                <input type="hidden" name="borrowers_id" , value="<?= $borrowers_id ?>">
                <div class="btn">
                    <button class="submit" type="submit">Confirm</button>
                    <button class="back" type="button" onclick="document.getElementById('delete_ui').classList.remove('active')">Back</button>
                </div>
            </form>
        </div>

        <div id="message">
            <h1>
                <?= $message ?>
            </h1>
        </div>

        <?php if (isset($_POST["i_want_to"])): ?>
            <?php echo "<script>
        setTimeout(() => {
            document.getElementById(\"message\").classList.add(\"active\");
            setTimeout(() => {
                document.getElementById(\"message\").classList.remove(\"active\");
            }, $message_duration);
        }, 500);
        </script>";
            ?>
        <?php endif; ?>
    </div>
</body>

</html>