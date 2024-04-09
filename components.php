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

function table(array $datas, string $button_text, string $button_url)
{
    if (count($datas)) {
        $gtc = str_repeat('1fr ', count($datas[0]) + 1);
        echo "<div class=\"table\" style=\"grid-template-columns: $gtc\">";
        foreach ($datas[0] as $key => $val):
            echo "<div class=\"header\">";
            echo underscore_strip($key);
            echo "</div>";
        endforeach;
        echo "<div class=\"header\"></div>";

        foreach ($datas as $index => $data):
            foreach ($data as $key => $value):
                echo "<div class=\"row\">";
                echo underscore_strip($value);
                echo "</div>";
            endforeach;
            $id = $data["id"];
            echo "<div class=\"row action-button\"><button onclick=\"window.location.href='$button_url?id=$id' \">$button_text</button></div>";
        endforeach;
        echo "</div>";
        return true;
    } else {
        return false;
    }

}