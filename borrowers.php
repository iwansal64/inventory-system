<?php

include_once ("./utilities/database.php");
include_once ('./utilities/functions.php');
include_once ('./utilities/components.php');
include_once ("./utilities/security.php");

login_or_redirect();

$conn = connect_to_mysql();


$message = "";
$message_duration = 500;
if (isset($_POST["i_want_to"]) && isset($_POST["borrow_id"]) && isset($_POST["item_name"])) {
    $i_want_to = htmlspecialchars($_POST["i_want_to"]);
    $borrow_id = htmlspecialchars($_POST["borrow_id"]);
    $item_name = htmlspecialchars($_POST["item_name"]);

    $borrow_data = get_data($conn, "SELECT * FROM borrow WHERE id=$borrow_id")[0];
    $borrow_quantity = $borrow_data["quantity"];
    $borrowers_name = $borrow_data["borrowers_name"];
    $item_name = $borrow_data["item_name"];

    if ($i_want_to == "return") {
        $result = update_data(
            $conn,
            "borrow",
            "id=$borrow_id",
            "status='returned'"
        );

        if ($result) {
            $result = update_data(
                $conn,
                "items",
                "item_name='$item_name'",
                "borrowed_count=borrowed_count-$borrow_quantity",
                "$borrowers_name return $item_name!"
            );

            if ($result) {
                $message = "Successfully update data!";
            } else {
                $error = $conn->error;
                $message = "Unsuccessfully update data! error = $error";
                $message_duration = 5000;
            }
        } else {
            $error = $conn->error;
            $message = "Unsuccessfully update data! error = $error";
            $message_duration = 5000;
        }
    }
}

$borrowers_datas = [];
if (isset($_GET["s"])) {
    $search = $_GET["s"];
    $borrowers_datas = get_data($conn, "SELECT * FROM borrow WHERE borrowers_name LIKE '%$search%' OR item_name LIKE '%$search%' OR status LIKE '%$search%' ORDER BY id DESC");
} else {
    $borrowers_datas = get_data($conn, "SELECT * FROM borrow ORDER BY id DESC");
}

if (isset($_GET["export_excel"])) {
    export_excel($borrowers_datas, "data-peminjam");
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowers</title>
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/borrowers.css">
    <link rel="stylesheet" href="styles/table.css">
    <link rel="stylesheet" href="styles/other_buttons.css">
</head>

<body>
    <?php navbar(); ?>
    <?php
    $key_excpetions = array(
        "start_borrow_date",
        "end_borrow_date"
    );
    ?>
    <?php if (count($borrowers_datas) > 0): ?>
        <div class="table_container">
            <div class="other_buttons">
                <button onclick="window.location.href = window.location.href+'?export_excel'; ">Export Excel</button>
            </div>
            <div class="table" style="grid-template-columns: <?= str_repeat('1fr ', count($borrowers_datas[0]) + 2 - count($key_excpetions)) ?>;">
                <?php foreach (array_keys($borrowers_datas[0]) as $key): ?>
                    <?php if (!in_array($key, $key_excpetions)): ?>
                        <div class="header">
                            <?= underscore_uppercase($key) ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <div class="header"></div>
                <div class="header"></div>

                <?php foreach ($borrowers_datas as $index => $admin_datas): ?>
                    <?php foreach ($admin_datas as $key => $value): ?>
                        <?php if (!in_array($key, $key_excpetions)): ?>
                            <div class="row">
                                <?= ucwords($value) ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php $borrow_status = $admin_datas["status"]; ?>

                    <div class="row action-button">
                        <?php if ($borrow_status == "borrowing"): ?>
                            <?php $borrow_id = $admin_datas["id"]; ?>
                            <?php $item_name = $admin_datas["item_name"]; ?>
                            <button onclick="
                    document.getElementById('return_ui').classList.add('active'); 
                    document.getElementById('borrow_id').value = '<?= $borrow_id ?>';
                    document.getElementById('item_name').value = '<?= $item_name ?>';
                    ">Return</button>
                        <?php else: ?>
                            <button disabled>Return</button>
                        <?php endif; ?>
                    </div>
                    <div class="row action-button">
                        <?php $borrow_id = $admin_datas["id"]; ?>
                        <button onclick="window.location.href='./borrower_info.php?id=<?= $borrow_id ?>';">Info</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <h1>No Shelf Yet..</h1>
    <?php endif; ?>
    <div id="return_ui" class="ui">
        <form action="" method="post">
            <h1>Are you sure?</h1>

            <input type="hidden" name="borrow_id" value="-1" id="borrow_id">
            <input type="hidden" name="item_name" value="-1" id="item_name">
            <input type="hidden" name="i_want_to" value="return">
            <button type="submit">Sure!</button>
            <button type="button" onclick="document.getElementById('return_ui').classList.remove('active')">Back</button>
        </form>
    </div>
    <script src="./table.js"></script>
</body>

</html>