<?php

include_once ("./utilities/database.php");
include_once ("./utilities/components.php");
include_once ("./utilities/security.php");

login_or_redirect();
$conn = connect_to_mysql();

$borrow_datas = get_data($conn, "SELECT * FROM borrow");

$borrow_datas_per_day = array();

foreach ($borrow_datas as $borrow_data) {
    $current_date = explode(" ", $borrow_data["start_borrow_date"], 2)[0];
    if (array_key_exists($current_date, $borrow_datas_per_day)) {
        $borrow_datas_per_day[$current_date] += 1;
    } else {
        $borrow_datas_per_day[$current_date] = 1;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/statistics.css">
</head>

<body>
    <?php navbar(); ?>
    <h1 class="main-title">Stats</h1>
    <div class="stats-container borrow-statistics">
        <h1 class="stats-title">Borrow Statistics</h1>
        <div class="graph borrow-data" data-min-x-size="600px">
            <?php foreach ($borrow_datas_per_day as $key => $value): ?>
            <div class="data-set">
                <div class="data-key" data-key="<?= $key ?>"></div>
                <div class="data-value" data-value="<?= $value ?>"></div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
    <script src="graph.js"></script>
</body>

</html>