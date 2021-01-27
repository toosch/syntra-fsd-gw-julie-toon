<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
require_once "autoload.php";

SaveFormData();

function SaveFormData()
{
    global $app_root;

    if ( $_SERVER['REQUEST_METHOD'] == "POST" )
    {

        $formname = $_POST['formname'];

        //Specifiek voor profile page
        if($formname == 'profile'){
            if ( ! isset( $_POST['btnUpdate'] ) )
            {
                GoHome(); exit;
            }
        }

        //controle CSRF token
        if ( ! key_exists("csrf", $_POST)) die("Missing CSRF");
        if ( ! hash_equals( $_POST['csrf'], $_SESSION['last_csrf'] ) ) die("Problem with CSRF");

        $_SESSION['last_csrf'] = "";

        //SANITIZATION

        if($formname != "post" OR $formname != "comment") {
            $_POST = StripSpaces($_POST);
            $_POST = ConvertSpecialChars($_POST);
        }

        $table = $pkey = $update = $insert = $where = $str_keys_values = "";

        //get important metadata
        if ( ! key_exists("table", $_POST)) die("Missing table");
        if ( ! key_exists("pkey", $_POST)) die("Missing pkey");

        $table = $_POST['table'];
        $pkey = $_POST['pkey'];

        //VALIDATION

        $sending_form_uri = $_SERVER['HTTP_REFERER'];
        CompareWithDatabase( $table, $pkey );

        if($formname == 'register'){
            if(validateForestCode($_POST['forest_invite'])){
                $forest = $_SESSION['forest']['forest_id'];
            };
            ValidateUsrEmail( $_POST['usr_email'] );
            CheckUniqueUsrEmail( $_POST['usr_email'] );
            ValidateUsrPassword( $_POST['usr_password'], $_POST['usr_checkPassword']);
        }

        if($formname == 'profile' AND $_POST['usr_password'] != ''){
            ValidateUsrPassword( $_POST['usr_password'], $_POST['usr_checkPassword']);
        }

        //terugkeren naar afzender als er een fout is
        if ( count($_SESSION['errors']) > 0)
        {
            $_SESSION['OLD_POST'] = $_POST;
            header( "Location: " . $sending_form_uri ); exit();
        }

        // INSERT OR UPDATE

        if ( $_POST["$pkey"] > 0 ) $update = true;
        else $insert = true;

        if ( $update ) $sql = "UPDATE $table SET ";
        if ( $insert ) $sql = "INSERT INTO $table SET ";

        //make key-value string part of SQL statement
        $keys_values = [];

        foreach ( $_POST as $field => $value )
        {
            //skip non-data fields
            if ( in_array( $field, [ 'formname', 'table', 'pkey', 'afterinsert', 'afterupdate', 'csrf', 'usr_checkPassword', 'btnUpdate', 'forest_invite' ] ) ) continue;

            //handle primary key field
            if ( $field == $pkey )
            {
                if ( $update ) $where = " WHERE $pkey = $value ";
                continue;
            }

            if ( $field == "usr_password" ) //encrypt usr_password
            {
                $value = password_hash( $value, PASSWORD_BCRYPT );
                $keys_values[] = " $field = '$value' " ;

            }
            else //all other data-fields
            {
                $keys_values[] = " $field = '$value' " ;
            }
        }

        //ADD TIMESTAMP AND USER ID if post or comment
        if($formname == "post" OR $formname == "comment" ){
            $userId = $_SESSION['usr_id'];
            $ts = new DateTime( 'NOW', new DateTimeZone('Europe/Brussels') );
            $ts = $ts->format('Y-m-d H:i:s');
            if($formname == "post")
            {
            array_push($keys_values, "pst_date = '$ts'" );
            array_push($keys_values, "pst_usr_id = '$userId'" );
            }
            if($formname == "comment")
            {
            array_push($keys_values, "comm_date = '$ts'" );
            array_push($keys_values, "comm_usr_id = '$userId'" );
            }
        }

        if($formname == "register") {
            array_push($keys_values, "usr_for_id = '$forest'");
        }

        // ADD IMAGE IF POST----------------
        if($formname == "post") {
            $imageCode = $_SESSION['upload-image'];
            array_push($keys_values, "pst_img = '$imageCode'" );
        }
        //----------------------------------

        $str_keys_values = implode(" , ", $keys_values );


        //extend SQL with key-values
        $sql .= $str_keys_values;

        //extend SQL with WHERE
        $sql .= $where;


        //run SQL
        $result = ExecuteSQL( $sql );

        // ENKEL FOR REGISTER: specifiek koppeltabel users-forest ---------

        if($formname == 'register'){
            $usr_name = $_POST['usr_name'];
            // Daarom zoeken we eerst de ID van de nieuwe gebruiker...
            $query = "SELECT usr_id FROM users WHERE usr_name = '$usr_name'";
            $usr_id = GetData($query)[0]['usr_id'];

            // En voegen die toe aan de koppeltabel met de juiste for_id
            $query = 'INSERT INTO users_forests VALUES ('.$usr_id.', '.$forest.')';
            ExecuteSQL($query);
        }

        //SUCCES MESSAGES REGISTRATION, PROFILE UPDATE------------------------------

        $affected = $result->rowCount();

        if($affected > 0){
            if($formname == 'profile') {
                $_SESSION['msgs'][] = "Your profile has been updated!";
            }
            if ($formname == 'register'){
                $_SESSION['msgs'][] = "You have succesfully registered, welcome!";
            }
        }

//        IF NOT REDIRECTED
//        print $sql ;
//        print "<br>";
//        print $affected . " records affected";

        //REDIRECT
        if ( $insert AND $_POST["afterinsert"] > "" ) header("Location: ../" . $_POST["afterinsert"] );
        if ( $update AND $_POST["afterupdate"] > "" ) header("Location: ../" . $_POST["afterupdate"] );
    }
}
