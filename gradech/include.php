<?php
header('Content-type: text/plain; charset=utf-8');

if( strpos($_SERVER['HTTP_REFERER'], '//gradech.skkedu.net') === false )
{
    echo 'error: Access not permitted';
    exit;
} 
else 
{
    $dir = './php/';

    include( './php/connect.php' );

    include( './php/changeBoard.php' );
    include( './php/changeGrade.php' );

    if( isset($_GET['getMember']) )
    {
        $g = new Grade();
        if( $g->getMember( $_GET['getMember'] ) == 0 )
        {
            echo '[]';
        } 
        else
        {
            echo json_encode($g->getMember( $_GET['getMember'] ));
        }
        exit;
    }

    if( isset($_GET['change']) )
    {
        $g = new Grade();
        $newman = $g->getMember( $_GET['change'] ); // array
        $g->changeGrade( $newman );
        exit;
    }

    if( isset($_GET['delete']) )
    {
        $g = new Grade();
        $newman = $g->getMember( $_GET['delete'] ); // array
        $g->deleteGrade( $newman );
        exit;
    }

    if( isset($_GET['archive']) )
    {
        $a = new Board();
        $a->archiveArticles( $_GET['archive'] );
    }
}
?>