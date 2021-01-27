<?php
require_once './lib/autoload.php';

//error_reporting( E_ALL );
//ini_set( 'display_errors', 1 );


echo printHTML("Mycelium: register", "head.html");
echo printHTML("register", "headerReg.html");

//get data
if ( count($old_post) > 0 )
{
    $data = [ 0 => [
        "usr_name" => $old_post['usr_name'],
        "usr_email" => $old_post['usr_email'],
        "usr_password" => $old_post['usr_password'],
        "forest_invite" => $old_post['forest_invite']
    ]
    ];
}
else $data = [ 0 => [ "usr_name" => "", "usr_email" => "", "usr_password" => "", "forest_invite" => "" ]];

//get template
$output = file_get_contents("templates/register.html");

//add extra elements
$extra_elements['csrf_token'] = GenerateCSRF( "register.php"  );

//merge
$output = buildHTML( $output, $data );
$output = buildExtraElements( $output, $extra_elements );
$output = mergeViewWithErrors( $output, $errors );
$output = removeEmptyErrorTags( $output, $data );

print $output;

echo file_get_contents('./templates/bottom-page.html');