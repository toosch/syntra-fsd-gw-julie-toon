<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

session_start();
session_destroy();

$subtitle = "You are logged out, but we know you'll be back soon...";

$html = file_get_contents('./templates/logout.html');
print $html = str_replace("@title@", $subtitle, $html);

