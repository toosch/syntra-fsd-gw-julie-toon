<?php
require_once 'autoload.php';

$query = "SELECT for_id, for_name FROM forests WHERE for_invite_code = '".$_POST['forest_invite']."'";
$forest = GetData($query);
$query = 'INSERT INTO users_forests VALUES ('.$_SESSION['usr_id'].', '.$forest[0]['for_id'].')';

$_SESSION['usr_for_id'] = $forest[0]['for_id'];
$_SESSION['active_forest_name'] = $forest[0]['for_name'];

ExecuteSQL($query);

// return to the homepage
header("Location: ../home.php");