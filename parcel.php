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
	
	function rx(){
		return round($this->x*1000);
	}
	
	function ry(){
		return round($this->y*1000);
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
	
	public $max;			// Максимальные координаты 
	public $min;			// Минимальные координаты
	
	public $polygon;		// Полиго участка (Текст описания для SVG)
	
	private	$pcnt; 			// Количество точек в контуре 	
	private $sq;			// Площадь контура
	
	public $log;			// Отладочная информация
	
	
//--------------------------------------- Конструктор класса -------------------------------------------------------
function __construct(){
		$this->sq=0;
		$this->pcnt=0;
		$this->act=TRUE;
		$this->max = new point(0, 0);
		$this->min = new point(0, 0);
		$this->log='';
	}
	

//------------------------------------		Формируем таблицу точек контура		------------------------------------	
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
	
	
//--------------------------------- Возвращает статус активности контура -------------------------------------------	
	function active(){
		return $this->act;
	}	
	

//------------------------------------------------------------------------------------------------------------------
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
			if(count($t)==2){
				$x=$t[0];
				$y=$t[1]; 
				$this->addpoint($x, $y);
			}
			// если есть еще номер точки в начале (его игнорируем)
			if(count($t)==3){
				$x=$t[1];
				$y=$t[2]; 
				$this->addpoint($x, $y);
			}
		}
	}	
	
	
//------------------------------------			Добавление точки		-------------------------------------
	function addpoint($x,$y){
		$this->points[$this->pcnt]=new point($x, $y);
		$this->plog("Count", $this->pcnt);
	
		if($this->pcnt==0){
			$this->plog("PCNT", $this->pcnt);
				
			$this->max = clone ($this->points[$this->pcnt]);
			$this->min = clone $this->points[$this->pcnt];
		}

		$this->pcnt++;
		$this->sq=0;

		// Debag
	
		$this->plog("x", $x);
		$this->plog("max_x", $this->max->x);
		$this->plog("min_x", $this->min->x);
				
		
		
		// Максимум и минимум
		
		if ($x > $this->max->x){ $this->max->x = $x; }
		if ($y > $this->max->y){ $this->max->y = $y; }
 	    if ($x < $this->min->x){ $this->min->x = $x; }
		if ($y < $this->min->y){ $this->min->y = $y; }

		
	}

//-------------------------------------			Подсчет площади			-------------------------------------	
	function sqr(){
		if ($this->sq==0){ 				//если площадь еще не посчитана
			$tmp = $this->points; 		// Копируем массив точек и добавляем первую точку в конец массива
			$tmp[$this->pcnt]= new  point($this->points[0]->x, $this->points[0]->y);
			$ts=0;
				
			for ($i=0;$i<$this->pcnt;$i++){
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
/*	function printcoord() {
		$tbl='<table>';
		foreach ($this->points as $p) {
			$tbl=$tbl."<tr><td>$p->x</td><td>$p->y</td></tr>";
		}
		$tbl=$tbl.'</table>';
		
		$tbl.='MAX_X:'.$this->max->x.'<hr>';
		
		return $tbl;
	}*/
	
//------------------------------- Вывод  координат в текст  ---------------------------------------
	function txtcoord($i) {
		$txt='';
		foreach ($this->points as $p) {
			$txt.=$i++.'	'.str_pad($p->x,16,' ',STR_PAD_RIGHT)."	".str_pad($p->y,16,' ',STR_PAD_RIGHT).PHP_EOL;
		}
		$txt=str_replace('.', ',', $txt);
		return $txt;
	}	
		
//-------------------------				Возвращаем количество точек			------------------------------------------
	function pcount(){
		return $this->pcnt;
	}
	
// ----------------------------- 			Реверс точек		--------------------------------------------------	
	function reverse_cord(){
		$t=array_reverse($this->points);
		$this->sq=0;
		$this->points=$t;
	}
	
//-----------------------------		Формирование SVG  контура	---------------------------------------------
	function getsvg($mx,$my){
		$pnt='';
		$c='M';
		foreach ($this->points as $pt){
			$x=$pt->rx()-$mx;
			$y=$pt->ry()-$my;
			$this->polygon.="$y,$x ";
			$pnt.="$c $y $x ";
			$c="L";		
		}
		return  $pnt.'Z';
	}


	
//--------------------------------		Возвращает полигон участка			--------------------------------------------- 
	
	function getpol($i){
		
		$pol='<polygon id="pl'.$i.'" onmousedown="select('.$i.');" class="lnk" onmouseout="hdinf();" onmouseover="inf('.$i.');" points="'.$this->polygon.'" />';
		
		return $pol;
	}
		
	
	
// ------------------         Формирование рамки отображения контура (viewbox) -----------------------------------------	
	function vbox($mx,$my,$zm){
		
		/*
			zm- коэфициент увеличения 
			0 - контур вписывается в рамку
			при увеличении рамка расширяется - контур уменьшается 
 		*/

		$x1 = $this->min->rx() - $mx;
		$y1 = $this->min->ry() - $my;

		$x2 = abs( $this->max->rx() - $this->min->rx() );
		$y2 = abs( $this->max->ry() - $this->min->ry() );
		
		$x1 = $x1 - ( $zm * $x2 );
		$y1 = $y1 - ( $zm * $y2 );

		$x2 = $x2 + ( $zm * $x2 ) * 2;
		$y2=$y2+($zm*$y2)*2;
		
		$vbox['y1'] = $y1;
		$vbox['x1'] = $x1;
		$vbox['y2'] = $y2;
		$vbox['x2'] = $x2;
		
		return $vbox;
	}
	
// ------------------------------------      Краткая информация о контуре		------------------------------	
	function getinf($num){
		$inf='Номер контура: '.$num;
		$inf.='<br>Количество точек: '.$this->pcnt;
		$inf.='<br>Площадь : '.$this->sqr();
		return $inf;		
	}




// Ведение лога +++++++++++++++++++++++++++++++++++++++++ 
function plog($var,$val){
	$this->log.=$var.' = '.$val.'<br>';
}

}


//***********************************************************************************************************
//*****											Класс участка											*****
//***********************************************************************************************************

class parcel{

	public  $bounds=array();	//	Массив участков
	private $sq;				//	Рассчитаная площадь
	private $bcnt; 				//	Количество контуров
	private $cfg;  				//	Начальная конфигурация
	private $mainbound; 		// 	Номер главного контура
	
	public $max_x;				
	public $max_y;
	public $min_x;
	public $min_y;
	
	// Видимая рамка
	public $x1;
	public $y1;
	public $x2;
	public $y2;
	
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
}


//-------------------------------		 Находим площадь участка		-----------------------------	
	function sqr(){ 

		$maxsq=0; //Площадь самого большого контура
		$tmpsq=0; // Суммарная дырок
		if($this->sq==0){
			// Обходим массив контуров и находим с максимальной площадью
			foreach ($this->bounds as $bnd){
			 if ($bnd->active()){ // Только активные контура	
					$tmpsq+=abs($bnd->sqr());
					if(abs($bnd->sqr())>$maxsq){$maxsq=abs($bnd->sqr());}
				}
			}	
			
			$tmpsq-=$maxsq;		
			
			$i=1;
			$fb=new bound();
			$fb=$this->bounds[1]; // Сохраняем первый контур
			$imax=1;	
			
			foreach ($this->bounds as $bnd){
				if ($bnd->active()==true){
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

			// -------  Если основной контур не первый cтавим в начало --------
			if($imax>1){
				$this->bounds[1]=$this->bounds[$imax];
				$this->bounds[$imax]=$fb;
			}

			$this->sq=$maxsq-$tmpsq;
		}
		return $this->sq;	
}

//------------------------------         Сразу округленная площадь ----------------------------------------
function rsqr($r){
	return round($this->sqr(),$r);
}

//----------------------				 Возвращаем таблицу контурав			--------------------------
	function boundtable(){
		$btbl='<table id="bounds">';
		$i=1;
		
		foreach ($this->bounds as $bnd){
			if ($bnd->rsqr(4)>0){$st='class="mainbound"';}else {$st='';}
			
			
			
			
			$btbl.="<tr id='bnd_$i' $st onclick='select($i);' ondblclick='slctzm($i);'>";
			$btbl.="<td># $i</td>";
			$btbl.="<td style='text-align:right;'>".$bnd->rsqr(4)."</td>";
			$btbl.="<td>".$bnd->pcount()."</td>";
			if ($i>1){
				
				if($bnd->active()){$chk='checked';}else{$chk='';}
				
				$btbl.='<td><label class="checkbox">
						<input type="checkbox" id="chkb'.$i.'" '.$chk.' onclick="updinf('.$i.');" />
						<div class="checkbox__text"></div>
						</label></td>';} else {	$btbl.="<td></td>";
						
					}
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
		$pr.="!".$this->bounds_count()."	$this->coordsys	".$this->point_cnt().PHP_EOL;
		
		$bi=1;
		$i=1;
		foreach ($this->bounds as $bnd){
			if($bnd->active()){
				$pr.='#'.$bi.';----------------------------------------------'.PHP_EOL;
				$pr.=$bnd->txtcoord($i);
				$i+=$bnd->pcount();
				$bi++;
			}
		}
		$pr.='@';
		return $pr;
	}
	
// ---------------------------------Скачать  Отдельный контур  --------------------------------
	function getboundfile($bid){
		$bnd=new bound();
		$bnd=$this->bounds[$bid];
		if ($bnd->sqr()<0){$bnd->reverse_cord();}
		$pr=";".PHP_EOL;
		$pr.="; Система координат: ".$this->coordsys.PHP_EOL;
		$pr.="; Площадь:".$bnd->sqr().PHP_EOL;
		$pr.="; Контуров : 1";
		$pr.="; Точек : ".$bnd->pcount().PHP_EOL;
		$pr.=";".PHP_EOL;
		$pr.="!1	$this->coordsys	".$bnd->pcount().PHP_EOL;
		$pr.='#1;----------------------------------------------'.PHP_EOL;
		$pr.=$bnd->txtcoord(1);
		$pr.='@';
	return $pr;
	}
	
// ------------------------------------------------------------- 	Подсчет общего количество точек в участке 
	function point_cnt(){
		$pcnt=0;
		foreach ($this->bounds as $bnd){
			if($bnd->active()){	
				$pcnt+=$bnd->pcount();
			}	
		}
		return $pcnt;		
	}
	
// ---------------------------------  Количество активных контуров	 --------------------------------
	function bounds_count(){
		$bcnt=0;
		foreach ($this->bounds as $bnd){
			if($bnd->active()){$bcnt++;}
		}
		return $bcnt;
	}

	
	
	
function bndset($b,$v){
		$this->sq=0;
		$this->bounds[$b]->act=$v;
	}
	
	
	
// -----------------------------------------	Генерация 	SVG
	function getsvg(){
			$mx=$this->bounds[1]->min->rx();
			$my=$this->bounds[1]->min->ry();
			
			
			$vbox=$this->bounds[1]->vbox($mx,$my,0);
			$this->savebox($vbox);
			
			$x1=$this->x1;
			$y1=$this->y1;
			$x2=$this->x2;
			$y2=$this->y2;

			
			$text='';
			$lnk='';
			
			$svg='';
			
			$i=1;		
			foreach ($this->bounds as $bnd){
				
				if($bnd->active()){	$svg.=$bnd->getsvg($mx,$my).PHP_EOL;}
				
				if($i>1){
					$lnk.=$bnd->getpol($i);
				}
				$i++;
			}
			echo '<svg   id="svg" viewBox="'."$y1 $x1 $y2 $x2".'">';
			echo '<path d="'.$svg.'"  />'.$lnk.'</svg>';
	}
	
//	
	
function setbndbox($bid,$zm){
	$mx=$this->bounds[1]->min->rx();
	$my=$this->bounds[1]->min->ry();
	
	$vbox=$this->bounds[$bid]->vbox($mx,$my,$zm);
	$this->savebox($vbox);
	return $vbox;
}
//--------- сохранение координат рамки отображения
function savebox($vbox){
	$this->x1=$vbox['x1'];
	$this->y1=$vbox['y1'];
	$this->x2=$vbox['x2'];
	$this->y2=$vbox['y2'];
}	

function mx(){
	return $this->bounds[1]->min->rx();
}	
	
function my(){
	return $this->bounds[1]->min->ry();
}


// Ведение лога	
	function plog($var,$val){
		$this->log.=$var.' = '.$val.'<br>';		
	}	
}
?>