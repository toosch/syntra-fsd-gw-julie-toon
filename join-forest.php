<?php
require_once './lib/autoload.php';

if ($_SESSION['auth']) {

printHTML("Mycelium", "head.html");
printNav("Join a forest!");

$extra_elements['csrf_token'] = GenerateCSRF("join-forest.php");

$html = file_get_contents("./templates/join-forest-form.html");
$html = buildExtraElements($html, $extra_elements);

print $html;

print file_get_contents('./templates/bottom-page.html');

} else {

    $subtitle = "Oops, you can't enter the forest here... go find another way explorer!";
    printHTML($subtitle, "logout.html");

}