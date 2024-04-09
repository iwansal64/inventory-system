<?php

require ("./components.php");
require ("./database.php");
$conn = connect_to_mysql();
$shelf_datas = get_data($conn, "SELECT * FROM shelf");

$insert_result = false;
if (isset($_POST["item_name"]) && isset($_POST["item_shelf_name"])) {
    $item_name = $_POST["item_name"];
    $item_shelf_name = $_POST["item_shelf_name"];
    $insert_result = insert_data($conn, "items", "(item_name, item_shelf)", "('$item_name', '$item_shelf_name')");
}

$item_datas = array();
$shelf_id = false;
$selected_shelf_name = "";
if (isset($_GET["id"])) {
    $shelf_id = $_GET["id"];
    foreach ($shelf_datas as $index => $shelf_data) {
        foreach ($shelf_data as $key => $value) {
            if ($key == "id" && $value == $shelf_id) {
                $selected_shelf_name = $shelf_data["shelf_name"];
                $item_datas = get_data($conn, "SELECT * FROM items WHERE item_shelf='$selected_shelf_name'");
            }
        }
    }
} else {
    $item_datas = get_data($conn, "SELECT * FROM items");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shelf</title>
    <link rel="stylesheet" href="styles/shelf.css">
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/table.css">
</head>

<body>
    <?php navbar(); ?>

    <div class="content-wrapper">
        <?php if ($selected_shelf_name != ""): ?>
            <h1 class="shelf-name"><?= ucwords($selected_shelf_name) ?> shelf</h1>
        <?php endif; ?>
        <div class="edit-buttons">
            <button class="add-item" onclick="document.getElementById('add-item-container').classList.add('active');">Add Item +</button>
        </div>
        <?php if (count($item_datas) > 0): ?>
            <div class="table" style="grid-template-columns: <?= str_repeat('1fr ', count($item_datas[0]) + 1) ?>;">
                <?php foreach (array_keys($item_datas[0]) as $key): ?>
                    <div class="header">
                        <?= underscore_strip($key) ?>
                    </div>
                <?php endforeach; ?>
                <div class="header"></div>

                <?php foreach ($item_datas as $index => $data): ?>
                    <?php foreach ($data as $key => $value): ?>
                        <div class="row">
                            <?= ucwords($value) ?>
                        </div>
                    <?php endforeach; ?>
                    <?php $id = $data["id"]; ?>
                    <div class="row action-button">
                        <button onclick="window.location.href='./item.php?id=<?= $id ?>'">Action</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <h1>No Item Yet..</h1>
        <?php endif; ?>
    </div>

    <div id="add-item-container">
        <form action="" method="post">
            <div>
                <label for="item_name">Item Name :</label>
                <input type="text" name="item_name" id="item_name">
            </div>
            <div>
                <label for="item_shelf_name">Item Table Group :</label>
                <select name="item_shelf_name" id="item_shelf_name">
                    <?php foreach ($shelf_datas as $shelf_data): ?>
                        <option value="<?= $shelf_data["shelf_name"] ?>" <?= $shelf_data["shelf_name"] == $selected_shelf_name ? "selected" : "" ?>>
                            <?= $shelf_data["shelf_name"] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit">Submit</button>
            <button type="button" onclick="document.getElementById('add-item-container').classList.remove('active');">Back</button>
        </form>
    </div>

    <div id="message">
        <h1 class="message">
            <?php
            if (isset($_POST["item_name"]) && isset($_POST["item_shelf_name"])) {
                if ($insert_result) {
                    echo "Successfully inserted data!";
                } else {
                    $error = $conn->error;
                    echo "Unsuccessfully inserted data! error : $error";
                }
            }
            ?>
        </h1>
    </div>

    <?php
    if (isset($_POST["item_name"]) && isset($_POST["item_shelf_name"])) {
        echo "<script>
            setTimeout(() => {
                document.getElementById('message').classList.add('active');
                setTimeout(() => {
                    document.getElementById('message').classList.remove('active');
                }, 2000);
            }, 500);
            </script>";
    }
    ?>

</body>

</html>