<?php

function underscore_strip($str)
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