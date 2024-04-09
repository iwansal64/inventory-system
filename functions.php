<?php

function underscore_strip($str)
{
    $res = str_replace("_", " ", $str);
    if (is_string($res)) {
        $res = ucwords($res);
    }
    return $res;
}