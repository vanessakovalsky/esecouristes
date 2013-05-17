
Xoffset=-80;
Yoffset= 20;
var isNS4=document.layers?true:false;
var isIE=document.all?true:false;
var isNS6=!isIE&&document.getElementById?true:false;
var old=!isNS4&&!isNS6&&!isIE;

var skn;
function initThis()
{
  if(isNS4)skn=document.d11;
  if(isIE)skn=document.all.d11.style;
  if(isNS6)skn=document.getElementById("d11").style;
}




function popup(_m,_b)
{
  var content="<TABLE  WIDTH=240 BORDER=1 BORDERCOLOR=black CELLPADDING=2 CELLSPACING=0 "+"BGCOLOR="+_b+"><TD ALIGN=left><FONT COLOR=black SIZE=2>"+_m+"</FONT></TD></TABLE>";
  if(old)
  {
    alert("You have an old web browser:\n"+_m);
	return;
  }
  else
  {
	if(isNS4)
	{
	  skn.document.open();
	  skn.document.write(content);
	  skn.document.close();
	  skn.visibility="visible";
	}
	if(isNS6)
	{
	  document.getElementById("d11").style.position="absolute";
	  document.getElementById("d11").style.left=x;
	  document.getElementById("d11").style.top=y;
	  document.getElementById("d11").innerHTML=content;
	  skn.visibility="visible";
	}
	if(isIE)
	{
	  document.all("d11").innerHTML=content;
	  skn.visibility="visible";
	}
  }
}

var x;
var y;
function get_mouse(e)
{
  x=(isNS4||isNS6)?e.pageX:event.clientX+document.body.scrollLeft; 
  y=(isNS4||isNS6)?e.pageY:event.clientY+document.body.scrollLeft; 
  if(isIE&&navigator.appVersion.indexOf("MSIE 4")==-1)
	  y+=document.body.scrollTop;
  skn.left=x+Xoffset;
  skn.top=y+Yoffset;
}


function removeBox()
{
  if(!old)
  {
	skn.visibility="hidden";
  }
}


if(isNS4)
  document.captureEvents(Event.MOUSEMOVE); 
if(isNS6)
  document.addEventListener("mousemove", get_mouse, true);
if(isNS4||isIE)
  document.onmousemove=get_mouse;

