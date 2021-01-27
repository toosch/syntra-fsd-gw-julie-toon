<?php
require_once './autoload.php';

// --> controleert tot welke forests de gebruiker toegang heeft
$query = 'SELECT forests.for_id, forests.for_name FROM users 
                        JOIN users_forests ON users.usr_id = users_forests.usr_id
                        JOIN forests ON forests.for_id = users_forests.for_id
                        WHERE users.usr_id = '.$_SESSION['usr_id'] ;
$result = GetData($query);

foreach ($result as $forest) {
    if ($forest['for_id']===htmlspecialchars($_GET["forest_id"])) {
        $_SESSION['usr_for_id'] = $_GET["forest_id"];
        $_SESSION['active_forest_name'] = $forest["for_name"];
        header("Location: ../home.php");
    }
}
// IF NOT REDIRECTED TO FOREST --> USER DOES NOT HAVE ACCESS
header("Location: ../home.php");

