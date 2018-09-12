<?php
require_once 'parcel.php';
session_start();
$ver=$_SESSION['version']; 


// если новый участок
if (isset($_POST['txt'])){
	$txt=$_POST['txt'];
	unset($_SESSION['crd']);
	unset($_POST['txt']);
}

// Режим отображения
if (isset($_SESSION['vmod'])){
	$vmod=$_SESSION['vmod'];
}else {$vmod='tbl';}
 
 $crd= new parcel();
  
 if (!isset($_SESSION['crd'])){
 	$crd->ltxt($txt);
 	$sqr=$crd->sqr();
 }
 else { // если добавлен контур
 			if (isset($_POST['bndadd'])){
			$crd=unserialize($_SESSION['crd']);
			$txt=$_POST['bndadd'];
			$crd->ltxt($txt);
			unset($_POST['bndadd']);
		 } 

 }	
 $crd->setbndbox(1, 0);
 $_SESSION['crd']=serialize($crd);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"  "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="icon" type="image/png" href="IMG/mapf.png" />
<link rel="stylesheet" type="text/css" href="css/checkbox.css">
<link rel="stylesheet" type="text/css" href="css/TableData.css">

<script type="text/javascript" src="jquery.js"></script>  
<script type="text/javascript" src="calck.js"></script>  

<title>Редактор Координат</title>

</head>

<body>

<div id="topdiv">
<a href='index.php'> <img id='btn_home' alt="На главную!" src="IMG\home1.png" ></a>
<a href='#' onclick='setmap()'> <img id='btn_map' alt="Вид" src="IMG\map.png" ></a>
<a href='#' onclick='settable()'> <img id='btn_table' alt="Вид" src="IMG\table.png" ></a>
<table id="toptbl">
<tr><td colspan="8" style="font-size: 24pt; font-family: courier; padding:8px;"> РЕДАКТОР КООРДИНАТ <?php echo"<span id='version'>$ver</span>" ?><br></td></tr>
<tr id="inf">
<td class="pr">Площадь:</td> <td class="vl" id="sqr"><?php echo $crd->rsqr(5);?></td>
<td class="pr">Контуров: </td><td class="vl" id="bcnt"> <?php echo $crd->bounds_count();?></td>
<td class="pr">Точек:</td><td class="vl" id="pcnt"><?php echo $crd->point_cnt(); ?></td>
<td class="pr">СК:</td> <td class="vl"> <?php echo $crd->coordsys; ?></td>
</tr>
</table>
</div>
<div id='col1'>
		<a href='#' onclick="showadd();"><div class="head" > Добавить Контур </div></a>	
			<div id="bnddiv"><?php echo $crd->boundtable();?><br>
		<br></div>
</div>


<div id='col2'>
		<a href='#' id="bndlink" ><div class="head"  > Скачать Файл Контура </div></a>	
		<div id="pntdiv"><?php echo $crd->bounds[1]->pointtable();?></div>
</div>


<div id='col3'>
<a href='getfile.php'><div class="head">Скачать Файл Участка</div></a>	
<div id="txtcoord"><pre><?php echo $crd->getparfile();?></pre><br></div>
</div>



<div id='col4' onMouseDown="start(event)" onmousemove="move(event)" onmouseup="stop(event)" onwheel="zoom(event)">
<img id="marker" src="IMG\marker.png">
<div id="convas" style="width: 100%; height:100%">
<?php  echo $crd->getsvg();
$_SESSION['crd']=serialize($crd);?>
</div>
</div>






<div id='shdw' onclick="cncl()"></div>




<div id="addbnd" >
<form action="calc.php" id="sendbnd" method="post" >
<table >
	<tr style='height: 70px;'>
		<td colspan="2" style="text-align: center; font-size: 28pt; font-family: calib;  color:navy;">
			<div id="capt">Добавить контур</div>
		</td>
	<tr style='height: 520px;'>
		<td >
				<div id="inpf">
 					<textarea name="bndadd" id="bndadd" ></textarea>
 				</div>
 		</td>
 		<td style="font-size: 14pt; font-family:courier; color:black; padding:20px; text-align: left;">
 		<span style="font-family: courier; font: 20pt bold; text-align: center; width: 100%; color:darkred; display: block; ">
 		!!! Внимание !!!</span>
		<ul>
			<li>Перед каждый контуром должна быть строчка с символом <b>#</b>  <hr></li>
			<li>Формат строки может быть: N	X Y (Номер точки [TAB] Координата X [TAB] Координата Y)<hr></li>
			<li>Формат строки может быть: X Y (Координата X [TAB] Координата Y)<hr></li>
			<li><b>[TAB]</b> - Символ табуляции или пробел<hr></li>
		</ul><br>
			<span style="text-align: center; width: 100%;  display: block; ">
				<input type="submit" value="Добавить" class='sbtn'>
				<input type="reset" value="Отмена" class='sbtn' onclick="cncl()">
			</span>
		</td>

</table>
</form>
  </div>




<div id="infdv">  

</div>


</body>
</html>