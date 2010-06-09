/**
 * Calendar Class
 */
function Calendar(){
	//default date format to use (based on PHP's date function)
	this.dateFormat = 'm/d/Y';

	//Build the div that contains the calendar, and attach it to the document body
	this.calendarContainer = document.createElement("div");
	this.calendarContainer.id = 'calendarContainer';
	this.calendarContainer.style.position = 'absolute';
	this.calendarContainer.style.visibility = 'hidden';
	this.calendarContainer.style.zIndex = '99';
	document.body.appendChild(this.calendarContainer);

	//The today var will be used to highlight "today"
	this.today = new Date();

	//Default the current month/year to todays month/year
	this.current = new Date();
};

/**
 * Used to display the calendar. in the correct position, and set the current
 * date info to whatever is already in the input
 *
 * @param HTMLInputObject target - reference to the input that gets filled
 *
 * @return void
 */
Calendar.prototype.showCal = function (target) {
	this.stopHideCal();
	this.target = target;
	var position = this.getElementPosition(this.target);
	var parentHeight = this.target.offsetHeight;

	this.target.value = this.interpretDate(this.target.value);
	this.selected = new Date(this.target.value);

	if (this.selected.getMonth() >=0 && this.selected.getMonth() <= 11) {
		this.current.setMonth(this.selected.getMonth());
	}

	//Throw in a little logic to see that the year is within 100 years of today...
	//so that we don't have people start at 0001 or something crazy
	if (Math.abs(this.selected.getFullYear()-this.today.getFullYear()) <= 100) {
		this.current.setFullYear(this.selected.getFullYear());
	}

	var thisCal = this;
	this.blurfunc = this.addEvent(this.target, 'blur', function(e) {thisCal.hideCal();});

	this.calendarContainer.style.left = position.left+10+'px';
	this.calendarContainer.style.top = position.top+this.target.offsetHeight+'px';
	this.setSelectVisibility('hidden');
	this.calendarContainer.style.visibility = 'visible';
	this.buildCalendar();
};

/**
 * Used to change the visibility of all select elements in the document. This
 * only affects Internet Explorer, as it is the only browser that will not allow
 * anything to display over a select.
 *
 * @param String vis - what to set the visibility of the selects to
 *
 * @return void
 */
Calendar.prototype.setSelectVisibility = function (vis) {
/*@cc_on

	var selects = document.getElementsByTagName('select');
	for(i in selects) {
		if(typeof(selects[i]) == 'object'){
			selects[i].style.visibility = vis;
		}
	}

@*/
}


/**
 * Used to set the format of the date string inserted into the text input.  Uses
 * formatting from PHP's date function
 *
 * @param {String} dateFormat
 *
 * @return void
 */
Calendar.prototype.setDateFormat = function (dateFormat) {
	this.dateFormat = dateFormat;
}

/**
 * Used to build the actual calendar, and attach it to the calendarContainer div
 *
 * @return void
 */
Calendar.prototype.buildCalendar = function () {
	var thisCal = this;
	var start_month = this.current;
	start_month.setDate(1);
	var start_month_day = start_month.getDay();


	var count = 0;
	var day_class = "";
	var calendarTable = document.createElement("table");
	calendarTable.className = "cal";
	calendarTable.cellPadding = 0;
	calendarTable.cellSpacing = 0;

	var thead = document.createElement("thead");
	var tr = document.createElement("tr");

	//Change Year DOWN button
	var td = document.createElement("td");
	var btn = document.createElement("button");
	this.addEvent(btn, 'click', function() {thisCal.changeMonth(-12);});
	btn.appendChild(document.createTextNode('<<'));
	td.appendChild(btn);
	tr.appendChild(td);

	//Change Month DOWN button
	var td = document.createElement("td");
	var btn = document.createElement("button");
	this.addEvent(btn, 'click', function() {thisCal.changeMonth(-1);});
	btn.appendChild(document.createTextNode('<'));
	td.appendChild(btn);
	tr.appendChild(td);

	//Show month Year
	var td = document.createElement("td");
	td.colSpan = 3;
	td.appendChild(document.createTextNode(this.current.format('M Y')));
	tr.appendChild(td);

	//Change Month UP button
	var td = document.createElement("td");
	var btn = document.createElement("button");
	this.addEvent(btn, 'click', function() {thisCal.changeMonth(1);});
	btn.appendChild(document.createTextNode('>'));
	td.appendChild(btn);
	tr.appendChild(td);

	//Change Year UP button
	var td = document.createElement("td");
	var btn = document.createElement("button");
	this.addEvent(btn, 'click', function() {thisCal.changeMonth(12);});
	btn.appendChild(document.createTextNode('>>'));
	td.appendChild(btn);
	tr.appendChild(td);

	thead.appendChild(tr);

	//Display the days of the week
	var tr = document.createElement("tr");
	tr.className = 'dow';
	for (i=0; i<7; i++) {
		var td = document.createElement("td");
		td.appendChild(document.createTextNode(Date.prototype.dayNames[i].substr(0,2)));
		tr.appendChild(td);
	}
	thead.appendChild(tr);
	calendarTable.appendChild(thead);

	//Display the "Show Today" Link.
	var tfoot = document.createElement("tfoot");
	var tr = document.createElement("tr");
	var td = document.createElement("td");
	td.colSpan = 7;
	var btn = document.createElement("button");
	this.addEvent(btn, 'click', function() {thisCal.showToday();});
	btn.appendChild(document.createTextNode('Show Today'));
	td.appendChild(btn);
	tr.appendChild(td);
	tfoot.appendChild(tr);
	calendarTable.appendChild(tfoot);

	//Create the actual days
	var tbody = document.createElement("tbody");
	var tr = document.createElement("tr");
	//Add calls containing only a hard-space until the day we start on
	for (i=0;i<start_month_day;i++) {
		var td = document.createElement("td");
		td.appendChild(document.createTextNode("\xA0"));
		tr.appendChild(td);
		count++;
	}

	for (i=1;i<=this.current.getDaysInMonth();i++) {
		//adjust this.current
		this.current.setDate(i);
		if (count < 7) {
			day_class = "";
			if (this.current.format('Ym')+this.current.pad(i) == this.today.format('Ymd')) {
				day_class = "today";
			}
			//See if the day we are processing is the "selected" day (currently IN the
			//target input).
			if (this.current.format('Ym')+this.current.pad(i) == this.selected.format('Ymd')) {
				if(day_class != ''){day_class += ' ';}
				day_class += "selected";
			}

			var td = document.createElement("td");

			var btn = document.createElement("button");
			btn.className = day_class;
			//This is part of a closure to allow it to use the correct date...and not
			//the last date in the loop
			var clickFunc = thisCal.getFunction(i);
			this.addEvent(btn, 'click', clickFunc);
			btn.appendChild(document.createTextNode(i));
			td.appendChild(btn);
			count++;
			tr.appendChild(td);
		} else {
			tbody.appendChild(tr);
			var tr = document.createElement("tr");
			count = 0;
			i--;
		}
	}
	//Add calls containing only a hard-space until the end
	for(count=count;count<7;count++) {
		var td = document.createElement("td");
		td.appendChild(document.createTextNode("\xA0"));
		tr.appendChild(td);
	}
	tbody.appendChild(tr);
	calendarTable.appendChild(tbody);

	//remove all children from the calendar container, and then append the new table
	while (this.calendarContainer.childNodes[0]) {
		this.calendarContainer.removeChild(this.calendarContainer.childNodes[0]);
	}
	this.calendarContainer.appendChild(calendarTable);
};

/**
 * A closure used to create a function for inserting the date into the field.
 * Month and Year are already known to this instance of Calendar, so we simply
 * pass in the day of the month (1-31)
 *
 * @param {int} i
 *
 * @return function
 */
Calendar.prototype.getFunction = function(i) {
	var thisCal = this;
	function tempFunc() {
		thisCal.fillDate(i);
		//If there is an onkeyup action set, run it.  This will let you attach
		//validation functions to the onkeyup
		if (typeof thisCal.target.onkeyup === 'function') {
			thisCal.target.onkeyup();
		}
	};
	return tempFunc;
};

/**
 * Used to hide the calendar, with a 200ms delay.  This way the calendar is
 * still there to receive a click, and does not disappear and reappear when we
 * change month or year.
 *
 * @return void
 */
Calendar.prototype.hideCal = function() {
	var thisCal = this;
	this.timeout_id = setTimeout(function() {thisCal.hideCalendar();}, 200);
};

/**
 * Can stop a request to hide the calendar
 *
 * @return void
 */
Calendar.prototype.stopHideCal = function () {
	if (this.timeout_id) {
		clearTimeout(this.timeout_id);
	}
};

/**
 * Hides the calendar and removes the blur event we attached to the text input
 *
 * @return void
 */
Calendar.prototype.hideCalendar = function() {
	this.remEvent(this.target, 'blur', this.blurfunc);
	this.setSelectVisibility('');
	this.calendarContainer.style.visibility = 'hidden';
}

/**
 * Put the correct date into the target text input formatted according to
 * this.dateFormat.  Then hide the calendar.
 *
 * @param {int} i - day of the month (1-31)
 *
 * @return void
 */
Calendar.prototype.fillDate = function(i) {
	this.current.setDate(i);
	this.target.value = this.current.format(this.dateFormat);
	this.hideCalendar();
};

/**
 * Changes the month of the calendar.  The only parameter is an integer to
 * adjust the month by (Usually -1 to go back one month, 1 to go forward one
 * month, -12 to go back one year, or 12 to go forward one year.  However, it
 * should be able to handle any integer value)
 * @param {int} adj
 */
Calendar.prototype.changeMonth = function (adj) {
	this.stopHideCal();
	this.current.setDate(1);
	this.current.setMonth(this.current.getMonth()+adj);
	this.buildCalendar();
};

Calendar.prototype.showToday = function() {
	this.stopHideCal();
	this.current = new Date();
	this.buildCalendar();
};

Calendar.prototype.getElementPosition = function(elemID) {
	var offsetTrail = elemID;
	var offsetLeft = 0;
	var offsetTop = 0;
	while (offsetTrail) {
		offsetLeft += offsetTrail.offsetLeft;
		offsetTop += offsetTrail.offsetTop;
		offsetTrail = offsetTrail.offsetParent;
	}
	if (navigator.userAgent.indexOf("Mac") != -1 &&
		typeof document.body.leftMargin != "undefined") {
		offsetLeft += document.body.leftMargin;
		offsetTop += document.body.topMargin;
	}
	return {left:offsetLeft, top:offsetTop};
};

Calendar.prototype.pad_zero = function(num) {
	return ((num <= 9) ? ("0" + num) : num);
};

Calendar.prototype.interpretDate = function(date) {
	date=date.replace(/[\\\.\-\s]/g,"/");
	date=date.replace(/^(\d\d)(\d\d)(\d\d)$/,"$1/$2/$3");
	date=date.replace(/^(\d\d)(\d\d)(\d\d\d\d)$/,"$1/$2/$3");
	date=date.replace(/^(\d\d)\/(\d\d)\/(\d\d)$/,"$1/$2/20$3");
	return date;
};

Calendar.prototype.addEvent = function (el, ev, f) {
  if (el.addEventListener) {
    el.addEventListener(ev, f, false);
	} else if(el.attachEvent) {
    el.attachEvent("on" + ev, f);
	} else {
    el['on' + ev] = f;
	}
	return f;
};

//make this available statically
Calendar.addEvent = Calendar.prototype.addEvent;

function calInit() {
	cal = new Calendar();
}

//Start the calendar onload
Calendar.addEvent(window, 'load', calInit);

Calendar.prototype.remEvent = function (el, ev, f) {
  if (el.removeEventListener) {
    el.removeEventListener(ev, f, false);
	} else if(el.detachEvent) {
    el.detachEvent("on" + ev, f);
	} else {
    el['on' + ev] = null;
	}
};



String.leftPad = function (s, len, ch) {
	var str = new String(s);
	if (ch == null) {
		ch = " ";
	}
	while (str.length < len) {
		str = ch + result;
	}
	return str;
}

/**
 * Date formatting Stuff
 */
Date.prototype.dayNames = new Array(
	"Sunday",
	"Monday",
	"Tuesday",
	"Wednesday",
	"Thursday",
	"Friday",
	"Saturday"
);

Date.prototype.monthNames = new Array(
	"January",
	"February",
	"March",
	"April",
	"May",
	"June",
	"July",
	"August",
	"September",
	"October",
	"November",
	"December"
);

Date.prototype.daysInMonths = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

Date.prototype.isLeapYear = function () {
	return (this.getFullYear() % 4 == 0) && ((this.getFullYear() % 100 != 0) || (this.getFullYear() % 400 == 0));
}

Date.prototype.pad = function (num) {
	if (Math.abs(num) <= 9) {
		if (num < 0) {
			return '-0'+Math.abs(num);
		} else {
			return '0'+num;
		}
	} else {
		return String(num);
	}
	return ((Math.abs(num) <= 9) ? ("0" + num) : num);
}

Date.prototype.format = function(format) {
	format = String(format).split("");
	var dateStr = '';

	for (var i = 0; i < format.length; i++) {
		switch (format[i]) {
			case 'd':
				dateStr += this.pad(this.getDate());
				break;
			case 'D':
				dateStr += this.dayNames[this.getDay()].substr(0,3);
				break;
			case 'j':
				dateStr += this.getDate();
				break;
			case 'l':
				dateStr += this.dayNames[this.getDay()];
				break;
			case 'N':
				dateStr += (this.getDay() == 0)? '7':this.getDay();
				break;
			case 'S':
				dateStr += this.getSuffix();
				break;
			case 'w':
				dateStr += this.getDay();
				break;
			case 'z':
				dateStr += this.getDayOfYear();
				break;
/*
			case 'W':
				dateStr += '*W*';
				break;
*/
			case 'F':
				dateStr += this.monthNames[this.getMonth()];
				break;
			case 'm':
				dateStr += this.pad(this.getMonth()+1);
				break;
			case 'M':
				dateStr += this.monthNames[this.getMonth()].substr(0,3);
				break;
			case 'n':
				dateStr += this.getMonth()+1;
				break;
			case 't':
				dateStr += this.getDaysInMonth();
				break;
			case 'L':
				dateStr += (this.isLeapYear())? 1:0;
				break;
/*
			case 'o':
				dateStr += '*o*';
				break;
*/
			case 'Y':
				dateStr += this.getFullYear();
				break;
			case 'y':
				dateStr += String(this.getFullYear()).substr(-2);
				break;
			case 'a':
				dateStr += this.getAmPm().toLowerCase();
				break;
			case 'A':
				dateStr += this.getAmPm().toUpperCase();
				break;
			case 'B':
				dateStr += this.getSwatchTime();
				break;
			case 'g':
				dateStr += (this.getHours()<= 12)? this.getHours():this.getHours()-12;
				break;
			case 'G':
				dateStr += this.getHours();
				break;
			case 'h':
				var hour = (this.getHours()<= 12)? this.getHours():this.getHours()-12;
				dateStr += this.pad(hour);
				break;
			case 'H':
				dateStr += this.pad(this.getHours());
				break;
			case 'i':
				dateStr += this.pad(this.getMinutes());
				break;
			case 's':
				dateStr += this.pad(this.getSeconds());
				break;
/*
			case 'e':
				dateStr += '*e*';
				break;
*/
			case 'I':
				dateStr += this.getDST();
				break;
			case 'O':
				dateStr += (this.pad(this.getTimezoneOffset()/60*-1)+'00');
				break;
			case 'P':
				dateStr += (this.pad(this.getTimezoneOffset()/60*-1)+':00');
				break;
/*
			case 'T':
				dateStr += '*T*';
				break;
*/
			case 'Z':
				dateStr += this.getTimezoneOffset()*60*-1;
				break;
			case 'c':
				dateStr += this.format('Y-m-d\\TH:i:sP');
				break;
			case 'r':
				dateStr += this.format('D, d M Y H:i:s O');
				break;
			case 'U':
				dateStr += Math.floor(this.getTime()/1000);
				break;
			case '\\':
				i++;
			default:
				dateStr += format[i];
		}
	}
	return dateStr;
}
Date.prototype.getDaysInMonth = function () {
	if (this.getMonth() == 1) {
		this.daysInMonths[1] = (this.isLeapYear())? 29:28;
	}
	return this.daysInMonths[this.getMonth()];
}
Date.prototype.getDayOfYear = function () {
	var start = Date.UTC(this.getFullYear(), 0, 0);
	var end = Date.UTC(this.getFullYear(), this.getMonth(), this.getDate());
	return Math.floor((end - start) / (1000 * 60 * 60 * 24));
}
Date.prototype.getSuffix = function () {
	d = this.getDate();
	if (d >=11 && d <= 13) {
		return 'th';
	} else {
		switch(String(d).substr(-1)) {
			case '1':
				return 'st';
			case '2':
				return 'nd';
			case '3':
				return 'rd';
			default:
				return 'th';
		}
	}
}

Date.prototype.getSwatchTime = function () {
	return String.leftPad(Math.floor((((this.getTime()/1000/60/60/24)%1)+(1/24))*1000), 3, '0');
}

Date.prototype.getDST = function () {
	var start = new Date(this.getFullYear(), 0, 1);
	return (this.getTimezoneOffset() != start.getTimezoneOffset()) ? '1':'0';
}

Date.prototype.getAmPm = function () {
	return (this.getHours() < 12)? 'am':'pm';
}
