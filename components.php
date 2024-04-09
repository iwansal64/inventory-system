<?php

include_once ("./functions.php");

function navbar()
{
    $navigation_links = array(
        "Warehouse" => "./",
        "Shelf" => "./shelf.php",
        "Borrowers" => "./borrowers.php",
        "Admins" => "./admins.php",
        "Timelines" => "./timelines.php"
    );

    $current_url = $_SERVER["REQUEST_URI"];
    $count = 1;
    $current_url = str_replace("/projects/inventory-system", "", $current_url, $count);
    $current_url = explode("?", $current_url)[0];
    $current_url = "." . $current_url;

    echo "<nav class=\"navbar\">
        <div class=\"top-side\">
            <div class=\"logo\">
                <h1><span>Mitra</span> Inventory</h1>
            </div>
            <div class=\"search\">
                <input type=\"text\" class=\"search-input\" placeholder=\"Search anything..\">
            </div>
            <div class=\"accounts\">
                <button class=\"notifications\">N</button>
                <button class=\"account\">A</button>
                <button class=\"search-button\" onclick=\"
                document.getElementsByClassName('search')[0].classList.toggle('active');
                \">S</button>
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