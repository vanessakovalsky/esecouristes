/////////////////// Plug-in file for CalendarXP 9.0 /////////////////
// This file is totally configurable. You may remove all the comments in this file to minimize the download size.
/////////////////////////////////////////////////////////////////////

///////////// Calendar Onchange Handler ////////////////////////////
// It's triggered whenever the calendar gets changed to y(ear),m(onth),d(ay)
// d = 0 means the calendar is about to switch to the month of (y,m); 
// d > 0 means a specific date [y,m,d] is about to be selected.
// e is a reference to the triggering event object
// Return a true value will cancel the change action.
// NOTE: DO NOT define this handler unless you really need to use it.
////////////////////////////////////////////////////////////////////
function fOnChange(y,m,d,e) {
	if (gContainer.gfFlat_s) gContainer.gfFlat_s.fSetCal(y,m,d,false);
	return false;  // return true to cancel the change.
}


///////////// Calendar AfterSelected Handler ///////////////////////
// It's triggered whenever a date gets fully selected.
// The selected date is passed in as y(ear),m(onth),d(ay)
// e is a reference to the triggering event object
// NOTE: DO NOT define this handler unless you really need to use it.
////////////////////////////////////////////////////////////////////
// function fAfterSelected(y,m,d,e) {}


///////////// Calendar Cell OnDrag Handler ///////////////////////
// It triggered when you try to drag a calendar cell. (y,m,d) is the cell date. 
// aStat = 0 means a mousedown is detected (dragstart)
// aStat = 1 means a mouseover between dragstart and dragend is detected (dragover)
// aStat = 2 means a mouseup is detected (dragend)
// e is a reference to the triggering event object
// Return true (when aStat=0) to skip the set-date process, as well as any related event handlers (e.g. fAfterSelect).
// NOTE: DO NOT define this handler unless you really need to use it.
////////////////////////////////////////////////////////////////////
// function fOnDrag(y,m,d,aStat,e) {}



////////////////// Calendar OnResize Handler ///////////////////////
// It's triggered after the calendar panel has finished drawing.
// NOTE: DO NOT define this handler unless you really need to use it.
////////////////////////////////////////////////////////////////////
function fOnResize() {
	if (NN4) return;	// re-stretch doesn't work in NN4
	// Strech-fix: resize the internal layer in accordance with the highest cell of the same week because the agenda contents could stretch the cell higher than preset value.
	// This fix is not required if the contents never overflow or you never use box-effect or gbFocus.
	var divs=IE4?document.all.tags("DIV"):document.getElementsByTagName("DIV"), cells=[];
	var j=0, maxH=0;
	for (var i=0;i<divs.length;i++)
		if (divs[i].className=="CalCell") {
			cells[j++]=divs[i];
			maxH=Math.max(maxH,divs[i].offsetHeight);
			if (j%7==0&&giCellHeight<maxH) {
				for (var k=1;k<=7; k++)
					cells[j-k].style.height=maxH+"px";
				maxH=0;
			}
		}
}

////////////////// Calendar fOnWeekClick Handler ///////////////////////
// It's triggered when the week number is clicked.
// NOTE: DO NOT define this handler unless you really need to use it.
////////////////////////////////////////////////////////////////////
// function fOnWeekClick(year, weekNo) {}


////////////////// Calendar fIsSelected Callback ///////////////////////
// It's triggered for every date passed in as y(ear) m(onth) d(ay). And if 
// the return value is true, that date will be rendered using the giMarkSelected,
// gcFGSelected, gcBGSelected and guSelectedBGImg theme options.
// NOTE: If NOT defined here, the engine will create one that checks the gdSelect only.
////////////////////////////////////////////////////////////////////
// function fIsSelected(y,m,d) {
//		return gdSelect[2]==d&&gdSelect[1]==m&&gdSelect[0]==y;
// }

// ====== predefined utility functions for use with agendas. ========
// load an url in the window/frame designated by "framename".
function popup(url,framename) {	
	var w=parent.open(url,framename,"top=200,left=200,width=400,height=200,scrollbars=1,resizable=1");
	if (w&&url.split(":")[0]=="mailto") w.close();
	else if (w&&!framename) w.focus();
}

// return the d(ate) of the q-th n-day of a specific m(onth) in a specific y(ear)
function getDateByDOW(y,m,q,n) { 
// q: 1 - 5 ( 5 denotes the last n-day )
// n: 0 - Sunday, 1 - Monday ... 6 - Saturday
	var dom=new Date(y,m-1,1).getDay();
	var d=7*q-6+n-dom;
	if (dom>n) d+=7;
	if (d>fGetDays(y)[m]) d-=7;
	return d;	// ranged from 1 to 31
}

// ====== Following are self-defined and/or custom-built functions! =======

gbHideTop=true;

// We create a function here to show you how to append multiple events to a day
// Note: Must use onmousedown for sub-events, onclick won't work in sub-events!!
function fAppendEvent(y, m, d, message, action, bgcolor, fgcolor, bgimg, boxit, html) {
	var ag=fGetEvent(y,m,d);
	if (ag==null) fAddEvent(y, m, d, message, action, bgcolor, fgcolor, bgimg, boxit, html);
	else fAddEvent(y, m, d, message?ag[0]+"\n"+message:ag[0], action?action:ag[1], bgcolor?bgcolor:ag[2], fgcolor?fgcolor:ag[3], bgimg?bgimg:ag[4], boxit?boxit:ag[5], ag[6]+html);
}

