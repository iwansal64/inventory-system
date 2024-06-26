<?php

include_once ("./utilities/database.php");
include_once ("./utilities/functions.php");
include_once ("./utilities/security.php");

login_or_redirect();

$conn = connect_to_mysql();

$insert_result = false;
$update_result = false;

$message = "";
$message_duration = 1500;

// <?php if ($_POST["i_want_to"] == "borrow"):
//     <?php if ($insert_result):
//         Successfully insert borrow data!
//     <?php else:
//         <?php $error = $conn->error;
//         Unsuccessfully insert borrow data.. Error = <?= $error
//     <?php endif;
// <?php else:
//     <?php if ($update_result):
//         Successfully edit item data!
//     <?php else:
//         <?php $error = $conn->error;
//         Unsuccessfully edit item data.. Error = <?= $error
//     <?php endif;
// <?php endif;
$shelf_datas = get_data($conn, "SELECT * FROM shelf");

$item_id = "-1";
if (isset($_GET["id"])) {
    $item_id = $_GET["id"];
}

$item_name = "";
if (isset($_GET["item_name"])) {
    $item_name = $_GET["item_name"];
}

$item_data = array();
if (isset($_POST["i_want_to"])) {
    $i_want_to = $_POST["i_want_to"];

    if ($i_want_to == "borrow") {
        if (isset($_POST["name"]) && isset($_POST["quantity"]) && isset($_POST["end_date"]) && isset($_POST["end_time"]) && isset($_POST["item_name"])) {
            $item_data = get_data($conn, "SELECT * FROM items WHERE id=$item_id OR item_name='$item_name'")[0];
            $item_id = $item_data["id"];
            $item_name = htmlspecialchars($_POST["item_name"]);
            $name = htmlspecialchars($_POST["name"]);
            $quantity = htmlspecialchars($_POST["quantity"]);
            $end_date = htmlspecialchars($_POST["end_date"]);
            $end_time = htmlspecialchars($_POST["end_time"]) . ":00";
            $end_datetime = $end_date . "T" . $end_time;


            if ($item_data["borrowed_count"] + $quantity > $item_data["item_quantity"]) {
                if ($item_data["borrowed_count"] > 0) {
                    $message = "Item is out of stock. cannot borrow again until previous borrower give the item back.";
                } else {
                    $message = "Item is not sufficient!";
                }
                $message_duration = 5000;
            } else {
                $insert_result = insert_data(
                    $conn,
                    "borrow",
                    "(item_name, borrowers_name, quantity, end_borrow_date)",
                    "('$item_name', '$name', $quantity, '$end_datetime')",
                    "Borrowing Item : ($item_name) x$quantity"
                );

                if ($insert_result) {
                    // $borrowed_count_res = intval($item_data["borrowed_count"]) + 1;
                    $insert_result = update_data(
                        $conn,
                        "items",
                        "id=$item_id",
                        "borrowed_count=borrowed_count+$quantity"
                    );

                    if ($insert_result) {
                        $message = "Successfully insert borrow data!";
                        $item_data["borrowed_count"] += 1;
                    } else {
                        $error = $conn->error;
                        $message = "Unsuccessfully borrow item.. Error = $error";
                        $message_duration = 5000;
                    }
                } else {
                    $error = $conn->error;
                    $message = "Unsuccessfully borrow item.. Error = $error";
                    $message_duration = 5000;
                }
            }

        }
    } else if ($i_want_to == "edit") {
        if (isset($_POST["item_id_before"]) && isset($_POST["item_name"]) && isset($_POST["item_shelf"]) && isset($_POST["item_quantity"])) {
            $item_name = trim(htmlspecialchars($_POST["item_name"]));
            $item_shelf = trim(htmlspecialchars($_POST["item_shelf"]));
            $item_id_before = trim(htmlspecialchars($_POST["item_id_before"]));
            $item_quantity = trim(htmlspecialchars($_POST["item_quantity"]));

            $found = false;
            foreach ($shelf_datas as $index => $shelf_data) {
                if ($shelf_data["shelf_name"] == $item_shelf) {
                    $found = true;
                }
            }

            if ($found) {
                $result = update_data(
                    $conn,
                    "items",
                    "id=$item_id_before",
                    "item_name='$item_name', item_shelf='$item_shelf', item_quantity=$item_quantity",
                    "Update item : ($item_name) data"
                );

                if ($result) {
                    $message = "Successfully edit item data";
                } else {
                    $error = $conn->error;
                    $message = "Unsuccessfully edit item data! Error : $error";
                    $message_duration = 5000;
                }
            } else {
                $message = "Shelf data cannot be found!";
                $message_duration = 5000;
            }
        }
        $item_data = get_data($conn, "SELECT * FROM items WHERE id=$item_id OR item_name='$item_name'")[0];
    } else if ($i_want_to == "delete") {
        if (isset($_POST["item_id"])) {
            $target_id = htmlspecialchars($_POST["item_id"]);

            $exist_item_id = get_data($conn, "SELECT id FROM items");

            $found = false;
            foreach ($exist_item_id as $index => $admins_data) {
                $borrow_id = $admins_data["id"];
                if ($borrow_id == $target_id) {
                    $found = true;
                }
            }

            if ($found) {
                $item_id = htmlspecialchars($_GET["id"]);
                $item_data = get_data($conn, "SELECT * FROM items WHERE id=$item_id OR item_name='$item_name'")[0];
                $item_name = $item_data['item_name'];
                $result = delete_data($conn, "items", "id=$target_id", "Delete item : ($item_name)");

                if ($result) {
                    $message = "Successfully deleted!";
                    foreach ($shelf_datas as $index => $shelf_data) {
                        if ($shelf_data["shelf_name"] == $item_data["item_shelf"]) {
                            $shelf_id = $shelf_data["id"];
                            header("Location: ./shelf.php?id=$shelf_id");
                        }
                    }
                } else {
                    $error = $conn->error;
                    $message = "Unsuccessfully delete item! error = $error";
                    $message_duration = 5000;
                }
            } else {
                $message = "Item not found!";
                $message_duration = 5000;
                $item_data = get_data($conn, "SELECT * FROM items WHERE id=$item_id OR item_name='$item_name'")[0];
            }
        }
    }
} else {
    $item_data = get_data($conn, "SELECT * FROM items WHERE id=$item_id OR item_name='$item_name'")[0];
}

$shelf_name = $item_data["item_shelf"];
$shelf_data = array();
foreach ($shelf_datas as $index => $admins_data) {
    if ($admins_data["shelf_name"] == $shelf_name) {
        $shelf_data = $admins_data;
    }
}
$shelf_id = $shelf_data["id"];
$item_name = $item_data["item_name"];
$item_quantity = $item_data["item_quantity"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product info : <?= $item_data["item_name"] ?></title>
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/item.css">
</head>

<body>
    <div class="content-wrapper">
        <h1 class="title">
            Item Name :
            <?= ucwords($item_data["item_name"]) ?>
        </h1>
        <div class="item-info">
            <?php foreach ($item_data as $key => $value): ?>
            <div>
                <h2 class="key">
                    <?= underscore_uppercase($key) ?>
                </h2>
                <h2 class="value">
                    <?php if ($key == "item_shelf"): ?>
                    <button onclick="window.location.href='./shelf.php?id=<?= $shelf_id ?>'">
                        <?= underscore_uppercase($value) ?>
                    </button>
                    <?php elseif ($key == "borrowed_count"): ?>
                    <button onclick="window.location.href='./borrowers.php?s=<?= $item_name ?>'">
                        <?= underscore_uppercase($value) ?>
                    </button>
                    <?php else: ?>
                    <?= underscore_uppercase($value) ?>
                    <?php endif; ?>

                </h2>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="action-buttons">
            <button class="back" onclick="window.location.href='./shelf.php'">Back</button>
            <button class="borrow" onclick="document.getElementById('borrow_ui').classList.add('active')">Borrow</button>
            <button class="edit" onclick="document.getElementById('edit_ui').classList.add('active')">Edit</button>
            <button class="delete" onclick="document.getElementById('delete_ui').classList.add('active')">Unreg</button>
        </div>

        <div id="borrow_ui" class="ui">
            <form action="" method="post">
                <div>
                    <label for="name">Name :</label>
                    <input type="text" name="name" id="name" placeholder="your name.." required>
                </div>

                <div>
                    <label for="quantity">Quantity :</label>
                    <input type="number" name="quantity" id="quantity" value="1">
                </div>

                <div>
                    <label for="end_date">End Date :</label>
                    <input type="date" name="end_date" id="end_date">
                </div>

                <div>
                    <label for="end_time">End Time :</label>
                    <input type="time" name="end_time" id="end_time" value="15:00">
                </div>

                <input type="hidden" name="i_want_to" value="borrow">
                <?php $item_name = $item_data["item_name"]; ?>
                <input type="hidden" name="item_name" value="<?= $item_name ?>">

                <div class="btn">
                    <button class="submit" type="submit">Submit</button>
                    <button class="back" type="button" onclick="document.getElementById('borrow_ui').classList.remove('active')">Back</button>
                </div>
            </form>
        </div>

        <div id="edit_ui" class="ui">
            <form action="" method="post">
                <div>
                    <label for="item_name">Item Name :</label>
                    <input type="text" name="item_name" id="item_name" value="<?= $item_name ?>" required>
                </div>

                <div>
                    <label for="item_quantity">Item Quantity :</label>
                    <input type="number" name="item_quantity" id="item_quantity" value="<?= $item_quantity ?>" required>
                </div>

                <div>
                    <label for="item_shelf">Item Shelf :</label>
                    <select name="item_shelf" id="item_shelf">
                        <?php foreach ($shelf_datas as $index => $admins_data): ?>
                        <?php $name = $admins_data["shelf_name"]; ?>
                        <option value="<?= $name ?>" <?= $name == $shelf_name ? 'selected' : '' ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <input type="hidden" name="item_id_before" value="<?= $item_id ?>">
                <input type="hidden" name="i_want_to" value="edit">
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
                <input type="hidden" name="item_id" , value="<?= $item_id ?>">
                <div class="btn">
                    <button class="submit" type="submit">Confirm</button>
                    <button class="back" type="button" onclick="document.getElementById('delete_ui').classList.remove('active')">Back</button>
                </div>
            </form>
        </div>

        <div id="message">
            <h1>
                <?php if (isset($_POST["i_want_to"])): ?>
                <?= $message ?>
                <?php endif; ?>
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

    <script>
    let date_split = new Date().toLocaleDateString().split("/");
    let res = date_split[2] + "-" + date_split[1] + "-" + date_split[0];
    document.getElementById("end_date").value = res;
    </script>
</body>

</html>