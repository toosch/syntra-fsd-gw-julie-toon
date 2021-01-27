<?php
require_once './lib/autoload.php';

//error_reporting( E_ALL );
//ini_set( 'display_errors', 1 );

if ($_SESSION['auth']) {
    //PRINT HEAD AND NAV
    echo printHTML("Mycelium: profile", "head.html");
    echo printNav("profile");

    //GET DATA
    $sql = "select * from users where usr_id = " . $_SESSION['usr_id'];
    $data = GetData($sql);

    //CSRF
    $extra_elements['csrf_token'] = GenerateCSRF("profile.php");

    //GET TEMPLATE
    $template = file_get_contents('./templates/profile.html');

    //PRINT MESSAGES IF ANY
    if(count($msgs) > 0){
        foreach ($msgs as $msg){
            $template = str_replace("@msg@", $msg, $template);
        }
    } else {
        $template = str_replace("@msg@", "", $template);
    }

    //MERGE DATA WITH TEMPLATE
    $output = buildHTML($template, $data);
    $output = buildExtraElements($output, $extra_elements);
    $output = mergeViewWithErrors( $output, $errors );
    $output = removeEmptyErrorTags( $output, $data );

    if (is_file("./img/users/profile-picture-".$_SESSION['usr_id'])) {
    $output = str_replace("@USR_IMG@", "./img/users/profile-picture-".$_SESSION['usr_id'], $output);
    } else {
    $output = str_replace("@USR_IMG@", "https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Placeholder_no_text.svg/1200px-Placeholder_no_text.svg.png", $output);
    }

    print $output;

    echo file_get_contents('./templates/bottom-page.html');

} else {
    $subtitle = "Oops, you can't enter the forest here... go find another way explorer!";
    printHTML($subtitle, "logout.html");
}