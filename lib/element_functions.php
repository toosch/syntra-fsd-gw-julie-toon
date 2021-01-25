<?php

//COLLECT COMMENTS

function listComments($postID){

    $queryComment = 'SELECT comm_body, comm_date, comm_usr_id, usr_name
                            FROM comments
                                LEFT JOIN posts on comm_pst_id = pst_id
                                LEFT JOIN users on usr_id = comm_usr_id
                            WHERE comm_pst_id = '.$postID.' ORDER BY comm_id DESC ';
    $comments = GetData($queryComment);
    $returnComment = "";

    foreach ( $comments as $comment )
    {
        $outputComment = file_get_contents('./templates/comment.html');
        $outputComment = str_replace("@USR_COMM@", $comment['usr_name'], $outputComment);
        $outputComment = str_replace("@COMM_DATE@", $comment['comm_date'], $outputComment);
        $outputComment = str_replace("@COMM_BODY@", $comment['comm_body'], $outputComment);

        $returnComment .= $outputComment;
    }

    return $returnComment;
}

// COLLECT POSTS

function listPosts($forestID){

    //RETRIEVE POSTS
    if(key_exists('offset', $_GET)){
        $offset = $_GET['offset'];
    } else {
        $offset = 0;
    }
    $query = 'SELECT pst_id, pst_usr_id, pst_title, pst_text, pst_img, usr_name, pst_date
                FROM posts
                LEFT JOIN users on usr_id = posts.pst_usr_id
                WHERE pst_for_id = '.$forestID.'
                ORDER BY pst_id desc
                LIMIT 10
                OFFSET '.$offset;


    $result = GetData($query);
    $returnValue ="";

    // RETURN POSTS (WITH THEIR COMMENTS)
    foreach ( $result as $post )
    {
        $outputPost = file_get_contents('./templates/post.html');
        $outputPost = str_replace( "@PST_USR@", $post['usr_name'], $outputPost );
        $outputPost = str_replace( "@PST_ID@", $post['pst_id'], $outputPost );
        $outputPost = str_replace( "@TITLE@", $post['pst_title'], $outputPost );
        $outputPost = str_replace( "@POST_DATE@", date("l d-M-y H:i:s T", strtotime($post['pst_date'])), $outputPost );
        $outputPost = str_replace( "@TEXT@", $post['pst_text'], $outputPost );
        $outputPost = str_replace( "@USR_ID@", $_SESSION['usr_id'], $outputPost );

        // IF IMAGE EXISTS
        if (is_file("./img/users/".$post['pst_img'])) {
            $imgHtml = '<img class="card-img-top" src="./img/users/'.$post['pst_img'].'" alt="post image" />';
            $outputPost = str_replace( "@IMAGE@", $imgHtml, $outputPost );
        } else {
            $outputPost = str_replace( "@IMAGE@", "", $outputPost );

        }

        // ADD COMMENTS TO OUTPUT
        $outputPost = str_replace("@COMMENTS@", listComments($post['pst_id']), $outputPost);
        $returnValue .= $outputPost;

    }
    // zijn er nog posts?
    if (count($result) >= 10) {
        $_SESSION['showmore'] = true;
    } else {
        $_SESSION['showmore'] = false;
    }
    return $returnValue;
}

function viewNext() {
    echo '<div class="w-100 d-flex my-5">';
    $offset = 10;
    if (key_exists('offset', $_GET) AND $_GET['offset'] != 0)
    {
        $offset = (int)$_GET['offset'] + 10;
        $offsetPrev = (int)$_GET['offset'] - 10;
    }
    if (isset($offsetPrev)) echo '<a class="mr-auto" href="./home.php?offset='.$offsetPrev.'"><button type="button" class="btn btn-secondary">Previous Posts</button></a>';
    if ($_SESSION['showmore'] == true)
    {
        echo '<a class="ml-auto" href="./home.php?offset='.$offset.'"><button type="button" class="btn btn-secondary">Next Posts</button></a>';
    } 
        echo '</div>';
    echo '</main>';
    }


function addForestsToNav($template) {
    $query = 'SELECT * FROM users 
                            JOIN users_forests ON users_forests.usr_id = users.usr_id
                            JOIN forests ON forests.for_id = users_forests.for_id
                        WHERE (users.usr_id = '.$_SESSION['usr_id'].')';
    $output = "";
    $result = GetData($query);
    foreach ($result as $forest) {
        $name = $forest['for_name'];
        $id = $forest['for_id'];
        $output .= '<li><a class="dropdown-item" href="./lib/changeforest.php?forest_id='.$id.'">'.$name.'</a></li>';
    }

    // ADD INVITE CODE FOR CURRENT FOREST
    $query = "SELECT for_invite_code FROM forests WHERE for_id = " . $_SESSION['usr_for_id'];
    $inviteCode = GetData($query)[0]['for_invite_code'];
    $template =  str_replace("@INVITECODE@", $inviteCode, $template);
    return str_replace("@FORESTS@", $output, $template);
}

