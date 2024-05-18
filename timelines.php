<?php

include_once ("./utilities/components.php");
include_once ("./utilities/database.php");
include_once ("./utilities/security.php");
include_once ("./utilities/functions.php");

login_or_redirect();

$conn = connect_to_mysql();
$timeline_datas = [];
if (isset($_GET["s"])) {
    $s = $_GET["s"];
    $timeline_datas = get_data($conn, "SELECT * FROM timelines WHERE information LIKE '%$s%' ORDER BY time DESC");
} else {
    $timeline_datas = get_data($conn, "SELECT * FROM timelines ORDER BY time DESC");
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
        <div class="table" style="grid-template-columns: repeat(<?= count($timeline_datas[0]) ?>, 1fr);">
            <?php foreach ($timeline_datas[0] as $key => $value): ?>
                <div class="header"><?= $key ?></div>
            <?php endforeach; ?>
            <?php foreach ($timeline_datas as $index => $timeline_data): ?>
                <?php foreach ($timeline_data as $key => $value): ?>
                    <div class="row link-button">
                        <?php if ($key == "link"): ?>
                            <?php if ($value): ?>
                                <button onclick="window.location.href = '<?= $value ?>'">Link</button>
                            <?php else: ?>
                                <button disabled>No Link</button>
                            <?php endif; ?>
                        <?php else: ?>
                            <?= $value ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>