<?php
require_once 'parcel.php';
session_start();
$crd= new parcel();

$crd=unserialize($_SESSION['crd']);


header('Content-type: text/html; charset=windows-1251');



echo $crd->getsvg();
$_SESSION['crd']=serialize($crd);

?>