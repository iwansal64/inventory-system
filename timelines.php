<?php

include_once ("./utilities/components.php");
include_once ("./utilities/database.php");
include_once ("./utilities/security.php");
include_once ("./utilities/functions.php");

login_or_redirect();

$conn = connect_to_mysql();
$admin_datas = get_data($conn, "SELECT * FROM admins");
$borrow_datas = get_data($conn, "SELECT * FROM borrow");
$item_datas = get_data($conn, "SELECT * FROM items");
$shelf_datas = get_data($conn, "SELECT * FROM shelf");
$unsorted_datas = array(
    "admins" => $admin_datas,
    "borrow" => $borrow_datas,
    "items" => $item_datas,
    "shelf" => $shelf_datas
);
$datas = array();

$max_iter = 50;
while (count($unsorted_datas) > 0 && $max_iter > 0) {
    $latest_date_time = new DateTime("@0");
    $latest_date_time_index = -1;
    $latest_date_time_table = -1;

    foreach ($unsorted_datas as $table => $unsorted_data) {
        foreach ($unsorted_data as $index => $data) {
            foreach ($data as $key => $value) {
                if (str_ends_with($key, "datetime") || str_ends_with($key, "date")) {
                    $data_date_time = new DateTime($value);
                    if ($data_date_time > $latest_date_time) {
                        $latest_date_time = $data_date_time;
                        $latest_date_time_index = $index;
                        $latest_date_time_table = $table;
                    }
                }
            }
        }
    }

    if ($latest_date_time_index == -1 || $latest_date_time_table == -1) {
        break;
    }
    $datas[] = array_merge($unsorted_datas[$latest_date_time_table][$latest_date_time_index], array("table" => $latest_date_time_table));
    unset($unsorted_datas[$latest_date_time_table][$latest_date_time_index]);
    $unsorted_datas[$latest_date_time_table] = array_values($unsorted_datas[$latest_date_time_table]);

    $max_iter -= 1;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timelines</title>
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/timeline.css">
    <link rel="stylesheet" href="styles/table.css">
</head>

<body>
    <?php navbar(); ?>
    <div class="content-wrapper">
        <div class="table" style="grid-template-columns: repeat(5, 1fr);">
            <div class="header">No.</div>
            <div class="header">Section</div>
            <div class="header">Information</div>
            <div class="header">Time</div>
            <div class="header">Link</div>
            <?php foreach ($datas as $index => $data): ?>
                <?php foreach ($data as $key => $value): ?>
                    <?php
                    $table = $data["table"];
                    $information = "";
                    $time = "";
                    $link = "";
                    if (!str_ends_with($key, "date") && !str_ends_with($key, "datetime")) {
                        continue;
                    }
                    $time = $value;

                    if (str_ends_with($key, "date")) {
                        $information = substr($key, 0, -5);
                    } else {
                        $information = substr($key, 0, -9);
                    }

                    if ($table == "admins") {
                        $link = "./admins.php";
                    } else if ($table == "items") {
                        $link = "./item.php?id=" . $data["id"];
                        $information .= " " . $data["item_name"];
                    } else if ($table == "shelf") {
                        $link = "./shelf.php?id=" . $data["id"];
                        $information .= " " . $data["shelf_name"];
                    } else if ($table == "borrow") {
                        $link = "./borrower_info.php?id=" . $data["id"];
                        $information .= " " . $data["item_name"];
                    }

                    ?>
                    <div class="row"><?= ($index + 1) ?></div>
                    <div class="row"><?= ucwords($table) ?></div>
                    <div class="row"><?= underscore_strip($information) ?></div>
                    <div class="row"><?= $time ?></div>
                    <div class="row action-button">
                        <button class="info" onclick="window.location.href='<?= $link ?>'">Open</button>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>