var md=0;
var sb=1;

var x=0;
var y=0;
var x1=0;
var y1=0;

var wt=0;
var ht=0;
var dm=0; 


$(window).resize(marker);

$(window).keydown(arrows);

function arrows(e){
	var rowCount = $('#bounds tr').length;
	
    if( e.keyCode == 38  &&  sb > 1) {
        sb--;
    } else if( e.keyCode == 40  && sb < (rowCount)) {
        sb++;
    } else return;
    select(sb);
}

$(document).ready(function() {
    var dropZone = $('#bndadd'),
        maxFileSize = 1000000; // максимальный размер файла - 1 мб.
});

function setmap(){
	$('#col2').hide();
	$('#col3').hide();
	$('#col4').show();
	$('#btn_map').hide();
	$('#btn_table').show();
}


function settable() {
	$('#col2').show();
	$('#col3').show();
	$('#col4').hide();
	$('#btn_map').show();
	$('#btn_table').hide();
}


function showadd(){  $('#shdw').show();
					 $('#addbnd').show();	};

function cncl(){
					$('#shdw').hide();
    				$('#addbnd').hide();	
    				$('#bndadd').val('');};

 function send(){
					$('#sendbnd').submit();

}
 
 

 

 //**********************************************************************
 // 					Увеличить и выбрать контур 				
 //**********************************************************************

 function slctzm(sl)
 {	sb=sl;
	//------------------------ Приближение к контуру ---------------------------------	  	
  select(sl);

			$.ajax({
					type: "POST",
			  		url: "map.php",
			        data: "bid="+sl,
			        success: function(msg){
				        $('svg').removeAttr('viewbox');
				        $('svg').each(function () { $(this)[0].setAttribute('viewBox', msg) });
				        marker(); 
			        }});
			
 }


//**********************************************************************
// 				Маркер на выбранный контур 				
//**********************************************************************
function marker(){
	if(sb==1){
		$('#marker').hide();
	}else{

		wt=$('#svg').width();
		ht=$('#svg').height();
	$('#marker').show();

	var rect=$('#pl'+sb)[0].getBoundingClientRect();
	var left=Math.round(rect.left-10+rect.width/2);
	var top=Math.round(rect.top-30+rect.height/2);
	$('#marker').css('left', left + 'px');
	$('#marker').css('top', top + 'px');
	}
}


//**********************************************************************
// 				Information Update				
//**********************************************************************
function updinf(sl){

	$.ajax({
		type: "POST",
  		url: "setbnd.php",
        data: "bid="+sl+"&sw="+$('#chkb'+sl).prop("checked"),
        success: function(msg){
        	var obj=$.parseJSON(msg);
        	console.log(msg+'1111');
        	$('#sqr').html(obj.sqr);
          	$('#pcnt').html(obj.pcnt);
          	$('#bcnt').html(obj.bcnt);
        }});
	
	$.ajax({
		type: "POST",
  		url: "getfile.php",
        data: "nf=1",
        success: function(msg){
          	$('#txtcoord').html('<pre>'+msg+'</pre>');
        }});

	//--------
	$.ajax({
		type: "POST",
  		url: "getmap.php",
        data: "nf=1",
        success: function(msg){
          	$('#convas').html(msg);
        }});

	
}


 
 //**********************************************************************
 // 						Выбор контура 				
 //**********************************************************************
function select(sl){
		$('#bndlink').attr("href", "getboundfile.php?bid="+sl);
			
		if(sl>0){sb=sl;}
		
		$('.pol').each(function () { $(this)[0].setAttribute('class', 'lnk') });
		
		if($('#chkb'+sb).prop("checked")){
			$('#bnd_'+sb).removeClass('dsbl');
			$('#pl'+sb).each(function () { 
				$(this)[0].setAttribute('class', 'pol') });
			
		}else{
			$('#pl'+sb).each(function () { $(this)[0].setAttribute('class', 'bndoff') });

			if(sb>1){ $('#bnd_'+sb).addClass('dsbl');}
		}
		
		$('.issel').removeClass('issel');
		
		$('#bnd_'+sb).addClass('issel');
	
		$.ajax({
			type: "POST",
	  		url: "bound.php",
	        data: "bid="+sl,
	        success: function(msg){
	          	$('#pntdiv').html(msg);
	        }});
	marker();
 }


function start(e) {
	
	select(0);
	md=1;
	x=e.pageX;
	y=e.pageY;
	
	
	wt=$('#col4').width();
	ht=$('#col4').height();
	if(wt > ht){dm=ht;}else{dm=wt;}
	
	
}

function stop(e){
	md=0;
	$('.lnk').show();
	$.ajax({url: "SetBox.php"});
}
//-----------------------------------------------------------------------------------------------------
function move(e){
    
	if(md>0){
		$('.lnk').hide();
		var dy=(x-e.pageX);
		var dx=(y-e.pageY);

		$.ajax({
				type: "POST",
		  		url: "map.php",
		        data: "dy="+dy+"&dx="+dx+'&dm='+dm,
		        success: function(msg){
		        $('svg').removeAttr('viewbox');
		        $('svg').each(function () { $(this)[0].setAttribute('viewBox', msg); marker(); });
				}});
		}	
}


// ---------------------------------------------  Zoooooom--------------------------------)))

function zoom(e){

	wt=$('#svg').width();
	ht=$('#svg').height();
	var dsx=(ht/2)-(e.clientY-$('#svg').position().top);
	var dsy=(wt/2)-(e.clientX-$('#svg').position().left);
	
	if(wt > ht){
		wt=ht;
		}
	
	$.ajax({
		type: "POST",
  		url: "map.php",
        data: "z="+Math.sign(e.wheelDelta)+"&dsy="+dsy +"&dsx="+dsx +'&wt='+wt,
        success: function(msg){
        $('svg').removeAttr('viewbox');
        $('svg').each(function () { $(this)[0].setAttribute('viewBox', msg); marker(); });
		}});
	$("#svg").css('stroke-width','-1%', function(){$("#svg").css('stroke-width','0');});
}


//****************************************************************************************
//*****						   Вывод информации о контуре 							******
//****************************************************************************************
var flinf = true;

function inf(bid){
	if (flinf){
	$.ajax({
	  	type: "POST",
			url: "infbnd.php",
      	data: "bid="+bid,
      	success: function(msg){
      	$('#infdv').html(msg);
     // 	$('#infdv').offset({top: e.clientY, left:e.clientX});
      	$('#infdv').show();
	  	}});
  	flinf=false;
}}
//****************************************************************************************
//*****						   Скрытие информации 		 							******
//****************************************************************************************

function hdinf(bid){ 	
	$('#infdv').hide();
	flinf=true;
 }







