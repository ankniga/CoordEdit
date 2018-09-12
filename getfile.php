<?php
require_once 'parcel.php';
session_start();
$crd= new parcel();

$crd=unserialize($_SESSION['crd']);

if(isset($_POST['nf'])){
//header('Content-type: text/html; charset=windows-1251');
}
else{
header('Content-disposition: attachment; filename=parcel.txt');
header('Content-type: text/plain');}
// далее записываем в файл текст

echo $crd->getparfile();

?>