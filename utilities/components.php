<?php

include_once ("./utilities/functions.php");

function navbar()
{
    $search = "";
    if (isset($_GET["s"])) {
        $search = $_GET["s"];
    }

    $navigation_links = array(
        "Warehouse" => "./",
        "Items" => "./shelf.php",
        "Borrowers" => "./borrowers.php",
        "Admins" => "./admins.php",
        "Timelines" => "./timelines.php",
        "Statistics" => "./statistics.php",
        "Logout" => "./logout.php"
    );

    $current_url = $_SERVER["REQUEST_URI"];
    $count = 1;
    $current_url = str_replace("/projects/inventory-system", "", $current_url, $count);
    $current_url = explode("?", $current_url)[0];
    $current_url = "." . $current_url;

    echo "<script src=\"search.js\" defer></script>
    <nav class=\"navbar\">
        <div class=\"top-side\">
            <div class=\"logo\">
                <h1><span>Mitra</span> Inventory</h1>
            </div>
            <div class=\"search\">
                <input type=\"text\" id=\"search-input\" class=\"search-input\" placeholder=\"Search anything..\" value=\"$search\">
            </div>
            <div class=\"accounts\">
                <button class=\"notifications\">N</button>
                <button class=\"account\">A</button>
                <button class=\"search-button\" id=\"search-button\">S</button>
                <div class=\"menu-trigger\">
                    <p>|||</p>
                    <input type=\"checkbox\" id=\"menu-trigger\" />
                </div>
            </div>
        </div>
        <div class=\"bottom-side\">
        <ul>";

    foreach ($navigation_links as $link_name => $link_url) {
        $class = $current_url == $link_url ? 'active' : '';
        echo "<a href=\"$link_url\" class=\"$class\">";
        echo $link_name;
        echo "</a>";
    }

    echo "</ul>
        </div>
        </nav>";
}

function pythagoras($val_1, $val_2)
{
    return sqrt(pow($val_1, 2) + pow($val_2, 2));
}

// function graph(array $datas, string $size, string $mode = "dot", string $background_color = "#333", string $point_color = "#999", string $line_color = "#666")
// {
// $max_val = max(array_merge($datas, array(count($datas)))) + 1;
// $gap = intval(ceil(100 / $max_val));

// echo "
// <div class=\"graph $mode\" style=\"width: $size; height: $size; background-color: $background_color;\">
// ";


// $index = 0;
// foreach ($datas as $value) {
//     $top_val = strval(100 - 100 * $value / $max_val) . "%";
//     $left_val = strval($index * $gap) . "%";

//     echo "
//     <div class=\"point\" data-value=\"$value\" style=\"position: absolute; top: $top_val; left: $left_val; background-color:$point_color;\"></div>
//     ";
//     $index += 1;
// }

// for ($i = 0; $i <= $max_val; $i++) {
//     $pos_value = strval($i * $gap) . "%";
//     $x_val = $max_val - $i;
//     $y_val = $i;
//     echo "
//     <div class=\"grid grid-x\" style=\"position: absolute; top: $pos_value\">
//         <p style=\"position: absolute; left: -10px; transform: translateX(-100%) translateY(-50%);\">$x_val</p>
//     </div>
//     <div class=\"grid grid-y\" style=\"position: absolute; left: $pos_value\">
//         <p style=\"position: absolute; bottom: -10px; transform: translateY(100%) translateX(-50%);\">$y_val</p>
//     </div>
//     ";
// }

// if ($mode == "line") {
//     for ($i = 1; $i < count($datas); $i++) {
//         $top_val = strval(100 - 100 * ($datas[$i] + $datas[$i - 1]) / 2 / $max_val) . "%";
//         $left_val = strval(($i + $i - 1) / 2 * $gap - ($gap / 2)) . "%";

//         $width = pythagoras($gap, ($datas[$i] - $datas[$i - 1]) * $gap);
//         $inverse_sin_val = -asin((($datas[$i] - $datas[$i - 1]) * $gap) / $width);
//         $rotation = "0rad";
//         var_dump($width);

//         $width = strval($width) . "%";

//         echo "
//         <div class=\"line\" style=\"position: absolute; top: $top_val; left: $left_val; background-color:$line_color; width: $width; transform: rotate($rotation)\"></div>
//         ";
//         $index += 1;
//     }
// }

// echo "
// </div>
// ";

// }