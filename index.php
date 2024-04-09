<?php
require ("./database.php");
require ("./components.php");

$conn = connect_to_mysql();

$insert_result = false;
if (isset($_POST["shelf_name"])) {
    $shelf_name = $_POST["shelf_name"];
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
        <?php if (!table($shelf_datas, "Enter", "./shelf.php")): ?>
            <h1>No shelf yet..</h1>
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