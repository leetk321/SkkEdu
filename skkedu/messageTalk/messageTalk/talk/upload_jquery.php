<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ajax Upload and Resize with jQuery and PHP</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="./js/jquery.form.js"></script>
 <script>
        $(document).ready(function() {
        //elements
        var progressbox     = $('#progressbox');
        var progressbar     = $('#progressbar');
        var statustxt       = $('#statustxt');
        var submitbutton    = $("#SubmitButton");
        var myform          = $("#UploadForm");
        var output          = $("#output");
        var completed       = '0%';

                $(myform).ajaxForm({
                    beforeSend: function() { //brfore sending form
                        submitbutton.attr('disabled', ''); // disable upload button
                        statustxt.empty();
                        progressbox.slideDown(); //show progressbar
                        progressbar.width(completed); //initial value 0% of progressbar
                        statustxt.html(completed); //set status text
                        statustxt.css('color','#000'); //initial color of status text
                    },
                    uploadProgress: function(event, position, total, percentComplete) { //on progress
                        progressbar.width(percentComplete + '%') //update progressbar percent complete
                        statustxt.html(percentComplete + '%'); //update status text
                        if(percentComplete>50)
                            {
                                statustxt.css('color','#fff'); //change status text to white after 50%
                            }
                        },
                    complete: function(response) { // on complete
                        output.html(response.responseText); //update element with received data
                        myform.resetForm();  // reset form
                        submitbutton.removeAttr('disabled'); //enable submit button
                        progressbox.slideUp(); // hide progressbar
                    }
            });
        });

    </script>
 <link href="./css/upload.css" rel="stylesheet" type="text/css" />
</head>
<body>

<form action="processupload.php" method="post" enctype="multipart/form-data" id="UploadForm">
<table width="500" border="0">
  <tr>
    <td>File : </td>
    <td><input name="ImageFile" type="file" onchange="document.getElementById('SubmitButton').click();"/></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit"  id="SubmitButton" value="Upload" style="display:none;" /></td>
  </tr>
</table>
</form>
<div id="progressbox"><div id="progressbar"></div ><div id="statustxt">0%</div ></div>
<div id="output"></div>
</body>
</html>