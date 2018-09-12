<?php
require_once 'parcel.php';
session_start();
//header('Content-type: text/html; charset=windows-1251');
if (isset($_POST['bid'])){
	$crd= new parcel();
	$bid=$_POST['bid'];
	$crd=unserialize($_SESSION['crd']);
	
	
	echo $crd->bounds[$bid]->getinf($bid);


}
?>