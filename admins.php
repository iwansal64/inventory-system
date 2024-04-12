<?php
include_once ("./utilities/components.php");
include_once ("./utilities/database.php");
include_once ("./utilities/security.php");


login_or_redirect();
$conn = connect_to_mysql();
$admin_datas = get_data($conn, "SELECT * FROM admins");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admins Informations</title>
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/admins.css">
    <link rel="stylesheet" href="styles/table.css">
</head>

<body>
    <?php navbar(); ?>

    <div class="content-wrapper">
        <?php if (count($admin_datas) > 0): ?>
        <div class="table" style="grid-template-columns: <?= str_repeat('1fr ', count($admin_datas[0]) - 1) ?>;">
            <?php foreach (array_keys($admin_datas[0]) as $key): ?>
            <?php if ($key == "password") {
                        continue;
                    } ?>
            <div class="header">
                <?= underscore_strip($key) ?>
            </div>
            <?php endforeach; ?>

            <?php foreach ($admin_datas as $index => $data): ?>
            <?php foreach ($data as $key => $value): ?>
            <?php if ($key == "password") {
                            continue;
                        } ?>
            <div class="row">
                <?= ucwords($value) ?>
            </div>
            <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <?php endif; ?>
    </div>
</body>

</html>