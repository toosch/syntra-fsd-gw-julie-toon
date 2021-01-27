<?php
require_once './lib/autoload.php';
//error_reporting( E_ALL );
//ini_set( 'display_errors', 1 );

$_SESSION["upload-image"] = uniqid($_SESSION['usr_id'] . "_", true);


if ($_SESSION['auth']) {

    echo printHTML("Mycelium", "head.html");
    echo printNav($_SESSION['active_forest_name']);

    //CSRF
    $extra_elements['csrf_token'] = GenerateCSRF("home.php");

    //GET TEMPLATE
    $newForm = file_get_contents("./templates/new-post-form.html");

    $newForm = str_replace("@csrf_token@", $extra_elements['csrf_token'], $newForm);
    $newForm = str_replace("@usr_for_id@", $_SESSION['usr_for_id'], $newForm);
    $newForm = str_replace("@usr_id@", $_SESSION['usr_id'], $newForm);

    if(empty($errors)){
        $newForm = str_replace("@del_error_msg@", "", $newForm);

    } else {
        $newForm = str_replace("@del_error_msg@", $errors['del_error_msg'], $newForm);
    }

    echo $newForm;

    $listPosts = listPosts($_SESSION["usr_for_id"]);
    $listPosts = str_replace("@csrf_token@", $extra_elements['csrf_token'], $listPosts);

    echo $listPosts;

    echo(viewNext());

    echo file_get_contents('./templates/bottom-page.html');

} else {

    $subtitle = "Oops, you can't enter the forest here... go find another way explorer!";
    printHTML($subtitle, "logout.html");

}

