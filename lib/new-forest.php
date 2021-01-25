<?php
require_once './autoload.php';

$uniqueInviteCode = uniqid();

// create a new forest with the name in the $_POST variable
$query = 'INSERT INTO forests (for_name, for_invite_code) VALUES ("'. htmlspecialchars($_POST['new_forest_name']) .'", "'. $uniqueInviteCode .'")';
ExecuteSQL($query);
// id van de nieuwe forest...
$query = "SELECT for_id FROM forests WHERE for_invite_code = '".$uniqueInviteCode."'";
$forestId = GetData($query)[0]['for_id'];
var_dump($forestId);
// ook in de koppeltabel!
$userId = $_SESSION['usr_id'];
$query = "INSERT INTO users_forests VALUES ($userId, $forestId)";
ExecuteSQL($query);

// nodig om de naam op de homepage te updaten.
$_SESSION['active_forest_name'] = $_POST['new_forest_name'];
// ook de usr forest id moet aangepast worden
$_SESSION['usr_for_id'] = $forestId;


// return to the homepage
header("Location: ../home.php");