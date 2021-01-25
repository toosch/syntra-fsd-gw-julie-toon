<?php

function GoHome()
{
    global $app_root;

    header("Location: " . $app_root . "/profile.php");
    exit;
}