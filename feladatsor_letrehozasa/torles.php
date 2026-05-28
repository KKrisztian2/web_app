<?php 
	session_start();
	include('../includes/overall/db_connect.php');
	$feladatsor = $_GET["feladatsor"];
	$sql = "DELETE FROM kerdes_feladat WHERE feladatsor_id='".$feladatsor."'";
	$result = $conn->query($sql);
	$sql = "DELETE FROM kiadott_feladatsorok WHERE feladatsor_id='".$feladatsor."'";
	$result = $conn->query($sql);
	$sql = "DELETE FROM feladatsorok WHERE id='".$feladatsor."'";
	$result = $conn->query($sql);

	include('../includes/overall/db_disconnect.php');
	header('Location: index.php');
	exit();

?>