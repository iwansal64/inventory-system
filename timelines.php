<?php

include_once ("./utilities/components.php");
include_once ("./utilities/database.php");
include_once ("./utilities/security.php");
include_once ("./utilities/functions.php");

login_or_redirect();

$conn = connect_to_mysql();
$timeline_datas = get_data($conn, "SELECT * FROM timelines");

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
        <div class="table" style="grid-template-columns: repeat(<?= count($timeline_datas[0]) + 1 ?>, 1fr);">
            <div class="header">No.</div>
            <?php foreach ($timeline_datas[0] as $key => $value): ?>
                <div class="header"><?= $key ?></div>
            <?php endforeach; ?>
            <?php foreach ($timeline_datas as $index => $timeline_data): ?>
                <div class="row"><?= $index + 1 ?></div>
                <?php foreach ($timeline_data as $key => $value): ?>
                    <div class="row">
                        <?php if ($key == "link"): ?>
                            <?php if ($value): ?>
                                <button onclick="window.location.href = '<?= $value ?>'">Link</button>
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