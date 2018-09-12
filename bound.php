<?php
require_once 'parcel.php';
session_start();

// ¬ывод таблицы координат дл¤ выбранного контура
if (isset($_POST['bid'])){
	$crd= new parcel();
	$bid=$_POST['bid'];
	$crd=unserialize($_SESSION['crd']);
	echo $crd->bounds[$bid]->pointtable();
}
?>
