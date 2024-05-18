<?php

include_once ("./utilities/security.php");

if (logout()) {
    header("Location: ./login.php");
} else {
    header("Location: ./");
}