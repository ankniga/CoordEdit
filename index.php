<?php session_start();
if (!isset($_SESSION['crd'])) {  unset( $_SESSION['crd']);}
$ver=$_SESSION['version']='2.2';
?>

<html>
<head>
<title>Преобразование координат</title>
<link rel="stylesheet" type="text/css" href="css\TableData.css">

</head>

<body style="display: flex; background-color: #e0e0e0;  overflow: hidden;">


<div id="topdiv" style="display:flex; font-size: 24pt; font-family: courier; padding:8px; height:50px;box-shadow: 0px 5px 19px -8px rgba(0,0,0,0.8);">
	<div style="margin:auto;">РЕДАКТОР КООРДИНАТ <?php echo"<span id='version'>$ver</span>"?></div>
</div>

<div id="cform">
	<form action="calc.php" method="post" >
	<table style="width: 1100px;"><tr>
	<td><div id="inpf"><textarea ondrop="drop(event)" id="txtinp" rows="30" cols="55" name="txt"></textarea></div></td> 
<td style="font: 16pt courier;color:black; padding-left:35px;">  
<span style="font: 24pt bold; text-align: center; width: 100%; color:navy; display: block; "><u>ИНСТРУКЦИЯ</u></span>
<ul>
	<li>В программе NKA_NET сохраните координаты участка в текстовый файл<hr></li>
	<li>Откройте файл координат в текстовом редакторе (Например Блокнот)<hr></li>
	<li>Выделите весь текст (<b>CTRL+A</b>)<hr></li>
	<li>Скопируйте в буфер обмена (<b>CTRL+C</b>)<hr></li>
	<li>Вставте из буфера обмена координаты в соседнее поле (<b>CTRL+V</b>)<hr></li>
	<li>Нажмите кнопку <b>[Далее]</b><hr></li>
</ul><br>

<span style="text-align: center; width: 100%;  display: block; ">
	<input type="submit" value="Далее" class='sbtn'>
	<input type="reset" value="Очистить" class='sbtn'>
</span>
</td></tr></table>
</form>
</div>

</body>
</html>