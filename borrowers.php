<?php

include_once ("./database.php");
include_once ('./functions.php');
include_once ('./components.php');

$conn = connect_to_mysql();


$message = "";
$message_duration = 500;
if (isset($_POST["i_want_to"]) && isset($_POST["borrow_id"]) && isset($_POST["item_name"])) {
    $i_want_to = htmlspecialchars($_POST["i_want_to"]);
    $borrow_id = htmlspecialchars($_POST["borrow_id"]);
    $item_name = htmlspecialchars($_POST["item_name"]);

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
                "borrowed_count=borrowed_count-1"
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

$borrowers_datas = get_data($conn, "SELECT * FROM borrow ORDER BY id DESC");

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
    <div class="table" style="grid-template-columns: <?= str_repeat('1fr ', count($borrowers_datas[0]) + 2 - count($key_excpetions)) ?>;">
        <?php foreach (array_keys($borrowers_datas[0]) as $key): ?>
        <?php if (!in_array($key, $key_excpetions)): ?>
        <div class="header">
            <?= underscore_strip($key) ?>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
        <div class="header"></div>
        <div class="header"></div>

        <?php foreach ($borrowers_datas as $index => $data): ?>
        <?php foreach ($data as $key => $value): ?>
        <?php if (!in_array($key, $key_excpetions)): ?>
        <div class="row">
            <?= ucwords($value) ?>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>

        <?php $borrow_status = $data["status"]; ?>

        <div class="row action-button">
            <?php if ($borrow_status == "borrowing"): ?>
            <?php $borrow_id = $data["id"]; ?>
            <?php $item_name = $data["item_name"]; ?>
            <button onclick="
                document.getElementById('return-ui').classList.add('active'); 
                document.getElementById('borrow_id').value = '<?= $borrow_id ?>';
                document.getElementById('item_name').value = '<?= $item_name ?>';
                ">Return</button>
            <?php else: ?>
            <button disabled>Return</button>
            <?php endif; ?>
        </div>
        <div class="row action-button">
            <?php $borrow_id = $data["id"]; ?>
            <button onclick="window.location.href='./borrower_info.php?id=<?= $borrow_id?>';">Info</button>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <h1>No Shelf Yet..</h1>
    <?php endif; ?>
    <div id="return-ui" class="ui">
        <form action="" method="post">
            <h1>Are you sure?</h1>

            <input type="hidden" name="borrow_id" value="-1" id="borrow_id">
            <input type="hidden" name="item_name" value="-1" id="item_name">
            <input type="hidden" name="i_want_to" value="return">
            <button type="submit">Sure!</button>
            <button type="button" onclick="document.getElementById('return-ui').classList.remove('active')">Back</button>
        </form>
    </div>
</body>

</html>