<?php

//*******************************************************
//*****					Класс точки					*****				
//*******************************************************
class point{
	public $x;
	public $y;
	public $err;

	function __construct($x,$y){
		$this->x=$x;
		$this->y=$y;
	}
}

//*******************************************************
//*****					Класс контура				*****
//*******************************************************
class bound{
	public $points=array();
	public $err;			// Признак ошибочночти контура
	public $main;			// Главный контур в участке
	public $act;			//активный контур
	public $max_x;
	public $max_y;
	public $min_x;
	public $min_y;
	
	private	$pcnt; 			// Количество точек в контуре 	
	private $sq;			// Площадь контура
	
	
	function __construct(){
		$this->sq=0;
		$this->pcnt=0;
		$this->act=TRUE;
	}
	
	
	
	function pointtable(){
		$i=1;
		$tbl='<table id="points">';
		foreach ($this->points as $pnt){
			$tbl.="<tr><td>$i</td><td> $pnt->x </td><td style='border-right:none;'> $pnt->y </td>";
			$i++;
		}
		$tbl.="</table>";
		return $tbl;
	}
	
	
	
	
	function active(){
		return $this->act;
	}	
	
	
	function checked(){
		if($this->act){
			return 'checked';
		}else {return '';}
	}
//-------------------------------		Принимаем координаты из текста --------------------------------------	
	function setpoints($txt){
		$str=explode("\n", $txt);
		foreach ($str as $f){
			$f=trim($f);
			$t= preg_split('/[\s]+/', $f);
			// если просто 2 координаты
			if(count($t)==2){$this->points[$this->pcnt++]=new point($t[0], $t[1]); }		
			// если есть еще номер точки в начале (его игнорируем)
			if(count($t)==3){$this->points[$this->pcnt++]=new point($t[1], $t[2]); }
			
		}
	}	
	
	
//------------------------------------			Добавление точки		-------------------------------------
	function addpoint($x,$y){
		$this->pcnt++;
		$this->points[($this->pcnt)-1]=new point($x, $y);
		$this->sq=0;
	}
	

	
	
	
	
//-------------------------------------			Подсчет площади			-------------------------------------	
	function sqr(){
		
		if ($this->sq==0){ //если площадь еще не посчитана

		
			$tmp=$this->points; 		// Копируем массив точек и добавляем первую точку в конец массива
			$tmp[$this->pcnt]= new  point($this->points[0]->x, $this->points[0]->y);
			$ts=0;
			
			
			$this->min_x=$tmp[1]->x;
			$this->min_y=$tmp[1]->y;
			$this->max_x=$tmp[1]->x;
			$this->max_y=$tmp[1]->y;
				
			for ($i=0;$i<$this->pcnt;$i++){
				
				
				// Максимум и минимум
				
					if ($tmp[$i]->x > $this->max_x){$this->max_x=$tmp[$i]->x;}
					if ($tmp[$i]->y > $this->max_y){$this->max_y=$tmp[$i]->y;}
					if ($tmp[$i]->x < $this->min_x){$this->min_x=$tmp[$i]->x;}
					if ($tmp[$i]->y < $this->min_y){$this->min_y=$tmp[$i]->y;}
					
				
				
				$x=$tmp[$i]->x;
				$y=$tmp[$i+1]->y;
				$ts=$ts+($x*$y); // Прямой обход
				}
			for ($i=0;$i<$this->pcnt;$i++){
				$x=$tmp[$i+1]->x;
				$y=$tmp[$i]->y;
				$ts=$ts-($x*$y); //Обратный обход
				}
			$this->sq=$ts/20000;
		}
		return $this->sq;  		
	}
	
//------------------------------- 	Округленная площадь	 ---------------------------------------
	
	function rsqr($r){
		return round($this->sqr(),$r);
	}	
	
//------------------------------- Вывод таблицы с координатами ---------------------------------------
	function printcoord() {
		$tbl='<table>';
		foreach ($this->points as $p) {
			$tbl=$tbl."<tr><td>$p->x</td><td>$p->y</td></tr>";
		}
		$tbl=$tbl.'</table>';
		return $tbl;
	}
	
//------------------------------- Вывод  координат в текст  ---------------------------------------
	function txtcoord($i) {
		$txt='';
		
		foreach ($this->points as $p) {
		//	$txt.=$i++.'	'.str_pad($p->x,16,'0',STR_PAD_RIGHT)."	".str_pad($p->y,16,'0',STR_PAD_RIGHT).PHP_EOL;
			$txt.=$i++.'	'.str_pad($p->x,16,' ',STR_PAD_RIGHT)."	".str_pad($p->y,16,' ',STR_PAD_RIGHT).PHP_EOL;
				
		//	$txt.=$i++.'	'.$p->x."	".$p->y.PHP_EOL;
				
		}
		$txt=str_replace('.', ',', $txt);
		return $txt;
	
	}	
		
//-------------------------------Возвращаем количество точек------------------------------------------
	function pcount(){
		return $this->pcnt;
	}
	
	
	function reverse_cord(){
		
		$t=array_reverse($this->points);
		$this->sq=0;
		$this->points=$t;
		
	}
	
	function getsvg($color){
		$pnt='';
		foreach ($this->points as $pt){
			
			$pnt.=$pt->y.','.(-1)*$pt->x.' ';
			
		}
		
		$svg='<polygon points="'.$pnt.'"fill="'.$color.'"  />';
		
		
		return $svg;
		
	}
	
	
	function vbox($zm){
		
		
		
		
		
		$x1=($this->min_y);
		$y1=-1*($this->max_x);
		
		$x2=abs($this->max_y-$this->min_y);
		$y2=abs($this->max_x-$this->min_x);
	
		
		$x1=$x1-($zm*$x2);
		$y1=$y1-($zm*$y2);

		$x2=$x2+($zm*$x2)*2;
		$y2=$y2+($zm*$y2)*2;
		
		
		$vbox=$x1.' '.$y1.' '.$x2 .' '.$y2;
		
		return $vbox;
	}
	
	
	
}


//*******************************************************
//*****					Класс участка				*****
//*******************************************************

class parcel{
	
	public  $bounds=array();
	private $sq;
	private $bcnt; //Количество контуров
	private $cfg;  //Начальная конфигурация;
	private $mainbound; // номер главного контура
	
	
	public  $coordsys;  //Систма координат
	public  $log;
	
	function __construct(){
		$this->sq=0;
		$this->bcnt=0;
		$this->coordsys=0;
		$this->log='';
	}
	
	function reset(){
		$this->sq=0;
		//$this->bcnt=0;
	//	$this->coordsys=0;
		
	}
	
	
	
	function addbound($txt){
		$this->sq=0;
		$tbnd=new bound();
		$tbnd->setpoints($txt);
		$this->bounds[++$this->bcnt]=$tbnd;
	}
	
// ---------------------------- Обработка текстового файла -----------------------------------

	function ltxt($txt){
	// Предварительная обработка (убираем все лишнее)
		$bnd='';
		$tbnd=explode("\n", $txt);
		foreach ($tbnd as $str)
		{   
			$str=trim($str);
			if($str!=''){
			switch ($str[0]){
				case ';':	break 1; 			// Коментраий
				case '@':	break 1;			// Конец файла
				case '!':	$this->cfg=$str;	//Служебная строка
							$t= preg_split('/[\s]+/', $str);
							$this->coordsys=$t[1];
							break 1;
				case '#': 	$bnd.='#'.PHP_EOL;	//Контур (убираем номер)
							break 1;
				default: 	$str=str_replace(',', '.', $str);
							$bnd.=$str.PHP_EOL;	// Все остальное (Должны остаться только координаты)	  
				}
			}
		}

	$bnd=explode("#", $bnd); 	//Разбиваем по контурам
	unset($bnd[0]);				// первый элемент всегда пустой	
	foreach ($bnd as $b){    	// Загружаем контура в участок
		$this->addbound($b);
	}
	//$this->plog($this->bounds);
}

	function sqr(){ // Находим площадь участка

		$maxsq=0; //Площадь самого большого контура
		$tmpsq=0; // Суммарная дырок
		
		if($this->sq==0){

			// Обходим массив контуров и находим с максимальной площадью
			foreach ($this->bounds as $bnd){
			 if ($bnd->active()){ // Только активные контура	
				$tmpsq+=abs($bnd->sqr());
				if(abs($bnd->sqr())>$maxsq){$maxsq=abs($bnd->sqr());}
				}}	
			
			$this->plog('maxsq', $maxsq);
			$this->plog('tmpsq', $tmpsq);
				
				
			$tmpsq-=$maxsq;		
			
			$this->plog('maxsq', $maxsq);
			$this->plog('tmpsq', $tmpsq);
				
			
			$i=1;
			$fb=new bound();
			$fb=$this->bounds[1]; // Сохраняем первый контур
			$imax=1;	
			
			foreach ($this->bounds as $bnd){
				if ($bnd->active()){
						if(abs($bnd->sqr())==$maxsq){
								$bnd->main=TRUE;
								$imax=$i;
								if ($bnd->sqr()<0){$bnd->reverse_cord();}
							}else {
								$bnd->main=FALSE;
								if ($bnd->sqr()>0){$bnd->reverse_cord();}
							}	
						$i++;
					}}

			// Если основной контур не первый cтавим в начало
			if($imax>1){
				$this->bounds[1]=$this->bounds[$imax];
				$this->bounds[$imax]=$fb;
			}

			$this->plog('maxsq', $maxsq);
			$this->plog('tmpsq', $tmpsq);
				
			
			$this->sq=$maxsq-$tmpsq;
		}
		return $this->sq;	
}

function rsqr($r){
	return round($this->sqr(),$r);
}

//----------------------				 Возвращаем таблицу контурав			--------------------------
	function boundtable(){
		$btbl='<table id="bounds">';
		$i=1;
		
		foreach ($this->bounds as $bnd){
			if ($bnd->rsqr(4)>0){$st='class="mainbound"';}else {$st='';}
			
			
			
			$btbl.="<tr id='bnd_$i' $st onclick='select($i);'>";
			$btbl.="<td># $i</td>";
			$btbl.="<td style='text-align:right;'>".$bnd->rsqr(4)."</td>";
			$btbl.="<td>".$bnd->pcount()."</td>";
			$btbl.="<td><input type='checkbox' name='bnd[$i]'".$bnd->checked()."></td>";
			$btbl.="</tr>";
			$i++;
		}
		$btbl.="</table>";
		return $btbl;
	}

// ---------------------------------  Готовый участок --------------------------------	
	
	function getparfile(){
		$pr=";".PHP_EOL;
		$pr.="; Система координат: ".$this->coordsys.PHP_EOL;
		$pr.="; Площадь:".$this->sqr().PHP_EOL;
		$pr.="; Контуров : ".$this->bounds_count().PHP_EOL;
		$pr.="; Точек : ".$this->point_cnt().PHP_EOL;
		$pr.=";".PHP_EOL;
		$pr.="!$this->bcnt	$this->coordsys	".$this->point_cnt().PHP_EOL;
		
		
		$bi=1;
		$i=1;
		foreach ($this->bounds as $bnd){
		
			$pr.='#'.$bi.';----------------------------------------------'.PHP_EOL;
			$pr.=$bnd->txtcoord($i);
			$i+=$bnd->pcount();
			$bi++;				
		}
		$pr.='@';
		return $pr;
	}
	
	
// ---------------------------------  Общее количество точек в участке --------------------------------
	
	function point_cnt(){
		$pcnt=0;
		foreach ($this->bounds as $bnd){
			$pcnt+=$bnd->pcount();
		}
		return $pcnt;		
	}
	
// ---------------------------------  		Количество контуров		 --------------------------------
	
	function bounds_count(){
		return $this->bcnt;
	}
	
	function bndset($b){
	/*	$i=1;
		foreach ($this->bounds as $bnd){
			if($b[$i]=='on')
		}
		
		*/
	}
	
	
	function getsvg(){
			$col='green';
			echo '<svg id="svg" viewBox="'.$this->bounds[1]->vbox(0).'">>';
//			echo $this->bounds[1]->getsvg();
			foreach ($this->bounds as $bnd){
				echo $bnd->getsvg($col).PHP_EOL;
				$col='white';
			}
			
			
			echo '</svg>';;
		
	}
	
	
	
	
	function plog($var,$val){
		$this->log.=$var.' = '.$val.'<br>';		
	}	
}
?>