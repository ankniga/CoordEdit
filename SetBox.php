<?php
require_once 'parcel.php';
session_start();
$crd= new parcel();

$crd=unserialize($_SESSION['crd']);
	$crd->x1=$_SESSION['tx'];
	$crd->y1=$_SESSION['ty'];
	
$_SESSION['crd']=serialize($crd);
?>