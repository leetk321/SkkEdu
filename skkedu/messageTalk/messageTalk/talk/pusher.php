<script src="http://js.pusher.com/2.1/pusher.min.js"></script>
<script type="text/javascript">
	var pusher = new Pusher('ed7eab9df3f1bad93db1');
	var channel = pusher.subscribe('my-channel');
	
	channel.bind('my-event', function(data) {
  		alert('An event was triggered with message: ' + data.message);
	});
</script>
<?php
/*
 * Created on 2013. 9. 4.
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>
TEST PUSHER.
