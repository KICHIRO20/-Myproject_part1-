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
