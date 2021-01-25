<?php
require_once './lib/autoload.php';

    //GET CSRF
    $extra_elements['csrf_token'] = GenerateCSRF("login-check.php");

    //GET TEMPLATE
    $template = file_get_contents('./templates/login.html');

    //PRINT SUCCESS MESSAGE IF ANY
    if(count($msgs) > 0){
        foreach ($msgs as $msg){
            $template = str_replace("@msg@", $msg, $template);
        }
    } else {
        $template = str_replace("@msg@", "", $template);
    }

    //PRINT ERROR MESSAGE IF ANY
    if(isset($_GET['err'])){
        if($_GET['err'] == 'email'){
            $template = str_replace("@error-msg@", "<div class='login__form__error'>Wrong email</div>", $template);
        } else {
            $template = str_replace("@error-msg@", "<div class='login__form__error'>Wrong password</div>", $template);
        }
    } else {
        $template = str_replace("@error-msg@", "", $template);
    }

    //MERGE CSRF WITH TEMPLATE
    $output = buildExtraElements($template, $extra_elements);

    print $output;