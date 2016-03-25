var hintTimeout = 0;
var hintLast = '';
var hints = new Array;

hints["options_stop"] = "Editing Product Options is unavailable in this mode";
hints["files_stop"] = "Editing Product Files is unavailable in this mode";
hints["images_stop"] = "Editing Product Images is unavailable in this mode";
hints["categories_stop"] = "Editing Product Categories is unavailable in this mode";
hints["quantity_discounts_stop"] = "Editing Quantity Discounts is unavailable in this mode";
hints["related_stop"] = "Editing Related Products is unavailable in this mode";
hints["terminator_stop"] = "Other Features are unavailable in this mode";

hints["featured_stop"] = "Editing Featured Products is unavailable in this mode"
hints["bestsellers_stop"] = "Editing Bestsellers is unavailable in this mode"

hints["area_stop"] = "Cannot edit Discounted Items in this mode, save the coupon first."
hints["scc_area_stop"] = "Cannot edit the Items List in this mode, save the rule first."

function showHint(event,obj,hid) {

	var d = document.getElementById('hint');
	if (!d) {
		d = document.createElement('DIV');
		d.className = 'hint';
		d.id = 'hint';
		document.body.appendChild(d);
	}

	if (hid!=hintLast) {
		d.innerHTML = "<span style='font-family: Tahoma,Sans-serif; font-size: 9pt;'>&nbsp;" + hints[hid] + "&nbsp;</span>";
		hintLast = hid;
	}

	d.style.display = 'block';
	d.style.border = 'solid 1px black';
	d.style.backgroundColor = 'white';

	var pos = findCursorPos(event);
	d.style.position = 'absolute';
	d.style.left = pos[0]+5+'px';
	d.style.top = pos[1]+20+'px';

	if (hintTimeout) clearTimeout(hintTimeout);
	hintTimeout = setTimeout("hideHint()",3000);

}

function hideHint() {
	var d = document.getElementById('hint');
	if (d) d.style.display = "none";	
}

function MyBrowser() {

	var ua, s, i;

	this.isIE    = false;
	this.isNS    = false;
	this.version = null;

	ua = navigator.userAgent;

	s = "MSIE";
	if ((i = ua.indexOf(s)) >= 0) {
		this.isIE = true;
		this.version = parseFloat(ua.substr(i + s.length));
		return;
	}

	s = "Opera";
	if ((i = ua.indexOf(s)) >= 0) {
		this.isIE = true;
		return;
	}

	s = "Netscape6/";
	if ((i = ua.indexOf(s)) >= 0) {
		this.isNS = true;
		this.version = parseFloat(ua.substr(i + s.length));
		return;
	}

	// Treat any other "Gecko" browser as NS 6.1.
	s = "Gecko";
	if ((i = ua.indexOf(s)) >= 0) {
		this.isNS = true;
		this.version = 6.1;
		return;
	}
}

var userBrowser = new MyBrowser();

function findCursorPos(event) {
	var x, y;
	if (userBrowser.isIE) {
		x = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft;
		y = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop;
	} else if (userBrowser.isNS) {
		x = event.clientX + window.scrollX;
		y = event.clientY + window.scrollY;
	}
	return (new Array(x,y));
}

function showSoftDiv(event,div_id)
{
	var pos = findCursorPos(event);
	d = document.getElementById(div_id);
	d.style.left = pos[0]+5+'px';
	d.style.top = pos[1]+20+'px';
	d.style.display = '';
}

function hideSoftDiv(div_id)
{
    document.getElementById(div_id).style.display = 'none';
}
