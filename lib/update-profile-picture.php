<?php
require_once './autoload.php';
/*
 * DIT SCRIPT DOET DE VERWERKING VOOR ELK BESTAND DAT MET DROPZONE
 * OPGELADEN WORDT
 */
$storeFolder = "../img/users/";    //WAAR BESTANDEN OPSLAAN?

//ONDERSTAANDE CODE WORDT PER VERZONDEN BESTAND UITGEVOERD
if ( ! empty($_FILES) )
{
    $tempFile = $_FILES['file']['tmp_name'];                //TIJDELIJKE BESTANDSNAAM
    $targetPath = dirname(__FILE__) . "/$storeFolder/";     //DOELFOLDER
    $targetFile = $targetPath . "profile-picture-" . $_SESSION['usr_id'];          //VOLLEDIGE DOELNAAM

    //bestand verplaatsen van de tmp folder naar de definitieve bestemming
    if ( ! move_uploaded_file($tempFile, $targetFile) )
    {
        header('HTTP/1.1 400 Bad request', true, 400);
        print "Verplaatsen van bestand $tempFile naar $targetFile is mislukt.<br>";
    }
    else
    {
        print "Bestand $targetFile opgeladen.<br>";
    }
}
