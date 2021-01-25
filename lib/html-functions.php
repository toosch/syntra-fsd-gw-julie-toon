<?php

//GENERAL HTML FUNCTION
function printHTML($title, $template){
    $html = "templates/" . $template;
    $html = file_get_contents($html);
    $html = str_replace("@title@", $title, $html);
    print $html;
}

function printNav($title){
    $template_nav = "templates/nav.html";
    $html_nav = file_get_contents($template_nav);
    $html_nav = str_replace("@title@", $title, $html_nav);
    $html_nav = addForestsToNav($html_nav);
    print $html_nav;
}

function buildHTML($template, $data)
{
    $returnValue = "";
    foreach ($data as $row) {
        $output = $template;
        foreach (array_keys($row) as $value) {
            $output = str_replace("@$value@", $row["$value"], $output);
        }
        $returnValue .= $output;
    }
    return $returnValue;
}

function buildExtraElements( $template, $elements )
{
    foreach ( $elements as $key => $element )
    {
        $template = str_replace( "@$key@", $element, $template );
    }
    return $template;
}

function mergeViewWithErrors( $template, $errors )
{
    foreach ( $errors as $key => $error )
    {
        $template = str_replace( "@$key@", "<p style='color:red'>$error</p>", $template );
    }
    return $template;
}

function removeEmptyErrorTags( $template, $data )
{
    foreach ( $data as $row )
    {
        foreach( array_keys($row) as $field )  //eerst "img_id", dan "img_title", ...
        {
            $template = str_replace( "@$field" . "_error@", "", $template );
        }
    }

    return $template;
}