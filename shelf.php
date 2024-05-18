<?php

include_once ("./utilities/components.php");
include_once ("./utilities/database.php");
include_once ("./utilities/security.php");

login_or_redirect();
$conn = connect_to_mysql();
$shelf_datas = get_data($conn, "SELECT * FROM shelf");

$insert_result = false;
if (isset($_POST["item_name"]) && isset($_POST["item_shelf_name"])) {
    $item_name = htmlspecialchars($_POST["item_name"]);
    $item_shelf_name = htmlspecialchars($_POST["item_shelf_name"]);
    $insert_result = insert_data($conn, "items", "(item_name, item_shelf)", "('$item_name', '$item_shelf_name')", "Inserting Item : ($item_name) to ($item_shelf_name)", "./item.php?item_name=$item_name");
} else if (isset($_POST["delete"]) && isset($_POST["shelf_name"])) {
    $shelf_name = $_POST["shelf_name"];
    delete_data($conn, "shelf", "shelf_name='$shelf_name'", "Delete Shelf : ($shelf_name)");
    header("Location: ./");
}

$item_datas = array();
$shelf_id = false;
$shelf_name = false;
$selected_shelf_name = "";
if (isset($_GET["id"]) || isset($_GET["shelf_name"])) {

    $shelf_id = "";
    $shelf_name = "";

    if (isset($_GET["id"])) {
        $shelf_id = htmlspecialchars($_GET["id"]);
    }

    if (isset($_GET["shelf_name"])) {
        $shelf_name = htmlspecialchars($_GET["shelf_name"]);
    }

    $selector = "";

    foreach ($shelf_datas as $index => $shelf_data) {
        foreach ($shelf_data as $key => $value) {
            if (($key == "id" && $value == $shelf_id) || ($key == "shelf_name" && $value == $shelf_name)) {
                $selected_shelf_name = $shelf_data["shelf_name"];
                if ($selector != "") {
                    $selector += " OR item_shelf='$selected_shelf_name'";
                } else {
                    $selector = "item_shelf='$selected_shelf_name'";
                }
            }
        }
    }

    if ($selector) {
        $item_datas = get_data($conn, "SELECT * FROM items WHERE $selector");
    } else {
        header("Location: ./");
    }
} else {
    if (isset($_GET["s"])) {
        $search = $_GET["s"];
        $item_datas = get_data($conn, "SELECT * FROM items WHERE item_name LIKE '%$search%' OR item_shelf LIKE '%$search%'");
    } else {
        $item_datas = get_data($conn, "SELECT * FROM items");
    }
}

if (isset($_GET["export_excel"])) {
    export_excel($item_datas, "data-item");
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
    <link rel="stylesheet" href="styles/other_buttons.css">
</head>

<body>
    <?php navbar(); ?>

    <div class="content-wrapper">
        <?php if ($selected_shelf_name != ""): ?>
            <h1 class="shelf-name"><?= ucwords($selected_shelf_name) ?> shelf</h1>
        <?php endif; ?>
        <?php if ($shelf_id || $shelf_name): ?>
            <div class="edit-buttons">
                <button class="add-item" onclick="document.getElementById('add-item-container').classList.add('active');">Add Item +</button>
                <button class="delete-shelf" onclick="document.getElementById('delete-shelf-container').classList.add('active');">Delete Shelf</button>
            </div>
        <?php endif; ?>
        <?php $key_excpetions = array("first_add_datetime"); ?>
        <?php if (count($item_datas) > 0): ?>
            <div class="table_container">
                <div class="other_buttons">
                    <button onclick="window.location.href = window.location.href+'?export_excel'; ">Export Excel</button>
                </div>
                <div class="table" style="grid-template-columns: <?= str_repeat('1fr ', count($item_datas[0]) + 1 - count($key_excpetions)) ?>;">
                    <?php foreach (array_keys($item_datas[0]) as $key): ?>
                        <?php if (in_array($key, $key_excpetions)) {
                            continue;
                        } ?>
                        <div class="header">
                            <?= underscore_uppercase($key) ?>
                        </div>
                    <?php endforeach; ?>
                    <div class="header"></div>

                    <?php foreach ($item_datas as $index => $item_data): ?>
                        <?php foreach ($item_data as $key => $value): ?>
                            <?php if (in_array($key, $key_excpetions)) {
                                continue;
                            } ?>
                            <div class="row">
                                <?= ucwords($value) ?>
                            </div>
                        <?php endforeach; ?>
                        <?php $borrow_id = $item_data["id"]; ?>
                        <div class="row action-button">
                            <button onclick="window.location.href='./item.php?id=<?= $borrow_id ?>'">Action</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <h1>No Item Yet..</h1>
        <?php endif; ?>
    </div>

    <?php if ($shelf_id || $shelf_name): ?>
        <div class="container" id="delete-shelf-container">
            <form action="" method="post">
                <?php $shelf_name = $shelf_data["shelf_name"]; ?>
                <input type="hidden" name="shelf_name" value="<?= $shelf_name ?>">
                <input type="hidden" name="delete" value="true">
                <button type="submit">Confirm Delete?</button>
                <button type="button" onclick="document.getElementById('delete-shelf-container').classList.remove('active');">Back</button>
            </form>
        </div>
        <div class="container" id="add-item-container">
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
    <?php endif; ?>

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

    <script src="./table.js"></script>
</body>

</html>