<?php

include_once ("./database.php");
include_once ("./security.php");

function underscore_uppercase($str)
{
    $res = str_replace("_", " ", $str);
    if (is_string($res)) {
        $res = ucwords($res);
    }
    return $res;
}

function alert(string $text, string $redirect = "")
{
    if ($redirect != "") {
        echo "<script>alert('$text'); window.location.href='$redirect';</script>";
    } else {
        echo "<script>alert('$text')</script>";
    }
}

function export_excel(array $datas, string $filename)
{
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename.xls\"");

    function export($records)
    {
        $heading = true;
        if (!empty($records))
            foreach ($records as $row) {
                if ($heading) {
                    // display field/column names as a first row
                    echo implode("\t", underscore_uppercase(array_keys($row))) . "\n";
                    $heading = false;
                }
                echo implode("\t", underscore_uppercase(array_values($row))) . "\n";
            }
        $username = get_user_data()["username"];
        $conn = connect_to_mysql();
        insert_timeline($conn, "$username exported excel file", "");
        exit;
    }
    export($datas);
}