<?php

session_start();

//SETUP ROUTE VARIABLES
$request_uri = explode("/", $_SERVER['REQUEST_URI']);
$app_root = "/" . $request_uri[1];

require_once 'db-credentials.php';
require_once 'pdo.php';
require_once 'html-functions.php';
require_once 'element_functions.php';
require_once 'security.php';
require_once 'routes.php';
require_once 'validate.php';
require_once 'sanitize.php';

$errors = [];

if ( key_exists( 'errors', $_SESSION ) AND is_array( $_SESSION['errors']) )
{
    $errors = $_SESSION['errors'];

}

//initialize $msgs array
$msgs = [];

if ( key_exists( 'msgs', $_SESSION ) AND is_array( $_SESSION['msgs']) )
{
    $msgs = $_SESSION['msgs'];

}

//initialize $old_post
$old_post = [];

if ( key_exists( 'OLD_POST', $_SESSION ) AND is_array( $_SESSION['OLD_POST']) )
{
    $old_post = $_SESSION['OLD_POST'];

}

//EMPTY ARRAYS
$_SESSION['errors'] = [];
$_SESSION['OLD_POST'] = [];
$_SESSION['msgs'] = [];

