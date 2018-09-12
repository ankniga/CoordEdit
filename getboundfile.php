<?php
require_once 'parcel.php';
session_start();
$crd= new parcel();

$crd=unserialize($_SESSION['crd']);

header('Content-disposition: attachment; filename=bound.txt');
header('Content-type: text/plain');
// далее записываем в файл текст
$bid=$_GET['bid'];


echo $crd->getboundfile($bid);

?>