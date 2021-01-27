<?php
require_once "autoload.php";

$sending_form_uri = $_SERVER['HTTP_REFERER'];
$owner = GetData("SELECT pst_usr_id FROM posts WHERE pst_id = '" . $_POST['del_pst_id'] ."'");


if($_SESSION['usr_id'] == $owner[0][0]) {

    $sql = "DELETE FROM posts WHERE pst_id = " . $_POST['del_pst_id'];
    var_dump($sql);

    ExecuteSQL($sql);

    header( "Location: " . $sending_form_uri );

} else {

    $_SESSION['errors']['del_error_msg'] = "Hey now, you can't delete someone else's post!";
    header( "Location: " . $sending_form_uri );
}




