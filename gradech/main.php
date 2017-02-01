<?php
if( strpos($_SERVER['HTTP_REFERER'], '//gradech.skkedu.net') === false )
{
    echo 'error: Access not permitted';
    exit;
} 
?>

var SKK = function()
{
    this.source = './include.php';
    this.groups = {
        edu : 3871,
        math : 3872,
        computer : 3873,
        han : 3874,
        newbie : 3875,
        oldbie : 3876
    }
}

SKK.prototype.ajax = function( query )
{
    var xhr = new XMLHttpRequest();
        xhr.open('GET', this.source + '?' + query, false );
        xhr.send();
    
    if( xhr.status == 200 && xhr.status < 300)
    {
        return xhr.response;
    }
    return false;
}

SKK.prototype.getMember = function( gid )
{
    if( !gid ){ gid = 'newbie'; }
    
    var source = this.ajax( 'getMember=' + this.groups[ gid ] );
    
    return JSON.parse( source );
}

SKK.prototype.countMember = function( gid )
{
    if( !gid ){ gid = 'newbie'; }

    return this.getMember( gid ).length;
}

SKK.prototype.changeGrade = function( gid )
{
    if( !gid ){ gid = 'newbie'; }
    
    var source = this.ajax( 'change=' + this.groups[ gid ] );
    
//    if( source === 'done' )
//    {
        return true;
//    }
//    return false;
}

SKK.prototype.deleteGrade = function( gid )
{
   if( !gid ){ gid = 'newbie'; } 
    
    var source = this.ajax( 'delete=' + this.groups[ gid ] );
    
    return true;
}

SKK.prototype.changeBoard = function( bid )
{
    if( !bid || 'all' ){
        var departments = ['edu', 'math', 'computer', 'han'];
        for( var i=0; i<departments.length; i++ )
        {
            var source = this.ajax( 'archive=' + departments[i] ); 
        }
    } 
    else 
    {
        var source = this.ajax( 'archive=' + bid );
    }
    return true;
}
