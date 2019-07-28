$(function(){
				
				/*$('.collapse_border tr:even').css('background-color','#D2FFD2');
				$('.collapse_border tr:odd').css('background-color','#F5F5F5');
				
				

				$('#pf_sortableTable1 tr:even').css('background-color','#D2FFD2');
				$('#pf_sortableTable1 tr:odd').css('background-color','#F5F5F5');
				
				$('.academics tr:even').css('background-color','#D2FFD2');
				$('.academics tr:odd').css('background-color','#F5F5F5');
				
				$('.bg tr:even').css('background-color','#D2FFD2');
				$('.bg tr:odd').css('background-color','#F5F5F5');*/
				
				
				$('.collapse_border tr:even').css('background-color','#EFEFEF');
				$('.collapse_border tr:odd').css('background-color','#fff');
				//#EFEFEF; color:#E4E4E4
				

				$('#pf_sortableTable1 tr:even').css('background-color','#9CB6C5');
				$('#pf_sortableTable1 tr:odd').css('background-color','#F5F5F5');
				
				$('.academics tr:even').css('background-color','#EFEFEF');
				$('.academics tr:odd').css('background-color','#fff');
				
				$('.bg tr:even').css('background-color','#9CB6C5');
				$('.bg tr:odd').css('background-color','#F5F5F5');
		})
		
	
		
	function small_page_2($print_page)
	{
			window.open($print_page,
			'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=no,width=640,height=600,directories=no,location=no');
			return false;
	
	}
	
	function member_reg_page($print_page)
	{
			window.open($print_page,
			'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=no,width=700,height=600,directories=no,location=no');
			return false;
	
	}
	
	
		

	function small_page($print_page)
	{
			window.open($print_page,
			'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=no,width=640,height=400,directories=no,location=no');
			return false;
	
	}
	
	function small_page21($print_page)
	{
			window.open($print_page,
			'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=no,width=640,height=100,directories=no,location=no');
			return false;
	
	}
	
	function print_info($print_page)
	{
			window.open($print_page,
			'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=no,width=640,height=640,directories=no,location=no');
			return false;
	
	}
	
	function print_info3($print_page)
	{
			window.open($print_page,
			'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=no,width=640,height=640,directories=no,location=no');
			return false;
	
	}
	
	
		function print_info2($print_page)
	{
			window.open($print_page,
			'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=no,width=1200,height=640,directories=no,location=no');
			return false;
	
	}


function x(page,str)
{
if (str=="")
  {
  document.getElementById("txtHint").innerHTML="";
  return;
  }
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","page?id="+str,true);
xmlhttp.send();
}


function timedRefresh(timeoutperiod)
{
	
	setTimeout("location.reload(true);",timeoutperiod );	
}

