<?php
	$db_conx = mysqli_connect("localhost", "root","root","test12");



//Evaluate the connection

if(mysqli_connect_errno()){
	echo "Connection problems with database. Please try again later!";
	exit();
} 

?> 

