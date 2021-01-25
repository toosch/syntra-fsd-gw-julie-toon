<?php
require_once './lib/autoload.php';

if ($_SESSION['auth']) {

    printHTML("Mycelium", "head.html");
    printNav("Plant a new forest!");

    $extra_elements['csrf_token'] = GenerateCSRF("new-forest.php");

    $html = file_get_contents("./templates/new-forest-form.html");

    $html = buildExtraElements($html, $extra_elements);

    print $html;

    print file_get_contents('./templates/bottom-page.html');

} else {

    $subtitle = "Oops, you can't enter the forest here... go find another way explorer!";
    printHTML($subtitle, "logout.html");

}