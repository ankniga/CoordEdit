<?php
require_once 'parcel.php';
session_start();

// Включение выключение контура исправить
	$crd= new parcel();
	$bid=$_POST['bid'];  // Номер контура
	$sw=$_POST['sw'];	 // Положение переключателя	
	$crd=unserialize($_SESSION['crd']);

	if($sw=='true'){$sw=TRUE;}else {$sw=FALSE;}
	
	$crd->bndset($bid, $sw);
	
	$ans['sqr']=$crd->rsqr(5);
	$ans['pcnt']=$crd->point_cnt();
	$ans['bcnt']=$crd->bounds_count();
	$_SESSION['crd']=serialize($crd);
	echo json_encode($ans);
?>