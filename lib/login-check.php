<?php
require_once 'autoload.php';

$query = '  SELECT * FROM users
                        JOIN users_forests ON users.usr_id = users_forests.usr_id
                        JOIN forests ON forests.for_id = users_forests.for_id
            WHERE (usr_email = "'.$_POST['usr_email'].'")';

$result = GetData($query);

if ($result == []) {

    header("Location: ../index.php?err=email");

} elseif (password_verify($_POST['usr_password'], $result[0]['usr_password'])) {
    $_SESSION['usr_name'] = $result[0]['usr_name'];
    $_SESSION['auth'] = TRUE;
    $_SESSION['usr_id'] = $result[0]['usr_id'];
    $_SESSION['usr_for_id'] = $result[0]['usr_for_id'];
    $_SESSION['active_forest_name'] = $result[0]['for_name'];

    $homeUrl = "../home.php";
    header("Location: $homeUrl");

} else {

    header("Location: ../index.php?err=pswd");

}


