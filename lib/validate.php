<?php
require_once "autoload.php";

function CompareWithDatabase( $table, $pkey ): void
{
        $data = GetData( "SHOW FULL COLUMNS FROM $table" );

    //overloop alle in de databank gedefinieerde velden van de tabel
    foreach ( $data as $row )
    {
        //haal veldnaam en veldtype uit de databank
        $fieldname = $row['Field']; //bv. img_title
        $can_be_null = $row['Null']; //bv. NO / YES

        list( $type, $length, $precision ) = GetFieldType( $row['Type'] );

        //zit het veld in $_POST?
        if ( key_exists( $fieldname, $_POST) )
        {
            $sent_value = $_POST[$fieldname];

            //INTEGER type
            if ( in_array( $type, explode("," , "INTEGER,INT,SMALLINT,TINYINT,MEDIUMINT,BIGINT" ) ) )
            {
                //is de ingevulde waarde ook een int?
                if ( ! isInt($sent_value) ) //nee
                {
                    $msg = $sent_value . " moet een geheel getal zijn";
                    $_SESSION['errors'][ "$fieldname" . "_error" ] = $msg;
                }
                else //ja
                {
                    $_POST[$fieldname] = (int) $sent_value;
                }
            }

            //FLOAT/DOUBLE type
            if ( in_array( $type, explode("," , "FLOAT,DOUBLE" ) ) )
            {
                //is de ingevulde waarde ook numeriek?
                if ( ! is_numeric($sent_value) ) //nee
                {
                    $msg = $sent_value . " moet een getal zijn (eventueel met decimalen)";
                    $_SESSION['errors'][ "$fieldname" . "_error" ] = $msg;
                }
                else //ja
                {
                    $_POST[$fieldname] = (float) $sent_value;
                }
            }

            //STRING type
            if ( in_array( $type, explode("," , "VARCHAR,TEXT" ) ) )
            {
                //is de tekst niet te lang?
                if ( strlen($sent_value) > $length )
                {
                    $msg = "Dit veld kan maximum $length tekens bevatten";
                    $_SESSION['errors'][ "$fieldname" . "_error" ] = $msg;
                }
            }

            //DATE type
            if ( $type == "DATE" )
            {
                if($sent_value == null){
                    continue;
                }
                elseif ( ! isDate( $sent_value) )
                {
                    $msg = $sent_value . " is geen geldige datum";
                    $_SESSION['errors'][ "$fieldname" . "_error" ] = $msg;
                }
            }

            //other types ...
        }
    }
}

function isInt($value) {
    return is_numeric($value) && floatval(intval($value)) === floatval($value);
}

function isDate($date) {
    return date('Y-m-d', strtotime($date)) === $date;
}

function GetFieldType( $definition )
{
    $length = 0;
    $precision = 0;

    //zit er een haakje in de definitie?
    if ( strpos( $definition, "(" ) !== false )
    {
        $type_parts = explode(  "(", $definition );
        $type = $type_parts[0];
        $between_brackets = str_replace( ")", "", $type_parts[1] );

        //zit er een komma tussen de haakjes?
        if ( strpos( $between_brackets, "," ) !== false )
        {
            list( $length, $precision ) = explode( ",", $between_brackets);
        }
        else $length = (int) $between_brackets; //cast int type
    }
    //geen haakje
    else $type = $definition;

    $type = strtoupper( $type ); //bv. INTEGER

    return [ $type, $length, $precision ];
}

function ValidateUsrEmail( $email )
{
    if (filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        return true;
    }
    else
    {
        $_SESSION['errors']['usr_email_error'] = "This is not a valid email address";
        return false;
    }
}

function CheckUniqueUsrEmail( $email )
{
    $sql = "SELECT * FROM users WHERE usr_email='" . $email . "'";
    $rows = GetData($sql);

    if (count($rows) > 0)
    {
        $_SESSION['errors']['usr_email_error'] = "This email address has already been registered";
        return false;
    }

    return true;
}

function validateUsrPassword($pswd, $pswdCheck) {
    if(strlen($pswd) < 8){
        $msg = "Password must be at least 8 characters long";
        $_SESSION['errors'][ "usr_password_error" ] = $msg;
    } elseif ($pswd != $pswdCheck) {
        $msg = "Your passwords don't match, try again";
        $_SESSION['errors'][ "usr_password_error" ] = $msg;
    }
}


function validateForestCode($inviteCode){
    $query = "SELECT * FROM forests WHERE for_invite_code = '".$inviteCode."'";
    $forest = GetData($query);
    if(count($forest) === 1){
        $_SESSION['forest']['forest_id'] = $forest[0]['for_id'];
        return true;

    } else {
        $_SESSION['errors']['forest_invite_error'] = "This forestcode is not valid";
        return false;
    }
}
