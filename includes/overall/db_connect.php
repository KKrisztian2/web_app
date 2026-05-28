<?php
	$conn=new mysqli("localhost","root","","szakdolgzat_wiqpm2");
	$conn->set_charset("utf8mb4");
		if($conn->connect_error){
			die($conn->connect_error);
		}
		mysqli_query($conn, "SET NAMES utf8");
?>