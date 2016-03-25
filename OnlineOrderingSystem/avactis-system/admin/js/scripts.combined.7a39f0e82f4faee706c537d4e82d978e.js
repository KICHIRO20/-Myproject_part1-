function go (url)
{
    if (url == '') return false;
    location.href=url;
    return true;
}
var rowShow=(document.all?'block':'table-row');

function decrypt_asymmetric(encrypted_value, encrypted_secret_key_value, private_key_value)
{
    return encrypted_value;
}

function setValue(element_id, value, b_parent)
{
    //default value
    b_parent = typeof(b_parent) != 'undefined' ? b_parent : false;
    if(b_parent == true)
    {
        el = parent.document.getElementById(element_id);
    } 
    else
    {
        el =        document.getElementById(element_id);
    }

    //"text
    //"td"
    //"select-one"
    if(el.type == "TD")
    {
        el.innerHTML = value;
         //TD
    }
    else if(el.type == "text")
    {
        el.value = value;
         //<input type="text">
    }
    else if(el.type == "select-one")
    {
        el.selectedIndex = value;
        if(el.onchange)
        {
            el.onchange();
        }
    }
}

/**
 * ��ࠢ��� ����஢���� ����� �� ����஢��.
 */
function decryptGroupJavascript(group_id, form_id, form_action_id, encrypted_data_index_id, form_target)
{
    //�������� action ��� (��७��ࠢ��� �� ����� Crypto), ᤥ���� ᠡ���.
    el = document.getElementById(encrypted_data_index_id);
    el.value = group_id;

    el = document.getElementById(form_action_id);
    old_form_action = el.value;
    el.value = 'DecryptRsaBlowfishJavascript';

    el = document.getElementById(form_id);
    old_form_target = el.target;
    el.target = form_target;
    el.submit();

    setTimeout('restoreFormParams("'+form_id+'","'+old_form_target+'","'+form_action_id+'","'+old_form_action+'");', 500); //0.5 s - wait a little; form.submit and other function calls may be asynchronious.
}

function restoreFormParams(form_id, old_form_target, form_action_id, old_form_action)
{
    el = document.getElementById(form_id);
    el.target = old_form_target;

    el = document.getElementById(form_action_id);
    el.value = old_form_action;
}

function toggleRows(prefix) {
    var el,i,state;
    rows = document.getElementsByTagName("tr");
    for (var i=0; i<rows.length; i++) {
        row = rows[i];
        if (row.id.indexOf(prefix) == -1) continue;
        state=('none'==row.style.display?1:0);
        row.style.display=(state?rowShow:'none');
    }
    img = document.getElementById("img_" + prefix);
    if (img) {
        if (state) {
            img.src = "images/minus.gif";
        } else {
            img.src = "images/plus.gif";
        }
    }
    SetCookie('rowShowState'+prefix, state);
}

function setFromState(prefix) {
    var el,i,state=GetCookie('rowShowState'+prefix)*1;
    rows = document.getElementsByTagName("tr");
    for (var i=0; i<rows.length; i++) {
        row = rows[i];
        if (row.id.indexOf(prefix) == -1) continue;
        row.style.display=(state?rowShow:'none');
    }
    img = document.getElementById("img_" + prefix);
    if (img) {
        if (state) {
            img.src = "images/minus.gif";
        } else {
            img.src = "images/plus.gif";
        }
    }
}

function setVisible(prefix) {
    var el=document.getElementById(prefix);
    rows = document.getElementsByTagName("tr");
    for (var i=0; i<rows.length; i++) {
        row = rows[i];
        if (row.id.indexOf(prefix) == -1) continue;
        row.style.display=(rowShow);
    }
    img = document.getElementById("img_" + prefix);
    if (img) {
        img.src = "images/minus.gif";
    }
}

function getCookieVal (offset) {
    var endstr = document.cookie.indexOf (";", offset);
    if (endstr == -1)
        endstr = document.cookie.length;
    return unescape(document.cookie.substring(offset, endstr));
}

function GetCookie (name) {
    var arg = name + "=";
    var alen = arg.length;
    var clen = document.cookie.length;
    var i = 0;
    while (i < clen) {
        var j = i + alen;
        if (document.cookie.substring(i, j) == arg)
            return getCookieVal (j);
        i = document.cookie.indexOf(" ", i) + 1;
        if (i == 0) 
            break; 
    }
    return null;
}

function SetCookie (name, value) {
    var argv = SetCookie.arguments;
    var argc = SetCookie.arguments.length;
    var expires = (2 < argc) ? argv[2] : null;
    var path = (3 < argc) ? argv[3] : null;
    var domain = (4 < argc) ? argv[4] : null;
    var secure = (5 < argc) ? argv[5] : false;
    document.cookie = name + "=" + escape (value) +
        ((expires == null) ? "" : ("; expires=" + expires.toGMTString())) +
        ((path == null) ? "" : ("; path=" + path)) +
        ((domain == null) ? "" : ("; domain=" + domain)) +
        ((secure == true) ? "; secure" : "");
}

function getAttrHelp(type_id, view_tag, attr_of_obj, WindowWidth, WindowHeight) {
	if (!WindowHeight)
	{
		WindowHeight = '220';
	}
	if (!WindowWidth)
	{
		WindowWidth = '352';
	}
	if (!attr_of_obj)
	{
		attr_of_obj='prod_attr';
	}
	var file_prefix = "";
	if (attr_of_obj=="custsl_attr"||attr_of_obj=="custbt_attr")
	{
		file_prefix = "cust_"
	}
    var bars_width = 47;
    var winl = (screen.width - WindowWidth) / 2;
    var wint = ((screen.height - WindowHeight) - bars_width) / 2;
    helpWnd = window.open(file_prefix+'attr_help.php?type_id='+type_id+'&view_tag='+view_tag+'&attr_of_obj='+attr_of_obj, 'help', 'top='+wint+',left='+winl+',width='+WindowWidth+',height='+WindowHeight+',toolbar=0,location=0,directories=0,status=0,menubar=0,copyhistory=0,resizable=yes');
    helpWnd.focus();
}

function openURLinNewMinimizedWindow(url, windowName) {
    //starting width and height
    var w = 100;
    var h = 100;
    var bars_width = 47;

    var winl = (screen.width - w) / 2;
    var wint = ((screen.height - h) - bars_width) / 2;
    
    var params = 'top='+wint+',left='+winl+',width='+w+',height='+h+',directories=no,location=no,menubar=no,scrollbars=yes,status=no,toolbar=no,resizable=yes';

    helpWnd = window.open(url, windowName, params);
    helpWnd.focus();
    return helpWnd;
}

function _openURLinNewWindow(url, windowName) {
    var w = 830;
    var h = 600;
    var bars_width = 47;

    var winl = (screen.width - w) / 2;
    var wint = ((screen.height - h) - bars_width) / 2;
    
    var params = 'top='+wint+',left='+winl+',width='+w+',height='+h+',directories=no,location=no,menubar=no,scrollbars=yes,status=no,toolbar=no,resizable=yes';

    helpWnd = window.open(url, windowName, params);
    helpWnd.focus();
    return helpWnd;
}

function openURLinNewWindow(url, windowName) {
	if (jQuery().colorbox)
	{
		var winW = screen.width - (25);
		var winH = screen.height - (screen.height * 0.2);
		if (winH >= 480) {
			winH = winH - (screen.height * 0.1);
		}
		jQuery.colorbox({iframe:true, href:url, initialWidth:"100px", initialHeight:"100px", maxHeight:"600px", maxWidth:"800px", width:winW, height:winH, overlayClose:false});
		return;
	}
	else{
		var w = 830;
		var h = 600;
		var bars_width = 47;

		var winl = (screen.width - w) / 2;
		var wint = ((screen.height - h) - bars_width) / 2;

		var params = 'top='+wint+',left='+winl+',width='+w+',height='+h+',directories=no,location=no,menubar=no,scrollbars=yes,status=no,toolbar=no,resizable=yes';

		helpWnd = window.open(url, windowName, params);
		helpWnd.focus();
		return helpWnd;
	}
}



function openURLinNewLargeWindow(url, windowName) {
    var params = 'directories=yes,location=yes,menubar=yes,scrollbars=yes,status=yes,toolbar=yes,resizable=yes';

    helpWnd = window.open(url, windowName, params);
    helpWnd.focus();
    return helpWnd;
}

function closeAndFocusParent()
{
    if(!(!window.opener || window.opener.closed))
    {
        window.opener.focus();
    }
    window.close();
    jQuery('#cboxClose').click();
    parent.jQuery.colorbox.close()
}

function closeAndReloadParent()
{
    if(!(!window.opener || window.opener.closed))
    {
        window.opener.location.reload();
        window.opener.focus();
    }
    window.close();
	parent.location.reload();
    jQuery('#cboxClose').click();
    parent.jQuery.colorbox.close();
}

function disableButtons(buttonsArray)
{
	for (i=0; i<buttonsArray.length; i++)
	{
		if(document.getElementById(buttonsArray[i]))
		{
			disableButton(buttonsArray[i]);
		}
	}
}

function disableLinks(linksArray)
{
    for (i=0; i<linksArray.length; i++)
    {
        if(document.getElementById(linksArray[i]))
        {
            disableLink(linksArray[i]);
        }
    }
}

var DOM = (typeof(document.getElementById) != 'undefined');
function selectItems (formId, idPrefix, selectAllId)
{
    if (!DOM) return;
    if (! idPrefix) {
        idPrefix = 'select_';
    }
    if (! selectAllId) {
        selectAllId = 'SelectAll';
    }
    var form = document.forms[formId];
    var selectAll = document.getElementById(selectAllId);
    var checked = selectAll.checked;
    var elements = form.elements;
    for(i = 0; i < elements.length; i++) {
        elem = elements[i];
        if (elem.id.indexOf(idPrefix) != -1) {
            elem.checked = checked;
            selectRow(elem);
        }
    }
    return true;
}

function selectRow (Element, css_class_name) {
    if (!DOM) return;
    //default value
    css_class_name = typeof(css_class_name) != 'undefined' ? css_class_name : 'selected';

    var selectedTableRow = Element.parentNode.parentNode;
    if (Element.checked) {
        selectedTableRow.className = css_class_name;
    } else {
        selectedTableRow.className = '';
    }
}

function selectRowAlways (Element, css_class_name) {
    if (!DOM) return;
    //default value
    css_class_name = typeof(css_class_name) != 'undefined' ? css_class_name : 'selected';

    var selectedTableRow = Element.parentNode.parentNode;
    selectedTableRow.className = css_class_name;
}

function selectRowDeselectOther (Element) {
    if (!DOM) return;
    var buttons_with_the_same_name = document.getElementsByName(Element.name); 
    
    for(var i=0; i<buttons_with_the_same_name.length; i++) 
    {
        if (Element != buttons_with_the_same_name[i]) 
        {
            buttons_with_the_same_name[i].checked = false;
        }
        buttons_with_the_same_name[i].parentNode.parentNode.className = '';
    }

    var selectedTableRow = Element.parentNode.parentNode;
    if (Element.checked) {
        selectedTableRow.className = 'selected';
    } else {
        selectedTableRow.className = '';
    }
}

function UncheckAll(formId, elementId)
{
    if (! elementId) {
        elementId = 'SelectAll';
    }
    var form = document.forms[formId];
    if (document.getElementById)
    {
        var selectAll = document.getElementById(elementId);
        selectAll.checked = false;
    }
    return true;
}

function number_format(num, digits, dec_point, thousands_sep)
{
    nStr = num.toFixed(digits);
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? dec_point + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    if (thousands_sep)
    {
        while (rgx.test(x1))
        {
            x1 = x1.replace(rgx, '$1' + thousands_sep + '$2');
        }
    }
    return x1 + x2;
}

function ShowHint(URL, WindowWidth, WindowHeight)
{
	if (jQuery().colorbox)
	{
		jQuery.colorbox({iframe:true, href:URL, initialWidth:"500px", initialHeight:"500px", maxHeight:"100%", maxWidth:"70%", width:"500px", height:"70%"});
		return;
	}
	if (!WindowHeight)
	{
		WindowHeight = '320';
	}
	if (!WindowWidth)
	{
		WindowWidth = '400';
	}
    var bars_width = 47;
    var winl = (screen.width - WindowWidth) / 2;
    var wint = ((screen.height - WindowHeight) - bars_width) / 2;
    hintWnd = window.open(URL, 'hint', 'top='+wint+',left='+winl+',width='+WindowWidth+',height='+WindowHeight+',toolbar=0,location=0,directories=0,status=0,menubar=0,copyhistory=0,resizable=yes,scrollbars=yes');
    hintWnd.focus();
}
function setFocusOnFirstElement()
{
    var i=0;
    while (document.forms[i])
    {
        form = document.forms[i];
        var j = 0;
        while (form.elements.item(j))
        {
            switch (form.elements.item(j).tagName)
            {
                case "INPUT":
                            if(form.elements.item(j).type == "text" && form.elements.item(j).clientHeight!=0 && form.elements.item(j).clientWidth!=0)
                            {
                                if (!form.elements.item(j).disabled)
                                {
                                    form.elements.item(j).focus();
                                    return;
                                }
                            }
                            break;
                case "TEXTAREA":
                            if(form.elements.item(j).clientHeight!=0 && form.elements.item(j).clientWidth!=0)
                            {
                                if (!form.elements.item(j).disabled)
                                {
                                    form.elements.item(j).focus();
                                    return;
                                }
                            }
                            break;
            }
            j++;
        }
        i++;
    }
}


function array_unshift(var_array)
{
    var A_u = 0;
    for (A_u = var_array.length-1; A_u >= 0; A_u--)
    {
        var_array[A_u + (arguments.length-1)] = var_array[A_u];
    }
    for (A_u = 0; A_u < (arguments.length-1); A_u++)
    {
        var_array[A_u] = arguments[A_u+1];
    }
    return var_array;
}

function is_array(variable)
{
   var source = variable.toSource();
   return (source.search(/^\[([^,]*,)+[^,]*\]/) != -1);
}

/*
Execute javascript function.
Each parameter from params array will be surrounded by quotes, 
so all parameters must be simple strings.
*/
function asc_exec_ellipsis_args_only_strings(function_name, params)
{
    command = function_name+"(";
    if(params.length >0)
    {
        command = command+"'"+params[0]+"'";
        for(i=1; i< params.length; i++)
        {
            command = command+", '" + params[i] + "'";
        }
    }
    command = command+")";
    return eval(command);
}

function disableBlock(block_id, z_index)
{
	var _element = document.getElementById(block_id);

    if(!_element)
        return;
	
	var _width  = _element.offsetWidth;
	var _height = _element.offsetHeight;
	var _left = _element.offsetLeft;
	var _top = _element.offsetTop;
	obj = _element;
	while(obj.offsetParent)
	{
		_left+=obj.offsetParent.offsetLeft;
		_top+=obj.offsetParent.offsetTop;
		obj=obj.offsetParent;
	};

    var d = document.getElementById('hd_'+block_id);
	if (!d) {
		d = document.createElement('DIV');
		d.id = 'hd_'+block_id;
		document.body.appendChild(d);
	};

    _width-=2;
    _height-=2;
    if(_width < 0) _width = 0;
    if(_height < 0) _height = 0;
    
    d.style.display = 'none';
    d.style.position = 'absolute';
    d.style.width = _width + 'px';
    d.style.height = _height + 'px';
    d.style.left = _left + 'px';
    d.style.top = _top + 'px';
    d.style.zIndex = z_index;
    d.style.border = 'solid 0px black';
    d.style.display = '';

    // add opacity
	if(navigator.userAgent.indexOf("MSIE 6")!=-1)
	{
	  d.style.backgroundImage='none';
	  d.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(src='images/halftranspixel.png', sizingMethod='scale')";
	}
	else
	{
	 // d.style.backgroundImage='url("images/halftranspixel.png")';
	}
};
function enableBlock(block_id)
{
    var _element = document.getElementById('hd_'+block_id);
    if(!_element)
        return;
        
    _element.style.display = 'none';
};
function showBlock(block_id, z_index)
{
    var el = document.getElementById(block_id);
    if (el)
    {
        el.style.zIndex = z_index;
        el.style.display = '';
    }
    else
    {
        alert("ERROR: function showBlock(): block_id '"+block_id+"' not found!");
    }
};
function hideBlock(block_id)
{
    var el = document.getElementById(block_id);
    if (el)
    {
        el.style.display = 'none';
    }
    else
    {
        alert("ERROR: function hideBlock(): block_id '"+block_id+"' not found!");
    }
};
function toggleBlock(block_id)
{
    var el = document.getElementById(block_id);
    if (el)
    {
        if (el.style.display == 'none')
        {
            el.style.display = '';
        }
        else
        {
           el.style.display = 'none';
        }
    }
    else
    {
        alert("ERROR: function hideBlock(): block_id '"+block_id+"' not found!");
    }
}

function repositionBlockForShowOver(div_id, over_id)
{
    over_el = document.getElementById(over_id);
    div_el = document.getElementById(div_id);

	var _width  = over_el.offsetWidth;
	var _height = over_el.offsetHeight;
	var _left = over_el.offsetLeft;
	var _top = over_el.offsetTop;
	obj = over_el;
	while(obj.offsetParent)
	{
		_left+=obj.offsetParent.offsetLeft;
		_top+=obj.offsetParent.offsetTop;
		obj=obj.offsetParent;
	};

    var _w2 = div_el.offsetWidth;
    var _h2 = div_el.offsetHeight;
    
        div_el.style.left = (_left + _width - _w2 ) / 2 + 'px';
        div_el.style.top = (_top - _h2 ) / 2 + 'px';
};


function disableButton(button_id)
{
    if(document.getElementById(button_id).className.indexOf(' button_disabled')==-1)
    {
        document.getElementById(button_id).className += ' button_disabled';
        document.getElementById(button_id).onclick = function() {};
    };
    if(document.getElementById(button_id).className.indexOf(' disabled')==-1)
    {
        document.getElementById(button_id).className += ' disabled';
        document.getElementById(button_id).onclick = function() {};
    };
};

function disableLink(link_id)
{
    if(document.getElementById(link_id).className.indexOf(' link_disabled')==-1)
    {
        document.getElementById(link_id).className += ' link_disabled';
        document.getElementById(link_id).onclick = function() {};
    };
    if(document.getElementById(link_id).className.indexOf(' disabled')==-1)
    {
        document.getElementById(link_id).className += ' disabled';
        document.getElementById(link_id).onclick = function() {};
    };
};

function enableButton(button_id,onclick_function)
{
    document.getElementById(button_id).className = document.getElementById(button_id).className.replace(' button_disabled','');
    document.getElementById(button_id).className = document.getElementById(button_id).className.replace(' disabled','');
    document.getElementById(button_id).onclick = onclick_function;
};

function enableLink(link_id,onclick_function)
{
    document.getElementById(link_id).className = document.getElementById(link_id).className.replace(' link_disabled','');
    document.getElementById(link_id).className = document.getElementById(link_id).className.replace(' disabled','');
    document.getElementById(link_id).onclick = onclick_function;
    document.getElementById(link_id).style.display = '';
};


function NavCellMouseOver(id)
{
	document.getElementById(id).className            = 'ActiveNavCellBorder';
	document.getElementById(id+'_content').className = 'ActiveNavCellContent';
	document.getElementById(id+'_header').className  = 'ActiveNavCellHeader';
};

function NavCellMouseOut(id)
{
	document.getElementById(id).className            = 'InactiveNavCellBorder';
	document.getElementById(id+'_content').className = 'InactiveNavCellContent';
	document.getElementById(id+'_header').className  = 'InactiveNavCellHeader';
};

function in_array(arr, datum, strict)
{
    if(strict) { function equals(a,b) { return a === b } }
    else { function equals(a,b) { return a == b } };

    for (var i in arr) {
        if (equals(arr[i], datum)) return true;
    }
    return false;
};

function array_search(arr, datum, strict)
{
    if(strict) { function equals(a,b) { return a === b } }
    else { function equals(a,b) { return a == b } };

    for (var i in arr) {
        if (equals(arr[i], datum)) return i;
    }
    return false;
};

String.prototype.stripTags = function() {
    return this.replace(/<\/?[^>]+>/gi, '');
};

String.prototype.escapeHTML = function() {
    var self = arguments.callee;
    self.text.data = this;
    return self.div.innerHTML;
};

String.prototype.unescapeHTML = function() {
    var div = document.createElement('div');
    div.innerHTML = this.stripTags();
    return div.childNodes[0] ? (div.childNodes.length > 1 ?
      jQueryA(div.childNodes).inject('', function(memo, node) { return memo+node.nodeValue }) :
      div.childNodes[0].nodeValue) : '';
};

function runScripts(scripts) {
    if (!scripts) return false;
    for (var i = 0; i < scripts.length; i++) {
        var thisScript = scripts[i];   
        var text;
        if (thisScript.src) {
            var newScript = document.createElement("script");
            newScript.type = thisScript.type;       
            newScript.language = thisScript.language;
            newScript.src = thisScript.src;             
            document.body.appendChild(newScript);   
        } else if (text = (thisScript.text || thisScript.innerHTML)) {
            var text = (""+text).replace(/^\s*<!\-\-/, '').replace(/\-\->\s*jQuery/, '');
            eval(text);
        }
    }
}

function putHtmlToElement(element_id, html)
{
	var el = document.getElementById(element_id);
	if (el)
	{
	    el.innerHTML = html;
	    runScripts(el.getElementsByTagName('SCRIPT'));
	}
	else
	{
	    alert("JS-ERROR: asc_http_request.processAnswer(): Element id '"+element_id+"' not found!");
	}
}

function dump_array(d,l) {
    if (l == null) l = 1;
    var s = '';
    if (typeof(d) == "object") {
        s += typeof(d) + " {\n";
        for (var k in d) {
            for (var i=0; i<l; i++) s += "  ";
            s += k+": " + dump_array(d[k],l+1);
        }
        for (var i=0; i<l-1; i++) s += "  ";
        s += "}\n"
    } else {
        s += "" + d + "\n";
    }
    return s;
}

function setActiveTheme(select)
{
	jQuery('.current_skin .failure, .current_skin .success').hide();
	jQuery('.current_skin .working').show();
	jQuery.ajax({
		url: 'edit_request.php', 
		data: { request: 'set_active_theme', name: select.value },
		complete: function () { jQuery('.current_skin .working').hide(); },
		cache: false,
		dataType: 'text',
		success: function (data) {
			if (data == 'ok') {
				jQuery('.current_skin .success').show();
			}
			else {
				jQuery('.current_skin .failure').show();
			}
		},
		error: function () {
			jQuery('.current_skin .failure').show();
		}
	});
	
}

/*!
 * jQuery UI Widget 1.10.2
 * http://jqueryui.com
 *
 * Copyright 2013 jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/jQuery.widget/
 */
(function( $, undefined ) {

var uuid = 0,
	slice = Array.prototype.slice,
	_cleanData = $.cleanData;
$.cleanData = function( elems ) {
	for ( var i = 0, elem; (elem = elems[i]) != null; i++ ) {
		try {
			$( elem ).triggerHandler( "remove" );
		// http://bugs.jquery.com/ticket/8235
		} catch( e ) {}
	}
	_cleanData( elems );
};

$.widget = function( name, base, prototype ) {
	var fullName, existingConstructor, constructor, basePrototype,
		// proxiedPrototype allows the provided prototype to remain unmodified
		// so that it can be used as a mixin for multiple widgets (#8876)
		proxiedPrototype = {},
		namespace = name.split( "." )[ 0 ];

	name = name.split( "." )[ 1 ];
	fullName = namespace + "-" + name;

	if ( !prototype ) {
		prototype = base;
		base = $.Widget;
	}

	// create selector for plugin
	$.expr[ ":" ][ fullName.toLowerCase() ] = function( elem ) {
		return !!$.data( elem, fullName );
	};

	$[ namespace ] = $[ namespace ] || {};
	existingConstructor = $[ namespace ][ name ];
	constructor = $[ namespace ][ name ] = function( options, element ) {
		// allow instantiation without "new" keyword
		if ( !this._createWidget ) {
			return new constructor( options, element );
		}

		// allow instantiation without initializing for simple inheritance
		// must use "new" keyword (the code above always passes args)
		if ( arguments.length ) {
			this._createWidget( options, element );
		}
	};
	// extend with the existing constructor to carry over any static properties
	$.extend( constructor, existingConstructor, {
		version: prototype.version,
		// copy the object used to create the prototype in case we need to
		// redefine the widget later
		_proto: $.extend( {}, prototype ),
		// track widgets that inherit from this widget in case this widget is
		// redefined after a widget inherits from it
		_childConstructors: []
	});

	basePrototype = new base();
	// we need to make the options hash a property directly on the new instance
	// otherwise we'll modify the options hash on the prototype that we're
	// inheriting from
	basePrototype.options = $.widget.extend( {}, basePrototype.options );
	$.each( prototype, function( prop, value ) {
		if ( !$.isFunction( value ) ) {
			proxiedPrototype[ prop ] = value;
			return;
		}
		proxiedPrototype[ prop ] = (function() {
			var _super = function() {
					return base.prototype[ prop ].apply( this, arguments );
				},
				_superApply = function( args ) {
					return base.prototype[ prop ].apply( this, args );
				};
			return function() {
				var __super = this._super,
					__superApply = this._superApply,
					returnValue;

				this._super = _super;
				this._superApply = _superApply;

				returnValue = value.apply( this, arguments );

				this._super = __super;
				this._superApply = __superApply;

				return returnValue;
			};
		})();
	});
	constructor.prototype = $.widget.extend( basePrototype, {
		//     : remove support for widgetEventPrefix
		// always use the name + a colon as the prefix, e.g., draggable:start
		// don't prefix for widgets that aren't DOM-based
		widgetEventPrefix: existingConstructor ? basePrototype.widgetEventPrefix : name
	}, proxiedPrototype, {
		constructor: constructor,
		namespace: namespace,
		widgetName: name,
		widgetFullName: fullName
	});

	// If this widget is being redefined then we need to find all widgets that
	// are inheriting from it and redefine all of them so that they inherit from
	// the new version of this widget. We're essentially trying to replace one
	// level in the prototype chain.
	if ( existingConstructor ) {
		$.each( existingConstructor._childConstructors, function( i, child ) {
			var childPrototype = child.prototype;

			// redefine the child widget using the same prototype that was
			// originally used, but inherit from the new version of the base
			$.widget( childPrototype.namespace + "." + childPrototype.widgetName, constructor, child._proto );
		});
		// remove the list of existing child constructors from the old constructor
		// so the old child constructors can be garbage collected
		delete existingConstructor._childConstructors;
	} else {
		base._childConstructors.push( constructor );
	}

	$.widget.bridge( name, constructor );
};

$.widget.extend = function( target ) {
	var input = slice.call( arguments, 1 ),
		inputIndex = 0,
		inputLength = input.length,
		key,
		value;
	for ( ; inputIndex < inputLength; inputIndex++ ) {
		for ( key in input[ inputIndex ] ) {
			value = input[ inputIndex ][ key ];
			if ( input[ inputIndex ].hasOwnProperty( key ) && value !== undefined ) {
				// Clone objects
				if ( $.isPlainObject( value ) ) {
					target[ key ] = $.isPlainObject( target[ key ] ) ?
						$.widget.extend( {}, target[ key ], value ) :
						// Don't extend strings, arrays, etc. with objects
						$.widget.extend( {}, value );
				// Copy everything else by reference
				} else {
					target[ key ] = value;
				}
			}
		}
	}
	return target;
};

$.widget.bridge = function( name, object ) {
	var fullName = object.prototype.widgetFullName || name;
	$.fn[ name ] = function( options ) {
		var isMethodCall = typeof options === "string",
			args = slice.call( arguments, 1 ),
			returnValue = this;

		// allow multiple hashes to be passed on init
		options = !isMethodCall && args.length ?
			$.widget.extend.apply( null, [ options ].concat(args) ) :
			options;

		if ( isMethodCall ) {
			this.each(function() {
				var methodValue,
					instance = $.data( this, fullName );
				if ( !instance ) {
					return $.error( "cannot call methods on " + name + " prior to initialization; " +
						"attempted to call method '" + options + "'" );
				}
				if ( !$.isFunction( instance[options] ) || options.charAt( 0 ) === "_" ) {
					return $.error( "no such method '" + options + "' for " + name + " widget instance" );
				}
				methodValue = instance[ options ].apply( instance, args );
				if ( methodValue !== instance && methodValue !== undefined ) {
					returnValue = methodValue && methodValue.jquery ?
						returnValue.pushStack( methodValue.get() ) :
						methodValue;
					return false;
				}
			});
		} else {
			this.each(function() {
				var instance = $.data( this, fullName );
				if ( instance ) {
					instance.option( options || {} )._init();
				} else {
					$.data( this, fullName, new object( options, this ) );
				}
			});
		}

		return returnValue;
	};
};

$.Widget = function( /* options, element */ ) {};
$.Widget._childConstructors = [];

$.Widget.prototype = {
	widgetName: "widget",
	widgetEventPrefix: "",
	defaultElement: "<div>",
	options: {
		disabled: false,

		// callbacks
		create: null
	},
	_createWidget: function( options, element ) {
		element = $( element || this.defaultElement || this )[ 0 ];
		this.element = $( element );
		this.uuid = uuid++;
		this.eventNamespace = "." + this.widgetName + this.uuid;
		this.options = $.widget.extend( {},
			this.options,
			this._getCreateOptions(),
			options );

		this.bindings = $();
		this.hoverable = $();
		this.focusable = $();

		if ( element !== this ) {
			$.data( element, this.widgetFullName, this );
			this._on( true, this.element, {
				remove: function( event ) {
					if ( event.target === element ) {
						this.destroy();
					}
				}
			});
			this.document = $( element.style ?
				// element within the document
				element.ownerDocument :
				// element is window or document
				element.document || element );
			this.window = $( this.document[0].defaultView || this.document[0].parentWindow );
		}

		this._create();
		this._trigger( "create", null, this._getCreateEventData() );
		this._init();
	},
	_getCreateOptions: $.noop,
	_getCreateEventData: $.noop,
	_create: $.noop,
	_init: $.noop,

	destroy: function() {
		this._destroy();
		// we can probably remove the unbind calls in 2.0
		// all event bindings should go through this._on()
		this.element
			.unbind( this.eventNamespace )
			// 1.9 BC for #7810
			//      remove dual storage
			.removeData( this.widgetName )
			.removeData( this.widgetFullName )
			// support: jquery <1.6.3
			// http://bugs.jquery.com/ticket/9413
			.removeData( $.camelCase( this.widgetFullName ) );
		this.widget()
			.unbind( this.eventNamespace )
			.removeAttr( "aria-disabled" )
			.removeClass(
				this.widgetFullName + "-disabled " +
				"ui-state-disabled" );

		// clean up events and states
		this.bindings.unbind( this.eventNamespace );
		this.hoverable.removeClass( "ui-state-hover" );
		this.focusable.removeClass( "ui-state-focus" );
	},
	_destroy: $.noop,

	widget: function() {
		return this.element;
	},

	option: function( key, value ) {
		var options = key,
			parts,
			curOption,
			i;

		if ( arguments.length === 0 ) {
			// don't return a reference to the internal hash
			return $.widget.extend( {}, this.options );
		}

		if ( typeof key === "string" ) {
			// handle nested keys, e.g., "foo.bar" => { foo: { bar: ___ } }
			options = {};
			parts = key.split( "." );
			key = parts.shift();
			if ( parts.length ) {
				curOption = options[ key ] = $.widget.extend( {}, this.options[ key ] );
				for ( i = 0; i < parts.length - 1; i++ ) {
					curOption[ parts[ i ] ] = curOption[ parts[ i ] ] || {};
					curOption = curOption[ parts[ i ] ];
				}
				key = parts.pop();
				if ( value === undefined ) {
					return curOption[ key ] === undefined ? null : curOption[ key ];
				}
				curOption[ key ] = value;
			} else {
				if ( value === undefined ) {
					return this.options[ key ] === undefined ? null : this.options[ key ];
				}
				options[ key ] = value;
			}
		}

		this._setOptions( options );

		return this;
	},
	_setOptions: function( options ) {
		var key;

		for ( key in options ) {
			this._setOption( key, options[ key ] );
		}

		return this;
	},
	_setOption: function( key, value ) {
		this.options[ key ] = value;

		if ( key === "disabled" ) {
			this.widget()
				.toggleClass( this.widgetFullName + "-disabled ui-state-disabled", !!value )
				.attr( "aria-disabled", value );
			this.hoverable.removeClass( "ui-state-hover" );
			this.focusable.removeClass( "ui-state-focus" );
		}

		return this;
	},

	enable: function() {
		return this._setOption( "disabled", false );
	},
	disable: function() {
		return this._setOption( "disabled", true );
	},

	_on: function( suppressDisabledCheck, element, handlers ) {
		var delegateElement,
			instance = this;

		// no suppressDisabledCheck flag, shuffle arguments
		if ( typeof suppressDisabledCheck !== "boolean" ) {
			handlers = element;
			element = suppressDisabledCheck;
			suppressDisabledCheck = false;
		}

		// no element argument, shuffle and use this.element
		if ( !handlers ) {
			handlers = element;
			element = this.element;
			delegateElement = this.widget();
		} else {
			// accept selectors, DOM elements
			element = delegateElement = $( element );
			this.bindings = this.bindings.add( element );
		}

		$.each( handlers, function( event, handler ) {
			function handlerProxy() {
				// allow widgets to customize the disabled handling
				// - disabled as an array instead of boolean
				// - disabled class as method for disabling individual parts
				if ( !suppressDisabledCheck &&
						( instance.options.disabled === true ||
							$( this ).hasClass( "ui-state-disabled" ) ) ) {
					return;
				}
				return ( typeof handler === "string" ? instance[ handler ] : handler )
					.apply( instance, arguments );
			}

			// copy the guid so direct unbinding works
			if ( typeof handler !== "string" ) {
				handlerProxy.guid = handler.guid =
					handler.guid || handlerProxy.guid || $.guid++;
			}

			var match = event.match( /^(\w+)\s*(.*)$/ ),
				eventName = match[1] + instance.eventNamespace,
				selector = match[2];
			if ( selector ) {
				delegateElement.delegate( selector, eventName, handlerProxy );
			} else {
				element.bind( eventName, handlerProxy );
			}
		});
	},

	_off: function( element, eventName ) {
		eventName = (eventName || "").split( " " ).join( this.eventNamespace + " " ) + this.eventNamespace;
		element.unbind( eventName ).undelegate( eventName );
	},

	_delay: function( handler, delay ) {
		function handlerProxy() {
			return ( typeof handler === "string" ? instance[ handler ] : handler )
				.apply( instance, arguments );
		}
		var instance = this;
		return setTimeout( handlerProxy, delay || 0 );
	},

	_hoverable: function( element ) {
		this.hoverable = this.hoverable.add( element );
		this._on( element, {
			mouseenter: function( event ) {
				$( event.currentTarget ).addClass( "ui-state-hover" );
			},
			mouseleave: function( event ) {
				$( event.currentTarget ).removeClass( "ui-state-hover" );
			}
		});
	},

	_focusable: function( element ) {
		this.focusable = this.focusable.add( element );
		this._on( element, {
			focusin: function( event ) {
				$( event.currentTarget ).addClass( "ui-state-focus" );
			},
			focusout: function( event ) {
				$( event.currentTarget ).removeClass( "ui-state-focus" );
			}
		});
	},

	_trigger: function( type, event, data ) {
		var prop, orig,
			callback = this.options[ type ];

		data = data || {};
		event = $.Event( event );
		event.type = ( type === this.widgetEventPrefix ?
			type :
			this.widgetEventPrefix + type ).toLowerCase();
		// the original event may come from any element
		// so we need to reset the target on the new event
		event.target = this.element[ 0 ];

		// copy original event properties over to the new event
		orig = event.originalEvent;
		if ( orig ) {
			for ( prop in orig ) {
				if ( !( prop in event ) ) {
					event[ prop ] = orig[ prop ];
				}
			}
		}

		this.element.trigger( event, data );
		return !( $.isFunction( callback ) &&
			callback.apply( this.element[0], [ event ].concat( data ) ) === false ||
			event.isDefaultPrevented() );
	}
};

$.each( { show: "fadeIn", hide: "fadeOut" }, function( method, defaultEffect ) {
	$.Widget.prototype[ "_" + method ] = function( element, options, callback ) {
		if ( typeof options === "string" ) {
			options = { effect: options };
		}
		var hasOptions,
			effectName = !options ?
				method :
				options === true || typeof options === "number" ?
					defaultEffect :
					options.effect || defaultEffect;
		options = options || {};
		if ( typeof options === "number" ) {
			options = { duration: options };
		}
		hasOptions = !$.isEmptyObject( options );
		options.complete = callback;
		if ( options.delay ) {
			element.delay( options.delay );
		}
		if ( hasOptions && $.effects && $.effects.effect[ effectName ] ) {
			element[ method ]( options );
		} else if ( effectName !== method && element[ effectName ] ) {
			element[ effectName ]( options.duration, options.easing, callback );
		} else {
			element.queue(function( next ) {
				$( this )[ method ]();
				if ( callback ) {
					callback.call( element[ 0 ] );
				}
				next();
			});
		}
	};
});

})( jQuery );

/*!
 * jQuery UI Button 1.10.2
 * http://jqueryui.com
 *
 * Copyright 2013 jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/button/
 *
 * Depends:
 *	jquery.ui.core.js
 *	jquery.ui.widget.js
 */
(function( $, undefined ) {

var lastActive, startXPos, startYPos, clickDragged,
	baseClasses = "ui-button ui-widget ui-state-default ui-corner-all",
	stateClasses = "ui-state-hover ui-state-active ",
	typeClasses = "ui-button-icons-only ui-button-icon-only ui-button-text-icons ui-button-text-icon-primary ui-button-text-icon-secondary ui-button-text-only",
	formResetHandler = function() {
		var buttons = $( this ).find( ":ui-button" );
		setTimeout(function() {
			buttons.button( "refresh" );
		}, 1 );
	},
	radioGroup = function( radio ) {
		var name = radio.name,
			form = radio.form,
			radios = $( [] );
		if ( name ) {
			name = name.replace( /'/g, "\\'" );
			if ( form ) {
				radios = $( form ).find( "[name='" + name + "']" );
			} else {
				radios = $( "[name='" + name + "']", radio.ownerDocument )
					.filter(function() {
						return !this.form;
					});
			}
		}
		return radios;
	};

$.widget( "ui.button", {
	version: "1.10.2",
	defaultElement: "<button>",
	options: {
		disabled: null,
		text: true,
		label: null,
		icons: {
			primary: null,
			secondary: null
		}
	},
	_create: function() {
		this.element.closest( "form" )
			.unbind( "reset" + this.eventNamespace )
			.bind( "reset" + this.eventNamespace, formResetHandler );

		if ( typeof this.options.disabled !== "boolean" ) {
			this.options.disabled = !!this.element.prop( "disabled" );
		} else {
			this.element.prop( "disabled", this.options.disabled );
		}

		this._determineButtonType();
		this.hasTitle = !!this.buttonElement.attr( "title" );

		var that = this,
			options = this.options,
			toggleButton = this.type === "checkbox" || this.type === "radio",
			activeClass = !toggleButton ? "ui-state-active" : "",
			focusClass = "ui-state-focus";

		if ( options.label === null ) {
			options.label = (this.type === "input" ? this.buttonElement.val() : this.buttonElement.html());
		}

		this._hoverable( this.buttonElement );

		this.buttonElement
			.addClass( baseClasses )
			.attr( "role", "button" )
			.bind( "mouseenter" + this.eventNamespace, function() {
				if ( options.disabled ) {
					return;
				}
				if ( this === lastActive ) {
					$( this ).addClass( "ui-state-active" );
				}
			})
			.bind( "mouseleave" + this.eventNamespace, function() {
				if ( options.disabled ) {
					return;
				}
				$( this ).removeClass( activeClass );
			})
			.bind( "click" + this.eventNamespace, function( event ) {
				if ( options.disabled ) {
					event.preventDefault();
					event.stopImmediatePropagation();
				}
			});

		this.element
			.bind( "focus" + this.eventNamespace, function() {
				// no need to check disabled, focus won't be triggered anyway
				that.buttonElement.addClass( focusClass );
			})
			.bind( "blur" + this.eventNamespace, function() {
				that.buttonElement.removeClass( focusClass );
			});

		if ( toggleButton ) {
			this.element.bind( "change" + this.eventNamespace, function() {
				if ( clickDragged ) {
					return;
				}
				that.refresh();
			});
			// if mouse moves between mousedown and mouseup (drag) set clickDragged flag
			// prevents issue where button state changes but checkbox/radio checked state
			// does not in Firefox (see ticket #6970)
			this.buttonElement
				.bind( "mousedown" + this.eventNamespace, function( event ) {
					if ( options.disabled ) {
						return;
					}
					clickDragged = false;
					startXPos = event.pageX;
					startYPos = event.pageY;
				})
				.bind( "mouseup" + this.eventNamespace, function( event ) {
					if ( options.disabled ) {
						return;
					}
					if ( startXPos !== event.pageX || startYPos !== event.pageY ) {
						clickDragged = true;
					}
			});
		}

		if ( this.type === "checkbox" ) {
			this.buttonElement.bind( "click" + this.eventNamespace, function() {
				if ( options.disabled || clickDragged ) {
					return false;
				}
			});
		} else if ( this.type === "radio" ) {
			this.buttonElement.bind( "click" + this.eventNamespace, function() {
				if ( options.disabled || clickDragged ) {
					return false;
				}
				$( this ).addClass( "ui-state-active" );
				that.buttonElement.attr( "aria-pressed", "true" );

				var radio = that.element[ 0 ];
				radioGroup( radio )
					.not( radio )
					.map(function() {
						return $( this ).button( "widget" )[ 0 ];
					})
					.removeClass( "ui-state-active" )
					.attr( "aria-pressed", "false" );
			});
		} else {
			this.buttonElement
				.bind( "mousedown" + this.eventNamespace, function() {
					if ( options.disabled ) {
						return false;
					}
					$( this ).addClass( "ui-state-active" );
					lastActive = this;
					that.document.one( "mouseup", function() {
						lastActive = null;
					});
				})
				.bind( "mouseup" + this.eventNamespace, function() {
					if ( options.disabled ) {
						return false;
					}
					$( this ).removeClass( "ui-state-active" );
				})
				.bind( "keydown" + this.eventNamespace, function(event) {
					if ( options.disabled ) {
						return false;
					}
					if ( event.keyCode === $.ui.keyCode.SPACE || event.keyCode === $.ui.keyCode.ENTER ) {
						$( this ).addClass( "ui-state-active" );
					}
				})
				// see #8559, we bind to blur here in case the button element loses
				// focus between keydown and keyup, it would be left in an "active" state
				.bind( "keyup" + this.eventNamespace + " blur" + this.eventNamespace, function() {
					$( this ).removeClass( "ui-state-active" );
				});

			if ( this.buttonElement.is("a") ) {
				this.buttonElement.keyup(function(event) {
					if ( event.keyCode === $.ui.keyCode.SPACE ) {
						//      pass through original event correctly (just as 2nd argument doesn't work)
						$( this ).click();
					}
				});
			}
		}

		//     : pull out $.Widget's handling for the disabled option into
		// $.Widget.prototype._setOptionDisabled so it's easy to proxy and can
		// be overridden by individual plugins
		this._setOption( "disabled", options.disabled );
		this._resetButton();
	},

	_determineButtonType: function() {
		var ancestor, labelSelector, checked;

		if ( this.element.is("[type=checkbox]") ) {
			this.type = "checkbox";
		} else if ( this.element.is("[type=radio]") ) {
			this.type = "radio";
		} else if ( this.element.is("input") ) {
			this.type = "input";
		} else {
			this.type = "button";
		}

		if ( this.type === "checkbox" || this.type === "radio" ) {
			// we don't search against the document in case the element
			// is disconnected from the DOM
			ancestor = this.element.parents().last();
			labelSelector = "label[for='" + this.element.attr("id") + "']";
			this.buttonElement = ancestor.find( labelSelector );
			if ( !this.buttonElement.length ) {
				ancestor = ancestor.length ? ancestor.siblings() : this.element.siblings();
				this.buttonElement = ancestor.filter( labelSelector );
				if ( !this.buttonElement.length ) {
					this.buttonElement = ancestor.find( labelSelector );
				}
			}
			this.element.addClass( "ui-helper-hidden-accessible" );

			checked = this.element.is( ":checked" );
			if ( checked ) {
				this.buttonElement.addClass( "ui-state-active" );
			}
			this.buttonElement.prop( "aria-pressed", checked );
		} else {
			this.buttonElement = this.element;
		}
	},

	widget: function() {
		return this.buttonElement;
	},

	_destroy: function() {
		this.element
			.removeClass( "ui-helper-hidden-accessible" );
		this.buttonElement
			.removeClass( baseClasses + " " + stateClasses + " " + typeClasses )
			.removeAttr( "role" )
			.removeAttr( "aria-pressed" )
			.html( this.buttonElement.find(".ui-button-text").html() );

		if ( !this.hasTitle ) {
			this.buttonElement.removeAttr( "title" );
		}
	},

	_setOption: function( key, value ) {
		this._super( key, value );
		if ( key === "disabled" ) {
			if ( value ) {
				this.element.prop( "disabled", true );
			} else {
				this.element.prop( "disabled", false );
			}
			return;
		}
		this._resetButton();
	},

	refresh: function() {
		//See #8237 & #8828
		var isDisabled = this.element.is( "input, button" ) ? this.element.is( ":disabled" ) : this.element.hasClass( "ui-button-disabled" );

		if ( isDisabled !== this.options.disabled ) {
			this._setOption( "disabled", isDisabled );
		}
		if ( this.type === "radio" ) {
			radioGroup( this.element[0] ).each(function() {
				if ( $( this ).is( ":checked" ) ) {
					$( this ).button( "widget" )
						.addClass( "ui-state-active" )
						.attr( "aria-pressed", "true" );
				} else {
					$( this ).button( "widget" )
						.removeClass( "ui-state-active" )
						.attr( "aria-pressed", "false" );
				}
			});
		} else if ( this.type === "checkbox" ) {
			if ( this.element.is( ":checked" ) ) {
				this.buttonElement
					.addClass( "ui-state-active" )
					.attr( "aria-pressed", "true" );
			} else {
				this.buttonElement
					.removeClass( "ui-state-active" )
					.attr( "aria-pressed", "false" );
			}
		}
	},

	_resetButton: function() {
		if ( this.type === "input" ) {
			if ( this.options.label ) {
				this.element.val( this.options.label );
			}
			return;
		}
		var buttonElement = this.buttonElement.removeClass( typeClasses ),
			buttonText = $( "<span></span>", this.document[0] )
				.addClass( "ui-button-text" )
				.html( this.options.label )
				.appendTo( buttonElement.empty() )
				.text(),
			icons = this.options.icons,
			multipleIcons = icons.primary && icons.secondary,
			buttonClasses = [];

		if ( icons.primary || icons.secondary ) {
			if ( this.options.text ) {
				buttonClasses.push( "ui-button-text-icon" + ( multipleIcons ? "s" : ( icons.primary ? "-primary" : "-secondary" ) ) );
			}

			if ( icons.primary ) {
				buttonElement.prepend( "<span class='ui-button-icon-primary ui-icon " + icons.primary + "'></span>" );
			}

			if ( icons.secondary ) {
				buttonElement.append( "<span class='ui-button-icon-secondary ui-icon " + icons.secondary + "'></span>" );
			}

			if ( !this.options.text ) {
				buttonClasses.push( multipleIcons ? "ui-button-icons-only" : "ui-button-icon-only" );

				if ( !this.hasTitle ) {
					buttonElement.attr( "title", $.trim( buttonText ) );
				}
			}
		} else {
			buttonClasses.push( "ui-button-text-only" );
		}
		buttonElement.addClass( buttonClasses.join( " " ) );
	}
});

$.widget( "ui.buttonset", {
	version: "1.10.2",
	options: {
		items: "button, input[type=button], input[type=submit], input[type=reset], input[type=checkbox], input[type=radio], a, :data(ui-button)"
	},

	_create: function() {
		this.element.addClass( "ui-buttonset" );
	},

	_init: function() {
		this.refresh();
	},

	_setOption: function( key, value ) {
		if ( key === "disabled" ) {
			this.buttons.button( "option", key, value );
		}

		this._super( key, value );
	},

	refresh: function() {
		var rtl = this.element.css( "direction" ) === "rtl";

		this.buttons = this.element.find( this.options.items )
			.filter( ":ui-button" )
				.button( "refresh" )
			.end()
			.not( ":ui-button" )
				.button()
			.end()
			.map(function() {
				return $( this ).button( "widget" )[ 0 ];
			})
				.removeClass( "ui-corner-all ui-corner-left ui-corner-right" )
				.filter( ":first" )
					.addClass( rtl ? "ui-corner-right" : "ui-corner-left" )
				.end()
				.filter( ":last" )
					.addClass( rtl ? "ui-corner-left" : "ui-corner-right" )
				.end()
			.end();
	},

	_destroy: function() {
		this.element.removeClass( "ui-buttonset" );
		this.buttons
			.map(function() {
				return $( this ).button( "widget" )[ 0 ];
			})
				.removeClass( "ui-corner-left ui-corner-right" )
			.end()
			.button( "destroy" );
	}
});

}( jQuery ) );

/*
 * jQuery UI Accordion 1.8.16
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Accordion
 *
 * Depends:
 *	jquery.ui.core.js
 *	jquery.ui.widget.js
 */
(function( $, undefined ) {

$.widget( "ui.accordion", {
	options: {
		active: 0,
		animated: "slide",
		autoHeight: true,
		clearStyle: false,
		collapsible: false,
		event: "click",
		fillSpace: false,
		header: "> li > :first-child,> :not(li):even",
		icons: {
			header: "ui-icon-triangle-1-e",
			headerSelected: "ui-icon-triangle-1-s"
		},
		navigation: false,
		navigationFilter: function() {
			return this.href.toLowerCase() === location.href.toLowerCase();
		}
	},

	_create: function() {
		var self = this,
			options = self.options;

		self.running = 0;

		self.element
			.addClass( "ui-accordion ui-widget ui-helper-reset" )
			// in lack of child-selectors in CSS
			// we need to mark top-LIs in a UL-accordion for some IE-fix
			.children( "li" )
				.addClass( "ui-accordion-li-fix" );

		self.headers = self.element.find( options.header )
			.addClass( "ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" )
			.bind( "mouseenter.accordion", function() {
				if ( options.disabled ) {
					return;
				}
				$( this ).addClass( "ui-state-hover" );
			})
			.bind( "mouseleave.accordion", function() {
				if ( options.disabled ) {
					return;
				}
				$( this ).removeClass( "ui-state-hover" );
			})
			.bind( "focus.accordion", function() {
				if ( options.disabled ) {
					return;
				}
				$( this ).addClass( "ui-state-focus" );
			})
			.bind( "blur.accordion", function() {
				if ( options.disabled ) {
					return;
				}
				$( this ).removeClass( "ui-state-focus" );
			});

		self.headers.next()
			.addClass( "ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" );

		if ( options.navigation ) {
			var current = self.element.find( "a" ).filter( options.navigationFilter ).eq( 0 );
			if ( current.length ) {
				var header = current.closest( ".ui-accordion-header" );
				if ( header.length ) {
					// anchor within header
					self.active = header;
				} else {
					// anchor within content
					self.active = current.closest( ".ui-accordion-content" ).prev();
				}
			}
		}

		self.active = self._findActive( self.active || options.active )
			.addClass( "ui-state-default ui-state-active" )
			.toggleClass( "ui-corner-all" )
			.toggleClass( "ui-corner-top" );
		self.active.next().addClass( "ui-accordion-content-active" );

		self._createIcons();
		self.resize();
		
		// ARIA
		self.element.attr( "role", "tablist" );

		self.headers
			.attr( "role", "tab" )
			.bind( "keydown.accordion", function( event ) {
				return self._keydown( event );
			})
			.next()
				.attr( "role", "tabpanel" );

		self.headers
			.not( self.active || "" )
			.attr({
				"aria-expanded": "false",
				"aria-selected": "false",
				tabIndex: -1
			})
			.next()
				.hide();

		// make sure at least one header is in the tab order
		if ( !self.active.length ) {
			self.headers.eq( 0 ).attr( "tabIndex", 0 );
		} else {
			self.active
				.attr({
					"aria-expanded": "true",
					"aria-selected": "true",
					tabIndex: 0
				});
		}

		// only need links in tab order for Safari
		if ( !$.browser.safari ) {
			self.headers.find( "a" ).attr( "tabIndex", -1 );
		}

		if ( options.event ) {
			self.headers.bind( options.event.split(" ").join(".accordion ") + ".accordion", function(event) {
				self._clickHandler.call( self, event, this );
				event.preventDefault();
			});
		}
	},

	_createIcons: function() {
		var options = this.options;
		if ( options.icons ) {
			$( "<span></span>" )
				.addClass( "ui-icon " + options.icons.header )
				.prependTo( this.headers );
			this.active.children( ".ui-icon" )
				.toggleClass(options.icons.header)
				.toggleClass(options.icons.headerSelected);
			this.element.addClass( "ui-accordion-icons" );
		}
	},

	_destroyIcons: function() {
		this.headers.children( ".ui-icon" ).remove();
		this.element.removeClass( "ui-accordion-icons" );
	},

	destroy: function() {
		var options = this.options;

		this.element
			.removeClass( "ui-accordion ui-widget ui-helper-reset" )
			.removeAttr( "role" );

		this.headers
			.unbind( ".accordion" )
			.removeClass( "ui-accordion-header ui-accordion-disabled ui-helper-reset ui-state-default ui-corner-all ui-state-active ui-state-disabled ui-corner-top" )
			.removeAttr( "role" )
			.removeAttr( "aria-expanded" )
			.removeAttr( "aria-selected" )
			.removeAttr( "tabIndex" );

		this.headers.find( "a" ).removeAttr( "tabIndex" );
		this._destroyIcons();
		var contents = this.headers.next()
			.css( "display", "" )
			.removeAttr( "role" )
			.removeClass( "ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content ui-accordion-content-active ui-accordion-disabled ui-state-disabled" );
		if ( options.autoHeight || options.fillHeight ) {
			contents.css( "height", "" );
		}

		return $.Widget.prototype.destroy.call( this );
	},

	_setOption: function( key, value ) {
		$.Widget.prototype._setOption.apply( this, arguments );
			
		if ( key == "active" ) {
			this.activate( value );
		}
		if ( key == "icons" ) {
			this._destroyIcons();
			if ( value ) {
				this._createIcons();
			}
		}
		// #5332 - opacity doesn't cascade to positioned elements in IE
		// so we need to add the disabled class to the headers and panels
		if ( key == "disabled" ) {
			this.headers.add(this.headers.next())
				[ value ? "addClass" : "removeClass" ](
					"ui-accordion-disabled ui-state-disabled" );
		}
	},

	_keydown: function( event ) {
		if ( this.options.disabled || event.altKey || event.ctrlKey ) {
			return;
		}

		var keyCode = $.ui.keyCode,
			length = this.headers.length,
			currentIndex = this.headers.index( event.target ),
			toFocus = false;

		switch ( event.keyCode ) {
			case keyCode.RIGHT:
			case keyCode.DOWN:
				toFocus = this.headers[ ( currentIndex + 1 ) % length ];
				break;
			case keyCode.LEFT:
			case keyCode.UP:
				toFocus = this.headers[ ( currentIndex - 1 + length ) % length ];
				break;
			case keyCode.SPACE:
			case keyCode.ENTER:
				this._clickHandler( { target: event.target }, event.target );
				event.preventDefault();
		}

		if ( toFocus ) {
			$( event.target ).attr( "tabIndex", -1 );
			$( toFocus ).attr( "tabIndex", 0 );
			toFocus.focus();
			return false;
		}

		return true;
	},

	resize: function() {
		var options = this.options,
			maxHeight;

		if ( options.fillSpace ) {
			if ( $.browser.msie ) {
				var defOverflow = this.element.parent().css( "overflow" );
				this.element.parent().css( "overflow", "hidden");
			}
			maxHeight = this.element.parent().height();
			if ($.browser.msie) {
				this.element.parent().css( "overflow", defOverflow );
			}

			this.headers.filter(':visible').each(function() {
				maxHeight -= $( this ).outerHeight( true );
			});

			this.headers.next()
				.each(function() {
					$( this ).height( Math.max( 0, maxHeight -
						$( this ).innerHeight() + $( this ).height() ) );
				})
				.css( "overflow", "auto" );
		} else if ( options.autoHeight ) {
			maxHeight = 0;
			this.headers.next()
				.each(function() {
					maxHeight = Math.max( maxHeight, $( this ).height( "" ).height() );
				})
				.height( maxHeight );
		}

		return this;
	},

	activate: function( index ) {
		//      this gets called on init, changing the option without an explicit call for that
		this.options.active = index;
		// call clickHandler with custom event
		var active = this._findActive( index )[ 0 ];
		this._clickHandler( { target: active }, active );

		return this;
	},

	_findActive: function( selector ) {
		return selector
			? typeof selector === "number"
				? this.headers.filter( ":eq(" + selector + ")" )
				: this.headers.not( this.headers.not( selector ) )
			: selector === false
				? $( [] )
				: this.headers.filter( ":eq(0)" );
	},

	//      isn't event.target enough? why the separate target argument?
	_clickHandler: function( event, target ) {
		var options = this.options;
		if ( options.disabled ) {
			return;
		}

		// called only when using activate(false) to close all parts programmatically
		if ( !event.target ) {
			if ( !options.collapsible ) {
				return;
			}
			this.active
				.removeClass( "ui-state-active ui-corner-top" )
				.addClass( "ui-state-default ui-corner-all" )
				.children( ".ui-icon" )
					.removeClass( options.icons.headerSelected )
					.addClass( options.icons.header );
			this.active.next().addClass( "ui-accordion-content-active" );
			var toHide = this.active.next(),
				data = {
					options: options,
					newHeader: $( [] ),
					oldHeader: options.active,
					newContent: $( [] ),
					oldContent: toHide
				},
				toShow = ( this.active = $( [] ) );
			this._toggle( toShow, toHide, data );
			return;
		}

		// get the click target
		var clicked = $( event.currentTarget || target ),
			clickedIsActive = clicked[0] === this.active[0];

		//      the option is changed, is that correct?
		//      if it is correct, shouldn't that happen after determining that the click is valid?
		options.active = options.collapsible && clickedIsActive ?
			false :
			this.headers.index( clicked );

		// if animations are still active, or the active header is the target, ignore click
		if ( this.running || ( !options.collapsible && clickedIsActive ) ) {
			return;
		}

		// find elements to show and hide
		var active = this.active,
			toShow = clicked.next(),
			toHide = this.active.next(),
			data = {
				options: options,
				newHeader: clickedIsActive && options.collapsible ? $([]) : clicked,
				oldHeader: this.active,
				newContent: clickedIsActive && options.collapsible ? $([]) : toShow,
				oldContent: toHide
			},
			down = this.headers.index( this.active[0] ) > this.headers.index( clicked[0] );

		// when the call to ._toggle() comes after the class changes
		// it causes a very odd bug in IE 8 (see #6720)
		this.active = clickedIsActive ? $([]) : clicked;
		this._toggle( toShow, toHide, data, clickedIsActive, down );

		// switch classes
		active
			.removeClass( "ui-state-active ui-corner-top" )
			.addClass( "ui-state-default ui-corner-all" )
			.children( ".ui-icon" )
				.removeClass( options.icons.headerSelected )
				.addClass( options.icons.header );
		if ( !clickedIsActive ) {
			clicked
				.removeClass( "ui-state-default ui-corner-all" )
				.addClass( "ui-state-active ui-corner-top" )
				.children( ".ui-icon" )
					.removeClass( options.icons.header )
					.addClass( options.icons.headerSelected );
			clicked
				.next()
				.addClass( "ui-accordion-content-active" );
		}

		return;
	},

	_toggle: function( toShow, toHide, data, clickedIsActive, down ) {
		var self = this,
			options = self.options;

		self.toShow = toShow;
		self.toHide = toHide;
		self.data = data;

		var complete = function() {
			if ( !self ) {
				return;
			}
			return self._completed.apply( self, arguments );
		};

		// trigger changestart event
		self._trigger( "changestart", null, self.data );

		// count elements to animate
		self.running = toHide.size() === 0 ? toShow.size() : toHide.size();

		if ( options.animated ) {
			var animOptions = {};

			if ( options.collapsible && clickedIsActive ) {
				animOptions = {
					toShow: $( [] ),
					toHide: toHide,
					complete: complete,
					down: down,
					autoHeight: options.autoHeight || options.fillSpace
				};
			} else {
				animOptions = {
					toShow: toShow,
					toHide: toHide,
					complete: complete,
					down: down,
					autoHeight: options.autoHeight || options.fillSpace
				};
			}

			if ( !options.proxied ) {
				options.proxied = options.animated;
			}

			if ( !options.proxiedDuration ) {
				options.proxiedDuration = options.duration;
			}

			options.animated = $.isFunction( options.proxied ) ?
				options.proxied( animOptions ) :
				options.proxied;

			options.duration = $.isFunction( options.proxiedDuration ) ?
				options.proxiedDuration( animOptions ) :
				options.proxiedDuration;

			var animations = $.ui.accordion.animations,
				duration = options.duration,
				easing = options.animated;

			if ( easing && !animations[ easing ] && !$.easing[ easing ] ) {
				easing = "slide";
			}
			if ( !animations[ easing ] ) {
				animations[ easing ] = function( options ) {
					this.slide( options, {
						easing: easing,
						duration: duration || 700
					});
				};
			}

			animations[ easing ]( animOptions );
		} else {
			if ( options.collapsible && clickedIsActive ) {
				toShow.toggle();
			} else {
				toHide.hide();
				toShow.show();
			}

			complete( true );
		}

		//      assert that the blur and focus triggers are really necessary, remove otherwise
		toHide.prev()
			.attr({
				"aria-expanded": "false",
				"aria-selected": "false",
				tabIndex: -1
			})
			.blur();
		toShow.prev()
			.attr({
				"aria-expanded": "true",
				"aria-selected": "true",
				tabIndex: 0
			})
			.focus();
	},

	_completed: function( cancel ) {
		this.running = cancel ? 0 : --this.running;
		if ( this.running ) {
			return;
		}

		if ( this.options.clearStyle ) {
			this.toShow.add( this.toHide ).css({
				height: "",
				overflow: ""
			});
		}

		// other classes are removed before the animation; this one needs to stay until completed
		this.toHide.removeClass( "ui-accordion-content-active" );
		// Work around for rendering bug in IE (#5421)
		if ( this.toHide.length ) {
			this.toHide.parent()[0].className = this.toHide.parent()[0].className;
		}

		this._trigger( "change", null, this.data );
	}
});

$.extend( $.ui.accordion, {
	version: "1.8.16",
	animations: {
		slide: function( options, additions ) {
			options = $.extend({
				easing: "swing",
				duration: 300
			}, options, additions );
			if ( !options.toHide.size() ) {
				options.toShow.animate({
					height: "show",
					paddingTop: "show",
					paddingBottom: "show"
				}, options );
				return;
			}
			if ( !options.toShow.size() ) {
				options.toHide.animate({
					height: "hide",
					paddingTop: "hide",
					paddingBottom: "hide"
				}, options );
				return;
			}
			var overflow = options.toShow.css( "overflow" ),
				percentDone = 0,
				showProps = {},
				hideProps = {},
				fxAttrs = [ "height", "paddingTop", "paddingBottom" ],
				originalWidth;
			// fix width before calculating height of hidden element
			var s = options.toShow;
			originalWidth = s[0].style.width;
			s.width( parseInt( s.parent().width(), 10 )
				- parseInt( s.css( "paddingLeft" ), 10 )
				- parseInt( s.css( "paddingRight" ), 10 )
				- ( parseInt( s.css( "borderLeftWidth" ), 10 ) || 0 )
				- ( parseInt( s.css( "borderRightWidth" ), 10) || 0 ) );

			$.each( fxAttrs, function( i, prop ) {
				hideProps[ prop ] = "hide";

				var parts = ( "" + $.css( options.toShow[0], prop ) ).match( /^([\d+-.]+)(.*)$/ );
				showProps[ prop ] = {
					value: parts[ 1 ],
					unit: parts[ 2 ] || "px"
				};
			});
			options.toShow.css({ height: 0, overflow: "hidden" }).show();
			options.toHide
				.filter( ":hidden" )
					.each( options.complete )
				.end()
				.filter( ":visible" )
				.animate( hideProps, {
				step: function( now, settings ) {
					// only calculate the percent when animating height
					// IE gets very inconsistent results when animating elements
					// with small values, which is common for padding
					if ( settings.prop == "height" ) {
						percentDone = ( settings.end - settings.start === 0 ) ? 0 :
							( settings.now - settings.start ) / ( settings.end - settings.start );
					}

					options.toShow[ 0 ].style[ settings.prop ] =
						( percentDone * showProps[ settings.prop ].value )
						+ showProps[ settings.prop ].unit;
				},
				duration: options.duration,
				easing: options.easing,
				complete: function() {
					if ( !options.autoHeight ) {
						options.toShow.css( "height", "" );
					}
					options.toShow.css({
						width: originalWidth,
						overflow: overflow
					});
					options.complete();
				}
			});
		},
		bounceslide: function( options ) {
			this.slide( options, {
				easing: options.down ? "easeOutBounce" : "swing",
				duration: options.down ? 1000 : 200
			});
		}
	}
});

})( jQuery );

/**
 *
 * Color picker
 * Author: Stefan Petre www.eyecon.ro
 * 
 * Dual licensed under the MIT and GPL licenses
 * 
 */
(function ($) {
	var ColorPicker = function () {
		var
			ids = {},
			inAction,
			charMin = 65,
			visible,
			tpl = '<div class="colorpicker"><div class="colorpicker_color"><div><div></div></div></div><div class="colorpicker_hue"><div></div></div><div class="colorpicker_new_color"></div><div class="colorpicker_current_color"></div><div class="colorpicker_hex"><input type="text" maxlength="6" size="6" /></div><div class="colorpicker_rgb_r colorpicker_field"><input type="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_rgb_g colorpicker_field"><input type="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_rgb_b colorpicker_field"><input type="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_hsb_h colorpicker_field"><input type="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_hsb_s colorpicker_field"><input type="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_hsb_b colorpicker_field"><input type="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_submit"></div></div>',
			defaults = {
				eventName: 'click',
				onShow: function () {},
				onBeforeShow: function(){},
				onHide: function () {},
				onChange: function () {},
				onSubmit: function () {},
				color: 'ff0000',
				livePreview: true,
				flat: false
			},
			fillRGBFields = function  (hsb, cal) {
				var rgb = HSBToRGB(hsb);
				$(cal).data('colorpicker').fields
					.eq(1).val(rgb.r).end()
					.eq(2).val(rgb.g).end()
					.eq(3).val(rgb.b).end();
			},
			fillHSBFields = function  (hsb, cal) {
				$(cal).data('colorpicker').fields
					.eq(4).val(hsb.h).end()
					.eq(5).val(hsb.s).end()
					.eq(6).val(hsb.b).end();
			},
			fillHexFields = function (hsb, cal) {
				$(cal).data('colorpicker').fields
					.eq(0).val(HSBToHex(hsb)).end();
			},
			setSelector = function (hsb, cal) {
				$(cal).data('colorpicker').selector.css('backgroundColor', '#' + HSBToHex({h: hsb.h, s: 100, b: 100}));
				$(cal).data('colorpicker').selectorIndic.css({
					left: parseInt(150 * hsb.s/100, 10),
					top: parseInt(150 * (100-hsb.b)/100, 10)
				});
			},
			setHue = function (hsb, cal) {
				$(cal).data('colorpicker').hue.css('top', parseInt(150 - 150 * hsb.h/360, 10));
			},
			setCurrentColor = function (hsb, cal) {
				$(cal).data('colorpicker').currentColor.css('backgroundColor', '#' + HSBToHex(hsb));
			},
			setNewColor = function (hsb, cal) {
				$(cal).data('colorpicker').newColor.css('backgroundColor', '#' + HSBToHex(hsb));
			},
			keyDown = function (ev) {
				var pressedKey = ev.charCode || ev.keyCode || -1;
				if ((pressedKey > charMin && pressedKey <= 90) || pressedKey == 32) {
					return false;
				}
				var cal = $(this).parent().parent();
				if (cal.data('colorpicker').livePreview === true) {
					change.apply(this);
				}
			},
			change = function (ev) {
				var cal = $(this).parent().parent(), col;
				if (this.parentNode.className.indexOf('_hex') > 0) {
					cal.data('colorpicker').color = col = HexToHSB(fixHex(this.value));
				} else if (this.parentNode.className.indexOf('_hsb') > 0) {
					cal.data('colorpicker').color = col = fixHSB({
						h: parseInt(cal.data('colorpicker').fields.eq(4).val(), 10),
						s: parseInt(cal.data('colorpicker').fields.eq(5).val(), 10),
						b: parseInt(cal.data('colorpicker').fields.eq(6).val(), 10)
					});
				} else {
					cal.data('colorpicker').color = col = RGBToHSB(fixRGB({
						r: parseInt(cal.data('colorpicker').fields.eq(1).val(), 10),
						g: parseInt(cal.data('colorpicker').fields.eq(2).val(), 10),
						b: parseInt(cal.data('colorpicker').fields.eq(3).val(), 10)
					}));
				}
				if (ev) {
					fillRGBFields(col, cal.get(0));
					fillHexFields(col, cal.get(0));
					fillHSBFields(col, cal.get(0));
				}
				setSelector(col, cal.get(0));
				setHue(col, cal.get(0));
				setNewColor(col, cal.get(0));
				cal.data('colorpicker').onChange.apply(cal, [col, HSBToHex(col), HSBToRGB(col)]);
			},
			blur = function (ev) {
				var cal = $(this).parent().parent();
				cal.data('colorpicker').fields.parent().removeClass('colorpicker_focus');
			},
			focus = function () {
				charMin = this.parentNode.className.indexOf('_hex') > 0 ? 70 : 65;
				$(this).parent().parent().data('colorpicker').fields.parent().removeClass('colorpicker_focus');
				$(this).parent().addClass('colorpicker_focus');
			},
			downIncrement = function (ev) {
				var field = $(this).parent().find('input').focus();
				var current = {
					el: $(this).parent().addClass('colorpicker_slider'),
					max: this.parentNode.className.indexOf('_hsb_h') > 0 ? 360 : (this.parentNode.className.indexOf('_hsb') > 0 ? 100 : 255),
					y: ev.pageY,
					field: field,
					val: parseInt(field.val(), 10),
					preview: $(this).parent().parent().data('colorpicker').livePreview					
				};
				$(document).bind('mouseup', current, upIncrement);
				$(document).bind('mousemove', current, moveIncrement);
			},
			moveIncrement = function (ev) {
				ev.data.field.val(Math.max(0, Math.min(ev.data.max, parseInt(ev.data.val + ev.pageY - ev.data.y, 10))));
				if (ev.data.preview) {
					change.apply(ev.data.field.get(0), [true]);
				}
				return false;
			},
			upIncrement = function (ev) {
				change.apply(ev.data.field.get(0), [true]);
				ev.data.el.removeClass('colorpicker_slider').find('input').focus();
				$(document).unbind('mouseup', upIncrement);
				$(document).unbind('mousemove', moveIncrement);
				return false;
			},
			downHue = function (ev) {
				var current = {
					cal: $(this).parent(),
					y: $(this).offset().top
				};
				current.preview = current.cal.data('colorpicker').livePreview;
				$(document).bind('mouseup', current, upHue);
				$(document).bind('mousemove', current, moveHue);
				ev.type = 'mousemove';
				$(document).trigger(ev);
			},
			moveHue = function (ev) {
				change.apply(
					ev.data.cal.data('colorpicker')
						.fields
						.eq(4)
						.val(parseInt(360*(150 - Math.max(0,Math.min(150,(ev.pageY - ev.data.y))))/150, 10))
						.get(0),
					[ev.data.preview]
				);
				return false;
			},
			upHue = function (ev) {
				fillRGBFields(ev.data.cal.data('colorpicker').color, ev.data.cal.get(0));
				fillHexFields(ev.data.cal.data('colorpicker').color, ev.data.cal.get(0));
				$(document).unbind('mouseup', upHue);
				$(document).unbind('mousemove', moveHue);
				return false;
			},
			downSelector = function (ev) {
				var current = {
					cal: $(this).parent(),
					pos: $(this).offset()
				};
				current.preview = current.cal.data('colorpicker').livePreview;
				$(document).bind('mouseup', current, upSelector);
				$(document).bind('mousemove', current, moveSelector);
				ev.type = 'mousemove';
				$(document).trigger(ev);
			},
			moveSelector = function (ev) {
				change.apply(
					ev.data.cal.data('colorpicker')
						.fields
						.eq(6)
						.val(parseInt(100*(150 - Math.max(0,Math.min(150,(ev.pageY - ev.data.pos.top))))/150, 10))
						.end()
						.eq(5)
						.val(parseInt(100*(Math.max(0,Math.min(150,(ev.pageX - ev.data.pos.left))))/150, 10))
						.get(0),
					[ev.data.preview]
				);
				return false;
			},
			upSelector = function (ev) {
				fillRGBFields(ev.data.cal.data('colorpicker').color, ev.data.cal.get(0));
				fillHexFields(ev.data.cal.data('colorpicker').color, ev.data.cal.get(0));
				$(document).unbind('mouseup', upSelector);
				$(document).unbind('mousemove', moveSelector);
				return false;
			},
			enterSubmit = function (ev) {
				$(this).addClass('colorpicker_focus');
			},
			leaveSubmit = function (ev) {
				$(this).removeClass('colorpicker_focus');
			},
			clickSubmit = function (ev) {
				var cal = $(this).parent();
				var col = cal.data('colorpicker').color;
				cal.data('colorpicker').origColor = col;
				setCurrentColor(col, cal.get(0));
				cal.data('colorpicker').onSubmit(col, HSBToHex(col), HSBToRGB(col), cal.data('colorpicker').el);
			},
			show = function (ev) {
				var cal = $('#' + $(this).data('colorpickerId'));
				cal.data('colorpicker').onBeforeShow.apply(this, [cal.get(0)]);
				var pos = $(this).offset();
				var viewPort = getViewport();
				var top = pos.top + this.offsetHeight;
				var left = pos.left;
				if (top + 176 > viewPort.t + viewPort.h) {
					top -= this.offsetHeight + 176;
				}
				if (left + 356 > viewPort.l + viewPort.w) {
					left -= 356;
				}
				cal.css({left: left + 'px', top: top + 'px'});
				if (cal.data('colorpicker').onShow.apply(this, [cal.get(0)]) != false) {
					cal.show();
				}
				$(document).bind('mousedown', {cal: cal}, hide);
				return false;
			},
			hide = function (ev) {
				if (!isChildOf(ev.data.cal.get(0), ev.target, ev.data.cal.get(0))) {
					if (ev.data.cal.data('colorpicker').onHide.apply(this, [ev.data.cal.get(0)]) != false) {
						ev.data.cal.hide();
					}
					$(document).unbind('mousedown', hide);
				}
			},
			isChildOf = function(parentEl, el, container) {
				if (parentEl == el) {
					return true;
				}
				if (parentEl.contains) {
					return parentEl.contains(el);
				}
				if ( parentEl.compareDocumentPosition ) {
					return !!(parentEl.compareDocumentPosition(el) & 16);
				}
				var prEl = el.parentNode;
				while(prEl && prEl != container) {
					if (prEl == parentEl)
						return true;
					prEl = prEl.parentNode;
				}
				return false;
			},
			getViewport = function () {
				var m = document.compatMode == 'CSS1Compat';
				return {
					l : window.pageXOffset || (m ? document.documentElement.scrollLeft : document.body.scrollLeft),
					t : window.pageYOffset || (m ? document.documentElement.scrollTop : document.body.scrollTop),
					w : window.innerWidth || (m ? document.documentElement.clientWidth : document.body.clientWidth),
					h : window.innerHeight || (m ? document.documentElement.clientHeight : document.body.clientHeight)
				};
			},
			fixHSB = function (hsb) {
				return {
					h: Math.min(360, Math.max(0, hsb.h)),
					s: Math.min(100, Math.max(0, hsb.s)),
					b: Math.min(100, Math.max(0, hsb.b))
				};
			}, 
			fixRGB = function (rgb) {
				return {
					r: Math.min(255, Math.max(0, rgb.r)),
					g: Math.min(255, Math.max(0, rgb.g)),
					b: Math.min(255, Math.max(0, rgb.b))
				};
			},
			fixHex = function (hex) {
				var len = 6 - hex.length;
				if (len > 0) {
					var o = [];
					for (var i=0; i<len; i++) {
						o.push('0');
					}
					o.push(hex);
					hex = o.join('');
				}
				return hex;
			}, 
			HexToRGB = function (hex) {
				var hex = parseInt(((hex.indexOf('#') > -1) ? hex.substring(1) : hex), 16);
				return {r: hex >> 16, g: (hex & 0x00FF00) >> 8, b: (hex & 0x0000FF)};
			},
			HexToHSB = function (hex) {
				return RGBToHSB(HexToRGB(hex));
			},
			RGBToHSB = function (rgb) {
				var hsb = {
					h: 0,
					s: 0,
					b: 0
				};
				var min = Math.min(rgb.r, rgb.g, rgb.b);
				var max = Math.max(rgb.r, rgb.g, rgb.b);
				var delta = max - min;
				hsb.b = max;
				if (max != 0) {
					
				}
				hsb.s = max != 0 ? 255 * delta / max : 0;
				if (hsb.s != 0) {
					if (rgb.r == max) {
						hsb.h = (rgb.g - rgb.b) / delta;
					} else if (rgb.g == max) {
						hsb.h = 2 + (rgb.b - rgb.r) / delta;
					} else {
						hsb.h = 4 + (rgb.r - rgb.g) / delta;
					}
				} else {
					hsb.h = -1;
				}
				hsb.h *= 60;
				if (hsb.h < 0) {
					hsb.h += 360;
				}
				hsb.s *= 100/255;
				hsb.b *= 100/255;
				return hsb;
			},
			HSBToRGB = function (hsb) {
				var rgb = {};
				var h = Math.round(hsb.h);
				var s = Math.round(hsb.s*255/100);
				var v = Math.round(hsb.b*255/100);
				if(s == 0) {
					rgb.r = rgb.g = rgb.b = v;
				} else {
					var t1 = v;
					var t2 = (255-s)*v/255;
					var t3 = (t1-t2)*(h%60)/60;
					if(h==360) h = 0;
					if(h<60) {rgb.r=t1;	rgb.b=t2; rgb.g=t2+t3}
					else if(h<120) {rgb.g=t1; rgb.b=t2;	rgb.r=t1-t3}
					else if(h<180) {rgb.g=t1; rgb.r=t2;	rgb.b=t2+t3}
					else if(h<240) {rgb.b=t1; rgb.r=t2;	rgb.g=t1-t3}
					else if(h<300) {rgb.b=t1; rgb.g=t2;	rgb.r=t2+t3}
					else if(h<360) {rgb.r=t1; rgb.g=t2;	rgb.b=t1-t3}
					else {rgb.r=0; rgb.g=0;	rgb.b=0}
				}
				return {r:Math.round(rgb.r), g:Math.round(rgb.g), b:Math.round(rgb.b)};
			},
			RGBToHex = function (rgb) {
				var hex = [
					rgb.r.toString(16),
					rgb.g.toString(16),
					rgb.b.toString(16)
				];
				$.each(hex, function (nr, val) {
					if (val.length == 1) {
						hex[nr] = '0' + val;
					}
				});
				return hex.join('');
			},
			HSBToHex = function (hsb) {
				return RGBToHex(HSBToRGB(hsb));
			},
			restoreOriginal = function () {
				var cal = $(this).parent();
				var col = cal.data('colorpicker').origColor;
				cal.data('colorpicker').color = col;
				fillRGBFields(col, cal.get(0));
				fillHexFields(col, cal.get(0));
				fillHSBFields(col, cal.get(0));
				setSelector(col, cal.get(0));
				setHue(col, cal.get(0));
				setNewColor(col, cal.get(0));
			};
		return {
			init: function (opt) {
				opt = $.extend({}, defaults, opt||{});
				if (typeof opt.color == 'string') {
					opt.color = HexToHSB(opt.color);
				} else if (opt.color.r != undefined && opt.color.g != undefined && opt.color.b != undefined) {
					opt.color = RGBToHSB(opt.color);
				} else if (opt.color.h != undefined && opt.color.s != undefined && opt.color.b != undefined) {
					opt.color = fixHSB(opt.color);
				} else {
					return this;
				}
				return this.each(function () {
					if (!$(this).data('colorpickerId')) {
						var options = $.extend({}, opt);
						options.origColor = opt.color;
						var id = 'collorpicker_' + parseInt(Math.random() * 1000);
						$(this).data('colorpickerId', id);
						var cal = $(tpl).attr('id', id);
						if (options.flat) {
							cal.appendTo(this).show();
						} else {
							cal.appendTo(document.body);
						}
						options.fields = cal
											.find('input')
												.bind('keyup', keyDown)
												.bind('change', change)
												.bind('blur', blur)
												.bind('focus', focus);
						cal
							.find('span').bind('mousedown', downIncrement).end()
							.find('>div.colorpicker_current_color').bind('click', restoreOriginal);
						options.selector = cal.find('div.colorpicker_color').bind('mousedown', downSelector);
						options.selectorIndic = options.selector.find('div div');
						options.el = this;
						options.hue = cal.find('div.colorpicker_hue div');
						cal.find('div.colorpicker_hue').bind('mousedown', downHue);
						options.newColor = cal.find('div.colorpicker_new_color');
						options.currentColor = cal.find('div.colorpicker_current_color');
						cal.data('colorpicker', options);
						cal.find('div.colorpicker_submit')
							.bind('mouseenter', enterSubmit)
							.bind('mouseleave', leaveSubmit)
							.bind('click', clickSubmit);
						fillRGBFields(options.color, cal.get(0));
						fillHSBFields(options.color, cal.get(0));
						fillHexFields(options.color, cal.get(0));
						setHue(options.color, cal.get(0));
						setSelector(options.color, cal.get(0));
						setCurrentColor(options.color, cal.get(0));
						setNewColor(options.color, cal.get(0));
						if (options.flat) {
							cal.css({
								position: 'relative',
								display: 'block'
							});
						} else {
							$(this).bind(options.eventName, show);
						}
					}
				});
			},
			showPicker: function() {
				return this.each( function () {
					if ($(this).data('colorpickerId')) {
						show.apply(this);
					}
				});
			},
			hidePicker: function() {
				return this.each( function () {
					if ($(this).data('colorpickerId')) {
						$('#' + $(this).data('colorpickerId')).hide();
					}
				});
			},
			setColor: function(col) {
				if (typeof col == 'string') {
					col = HexToHSB(col);
				} else if (col.r != undefined && col.g != undefined && col.b != undefined) {
					col = RGBToHSB(col);
				} else if (col.h != undefined && col.s != undefined && col.b != undefined) {
					col = fixHSB(col);
				} else {
					return this;
				}
				return this.each(function(){
					if ($(this).data('colorpickerId')) {
						var cal = $('#' + $(this).data('colorpickerId'));
						cal.data('colorpicker').color = col;
						cal.data('colorpicker').origColor = col;
						fillRGBFields(col, cal.get(0));
						fillHSBFields(col, cal.get(0));
						fillHexFields(col, cal.get(0));
						setHue(col, cal.get(0));
						setSelector(col, cal.get(0));
						setCurrentColor(col, cal.get(0));
						setNewColor(col, cal.get(0));
					}
				});
			}
		};
	}();
	$.fn.extend({
		ColorPicker: ColorPicker.init,
		ColorPickerHide: ColorPicker.hidePicker,
		ColorPickerShow: ColorPicker.showPicker,
		ColorPickerSetColor: ColorPicker.setColor
	});
})(jQuery)
jQuery.fn.SkinsPanel = function (settings)
{
	var editor_mode, theme_mode, env_mode, working_mode;

	var edited_theme = settings.edited_theme;
	var active_theme = settings.active_theme;
	var themes_indexed = {};
	
	var $panel = $(this);
	$panel.bind('env_mode', function (event, mode) {
		env_mode = mode;
		syncMode();
	});
	
	loadSkins();
	
	try {
		$panel.find('.mode').buttonset();
	}
	catch(e) {}
	$panel.find('.btn.mode').click(function () {
		var $this = $(this);
		if (! $this.hasClass('disabled')) {
			if ($this.hasClass('checked') && ! confirmLoosingChanges(settings.labels.alert_not_saved_theme_nav)) {
				return;
			}
			setEditorMode($this.hasClass('checked') ? 'navigation' : 'editing');
		}
	});
	
	$panel.find('.toolbar .follow').click(function () {
		$(this).toggleClass('stop');
	});
	$panel.find('.add_skin input[type=text]')
	.bind('keypress', function (event) {
		var $this = $(this);
		if (event.charCode > 0 && String.fromCharCode(event.charCode).match(/[^A-Za-z0-9\.\-_]/)) {
			event.preventDefault();
			return false;
		}
		return true;
	})
	.bind('change _click blur keyup _mouseup', function () {
		var $this = $(this);
		$this.val($this.val().replace(/[^A-Za-z0-9\.\-_]/g, ''));
		$this.focus();
	});
	$panel.find('.add_skin .submit').click(addSkin);
	
	$panel.find('.status_what_to_do .close a').click(function () {
		$panel.find('.status_what_to_do').fadeOut(100, function () {
			$panel.find('.accordion').trigger('resize');
		});
	});
	
	$panel.find('.themes_panel .make_theme_active').click(function () {
		$panel.SkinsPanelStatus('.working');
		$.ajax({ 
			url: 'edit_request.php', 
			data: { request: 'set_active_theme', name: edited_theme },
			cache: false,
			dataType: 'text',
			success: function (data) {
				if (data == 'ok') {
					$panel.SkinsPanelStatus('.set_active_ok');
					active_theme = edited_theme;
					$panel.find('.existing ul li').removeClass('active');
					$panel.find('.existing ul li[skin_name=' + active_theme + ']').addClass('active');
				}
				else {
					$panel.SkinsPanelStatus('.set_active_fail', settings.labels.set_active_fail_wtd);
				}
			},
			error: function () {
				$panel.SkinsPanelStatus('.set_active_fail', settings.labels.set_active_fail_wtd);
			}
		});
	});
	
	$panel.find('.bookmarks div.button_go').click(function () {
		var url = $panel.find('.bookmarks .url input[type=text]').val();
		var frame = window.parent.document.getElementById('storefront');
		if (frame && url) {
			frame.contentWindow.location.href = url;
		}
	});
	
	var $accordion = $panel.find('.accordion');
	$accordion.accordion({ fillSpace: true, header: 'h2' }).bind('resize', function () {
		setAccordionHeight();
	});
	$(window).resize(function () {
		setAccordionHeight(); 
	});

	$panel.find('.editor_panel_loading').hide();
	setAccordionHeight();
	setEditorMode('editing');



	function loadSkins()
	{
		$panel.find('.loading').show();
		$.ajax({
			url: 'edit_request.php',
			data: { request: 'get_theme_list' },
			complete: function () { $panel.find('.loading').hide(); },
			cache: false,
			dataType: 'json',
			success: function (data) {
				if (data && data != null) {
					themes_indexed = data;
					showThemes();
				}
			},
			error: function (XMLHttpRequest, textStatus) {
				$panel.find('.loading_failed').show();
			}
		});
	}
	
	function showThemes()
	{
		var c = 0;
		for (var k in themes_indexed) c++;
		if (c > 0) {
			$panel.find('.no_skins').hide();
			var $list = $panel.find('.existing ul');
			$list.find('li:not(.default)').remove();
			var edited_theme_exists = false;
			for (var k in themes_indexed) {
				var skin = themes_indexed[k];
				var $item = $($panel.find('.theme_item_template').html().replace('%skin_name%', skin.name));
				$item
					.attr('skin_name', skin.name)
					.appendTo($list)
					.find('.remove').click(function (event) {
						event.stopPropagation();
						$(this).parent().find('.error,.message,.what_to_do').hide().end().find('.confirm_remove').toggle();
					}).end()
					.find('.yes').click(function (event) {
						$(this).parent().hide();
						removeSkin($(this).parents('li'));
					}).end()
					.find('.no').click(function (event) {
						$(this).parent().hide();
					}).end()
					.find('.confirm_remove').click(function (event) {
						event.stopPropagation();
					}).end();
				$item.addClass(skin.editable ? 'editable' : 'ro');
			}
			
			$panel.find('.existing ul li[skin_name=' + active_theme + ']').addClass('active');
			setEditedTheme(edited_theme);
			$panel.find('.existing ul li').click(function () {
				if (! confirmLoosingChanges(settings.labels.alert_not_saved_theme)) {
					return false;
				}
				var $this = $(this);
				var name = $this.attr('skin_name');
				$.ajax({ url: 'edit_request.php', data: { request: 'set_edited_theme', name: name } });
				var theme = setEditedTheme(name);
				if (window.parent && window.parent.editor) {
					window.parent.editor.setTheme(theme);
				}
			});
			$panel.find('.existing ul').find('li:first').addClass('ui-corner-top').end().find('li:last').addClass('ui-corner-bottom');
			$panel.find('.make_theme_active').show();
		}
		else {
			$panel.find('.make_theme_active').hide();
			$panel.find('.no_skins').show();
		}
	}
	
	function addSkin()
	{
		var $name = $('.add_skin input');
		if ($name.val() == '') {
			$name.focus();
			return;
		}
		$panel.SkinsPanelStatus();
		$panel.find('.add_skin .what_to_do, .add_skin .message').hide();
		$panel.find('.add_skin .submit').hide();
		$panel.find('.add_skin .adding').show();
		$.ajax({
			url: 'edit_request.php', 
			data: { request: 'add_theme', name: $name.val() },
			complete: function () {
				$panel.find('.add_skin .adding').hide();
				$panel.find('.add_skin .submit').show();
			},
			cache: false,
			dataType: 'json',
			success: function (data) {
				$panel.find('.add_skin .message')
					.addClass(data.result ? 'success' : 'error')
					.removeClass(data.result ? 'error' : 'success')
					.html(data.message)
					.show();
				if (data.result) {
					loadSkins();
					$('.add_skin input').val('');
				}
				if (data.what_to_do) {
					$panel.find('.add_skin .what_to_do').html(data.what_to_do).show();
				}
			},
			error: function () {
				$panel.find('.add_skin .what_to_do').find('.text').html(data.what_to_do).end().show();
			}
		});
	}

	function setEditedTheme(new_theme)
	{
		edited_theme = themes_indexed[new_theme] ? new_theme : '';
		$panel.find('.existing ul li').removeClass('edited');
		$panel.find('.existing ul li[skin_name='+edited_theme+']').addClass('edited');
		if (edited_theme == '') {
			$panel.find('.read_only').hide();
			$panel.find('.default_chosen').show();
			setThemeMode('default');
		}
		else if (themes_indexed[edited_theme].editable) {
			$panel.find('.read_only').hide();
			$panel.find('.default_chosen').hide();
			setThemeMode('editable');
			$panel.find('.theme_editor .choose_element').show();
		}
		else {
			$panel.find('.default_chosen').hide();
			$panel.find('.read_only u.theme_file').text(themes_indexed[edited_theme].path);
			$panel.find('.read_only').show();
			setThemeMode('readonly');
		}
		return edited_theme == '' ? {} : themes_indexed[edited_theme];
	}
	
	function removeSkin($skin_li)
	{
		var $removing = $skin_li.find('.removing').show();
		
		$.ajax({
			url: 'edit_request.php',
			data: { request: 'remove_theme', name: $skin_li.attr('skin_name') },
			complete: function () { $removing.hide(); },
			cache: false,
			dataType: 'json',
			success: function (data) {
				if (data.result) {
					loadSkins();
				}
				else {
					$skin_li.find('.message').html(data.message).fadeIn(100);
					if (data.what_to_do) {
						$skin_li.find('.what_to_do').html(data.what_to_do).show();
					}
				}
			},
			error: function (XMLHttpRequest, textStatus) {
				$skin_li.find('.error').show();
			}
		});
	}

	function setEditorMode(mode)
	{
		if (editor_mode != mode) {
			editor_mode = mode;
			syncMode();
    		$panel.find('.toolbar .btn.mode')[editor_mode == 'editing' ? 'addClass' : 'removeClass']('checked');
		}
	}
	
	function setThemeMode(mode)
	{
		if (theme_mode != mode) {
			theme_mode = mode;
			syncMode();
		}
	}
	
	function syncMode()
	{
		typeof console != 'undefined' && console.debug && console.debug('syncMode(): editor mode: ', editor_mode, ', theme mode: ', theme_mode, ', environment mode:', env_mode);
		$panel.find('.toolbar .btn.mode')[theme_mode == 'editable' ? 'removeClass' : 'addClass']('disabled');
		applyMode(editor_mode == 'editing' && theme_mode == 'editable' && env_mode == 'ready' ? 'editing' : 'navigation');
	}
	
	function applyMode(mode)
	{
		typeof console != 'undefined' && console.debug && console.debug('css editor panel::applyMode():', mode, working_mode, env_mode);
		if (working_mode == 'editing') {
			if (working_mode != mode) {
				$panel.find('.navigation_mode').hide()
				$panel.find('h2.customization, h2.properties').show();
	    		$panel.find('.accordion').show().trigger('resize');
	    		if (window.parent && window.parent.editor) {
	    			window.parent.editor.setEditorMode('editing');
	    		}
			}
		}
		else {
			var msg;
			if (env_mode == 'https_error') {
				msg = '.https';
			}
			else if (theme_mode == 'editable') {
				msg = '.editable';
			}
			else {
				msg = '.readonly';
			}
			typeof console != 'undefined' && console.debug && console.debug('show message:', msg);
			$panel.find('.navigation_mode').show().children().hide().filter(msg).show();
			$panel.find('h2.customization, h2.properties').hide();
    		$panel.find('.accordion').show().trigger('resize').accordion('activate', 0);
    		if (working_mode != mode && window.parent && window.parent.editor) {
    			window.parent.editor.setEditorMode('navigation');
    		}
		}
		working_mode = mode;
	}
	
	function setAccordionHeight()
	{
		var static_panels_height = 5;
		$panel.find('.editor_panel > *:not(.accordionResizer):visible').each(function () {
			static_panels_height += $(this).outerHeight(true);
		});
		var new_height = ($('html').height() - static_panels_height)+'px';
		typeof console != 'undefined' && console.debug && console.debug('New accordion height:', new_height, $panel.find('.accordionResizer').css('height')); 
		if (1 || $panel.find('.accordionResizer').css('height') != new_height) {
			$panel.find('.accordionResizer').css('height', new_height);
			$accordion.accordion('resize');
		}
	}
	
	function confirmLoosingChanges(message)
	{
		if (window.parent && window.parent.editor) {
			if (window.parent.editor.hasUnsavedChanges()) {
				return window.confirm(message);
			}
		}
		return true;
	}
	
};

jQuery.fn.SkinsPanelStatus = function (message, what_to_do, show_save_button)
{
	var $panel = $(this);
	var $status = $panel.find('.status');
	var $wtd = $status.find('.status_what_to_do');
	var $save = $wtd.find('.save');
	
	if (message) {
		$status.children(':not('+message+')').hide();
		$status.children(message).show();
    	if (what_to_do) {
    		$wtd.find('.text').html(what_to_do);
    		$save [show_save_button ? 'show' : 'hide'] ();
    		$wtd.show();
    	}
    	else {
    		$wtd.hide();
    	}
		$status.show();
	}
	else {
		if ($status.is(':visible')) {
			$status.hide();
		}
	}
	
	$panel.find('.accordion').trigger('resize');
};

//function initRuleExtensions($)
//{

function createProperties(set, rule, props_names)
{
	var opts_border_width = ['thin', 'medium', 'thick'];
	var opts_font_size = ['xx-small', 'x-small', 'small', 'medium', 'large', 'x-large', 'xx-large', 'larger', 'smaller'];
	var opts_line_height = ['normal'];

	for (var i = 0; i < props_names.length; i++) {
		switch(props_names[i])
		{
		case 'color': set.CSSEditorProperty('color', rule, 'Color').CSSEditorColorPicker('color', rule); break;
		case 'fontFamily': set.CSSEditorProperty('fontFamily', rule, 'Font').CSSEditorFontPicker('fontFamily', rule); break;
		case 'fontSize': set.CSSEditorProperty('fontSize', rule, 'Size').CSSEditorLength('fontSize', rule, true).CSSEditorAddOptions(opts_font_size); break;
		case 'lineHeight': set.CSSEditorProperty('lineHeight', rule, 'Line').CSSEditorLength('lineHeight', rule, true).CSSEditorAddOptions(opts_line_height); break;
		case 'fontWeight': set.CSSEditorProperty('fontWeight', rule, 'Bold').CSSEditorFontWeight('fontWeight', rule); break;
		case 'fontStyle': set.CSSEditorProperty('fontStyle', rule, 'Italic').CSSEditorFontStyle('fontStyle', rule); break;
		case 'textDecoration': set.CSSEditorProperty('textDecoration', rule, 'Deco').CSSEditorFontDeco('textDecoration', rule); break;
		case 'textAlign': set.CSSEditorProperty('textAlign', rule, 'Align').CSSEditorTextAlign('textAlign', rule); break;
		case 'textIndent': set.CSSEditorProperty('textIndent', rule, 'Indent').CSSEditorLength('textIndent', rule); break;
		
		case 'backgroundColor': set.CSSEditorProperty('backgroundColor', rule, 'Color').CSSEditorTransparency('backgroundColor', rule).CSSEditorColorPicker('backgroundColor', rule); break;
		case 'backgroundImage': set.CSSEditorProperty('backgroundImage', rule, 'Image').CSSEditorBackgroundImage('backgroundImage', rule); break;
		case 'backgroundRepeat': set.CSSEditorProperty('backgroundRepeat', rule, 'Repeat').CSSEditorBackgroundRepeat('backgroundRepeat', rule); break;
		case 'backgroundAttachment': set.CSSEditorProperty('backgroundAttachment', rule, 'Attachment').CSSEditorBackgroundAttachment('backgroundAttachment', rule); break;
		case 'backgroundPosition': set.CSSEditorProperty('backgroundPosition', rule, 'Position').CSSEditorBackgroundPosition('backgroundPosition', rule).CSSEditorAddOptions(['left','center','right'], 'x_axis').CSSEditorAddOptions(['top','center','bottom'], 'y_axis'); break;
		
		case 'borderWidth': 
		case 'borderTopWidth': 
		case 'borderRightWidth': 
		case 'borderBottomWidth': 
		case 'borderLeftWidth': set.CSSEditorProperty(props_names[i], rule, 'Width').CSSEditorLength(props_names[i], rule).CSSEditorAddOptions(opts_border_width); break;
		
		case 'borderColor': 
		case 'borderTopColor': 
		case 'borderRightColor':
		case 'borderBottomColor':
		case 'borderLeftColor': set.CSSEditorProperty(props_names[i], rule, 'Color').CSSEditorTransparency(props_names[i], rule).CSSEditorColorPicker(props_names[i], rule); break;
		
		case 'borderStyle':
		case 'borderTopStyle':
		case 'borderRightStyle':
		case 'borderBottomStyle':
		case 'borderLeftStyle': set.CSSEditorProperty(props_names[i], rule, 'Style').CSSEditorBorderStyle(props_names[i], rule); break;
		
		case 'width': set.CSSEditorProperty('width', rule, 'Width').CSSEditorLength('width', rule, true).CSSEditorAddOptions({auto: 'auto'}); break;
		case 'height': set.CSSEditorProperty('height', rule, 'Height').CSSEditorLength('height', rule, true); break;
		
		case 'left': set.CSSEditorProperty('left', rule, 'Left').CSSEditorLength('left', rule, true, true); break;
		case 'right': set.CSSEditorProperty('right', rule, 'Right').CSSEditorLength('right', rule, true, true); break;
		case 'top': set.CSSEditorProperty('top', rule, 'Top').CSSEditorLength('top', rule, true, true); break;
		case 'bottom': set.CSSEditorProperty('bottom', rule, 'Bottom').CSSEditorLength('bottom', rule, true, true); break;
		
		case 'paddingTop': set.CSSEditorProperty('paddingTop', rule, 'Top').CSSEditorLength('paddingTop', rule, true); break;
		case 'paddingRight': set.CSSEditorProperty('paddingRight', rule, 'Right').CSSEditorLength('paddingRight', rule, true); break;
		case 'paddingBottom': set.CSSEditorProperty('paddingBottom', rule, 'Bottom').CSSEditorLength('paddingBottom', rule, true); break;
		case 'paddingLeft': set.CSSEditorProperty('paddingLeft', rule, 'Left').CSSEditorLength('paddingLeft', rule, true); break;
		
		case 'marginTop': set.CSSEditorProperty('marginTop', rule, 'Top').CSSEditorLength('marginTop', rule, true, true).CSSEditorAddOptions({auto: 'auto'}); break;
		case 'marginRight': set.CSSEditorProperty('marginRight', rule, 'Right').CSSEditorLength('marginRight', rule, true, true).CSSEditorAddOptions({auto: 'auto'}); break;
		case 'marginBottom': set.CSSEditorProperty('marginBottom', rule, 'Bottom').CSSEditorLength('marginBottom', rule, true, true).CSSEditorAddOptions({auto: 'auto'}); break;
		case 'marginLeft': set.CSSEditorProperty('marginLeft', rule, 'Left').CSSEditorLength('marginLeft', rule, true, true).CSSEditorAddOptions({auto: 'auto'}); break;
		}
	}
}

var editors = 
	[
	 {	setName: 'Font', setCode: 'text', properties: [ 'fontSize', 'fontFamily', 'fontWeight', 'fontStyle', 'textDecoration', 'color' ] },
	 {  setName: 'Paragraph', setCode: 'para', properties: [ 'textAlign', 'textIndent', 'lineHeight' ] },
//	 {	setName: 'Align', setCode: 'text-align', properties: [ 'textAlign' ] },
//	 {	setName: 'Line Height', setCode: 'line-height', properties: [ 'lineHeight' ] },
	 
	 {	setName: 'Background', setCode: 'background', properties: [ 'backgroundColor', 'backgroundImage', 'backgroundRepeat', 'backgroundAttachment', 'backgroundPosition' ] },
	 
	 {	setName: 'Top Border', setCode: 'border-top', alias: 'border', properties: [ 'borderTopColor', 'borderTopWidth', 'borderTopStyle' ] },
	 {	setName: 'Right Border', setCode: 'border-right', alias: 'border', properties: [ 'borderRightColor', 'borderRightWidth', 'borderRightStyle' ] },
	 {	setName: 'Bottom Border', setCode: 'border-bottom', alias: 'border', properties: [ 'borderBottomColor', 'borderBottomWidth', 'borderBottomStyle' ] },
	 {	setName: 'Left Border', setCode: 'border-left', alias: 'border', properties: [ 'borderLeftColor', 'borderLeftWidth', 'borderLeftStyle' ] },
	 
	 {	setName: 'Padding', setCode: 'padding', properties: [ 'paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft' ] },
	 {	setName: 'Margin', setCode: 'margin', properties: [ 'marginTop', 'marginRight', 'marginBottom', 'marginLeft' ] },
	 
	 {	setName: 'Size', setCode: 'size', properties: [ 'width', 'height' ] },
	 {	setName: 'Position', setCode: 'position', properties: [ 'left', 'top', 'right', 'bottom' ] }
	];

function createEditors(rule, properties)
{
	for (var i = 0; i < editors.length; i++) {
		var ed = editors[i];
		if (true || rule.isEditable(ed.setCode)) {
			if (ed.properties) {
				var set = properties.CSSEditorPropertySet(ed.setName);
				createProperties(set, rule, ed.properties);
			}
			if (ed.sets) {
				for (var j = 0; j < editors.length; j++) {
					var _ed = editors[j];
					if (jQuery.inArray(_ed.setCode, ed.sets) >= 0) {
						if (_ed.properties) {
							var set = properties.CSSEditorPropertySet(_ed.setName);
							createProperties(set, rule, _ed.properties);
						}
					}
					
				}
			}
		}
	}
}

$.fn.CSSEditorSingleRule = function(rule)
{
	createEditors(rule, $(this));
	return this;
};

$.fn.CSSEditorRuleCommon = function(rule, open)
{
	var created = false;
	var name = rule.getHumanName();
	var widget = $('<div class="rule"></div>').prependTo(this);
	var header = $('<div class="header"></div>').appendTo(widget);
	var icon = $('<span class="plus-minus">Collapse</span>').appendTo(header);
	var name = $('<a href="javascript:void(0)">'+name+'</a>').appendTo(header);
	var properties = $('<div class="properties"></div>').appendTo(widget);

	header.click(function () {
		if (properties.is(':hidden') && ! created) {
			created = true;
			createEditors(rule, properties);
		}
		properties.slideToggle(100);
		icon.toggleClass('minus');
	});
	
	if (open) {
		header.click();
		properties.click();
	}
	
	return this;
};

$.fn.CSSEditorRuleSpecial = function(special_editor)
{
	for (var i = 0; i < special_editor.sets.length; i++) {
		var widget = $('<div class="special"></div>').appendTo(this);
		var header = $('<div class="header"></div>').appendTo(widget).append(special_editor.sets[i].name);
		var properties = $('<div class="properties"></div>').appendTo(widget);
	 	for (var j = 0; j < special_editor.sets[i].rules.length; j++) {
	 		var rule = special_editor.sets[i].rules[j];
		 	createEditors(rule.rule, properties);
	 	}
		properties.slideDown(100);
	}
	
};

//}
//function initPropertyExtensions($)
//{

function getCSSEdNextId ()
{
	if (! window.css_id) {
		window.css_id = 1;
	}
	return 'cssed_' + window.css_id++;
};

function getUnitsOptions (units)
{
	var names = {
			'px': 'pixels',
			'in': 'inches',
			'pc': 'picas',
			'pt': 'points'
	};
	var options = '';
	for (var i = 0; i < units.length; i++) {
		var name = names[units[i]] ? names[units[i]] : units[i];
		options += '<option value="'+units[i]+'">'+name+'</option>';
	}
	return options;
}

function parseCSSColor (css_color)
{
	var r = css_color.match(/rgb\s*\(\s*(\d+),\s*(\d+),\s*(\d+)\s*\)/);
	if (r) {
		return {r:r[1], g:r[2], b:r[3]};
	}
	r = css_color.match(/\#(......)$/);
	if (r) {
		return r[1];
	}
	r = css_color.match(/\#(.)(.)(.)$/);
	if (r) {
		return r[1]+r[1]+r[2]+r[2]+r[3]+r[3];
	}
	return '';
};

/*
 * Titled Set of Properties
 */
$.fn.CSSEditorPropertySet = function (title) 
{
	var set = $('<div class="property_editor"><div class="title">'+title+'</div></div>').appendTo(this);
	return set;
};

/*
 * Common Empty Property Editor 
 */
$.fn.CSSEditorProperty = function (prop_name, rule, title)
{
	var save;
	var ed = $('<div class="prop_ed"></div>').appendTo(this);
	var label = $('<div class="prop_lbl">'+title+'</div>').appendTo(ed);
	var reset = $('<div class="reset"></div>').appendTo(ed);
	var value = $('<div class="prop_val"></div>').appendTo(ed);
	var val_h = $('<div class="prop_val_h">'+rule.getPropertyDefaultValue(prop_name)+'&nbsp;</div>').appendTo(ed);
	ed.append('<div class="clear"></div>');
	ed.data('value', value);
	
	function hideValueVisual() {
		value.hide();
		val_h.show();
		reset.hide();
		ed.addClass('prop_btn');
		ed.one('click', function(event) {
			event.stopPropagation();
			showValue();
		});
	}
	function hideValue() {
		save = rule.getPropertyActualValue(prop_name);
		ed.trigger('hide');
		rule.setActualPropertyValue(prop_name, '', ed);
		hideValueVisual();
	}
	function showValueVisual() {
		ed.removeClass('prop_btn');
		ed.unbind('click');
		val_h.hide();
		value.show();
		reset.show();
	}
	function showValue() {
		if (save === undefined) {
			var v = rule.getPropertyDefaultValue(prop_name);
			if (v != '') {
				rule.setActualPropertyValue(prop_name, v, ed);
			}
			else {
				ed.trigger('default');
			}
		}
		else {
			rule.setActualPropertyValue(prop_name, save, ed);
		}
		ed.trigger('show');
		showValueVisual();
	}
	
	reset.click(function(event) {
		event.stopPropagation();
		hideValue();
	});
	
	ed.bind('syncdisplay', function () {
		rule.getPropertyActualValue(prop_name) ? showValueVisual() : hideValueVisual();
	})
	.trigger('syncdisplay');
	
	
	return ed;
};

/*
 * Add Transparency Option into Property Editor
 */
$.fn.CSSEditorTransparency = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');

	var save;
	var radio_name = getCSSEdNextId();
	
	var choose_color = $('<input type="radio" name="'+radio_name+'" id="'+radio_name+'_1" class="float_left input_radio choose_color" />').appendTo(value);
	var color_value = $('<label class="float_left color_value" for="'+radio_name+'_1"></label>').appendTo(value);
	var choose_trans = $('<input type="radio" name="'+radio_name+'" id="'+radio_name+'_2" class="float_left input_radio choose_trans" />').appendTo(value);
	value.append('<label class="float_left color_value" for="'+radio_name+'_2">Transparent</label>');
	this.data('value', color_value);

	function isTransparent() {
		var color = rule.getPropertyActualValue(prop_name);
		return color == 'transparent';
	}
	function fetchPropertyValue() {
		(isTransparent() ? choose_trans : choose_color).attr('checked', true);
	}
	
	function setTransparent() {
		save = rule.getPropertyActualValue(prop_name);
		rule.setActualPropertyValue(prop_name, 'transparent', _this);
		_this.trigger('transparent');
	}
	function setOpaque() {
		if (save === undefined) {
			_this.trigger('default');
		}
		else {
			rule.setActualPropertyValue(prop_name, save, _this);
		}
		_this.trigger('color');
	}
	choose_color.bind('change keyup click', function (event) {
		event.stopPropagation();
		if (isTransparent()) {
			setOpaque();
		}
	});
	choose_trans.bind('change keyup click', function (event) {
		event.stopPropagation();
		if (! isTransparent()) {
			setTransparent();
		}
	});
	
	this.bind('picker', function (event) {
		if (! choose_color.attr('checked')) {
			choose_color.attr('checked', true);
			setOpaque();
		}
	});
	this.bind('show restore', function (event) {
		fetchPropertyValue();
	});
	
	fetchPropertyValue();
	
	return this;
};

/*
 * Common Color Picker 
 */
$.fn.CSSEditorColorPicker = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');
	var preview = $('<div class="preview_color"></div>').appendTo(value);
	var preview_c = $('<div></div>').appendTo(preview);

	var picker = $('<div class="color_picker"></div>').appendTo(this);
	
	function fetchPropertyValue() {
		var clr = rule.getPropertyActualValue(prop_name);
		preview_c.css('backgroundColor', clr);
		picker.ColorPickerSetColor(parseCSSColor(clr));
	}
	
	picker.ColorPicker({			
		flat: true,
		color: '#00ff00',
		onChange: function(hsb, hex, rgb) {
			rule.setActualPropertyValue(prop_name, '#' + hex, _this);
			preview_c.css('backgroundColor', '#' + hex);
			_this.trigger('change_prop');
		},
		livePreview: false
	});
	preview.bind('click', function() {
		if (! picker.is(':visible')) {
			_this.trigger('picker');
		}
		picker.slideToggle(100);
	});
	
	this.bind('hide transparent', function (event) {
		picker.slideUp(0);
	});
	this.bind('default', function (event) {
		clr = prop_name == 'backgroundColor' ? '#fff' : '#000';
		rule.setActualPropertyValue(prop_name, clr, _this);
	});
	this.bind('show restore transparent color', function(event){
		fetchPropertyValue();
	});
	
	fetchPropertyValue();
	
	return this;
};

$.fn.CSSEditorFontPicker = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');

	var select = $(
		'<select class="font_family">' +
			'<option value="Arial,Sans-serif" style="font-family:Arial,Sans-serif">Arial</option>' +
			'<option value="Verdana,Sans-serif" style="font-family:Verdana,Sans-serif">Verdana</option>' +
			'<option value="Tahoma,Verdana,Sans-serif" style="font-family:Tahoma,Verdana,Sans-serif">Tahoma</option>' +
			'<option value="\'Arial Black\',Sans-serif" style="font-family:\'Arial Black\',Sans-serif">Arial Black</option>' +
			'<option value="\'Times New Roman\',serif" style="font-family:\'Times New Roman\',serif">Times New Roman</option>' +
			'<option value="Georgia,serif" style="font-family:Georgia,serif">Georgia</option>' +
			'<option value="\'Courier New\',monospace" style="font-family:\'Courier New\',monospace">Courier New</option>' +
			'<option value="\'Comic Sans MS\',Sans-serif" style="font-family:\'Comic Sans MS\',Sans-serif">Comic Sans MS</option>' +
		'</select>').appendTo(value);
	
	function fetchPropertyValue() {
		select.val(rule.getPropertyActualValue(prop_name));
	}
	
	select.bind('change keyup', function (event) {
		rule.setActualPropertyValue(prop_name, select.val(), _this);
		_this.trigger('change_prop');
		event.stopPropagation();
	});
	
	this.bind('default', function(event){
		rule.setActualPropertyValue(prop_name, 'Arial,Sans-serif', _this);
	});
	this.bind('show restore', function(event){
		fetchPropertyValue();
	});

	fetchPropertyValue();
	
	return this;
};

/*
 * Font Wheight Checkbox (Normal / Bold)
 */
$.fn.CSSEditorFontWeight = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');
	
	var weight_value = $('<input type="checkbox" class="input_cb font_weight" />').appendTo(value);
	
	function fetchPropertyValue() {
		weight_value.attr('checked', rule.getPropertyActualValue(prop_name) == 'bold');
	}
	function setPropertyValue() {
		rule.setActualPropertyValue(prop_name, weight_value.is(':checked') ? 'bold' : 'normal', _this);
	}
	
	weight_value.bind('change keyup click', function (event) {
		setPropertyValue();
		_this.trigger('change_prop');
		event.stopPropagation();
	});
	
	this.bind('default', function(event){
		rule.setActualPropertyValue(prop_name, 'normal', _this);
	});
	this.bind('show restore', function(event){
		fetchPropertyValue();
	});
	
	fetchPropertyValue();
	
	return this;
};

/*
 * Font Style Checkbox (Normal / Italic)
 */
$.fn.CSSEditorFontStyle = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');
	
	var style_value = $('<input type="checkbox" class="input_cb font_style" />').appendTo(value);
	
	function fetchPropertyValue() {
		style_value.attr('checked', rule.getPropertyActualValue(prop_name) == 'italic');
	}
	function setPropertyValue() {
		rule.setActualPropertyValue(prop_name, style_value.is(':checked') ? 'italic' : 'normal', _this);
	}
	
	style_value.bind('change keyup click', function (event) {
		setPropertyValue();
		_this.trigger('change_prop');
		event.stopPropagation();
	});
	
	this.bind('default', function(event){
		rule.setActualPropertyValue(prop_name, 'normal', _this);
	});
	this.bind('show restore', function(event){
		fetchPropertyValue();
	});
	
	fetchPropertyValue();
	
	return this;
};

/*
 * Four Font Decoration Checkboxes (Underline, Overline, Strike-through, Blink)
 */
$.fn.CSSEditorFontDeco = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');
	var id;
	
	id = getCSSEdNextId();
	var under_value = $('<input type="checkbox" class="input_cb text_underline" id="'+id+'" />').appendTo(value);
	value.append(' <label for="'+id+'">Underline</label><br />');
	id = getCSSEdNextId();
	var over_value = $('<input type="checkbox" class="input_cb text_overline" id="'+id+'" />').appendTo(value);
	value.append(' <label for="'+id+'">Overline</label><br />');
	id = getCSSEdNextId();
	var throu_value = $('<input type="checkbox" class="input_cb text_linethrough" id="'+id+'" />').appendTo(value);
	value.append(' <label for="'+id+'">Line-through</label><br />');
	id = getCSSEdNextId();
	var blink_value = $('<input type="checkbox" class="input_cb text_blink" id="'+id+'" />').appendTo(value);
	value.append(' <label for="'+id+'">Blink</label>');
	var all_values = value.children('.input_cb');
	
	function fetchPropertyValue() {
		var v = rule.getPropertyActualValue(prop_name);
		under_value.attr('checked', v.indexOf('underline') >= 0);
		over_value.attr('checked', v.indexOf('overline') >= 0);
		throu_value.attr('checked', v.indexOf('line-through') >= 0);
		blink_value.attr('checked', v.indexOf('blink') >= 0);
	}
	function setPropertyValue() {
		rule.setActualPropertyValue(prop_name, 
				(under_value.is(':checked') ? 'underline ' : '') +
				(over_value.is(':checked') ? 'overline ' : '') +
				(throu_value.is(':checked') ? 'line-through ' : '') +
				(blink_value.is(':checked') ? 'blink ' : '') || 'none', _this);
	}
	
	all_values.bind('change keyup click', function (event) {
		setPropertyValue();
		_this.trigger('change_prop');
		event.stopPropagation();
	});
	
	this.bind('default', function(event){
		rule.setActualPropertyValue(prop_name, 'none', _this);
	});
	this.bind('show restore', function (event) {
		fetchPropertyValue();
	});
	
	fetchPropertyValue();
	
	return this;
};

/*
 * Horizontal Text Align
 */
$.fn.CSSEditorTextAlign = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');

	var select = $(
		'<select class="text_align">' +
			'<option value="left">left</option>' +
			'<option value="right">right</option>' +
			'<option value="center">center</option>' +
			'<option value="justify">justify</option>' +
		'</select>').appendTo(value);
	
	function fetchPropertyValue() {
		var align = rule.getPropertyActualValue(prop_name);
		select.val(align);
	}
	
	select.bind('change keyup', function (event) {
		rule.setActualPropertyValue(prop_name, select.val(), _this);
		_this.trigger('change_prop');
		event.stopPropagation();
	});
	
	this.bind('default', function (event) {
		rule.setActualPropertyValue(prop_name, 'left', _this);
	});
	this.bind('show restore', function (event) {
		fetchPropertyValue();
	});

	fetchPropertyValue();
	
	return this;
};

/*
 * Choose Existing or Upload New Background Image
 */
$.fn.CSSEditorBackgroundImage = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');

	var preview = $('<div class="image_name"></div>').appendTo(value);
	var upload = $('<a class="float_left btn24 btn_upload" title="Upload New Image" href="javascript:void(0)"></a>').appendTo(value);
	var choose = $('<a class="float_left btn24 btn_choose" title="Choose Uploaded Image" href="javascript:void(0)"></a>').appendTo(value);
	var none = $('<a class="float_left btn24 btn_none" title="No Image" href="javascript:void(0)"></a>').appendTo(value);
	
	function fetchPropertyValue() {
		var image = rule.getPropertyActualValue(prop_name);
		if (image == '') {
		}
		else
		if (image == 'none') {
			preview.html('- None -');
		}
		else {
			var r = image.match(/url\(['"]?\.\.\/images\/([^'"]+)['"]?\)/);
			if (r) {
				preview.html(r[1]);
			}
		}
	}
	function setPropertyValue() {
	}
	
	upload.click(function (event) {
		openURLinNewWindow('edit_upload_image.php', 'upload_bg_image');
		window.onImageUploaded = function(url) {
			rule.setActualPropertyValue(prop_name, 'url('+url+')', _this);
			_this.trigger('change_prop');
		};
	});
	choose.click(function (event) {
		openURLinNewWindow('edit_choose_image.php', 'choose_bg_image');
		window.onImageChoosed = function(url) {
			rule.setActualPropertyValue(prop_name, 'url('+url+')', _this);
			_this.trigger('change_prop');
		};
	});
	none.click(function (event) {
		rule.setActualPropertyValue(prop_name, 'none', _this);
		_this.trigger('change_prop');
	});
	this.bind('default', function (event) {
		rule.setActualPropertyValue(prop_name, 'none', _this);
	});
	this.bind('show restore change_prop', function (event) {
		fetchPropertyValue();
	});
	
	fetchPropertyValue();
	
	return this;
};

/*
 * Background Repeat Option
 */
$.fn.CSSEditorBackgroundRepeat = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');

	var select = $(
		'<select class="backgnd_repeat">' +
			'<option value="repeat">repeat</option>' +
			'<option value="repeat-x">repeat-x</option>' +
			'<option value="repeat-y">repeat-y</option>' +
			'<option value="no-repeat">no-repeat</option>' +
		'</select>').appendTo(value);
	
	function fetchPropertyValue() {
		var align = rule.getPropertyActualValue(prop_name);
		select.val(align);
	}
	
	select.bind('change keyup', function (event) {
		rule.setActualPropertyValue(prop_name, select.val(), _this);
		_this.trigger('change_prop');
		event.stopPropagation();
	});
	
	this.bind('default', function (event) {
		rule.setActualPropertyValue(prop_name, 'repeat', _this);
	});
	this.bind('show restore', function (event) {
		fetchPropertyValue();
	});

	fetchPropertyValue();
	
	return this;
};

/*
 * Scroll or Fixed Background Image
 */
$.fn.CSSEditorBackgroundAttachment = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');

	var select = $(
		'<select class="backgnd_attach">' +
			'<option value="scroll">scroll</option>' +
			'<option value="fixed">fixed</option>' +
		'</select>').appendTo(value);
	
	function fetchPropertyValue() {
		var align = rule.getPropertyActualValue(prop_name);
		select.val(align);
	}
	
	select.bind('change keyup', function (event) {
		rule.setActualPropertyValue(prop_name, select.val(), _this);
		_this.trigger('change_prop');
		event.stopPropagation();
	});
	
	this.bind('default', function (event) {
		rule.setActualPropertyValue(prop_name, 'scroll', _this);
	});
	this.bind('show restore', function (event) {
		fetchPropertyValue();
	});

	fetchPropertyValue();
	
	return this;
};

/*
 * Align of Background Image
 */
$.fn.CSSEditorBackgroundPosition = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');

	var x_axis = $('<div class="bkgnd_pos_axis x">').appendTo(value).CSSEditorWidgetLength(true, true);
	var y_axis = $('<div class="bkgnd_pos_axis y">').appendTo(value).CSSEditorWidgetLength(true, true);
	this.data('x_axis', x_axis.data('select'));
	this.data('y_axis', y_axis.data('select'));
	
	function fetchPropertyValue() {
		var pos = rule.getPropertyActualValue(prop_name);
		if (pos != '') {
			var p = pos.split(/\s+/, 2);
			x_axis.trigger('set', [p[0]]);
			y_axis.trigger('set', [p[1]]);
		}
	}
	function setPositionValue(index, value) {
		var pos = rule.getPropertyActualValue(prop_name);
		if (pos != '') {
			var p = pos.split(/\s+/, 2);
			p[index] = value;
			rule.setActualPropertyValue(prop_name, p.join(' '), _this);
		}
	}
	x_axis.bind('change_value', function (event, value) {
		setPositionValue(0, value);
		_this.trigger('change_prop');
	});
	y_axis.bind('change_value', function (event, value) {
		setPositionValue(1, value);
		_this.trigger('change_prop');
	});
	this.bind('default', function (event) {
		rule.setActualPropertyValue(prop_name, 'left top', _this);
	});
	this.bind('show restore', function (event) {
		fetchPropertyValue();
	});

	fetchPropertyValue();
	
	return this;
};

/*
 * Border Line Style
 */
$.fn.CSSEditorBorderStyle = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');

	var select = $(
		'<select class="border_style">' +
			'<option value="outset">none</option>' +
			'<option value="hidden">hidden</option>' +
			'<option value="solid">solid</option>' +
			'<option value="dotted">dotted</option>' +
			'<option value="dashed">dashed</option>' +
			'<option value="double">double</option>' +
			'<option value="groove">groove</option>' +
			'<option value="ridge">ridge</option>' +
			'<option value="inset">inset</option>' +
			'<option value="outset">outset</option>' +
		'</select>').appendTo(value);
	
	function fetchPropertyValue() {
		select.val(rule.getPropertyActualValue(prop_name));
	}
	
	select.bind('change keyup', function (event) {
		rule.setActualPropertyValue(prop_name, select.val(), _this);
		_this.trigger('change_prop');
		event.stopPropagation();
	});
	
	this.bind('default', function (event) {
		rule.setActualPropertyValue(prop_name, 'none', _this);
	});
	this.bind('show restore', function (event) {
		fetchPropertyValue();
	});

	fetchPropertyValue();
	
	return this;
};

/*
 * Common Length Input Widget
 */
$.fn.CSSEditorWidgetLength = function (percentage, negative)
{
	var _this = this;
	var units_str = 'em|ex|px|cm|mm|in|pc|pt';
	if (percentage) {
		units_str += '|%';
	}
	var units_array = units_str.split('|');
	var units_regexp = new RegExp('([+-]?\\d*[\\.,]?\\d+)\\s*('+units_str+')', 'i');
	
	var up_down = $('<input class="text_button up-down" readonly="readonly" />').appendTo(this);
	var input = $('<input class="input_text size_value" size="3" type="text" />').appendTo(this);
	var predefined = $('<input class="text_button predefined" readonly="readonly" />').appendTo(this);
	var dd_pt = CSSEditorCustomDropdown([5,6,7,8,9,10,11,12,14,16,18,20,24,28,32,36,40,48,72,120]).appendTo(this);
	var dd_px = CSSEditorCustomDropdown([0,1,2,3,4,5,6,7,8,9,10,11,12,14,16,18,20,22,24,26,28,30,35,40,45,50,75,100,150]).appendTo(this);
	var dd_mm = CSSEditorCustomDropdown([0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1,1.1,1.2,1.3,1.4,1.5,1.6,1.7,1.9,2,2.2,2.4,2.6,2.8,3,3.5,4,4.5,5,7.5,10,15]).appendTo(this);
	var dd_pr = CSSEditorCustomDropdown([0,1,2,3,4,5,10,15,20,25,30,40,50,60,70,80,90,100]).appendTo(this);
	var select = $('<select class="backgnd_position">' + getUnitsOptions(units_array) + '</select>').appendTo(this);
	this.data('select', select);
	
	function getValue() {
		if (input.is(':visible')) {
			var v = $.trim(input.val());
			return (v == '' ? '0' : v) + select.val();
		}
		else {
			return select.val();
		}
	}
	
	input.bind('keyup mouseup up_down predefined', function (event) {
		if (+ $(this).val() < 0 && ! negative) {
			$(this).val(0);
		}
		_this.trigger('change_value', [getValue()]);
	});
	up_down.bind('click', function (event) {
		var $btn = $(this);
		var offset = $btn.offset();
		var position = $btn.position();
		var val = + input.val();
		if (event.clientY - offset.top < 10) {
			val += getDelta(val, '+');
		}
		else {
			val -= getDelta(val, '-');
		}
		if (val < 0 && ! negative) {
			val = 0;
		}
		input.val(Math.round(val*100)/100);
		input.trigger('up_down');
	});
	
	predefined.bind('click', function (event) {
		if (event.target == window.css_editor_dropdown_button) {
			hideDropdown();
		}
		else {
    		switch (select.val()) {
    			case 'px': showDropdown(dd_px[0], predefined[0]); break;
    			case 'pt': showDropdown(dd_pt[0], predefined[0]); break;
    			case '%':  showDropdown(dd_pr[0], predefined[0]); break;
    			case 'ex':
    			case 'em':
    			case 'cm':
    			case 'mm':
    			case 'pc':
    			case 'in': showDropdown(dd_mm[0], predefined[0]);
    		}
		}
	});
	
	select.bind('click keyup change', function (event) {
		if (jQuery.inArray(select.val(), units_array) >= 0) {
			if (input.val() == '') {
				input.val(0);
			}
			up_down.show();
			input.show();
			predefined.show();
		}
		else {
			up_down.hide();
			input.hide();
			predefined.hide();
		}
		_this.trigger('change_value', [getValue()]);
	});
	this.bind('set', function (event, value) {
		var r = value.match(units_regexp);
		if (r) {
			input.val(r[1]);
			select.val(r[2]);
			up_down.show();
			input.show();
			predefined.show();
		}
		else {
			select.val(value);
			up_down.hide();
			input.hide();
			predefined.hide();
		}
	});
	
	function getDelta(val, dir)
	{
		if (val < 0) {
			dir = dir == '+' ? '-' : '+';
		}
		val = Math.abs(val);
		switch (select.val()) {
			case 'px':
			case 'pt':
			case 'mm':
			case '%':
        		if (val < (dir == '+' ? 20 : 21)) return 1;
        		if (val < (dir == '+' ? 40 : 41)) return 5;
        		if (val < (dir == '+' ? 100 : 101)) return 10;
        		return 25;

			case 'ex':
			case 'em':
			case 'cm':
			case 'in':
			case 'pc':
        		if (val < (dir == '+' ? 2 : 2.1)) return 0.1;
        		if (val < (dir == '+' ? 4 : 4.1)) return 0.2;
        		if (val < (dir == '+' ? 10 : 10.1)) return 0.5;
        		return 1;
		}

	}
	function CSSEditorCustomDropdown(options)
	{
		for (var i = 0; i < options.length; i++) {
			options[i] = '<li>' + options[i] + '</li>';
		}
		var div = $('<div class="drop-down"><ul>' + options.join('') + '</ul></div>');
		div.bind('click', function (event) {
			event.stopPropagation();
			div.hide();
			input.val($(event.target).html());
			input.trigger('up_down');
		});
		if (! window.css_editor_dropdown_initialized) {
    		$(document).bind('mousedown', function (event) {
    			if (! window.css_editor_dropdown_current)
    				return;
    			if (event.target == window.css_editor_dropdown_current ||
    					event.target.parentNode && event.target.parentNode.parentNode == window.css_editor_dropdown_current)
    				return;
    			if (event.target == window.css_editor_dropdown_button) {
    				event.stopPropagation();
    				event.stopImmediatePropagation();
    				event.preventDefault();
    				return;
    			}
    				
    			hideDropdown();
    		});
    		window.css_editor_dropdown_initialized = true;
		}
		return div;
	}
	
	function showDropdown(dd, btn)
	{
		window.css_editor_dropdown_current = dd;
		window.css_editor_dropdown_button = btn;
		$(dd).show();
	}
	
	function hideDropdown()
	{
		$(window.css_editor_dropdown_current).hide();
		window.css_editor_dropdown_current = undefined;
		window.css_editor_dropdown_button = undefined;
	}
	
	return this;
};

/*
 * Common Editor for Values of Type <Length>
 */
$.fn.CSSEditorLength = function (prop_name, rule, percentage, negative)
{
	var _this = this;
	var value = this.data('value');
	if (value === undefined || value === null) {
		return this;
	}
	
	value.CSSEditorWidgetLength(percentage, negative);
	this.data('select', value.data('select'));

	function fetchPropertyValue() {
		value.trigger('set', [ rule.getPropertyActualValue(prop_name) ]);
	}
	value.bind('change_value', function (event, value) {
		rule.setActualPropertyValue(prop_name, value, _this);
		_this.trigger('change_prop');
	});
	this.bind('default', function (event) {
		rule.setActualPropertyValue(prop_name, '0px', _this);
	});
	this.bind('show restore', function (event) {
		fetchPropertyValue();
	});

	fetchPropertyValue();
	
	return this;
};

/*
 * Add Options into <Length> Property Editor
 */
$.fn.CSSEditorAddOptions = function (options, selector, to_begin)
{
	var _this = this;
	var select = this.data(selector ? selector : 'select');
	if (select === undefined) {
		return this;
	}
	var options_str = '';
	if (options.length) {
		for (var i = 0; i < options.length; i++) {
			options_str += '<option value="'+options[i]+'">'+options[i]+'</option>';
		}
	}
	else {
		for (var k in options) {
			options_str += '<option value="'+k+'">'+options[k]+'</option>';
		}
	}
	if (to_begin) {
		select.prepend(options_str);
	}
	else {
		select.append(options_str);
	}
	this.trigger('restore');
	
	return this;
};

//}
