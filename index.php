<?php
include_once ("./utilities/database.php");
include_once ("./utilities/components.php");
include_once ("./utilities/security.php");

login_or_redirect();
$conn = connect_to_mysql();

$insert_result = false;
if (isset($_POST["shelf_name"])) {
    $shelf_name = htmlspecialchars($_POST["shelf_name"]);
    $insert_result = insert_data($conn, "shelf", "(shelf_name)", "('$shelf_name')");
}

$shelf_datas = get_data($conn, "SELECT * FROM shelf ORDER BY id ASC");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System</title>
    <link rel="stylesheet" href="styles/main_page.css">
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/table.css">
</head>

<body>
    <?php navbar(); ?>

    <div class="content-wrapper">
        <div class="edit-buttons">
            <button class="add-shelf" onclick="document.getElementById('add-shelf-container').classList.add('active');">Add Shelf +</button>
        </div>
        <?php if (count($shelf_datas) > 0): ?>
                <div class="table" style="grid-template-columns: <?= str_repeat('1fr ', count($shelf_datas[0]) + 1) ?>;">
                    <?php foreach (array_keys($shelf_datas[0]) as $key): ?>
                            <div class="header">
                                <?= underscore_strip($key) ?>
                            </div>
                    <?php endforeach; ?>
                    <div class="header"></div>

                    <?php foreach ($shelf_datas as $index => $admin_datas): ?>
                            <?php foreach ($admin_datas as $key => $value): ?>
                                    <div class="row">
                                        <?= ucwords($value) ?>
                                    </div>
                            <?php endforeach; ?>
                            <?php $borrow_id = $admin_datas["id"]; ?>
                            <div class="row action-button">
                                <button onclick="window.location.href='./shelf.php?id=<?= $borrow_id ?>'">Enter</button>
                            </div>
                    <?php endforeach; ?>
                </div>
        <?php else: ?>
                <h1>No Shelf Yet..</h1>
        <?php endif; ?>
    </div>

    <div id="add-shelf-container">
        <form action="" method="post">
            <div>
                <label for="shelf_name">Shelf Name :</label>
                <input type="text" name="shelf_name" id="shelf_name">
            </div>
            <button type="submit">Submit</button>
            <button type="button" onclick="document.getElementById('add-shelf-container').classList.remove('active');">Back</button>
        </form>
    </div>

    <div id="message">
        <h1 class="message">
            <?php
            if (isset($_POST["shelf_name"])) {
                if ($insert_result) {
                    echo "Successfully insert shelf!";
                } else {
                    $error = $conn->error;
                    echo "Unsuccessfully insert shelf! Error : $error";
                }
            }
            ?>
        </h1>
    </div>

    <?php

    if (isset($_POST["shelf_name"])) {
        echo "
            <script>
                setTimeout(() => {
                    document.getElementById('message').classList.add('active')
                    setTimeout(() => {
                        document.getElementById('message').classList.remove('active')
                    }, 2000);
                }, 500);
            </script>
            ";
        $_POST["shelf_name"] = "";
    }

    ?>

</body>

</html>