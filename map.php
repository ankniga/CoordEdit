<?php
require_once 'parcel.php';
session_start();

$crd = new parcel();

$crd = unserialize($_SESSION['crd']);



// -------------------- Перемещение и масштабирование к выбранному контуру
if (isset($_POST['bid'])){
	$bid=$_POST['bid'];
	if ($bid>1){$zm=0.5;}else {$zm=0;}
    	$vbox=$crd->setbndbox($bid, $zm);
		$x1=$vbox['x1'];
		$y1=$vbox['y1'];
		$x2=$vbox['x2'];
		$y2=$vbox['y2'];
		
		echo "$y1 $x1 $y2 $x2";
		
}
// ---------------------------------- Перемещение ------------------------------------


if (isset($_POST['dy'])){

	$dy=$_POST['dy'];
	$dx=$_POST['dx'];
	$dm=$_POST['dm'];
	
	$x1=$crd->x1;
	$y1=$crd->y1;
	$x2=$crd->x2;
	$y2=$crd->y2;

	$x1-=round($dx*$x2/$dm);
	$y1+=round($dy*$x2/$dm);

	$_SESSION['tx']=$x1;
	$_SESSION['ty']=$y1;
	echo "$y1 $x1 $y2 $x2";
}



// ---------------------------------Зууууум-------------------------------------------------
if (isset($_POST['z'])){

	$z=$_POST['z'];
	
	$dy=$_POST['dsy'];
	$dx=$_POST['dsx'];
	$wt=$_POST['wt'];
	
	if(!isset($_SESSION['zm'])){$zm=0;}else {$zm=$_SESSION['zm'];}
	$zm-=0.2*$z;
	
	$x1=$crd->x1;
	$y1=$crd->y1;
	$x2=$crd->x2;
	$y2=$crd->y2;
	

	$x1+=round($dx*$x2/$wt);
	$y1-=round($dy*$x2/$wt);

	$x1=round($x1-($zm*$x2));
	$y1=round($y1-($zm*$y2));
	$x2=round($x2+($zm*$x2)*2);
	$y2=round($y2+($zm*$y2)*2);

	$x1-=round($dx*$x2/$wt);
	$y1+=round($dy*$x2/$wt);
	
	$crd->x1=$x1;
	$crd->y1=$y1;
	$crd->x2=$x2;
	$crd->y2=$y2;
	echo "$y1 $x1 $y2 $x2";
}


$_SESSION['crd']=serialize($crd);

?>
