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
 * Отправить зашифрованные данные на расшифровку.
 */
function decryptGroupJavascript(group_id, form_id, form_action_id, encrypted_data_index_id, form_target)
{
    //Поменять action формы (перенаправить на модуль Crypto), сделать сабмит.
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
		$.colorbox({iframe:true, href:url, initialWidth:"500px", initialHeight:"500px", maxHeight:"100%", maxWidth:"100%", width:"830px", height:"90%", overlayClose:false});
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
    $('#cboxClose').click();
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
    $('#cboxClose').click();
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
		$.colorbox({iframe:true, href:URL, initialWidth:"500px", initialHeight:"500px", maxHeight:"100%", maxWidth:"70%", width:"500px", height:"70%"});
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
    d.style.border = 'solid 1px black';
    d.style.display = '';

    // add opacity
	if(navigator.userAgent.indexOf("MSIE 6")!=-1)
	{
	  d.style.backgroundImage='none';
	  d.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(src='images/halftranspixel.png', sizingMethod='scale')";
	}
	else
	{
	  d.style.backgroundImage='url("images/halftranspixel.png")';
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
    
    div_el.style.left = _left + (_width - _w2 ) / 2 + 'px';
    div_el.style.top = _top + (_height - _h2 ) / 2 + 'px';
};


function disableButton(button_id)
{
    if(document.getElementById(button_id).className.indexOf('button_disabled')==-1)
    {
        document.getElementById(button_id).className += ' button_disabled';
        document.getElementById(button_id).onclick = function() {};
    };
};

function disableLink(link_id)
{
    if(document.getElementById(link_id).className.indexOf('link_disabled')==-1)
    {
        document.getElementById(link_id).className += ' link_disabled';
        document.getElementById(link_id).onclick = function() {};
    };
};

function enableButton(button_id,onclick_function)
{
    document.getElementById(button_id).className = document.getElementById(button_id).className.replace(' button_disabled','');
    document.getElementById(button_id).onclick = onclick_function;
};

function enableLink(link_id,onclick_function)
{
    document.getElementById(link_id).className = document.getElementById(link_id).className.replace(' link_disabled','');
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
      $A(div.childNodes).inject('', function(memo, node) { return memo+node.nodeValue }) :
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
            var text = (""+text).replace(/^\s*<!\-\-/, '').replace(/\-\->\s*$/, '');
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

var numeric = "0123456789";

/* This array will be redefined in validate.msgs.js.tpl */
var messages = ['{INTEGER}', '{FLOAT}', '{CURRENCY}', '{STRING1024}', '{STRING128}', '{STRING256}', '{STRING512}', '{WEIGHT}', '{ITEM}'];

function formatInput(input) {
    var type = input.getAttribute('patternType');
    if (type == null || type == "") return;
    if (type == 'currency') return formatCurrency(input, messages[2]);
    if (type == 'float') return formatFloat(input, messages[1]);
    if (type == 'integer') return formatInteger(input, messages[0]);
    if (type == 'string1024') return formatString1024(input, messages[3]);
    if (type == 'string128') return formatString128(input, messages[4]);
    if (type == 'string256') return formatString256(input, messages[5]);
    if (type == 'string512') return formatString512(input, messages[6]);
    if (type == 'currency') return formatCurrency(input, messages[2]);
    if (type == 'weight') return formatWeight(input, messages[7]);
    if (type == 'item') return formatInteger(input, messages[8]);
    unit_select = document.getElementById(type);
    if (!unit_select) return;
    pattern = unit_select.options[unit_select.selectedIndex].getAttribute('patternType');
    if (pattern == 'currency') return formatCurrency(input, messages[2]);
    if (pattern == 'integer') return formatInteger(input, messages[0]);
    if (pattern == 'float') return formatFloat(input, messages[1]);
}

function showPopup (input, text) {
    if (window.createPopup) {
        var oPopup = window.createPopup();
        var oPopupBody = oPopup.document.body;
        oPopupBody.style.backgroundColor = "lightyellow";
        oPopupBody.style.border = "solid black 1px";
        oPopupBody.style.fontFamily = "Tahoma";
        oPopupBody.style.fontSize = "11px";
        oPopupBody.innerHTML = "&nbsp;<strong>" + text + "</strong>";
        oPopup.show(15, 15, 320, 34, input);
        input.focus();
        //input.select();
    } else {
        alert(text);
    }
    return;
}

function showPopup_addProduct (input, text) {
    if (window.createPopup) {
        var oPopup = window.createPopup();
        var oPopupBody = oPopup.document.body;
        oPopupBody.style.backgroundColor = "lightyellow";
        oPopupBody.style.border = "solid black 1px";
        oPopupBody.style.fontFamily = "Tahoma";
        oPopupBody.style.fontSize = "11px";
        oPopupBody.innerHTML = "&nbsp;<strong>" + text + "</strong>";
        oPopup.show(15, 15, 320, 34, input);
/*        input.focus();
        input.select(); */
    } else {
        alert(text);
    }
    return;
}

function isNumber (num) {
    for(i = 0, found = false; i < num.length; i++) {
        ch = num.charAt(i);
        if (numeric.indexOf(ch) > -1) {
            found = true;
            break;
        }
    }
    if (!found) {
        return false;
    }
    return true;
}

function formatCurrency(input, text) {

	var decimals = input.getAttribute('decimals');
	var dec_point = input.getAttribute('dec_point');
    return formatFloat(input, text, decimals, dec_point);
}

function formatWeight(input, text) {

	var decimals = input.getAttribute('decimals');
	var dec_point = input.getAttribute('dec_point');
    return formatFloat(input, text, decimals, dec_point);
}

function formatFloat(input, text, fractional_part_length, dec_point) {
    var num = input.value;
    if (num.length == 0)
        return;
    var num = input.value.replace(/[^0123456789]/g, '.').replace(/ /g, '').replace(/\,/g, '.')
    																	  .replace(/\-/g, '.')
    																	  .replace(/\=/g, '.')
    																	  .replace(/\//g, '.')
    																	  .replace(/\;/g, '.')
    																	  .replace(/\:/g, '.')
    																	  .replace(/\'/g, '.');

    if (!isNumber(num.replace(/\./g, ''))) {
        input.value = '';
        showPopup (input, text);
        return;
    }


    var num_parts = num.split('.');
    if(num_parts.length >0 && num_parts[0] == '')
    {
        //alert('[' + num_parts[0] + ']');
        num_parts[0] = '0';
    }

    var integer_part_has_been_already_found = 0;
    var num = '';
    for(i = 0; i < num_parts.length; i++) {
        if(num_parts[i] != '')
        {
            if(integer_part_has_been_already_found == 1)
            {
                num_parts[i] = (num_parts[i] + '0000000000').substring(0,fractional_part_length);
            }

            num = num + num_parts[i];
            if(integer_part_has_been_already_found == 1)
            {
                break;
            }
            else
            {
                integer_part_has_been_already_found = 1;
                num = num + dec_point;
            }
        }
    }

    //: LOCALIZATION , not only '.' may be delimiter
    if(num.charAt(num.length-1) == dec_point)
    {   if(fractional_part_length != null)
        {
            num = num + ('0000000000'.substring(0,fractional_part_length));
        }
        else
        {
            num = num + '000';
        }
    }

    input.value = num;
}

function formatInteger(input, text) {
    var negative = false;
    var num = input.value;
    if (num.length == 0)
        return;
    if ((num*1)<0)
    {
        negative = true;
    }

    var num = input.value.replace(/\,/g, '.').replace(/ /g, '').replace(/[^0123456789\.\,]/g, '');
    var num_parts = num.split('.');
    var num = num_parts[0];

    if (!isNumber(num)) {
        input.value = '';
        showPopup (input, text);
        return;
    }

    if (negative)
        input.value = '-'+num;
    else
        input.value = num;
}

function formatString1024(input, text) {
    var num = input.value;

    if (num.length > 1024){
        input.value = num.substr(0, 1024);
        showPopup (input, text);
        return;
    }
}

function formatString128(input, text) {
    var num = input.value;

    if (num.length > 128){
        input.value = num.substr(0, 128);
        showPopup (input, text);
        return;
    }
}

function formatString256(input, text) {
    var num = input.value;

    if (num.length > 256){
        input.value = num.substr(0, 256);
        showPopup (input, text);
        return;
    }
}

function formatString512(input, text) {
    var num = input.value;

    if (num.length > 512){
        input.value = num.substr(0, 512);
        showPopup (input, text);
        return;
    }
}
/**
 * JsHttpRequest: JavaScript "AJAX" data loader (form support only!)
 * Minimized version: see debug directory for the complete one.
 *
 * @license LGPL
 * @author Dmitry Koterov, http://en.dklab.ru/lib/JsHttpRequest/
 * @version 5.x $Id$
 */
function JsHttpRequest(){
var t=this;
t.onreadystatechange=null;
t.readyState=0;
t.responseText=null;
t.responseXML=null;
t.status=200;
t.statusText="OK";
t.responseJS=null;
t.caching=false;
t.loader=null;
t.session_name="PHPSESSID";
t._ldObj=null;
t._reqHeaders=[];
t._openArgs=null;
t._errors={inv_form_el:"Invalid FORM element detected: name=%, tag=%",must_be_single_el:"If used, <form> must be a single HTML element in the list.",js_invalid:"JavaScript code generated by backend is invalid!\n%",url_too_long:"Cannot use so long query with GET request (URL is larger than % bytes)",unk_loader:"Unknown loader: %",no_loaders:"No loaders registered at all, please check JsHttpRequest.LOADERS array",no_loader_matched:"Cannot find a loader which may process the request. Notices are:\n%"};
t.abort=function(){
with(this){
if(_ldObj&&_ldObj.abort){
_ldObj.abort();
}
_cleanup();
if(readyState==0){
return;
}
if(readyState==1&&!_ldObj){
readyState=0;
return;
}
_changeReadyState(4,true);
}
};
t.open=function(_2,_3,_4,_5,_6){
with(this){
if(_3.match(/^((\w+)\.)?(GET|POST)\s+(.*)/i)){
this.loader=RegExp.$2?RegExp.$2:null;
_2=RegExp.$3;
_3=RegExp.$4;
}
try{
if(document.location.search.match(new RegExp("[&?]"+session_name+"=([^&?]*)"))||document.cookie.match(new RegExp("(?:;|^)\\s*"+session_name+"=([^;]*)"))){
_3+=(_3.indexOf("?")>=0?"&":"?")+session_name+"="+this.escape(RegExp.$1);
}
}
catch(e){
}
_openArgs={method:(_2||"").toUpperCase(),url:_3,asyncFlag:_4,username:_5!=null?_5:"",password:_6!=null?_6:""};
_ldObj=null;
_changeReadyState(1,true);
return true;
}
};
t.send=function(_7){
if(!this.readyState){
return;
}
this._changeReadyState(1,true);
this._ldObj=null;
var _8=[];
var _9=[];
if(!this._hash2query(_7,null,_8,_9)){
return;
}
var _a=null;
if(this.caching&&!_9.length){
_a=this._openArgs.username+":"+this._openArgs.password+"@"+this._openArgs.url+"|"+_8+"#"+this._openArgs.method;
var _b=JsHttpRequest.CACHE[_a];
if(_b){
this._dataReady(_b[0],_b[1]);
return false;
}
}
var _c=(this.loader||"").toLowerCase();
if(_c&&!JsHttpRequest.LOADERS[_c]){
return this._error("unk_loader",_c);
}
var _d=[];
var _e=JsHttpRequest.LOADERS;
for(var _f in _e){
var ldr=_e[_f].loader;
if(!ldr){
continue;
}
if(_c&&_f!=_c){
continue;
}
var _11=new ldr(this);
JsHttpRequest.extend(_11,this._openArgs);
JsHttpRequest.extend(_11,{queryText:_8.join("&"),queryElem:_9,id:(new Date().getTime())+""+JsHttpRequest.COUNT++,hash:_a,span:null});
var _12=_11.load();
if(!_12){
this._ldObj=_11;
JsHttpRequest.PENDING[_11.id]=this;
return true;
}
if(!_c){
_d[_d.length]="- "+_f.toUpperCase()+": "+this._l(_12);
}else{
return this._error(_12);
}
}
return _f?this._error("no_loader_matched",_d.join("\n")):this._error("no_loaders");
};
t.getAllResponseHeaders=function(){
with(this){
return _ldObj&&_ldObj.getAllResponseHeaders?_ldObj.getAllResponseHeaders():[];
}
};
t.getResponseHeader=function(_13){
with(this){
return _ldObj&&_ldObj.getResponseHeader?_ldObj.getResponseHeader(_13):null;
}
};
t.setRequestHeader=function(_14,_15){
with(this){
_reqHeaders[_reqHeaders.length]=[_14,_15];
}
};
t._dataReady=function(_16,js){
with(this){
if(caching&&_ldObj){
JsHttpRequest.CACHE[_ldObj.hash]=[_16,js];
}
responseText=responseXML=_16;
responseJS=js;
if(js!==null){
status=200;
statusText="OK";
}else{
status=500;
statusText="Internal Server Error";
}
_changeReadyState(2);
_changeReadyState(3);
_changeReadyState(4);
_cleanup();
}
};
t._l=function(_18){
var i=0,p=0,msg=this._errors[_18[0]];
while((p=msg.indexOf("%",p))>=0){
var a=_18[++i]+"";
msg=msg.substring(0,p)+a+msg.substring(p+1,msg.length);
p+=1+a.length;
}
return msg;
};
t._error=function(msg){
msg=this._l(typeof (msg)=="string"?arguments:msg);
msg="JsHttpRequest: "+msg;
if(!window.Error){
throw msg;
}else{
if((new Error(1,"test")).description=="test"){
throw new Error(1,msg);
}else{
throw new Error(msg);
}
}
};
t._hash2query=function(_1e,_1f,_20,_21){
if(_1f==null){
_1f="";
}
if((""+typeof (_1e)).toLowerCase()=="object"){
var _22=false;
if(_1e&&_1e.parentNode&&_1e.parentNode.appendChild&&_1e.tagName&&_1e.tagName.toUpperCase()=="FORM"){
_1e={form:_1e};
}
for(var k in _1e){
var v=_1e[k];
if(v instanceof Function){
continue;
}
var _25=_1f?_1f+"["+this.escape(k)+"]":this.escape(k);
var _26=v&&v.parentNode&&v.parentNode.appendChild&&v.tagName;
if(_26){
var tn=v.tagName.toUpperCase();
if(tn=="FORM"){
_22=true;
}else{
if(tn=="INPUT"||tn=="TEXTAREA"||tn=="SELECT"){
}else{
return this._error("inv_form_el",(v.name||""),v.tagName);
}
}
_21[_21.length]={name:_25,e:v};
}else{
if(v instanceof Object){
this._hash2query(v,_25,_20,_21);
}else{
if(v===null){
continue;
}
if(v===true){
v=1;
}
if(v===false){
v="";
}
_20[_20.length]=_25+"="+this.escape(""+v);
}
}
if(_22&&_21.length>1){
return this._error("must_be_single_el");
}
}
}else{
_20[_20.length]=_1e;
}
return true;
};
t._cleanup=function(){
var _28=this._ldObj;
if(!_28){
return;
}
JsHttpRequest.PENDING[_28.id]=false;
var _29=_28.span;
if(!_29){
return;
}
_28.span=null;
var _2a=function(){
_29.parentNode.removeChild(_29);
};
JsHttpRequest.setTimeout(_2a,50);
};
t._changeReadyState=function(s,_2c){
with(this){
if(_2c){
status=statusText=responseJS=null;
responseText="";
}
readyState=s;
if(onreadystatechange){
onreadystatechange();
}
}
};
t.escape=function(s){
return escape(s).replace(new RegExp("\\+","g"),"%2B");
};
}
JsHttpRequest.COUNT=0;
JsHttpRequest.MAX_URL_LEN=2000;
JsHttpRequest.CACHE={};
JsHttpRequest.PENDING={};
JsHttpRequest.LOADERS={};
JsHttpRequest._dummy=function(){
};
JsHttpRequest.TIMEOUTS={s:window.setTimeout,c:window.clearTimeout};
JsHttpRequest.setTimeout=function(_2e,dt){
window.JsHttpRequest_tmp=JsHttpRequest.TIMEOUTS.s;
if(typeof (_2e)=="string"){
id=window.JsHttpRequest_tmp(_2e,dt);
}else{
var id=null;
var _31=function(){
_2e();
delete JsHttpRequest.TIMEOUTS[id];
};
id=window.JsHttpRequest_tmp(_31,dt);
JsHttpRequest.TIMEOUTS[id]=_31;
}
window.JsHttpRequest_tmp=null;
return id;
};
JsHttpRequest.clearTimeout=function(id){
window.JsHttpRequest_tmp=JsHttpRequest.TIMEOUTS.c;
delete JsHttpRequest.TIMEOUTS[id];
var r=window.JsHttpRequest_tmp(id);
window.JsHttpRequest_tmp=null;
return r;
};
JsHttpRequest.query=function(url,_35,_36,_37){
var req=new this();
req.caching=!_37;
req.onreadystatechange=function(){
if(req.readyState==4){
_36(req.responseJS,req.responseText);
}
};
req.open(null,url,true);
_35.__ASC_FORM_ID__ = __ASC_FORM_ID__;
req.send(_35);
};
JsHttpRequest.dataReady=function(d){
var th=this.PENDING[d.id];
delete this.PENDING[d.id];
if(th){
th._dataReady(d.text,d.js);
}else{
if(th!==false){
throw "dataReady(): unknown pending id: "+d.id;
}
}
};
JsHttpRequest.extend=function(_3b,src){
for(var k in src){
_3b[k]=src[k];
}
};
JsHttpRequest.LOADERS.form={loader:function(req){
JsHttpRequest.extend(req._errors,{form_el_not_belong:"Element \"%\" does not belong to any form!",form_el_belong_diff:"Element \"%\" belongs to a different form. All elements must belong to the same form!",form_el_inv_enctype:"Attribute \"enctype\" of the form must be \"%\" (for IE), \"%\" given."});
this.load=function(){
var th=this;
if(!th.method){
th.method="POST";
}
th.url+=(th.url.indexOf("?")>=0?"&":"?")+"JsHttpRequest="+th.id+"-"+"form";
if(th.method=="GET"){
if(th.queryText){
th.url+=(th.url.indexOf("?")>=0?"&":"?")+th.queryText;
}
if(th.url.length>JsHttpRequest.MAX_URL_LEN){
return ["url_too_long",JsHttpRequest.MAX_URL_LEN];
}
var p=th.url.split("?",2);
th.url=p[0];
th.queryText=p[1]||"";
}
var _41=null;
var _42=false;
if(th.queryElem.length){
if(th.queryElem[0].e.tagName.toUpperCase()=="FORM"){
_41=th.queryElem[0].e;
_42=true;
th.queryElem=[];
}else{
_41=th.queryElem[0].e.form;
for(var i=0;i<th.queryElem.length;i++){
var e=th.queryElem[i].e;
if(!e.form){
return ["form_el_not_belong",e.name];
}
if(e.form!=_41){
return ["form_el_belong_diff",e.name];
}
}
}
if(th.method=="POST"){
var _45="multipart/form-data";
var _46=(_41.attributes.encType&&_41.attributes.encType.nodeValue)||(_41.attributes.enctype&&_41.attributes.enctype.value)||_41.enctype;
if(_46!=_45){
return ["form_el_inv_enctype",_45,_46];
}
}
}
var d=_41&&(_41.ownerDocument||_41.document)||document;
var _48="jshr_i_"+th.id;
var s=th.span=d.createElement("DIV");
s.style.position="absolute";
s.style.display="none";
s.style.visibility="hidden";
s.innerHTML=(_41?"":"<form"+(th.method=="POST"?" enctype=\"multipart/form-data\" method=\"post\"":"")+"></form>")+"<iframe name=\""+_48+"\" id=\""+_48+"\" style=\"width:0px; height:0px; overflow:hidden; border:none\"></iframe>";
if(!_41){
_41=th.span.firstChild;
}
d.body.insertBefore(s,d.body.lastChild);
var _4a=function(e,_4c){
var sv=[];
var _4e=e;
if(e.mergeAttributes){
var _4e=d.createElement("form");
_4e.mergeAttributes(e,false);
}
for(var i=0;i<_4c.length;i++){
var k=_4c[i][0],v=_4c[i][1];
sv[sv.length]=[k,_4e.getAttribute(k)];
_4e.setAttribute(k,v);
}
if(e.mergeAttributes){
e.mergeAttributes(_4e,false);
}
return sv;
};
var _52=function(){
top.JsHttpRequestGlobal=JsHttpRequest;
var _53=[];
if(!_42){
for(var i=0,n=_41.elements.length;i<n;i++){
_53[i]=_41.elements[i].name;
_41.elements[i].name="";
}
}
var qt=th.queryText.split("&");
for(var i=qt.length-1;i>=0;i--){
var _57=qt[i].split("=",2);
var e=d.createElement("INPUT");
e.type="hidden";
e.name=unescape(_57[0]);
e.value=_57[1]!=null?unescape(_57[1]):"";
_41.appendChild(e);
}
for(var i=0;i<th.queryElem.length;i++){
th.queryElem[i].e.name=th.queryElem[i].name;
}
var sv=_4a(_41,[["action",th.url],["method",th.method],["onsubmit",null],["target",_48]]);
_41.submit();
_4a(_41,sv);
for(var i=0;i<qt.length;i++){
_41.lastChild.parentNode.removeChild(_41.lastChild);
}
if(!_42){
for(var i=0,n=_41.elements.length;i<n;i++){
_41.elements[i].name=_53[i];
}
}
};
JsHttpRequest.setTimeout(_52,100);
return null;
};
}};


    function NewWindow(windowName, windowURL, action, showWindow, alert_message)
    {
        if (!showWindow)
        {
            return;
        }
        var newwin;
        var URL = windowURL;
        if (action == 'Info' || action == 'Edit' || action == 'Del' || action == 'Move')
        {
            var i=0;
            var elem = document.catListForm.category_id;
            if (elem)
            {
                if (!elem.length)
                {
                    URL = windowURL+elem.value;
                }
                else
                {
                    while (elem[i])
                    {
                        if (elem[i].checked)
                        {
                            URL = windowURL+elem[i].value;
                            break;
                        }
                        i++;
                    }
                }
                if (URL == windowURL)
                {
                    alert(alert_message);
                    return;
                }
            }
            else
            {
                alert(alert_message);
                return;
            }
        }
/*        newwin = window.open(URL, windowName); */
        var newWin = openURLinNewWindow(URL, windowName);
        //newWin.focus();
    }

    function NewWindowExt(windowName, windowURL, action, showWindow, alert_message, form_name, el_name)
    {
        if (!showWindow)
        {
            return;
        }
        var newwin;
        var URL = windowURL;
        if (action == 'Info' || action == 'Edit' || action == 'Del' || action == 'Move')
        {
            var i=0;
            var elem = document.forms[form_name][el_name];
            if (elem)
            {
                if (!elem.length)
                {
                    URL = windowURL+elem.value;
                }
                else
                {
                    while (elem[i])
                    {
                        if (elem[i].checked)
                        {
                            URL = windowURL+elem[i].value;
                            break;
                        }
                        i++;
                    }
                }
                if (URL == windowURL)
                {
                    alert(alert_message);
                    return;
                }
            }
            else
            {
                alert(alert_message);
                return;
            }
        }
/*        newwin = window.open(URL, windowName); */
        var newWin = openURLinNewWindow(URL, windowName);
        //newWin.focus();
    }

//\/////
//\  overLIB 4.21 - You may not remove or change this notice.
//\  Copyright Erik Bosrup 1998-2004. All rights reserved.
//\
//\  Contributors are listed on the homepage.
//\  This file might be old, always check for the latest version at:
//\  http://www.bosrup.com/web/overlib/
//\
//\  Please read the license agreement (available through the link above)
//\  before using overLIB. Direct any licensing questions to erik@bosrup.com.
//\
//\  Do not sell this as your own work or remove this copyright notice. 
//\  For full details on copying or changing this script please read the
//\  license agreement at the link above. Please give credit on sites that
//\  use overLIB and submit changes of the script so other people can use
//\  them as well.
//   $Revision: 1.119 $                $Date: 2005/07/02 23:41:44 $
//\/////
//\mini

////////
// PRE-INIT
// Ignore these lines, configuration is below.
////////
var olLoaded = 0;var pmStart = 10000000; var pmUpper = 10001000; var pmCount = pmStart+1; var pmt=''; var pms = new Array(); var olInfo = new Info('4.21', 1);
var FREPLACE = 0; var FBEFORE = 1; var FAFTER = 2; var FALTERNATE = 3; var FCHAIN=4;
var olHideForm=0;  // parameter for hiding SELECT and ActiveX elements in IE5.5+ 
var olHautoFlag = 0;  // flags for over-riding VAUTO and HAUTO if corresponding
var olVautoFlag = 0;  // positioning commands are used on the command line
var hookPts = new Array(), postParse = new Array(), cmdLine = new Array(), runTime = new Array();
// for plugins
registerCommands('donothing,inarray,caparray,sticky,background,noclose,caption,left,right,center,offsetx,offsety,fgcolor,bgcolor,textcolor,capcolor,closecolor,width,border,cellpad,status,autostatus,autostatuscap,height,closetext,snapx,snapy,fixx,fixy,relx,rely,fgbackground,bgbackground,padx,pady,fullhtml,above,below,capicon,textfont,captionfont,closefont,textsize,captionsize,closesize,timeout,function,delay,hauto,vauto,closeclick,wrap,followmouse,mouseoff,closetitle,cssoff,compatmode,cssclass,fgclass,bgclass,textfontclass,captionfontclass,closefontclass');

////////
// DEFAULT CONFIGURATION
// Settings you want everywhere are set here. All of this can also be
// changed on your html page or through an overLIB call.
////////
if (typeof ol_fgcolor=='undefined') var ol_fgcolor="#CCCCFF";
if (typeof ol_bgcolor=='undefined') var ol_bgcolor="#333399";
if (typeof ol_textcolor=='undefined') var ol_textcolor="#000000";
if (typeof ol_capcolor=='undefined') var ol_capcolor="#FFFFFF";
if (typeof ol_closecolor=='undefined') var ol_closecolor="#9999FF";
if (typeof ol_textfont=='undefined') var ol_textfont="Verdana,Arial,Helvetica";
if (typeof ol_captionfont=='undefined') var ol_captionfont="Verdana,Arial,Helvetica";
if (typeof ol_closefont=='undefined') var ol_closefont="Verdana,Arial,Helvetica";
if (typeof ol_textsize=='undefined') var ol_textsize="1";
if (typeof ol_captionsize=='undefined') var ol_captionsize="1";
if (typeof ol_closesize=='undefined') var ol_closesize="1";
if (typeof ol_width=='undefined') var ol_width="200";
if (typeof ol_border=='undefined') var ol_border="1";
if (typeof ol_cellpad=='undefined') var ol_cellpad=2;
if (typeof ol_offsetx=='undefined') var ol_offsetx=10;
if (typeof ol_offsety=='undefined') var ol_offsety=10;
if (typeof ol_text=='undefined') var ol_text="Default Text";
if (typeof ol_cap=='undefined') var ol_cap="";
if (typeof ol_sticky=='undefined') var ol_sticky=0;
if (typeof ol_background=='undefined') var ol_background="";
if (typeof ol_close=='undefined') var ol_close="Close";
if (typeof ol_hpos=='undefined') var ol_hpos=RIGHT;
if (typeof ol_status=='undefined') var ol_status="";
if (typeof ol_autostatus=='undefined') var ol_autostatus=0;
if (typeof ol_height=='undefined') var ol_height=-1;
if (typeof ol_snapx=='undefined') var ol_snapx=0;
if (typeof ol_snapy=='undefined') var ol_snapy=0;
if (typeof ol_fixx=='undefined') var ol_fixx=-1;
if (typeof ol_fixy=='undefined') var ol_fixy=-1;
if (typeof ol_relx=='undefined') var ol_relx=null;
if (typeof ol_rely=='undefined') var ol_rely=null;
if (typeof ol_fgbackground=='undefined') var ol_fgbackground="";
if (typeof ol_bgbackground=='undefined') var ol_bgbackground="";
if (typeof ol_padxl=='undefined') var ol_padxl=1;
if (typeof ol_padxr=='undefined') var ol_padxr=1;
if (typeof ol_padyt=='undefined') var ol_padyt=1;
if (typeof ol_padyb=='undefined') var ol_padyb=1;
if (typeof ol_fullhtml=='undefined') var ol_fullhtml=0;
if (typeof ol_vpos=='undefined') var ol_vpos=BELOW;
if (typeof ol_aboveheight=='undefined') var ol_aboveheight=0;
if (typeof ol_capicon=='undefined') var ol_capicon="";
if (typeof ol_frame=='undefined') var ol_frame=self;
if (typeof ol_timeout=='undefined') var ol_timeout=0;
if (typeof ol_function=='undefined') var ol_function=null;
if (typeof ol_delay=='undefined') var ol_delay=0;
if (typeof ol_hauto=='undefined') var ol_hauto=0;
if (typeof ol_vauto=='undefined') var ol_vauto=0;
if (typeof ol_closeclick=='undefined') var ol_closeclick=0;
if (typeof ol_wrap=='undefined') var ol_wrap=0;
if (typeof ol_followmouse=='undefined') var ol_followmouse=1;
if (typeof ol_mouseoff=='undefined') var ol_mouseoff=0;
if (typeof ol_closetitle=='undefined') var ol_closetitle='Close';
if (typeof ol_compatmode=='undefined') var ol_compatmode=0;
if (typeof ol_css=='undefined') var ol_css=CSSOFF;
if (typeof ol_fgclass=='undefined') var ol_fgclass="";
if (typeof ol_bgclass=='undefined') var ol_bgclass="";
if (typeof ol_textfontclass=='undefined') var ol_textfontclass="";
if (typeof ol_captionfontclass=='undefined') var ol_captionfontclass="";
if (typeof ol_closefontclass=='undefined') var ol_closefontclass="";

////////
// ARRAY CONFIGURATION
////////

// You can use these arrays to store popup text here instead of in the html.
if (typeof ol_texts=='undefined') var ol_texts = new Array("Text 0", "Text 1");
if (typeof ol_caps=='undefined') var ol_caps = new Array("Caption 0", "Caption 1");

////////
// END OF CONFIGURATION
// Don't change anything below this line, all configuration is above.
////////





////////
// INIT
////////
// Runtime variables init. Don't change for config!
var o3_text="";
var o3_cap="";
var o3_sticky=0;
var o3_background="";
var o3_close="Close";
var o3_hpos=RIGHT;
var o3_offsetx=2;
var o3_offsety=2;
var o3_fgcolor="";
var o3_bgcolor="";
var o3_textcolor="";
var o3_capcolor="";
var o3_closecolor="";
var o3_width=100;
var o3_border=1;
var o3_cellpad=2;
var o3_status="";
var o3_autostatus=0;
var o3_height=-1;
var o3_snapx=0;
var o3_snapy=0;
var o3_fixx=-1;
var o3_fixy=-1;
var o3_relx=null;
var o3_rely=null;
var o3_fgbackground="";
var o3_bgbackground="";
var o3_padxl=0;
var o3_padxr=0;
var o3_padyt=0;
var o3_padyb=0;
var o3_fullhtml=0;
var o3_vpos=BELOW;
var o3_aboveheight=0;
var o3_capicon="";
var o3_textfont="Verdana,Arial,Helvetica";
var o3_captionfont="Verdana,Arial,Helvetica";
var o3_closefont="Verdana,Arial,Helvetica";
var o3_textsize="1";
var o3_captionsize="1";
var o3_closesize="1";
var o3_frame=self;
var o3_timeout=0;
var o3_timerid=0;
var o3_allowmove=0;
var o3_function=null; 
var o3_delay=0;
var o3_delayid=0;
var o3_hauto=0;
var o3_vauto=0;
var o3_closeclick=0;
var o3_wrap=0;
var o3_followmouse=1;
var o3_mouseoff=0;
var o3_closetitle='';
var o3_compatmode=0;
var o3_css=CSSOFF;
var o3_fgclass="";
var o3_bgclass="";
var o3_textfontclass="";
var o3_captionfontclass="";
var o3_closefontclass="";

// Display state variables
var o3_x = 0;
var o3_y = 0;
var o3_showingsticky = 0;
var o3_removecounter = 0;

// Our layer
var over = null;
var fnRef, hoveringSwitch = false;
var olHideDelay;

// Decide browser version
var isMac = (navigator.userAgent.indexOf("Mac") != -1);
var olOp = (navigator.userAgent.toLowerCase().indexOf('opera') > -1 && document.createTextNode);  // Opera 7
var olNs4 = (navigator.appName=='Netscape' && parseInt(navigator.appVersion) == 4);
var olNs6 = (document.getElementById) ? true : false;
var olKq = (olNs6 && /konqueror/i.test(navigator.userAgent));
var olIe4 = (document.all) ? true : false;
var olIe5 = false; 
var olIe55 = false; // Added additional variable to identify IE5.5+
var docRoot = 'document.body';

// Resize fix for NS4.x to keep track of layer
if (olNs4) {
	var oW = window.innerWidth;
	var oH = window.innerHeight;
	window.onresize = function() { if (oW != window.innerWidth || oH != window.innerHeight) location.reload(); }
}

// Microsoft Stupidity Check(tm).
if (olIe4) {
	var agent = navigator.userAgent;
	if (/MSIE/.test(agent)) {
		var versNum = parseFloat(agent.match(/MSIE[ ](\d{1,2}\.\d+)\.*/i)[1]);
		if (versNum >= 5){
			olIe5=true;
			olIe55=(versNum>=5.5&&!olOp) ? true : false;
			if (olNs6) olNs6=false;
		}
	}
	if (olNs6) olIe4 = false;
}

// Check for compatability mode.
if (document.compatMode && document.compatMode == 'CSS1Compat') {
	docRoot= ((olIe4 && !olOp) ? 'document.documentElement' : docRoot);
}

// Add window onload handlers to indicate when all modules have been loaded
// For Netscape 6+ and Mozilla, uses addEventListener method on the window object
// For IE it uses the attachEvent method of the window object and for Netscape 4.x
// it sets the window.onload handler to the OLonload_handler function for Bubbling
if(window.addEventListener) window.addEventListener("load",OLonLoad_handler,false);
else if (window.attachEvent) window.attachEvent("onload",OLonLoad_handler);

var capExtent;

////////
// PUBLIC FUNCTIONS
////////

// overlib(arg0,...,argN)
// Loads parameters into global runtime variables.
function overlib() {
	if (!olLoaded || isExclusive(overlib.arguments)) return true;
	if (olCheckMouseCapture) olMouseCapture();
	if (over) {
		over = (typeof over.id != 'string') ? o3_frame.document.all['overDiv'] : over;
		cClick();
	}

	// Load defaults to runtime.
  olHideDelay=0;
	o3_text=ol_text;
	o3_cap=ol_cap;
	o3_sticky=ol_sticky;
	o3_background=ol_background;
	o3_close=ol_close;
	o3_hpos=ol_hpos;
	o3_offsetx=ol_offsetx;
	o3_offsety=ol_offsety;
	o3_fgcolor=ol_fgcolor;
	o3_bgcolor=ol_bgcolor;
	o3_textcolor=ol_textcolor;
	o3_capcolor=ol_capcolor;
	o3_closecolor=ol_closecolor;
	o3_width=ol_width;
	o3_border=ol_border;
	o3_cellpad=ol_cellpad;
	o3_status=ol_status;
	o3_autostatus=ol_autostatus;
	o3_height=ol_height;
	o3_snapx=ol_snapx;
	o3_snapy=ol_snapy;
	o3_fixx=ol_fixx;
	o3_fixy=ol_fixy;
	o3_relx=ol_relx;
	o3_rely=ol_rely;
	o3_fgbackground=ol_fgbackground;
	o3_bgbackground=ol_bgbackground;
	o3_padxl=ol_padxl;
	o3_padxr=ol_padxr;
	o3_padyt=ol_padyt;
	o3_padyb=ol_padyb;
	o3_fullhtml=ol_fullhtml;
	o3_vpos=ol_vpos;
	o3_aboveheight=ol_aboveheight;
	o3_capicon=ol_capicon;
	o3_textfont=ol_textfont;
	o3_captionfont=ol_captionfont;
	o3_closefont=ol_closefont;
	o3_textsize=ol_textsize;
	o3_captionsize=ol_captionsize;
	o3_closesize=ol_closesize;
	o3_timeout=ol_timeout;
	o3_function=ol_function;
	o3_delay=ol_delay;
	o3_hauto=ol_hauto;
	o3_vauto=ol_vauto;
	o3_closeclick=ol_closeclick;
	o3_wrap=ol_wrap;	
	o3_followmouse=ol_followmouse;
	o3_mouseoff=ol_mouseoff;
	o3_closetitle=ol_closetitle;
	o3_css=ol_css;
	o3_compatmode=ol_compatmode;
	o3_fgclass=ol_fgclass;
	o3_bgclass=ol_bgclass;
	o3_textfontclass=ol_textfontclass;
	o3_captionfontclass=ol_captionfontclass;
	o3_closefontclass=ol_closefontclass;
	
	setRunTimeVariables();
	
	fnRef = '';
	
	// Special for frame support, over must be reset...
	o3_frame = ol_frame;
	
	if(!(over=createDivContainer())) return false;

	parseTokens('o3_', overlib.arguments);
	if (!postParseChecks()) return false;

	if (o3_delay == 0) {
		return runHook("olMain", FREPLACE);
 	} else {
		o3_delayid = setTimeout("runHook('olMain', FREPLACE)", o3_delay);
		return false;
	}
}

// Clears popups if appropriate
function nd(time) {
	if (olLoaded && !isExclusive()) {
		hideDelay(time);  // delay popup close if time specified

		if (o3_removecounter >= 1) { o3_showingsticky = 0 };
		
		if (o3_showingsticky == 0) {
			o3_allowmove = 0;
			if (over != null && o3_timerid == 0) runHook("hideObject", FREPLACE, over);
		} else {
			o3_removecounter++;
		}
	}
	
	return true;
}

// The Close onMouseOver function for stickies
function cClick() {
	if (olLoaded) {
		runHook("hideObject", FREPLACE, over);
		o3_showingsticky = 0;	
	}	
	return false;
}

// Method for setting page specific defaults.
function overlib_pagedefaults() {
	parseTokens('ol_', overlib_pagedefaults.arguments);
}


////////
// OVERLIB MAIN FUNCTION
////////

// This function decides what it is we want to display and how we want it done.
function olMain() {
	var layerhtml, styleType;
 	runHook("olMain", FBEFORE);
 	
	if (o3_background!="" || o3_fullhtml) {
		// Use background instead of box.
		layerhtml = runHook('ol_content_background', FALTERNATE, o3_css, o3_text, o3_background, o3_fullhtml);
	} else {
		// They want a popup box.
		styleType = (pms[o3_css-1-pmStart] == "cssoff" || pms[o3_css-1-pmStart] == "cssclass");

		// Prepare popup background
		if (o3_fgbackground != "") o3_fgbackground = "background=\""+o3_fgbackground+"\"";
		if (o3_bgbackground != "") o3_bgbackground = (styleType ? "background=\""+o3_bgbackground+"\"" : o3_bgbackground);

		// Prepare popup colors
		if (o3_fgcolor != "") o3_fgcolor = (styleType ? "bgcolor=\""+o3_fgcolor+"\"" : o3_fgcolor);
		if (o3_bgcolor != "") o3_bgcolor = (styleType ? "bgcolor=\""+o3_bgcolor+"\"" : o3_bgcolor);

		// Prepare popup height
		if (o3_height > 0) o3_height = (styleType ? "height=\""+o3_height+"\"" : o3_height);
		else o3_height = "";

		// Decide which kinda box.
		if (o3_cap=="") {
			// Plain
			layerhtml = runHook('ol_content_simple', FALTERNATE, o3_css, o3_text);
		} else {
			// With caption
			if (o3_sticky) {
				// Show close text
				layerhtml = runHook('ol_content_caption', FALTERNATE, o3_css, o3_text, o3_cap, o3_close);
			} else {
				// No close text
				layerhtml = runHook('ol_content_caption', FALTERNATE, o3_css, o3_text, o3_cap, "");
			}
		}
	}	

	// We want it to stick!
	if (o3_sticky) {
		if (o3_timerid > 0) {
			clearTimeout(o3_timerid);
			o3_timerid = 0;
		}
		o3_showingsticky = 1;
		o3_removecounter = 0;
	}

	// Created a separate routine to generate the popup to make it easier
	// to implement a plugin capability
	if (!runHook("createPopup", FREPLACE, layerhtml)) return false;

	// Prepare status bar
	if (o3_autostatus > 0) {
		o3_status = o3_text;
		if (o3_autostatus > 1) o3_status = o3_cap;
	}

	// When placing the layer the first time, even stickies may be moved.
	o3_allowmove = 0;

	// Initiate a timer for timeout
	if (o3_timeout > 0) {          
		if (o3_timerid > 0) clearTimeout(o3_timerid);
		o3_timerid = setTimeout("cClick()", o3_timeout);
	}

	// Show layer
	runHook("disp", FREPLACE, o3_status);
	runHook("olMain", FAFTER);

	return (olOp && event && event.type == 'mouseover' && !o3_status) ? '' : (o3_status != '');
}

////////
// LAYER GENERATION FUNCTIONS
////////
// These functions just handle popup content with tags that should adhere to the W3C standards specification.

// Makes simple table without caption
function ol_content_simple(text) {
	var cpIsMultiple = /,/.test(o3_cellpad);
	var txt = '<table width="'+o3_width+ '" border="0" cellpadding="'+o3_border+'" cellspacing="0" '+(o3_bgclass ? 'class="'+o3_bgclass+'"' : o3_bgcolor+' '+o3_height)+'><tr><td><table width="100%" border="0" '+((olNs4||!cpIsMultiple) ? 'cellpadding="'+o3_cellpad+'" ' : '')+'cellspacing="0" '+(o3_fgclass ? 'class="'+o3_fgclass+'"' : o3_fgcolor+' '+o3_fgbackground+' '+o3_height)+'><tr><td valign="TOP"'+(o3_textfontclass ? ' class="'+o3_textfontclass+'">' : ((!olNs4&&cpIsMultiple) ? ' style="'+setCellPadStr(o3_cellpad)+'">' : '>'))+(o3_textfontclass ? '' : wrapStr(0,o3_textsize,'text'))+text+(o3_textfontclass ? '' : wrapStr(1,o3_textsize))+'</td></tr></table></td></tr></table>';

	set_background("");
	return txt;
}

// Makes table with caption and optional close link
function ol_content_caption(text,title,close) {
	var nameId, txt, cpIsMultiple = /,/.test(o3_cellpad);
	var closing, closeevent;

	closing = "";
	closeevent = "onmouseover";
	if (o3_closeclick == 1) closeevent = (o3_closetitle ? "title='" + o3_closetitle +"'" : "") + " onclick";
	if (o3_capicon != "") {
	  nameId = ' hspace = \"5\"'+' align = \"middle\" alt = \"\"';
	  if (typeof o3_dragimg != 'undefined' && o3_dragimg) nameId =' hspace=\"5\"'+' name=\"'+o3_dragimg+'\" id=\"'+o3_dragimg+'\" align=\"middle\" alt=\"Drag Enabled\" title=\"Drag Enabled\"';
	  o3_capicon = '<img src=\"'+o3_capicon+'\"'+nameId+' />';
	}

	if (close != "")
		closing = '<td '+(!o3_compatmode && o3_closefontclass ? 'class="'+o3_closefontclass : 'align="RIGHT')+'"><a href="javascript:return '+fnRef+'cClick();"'+((o3_compatmode && o3_closefontclass) ? ' class="' + o3_closefontclass + '" ' : ' ')+closeevent+'="return '+fnRef+'cClick();">'+(o3_closefontclass ? '' : wrapStr(0,o3_closesize,'close'))+close+(o3_closefontclass ? '' : wrapStr(1,o3_closesize,'close'))+'</a></td>';
	txt = '<table width="'+o3_width+ '" border="0" cellpadding="'+o3_border+'" cellspacing="0" '+(o3_bgclass ? 'class="'+o3_bgclass+'"' : o3_bgcolor+' '+o3_bgbackground+' '+o3_height)+'><tr><td><table width="100%" border="0" cellpadding="2" cellspacing="0"><tr><td'+(o3_captionfontclass ? ' class="'+o3_captionfontclass+'">' : '>')+(o3_captionfontclass ? '' : '<b>'+wrapStr(0,o3_captionsize,'caption'))+o3_capicon+title+(o3_captionfontclass ? '' : wrapStr(1,o3_captionsize)+'</b>')+'</td>'+closing+'</tr></table><table width="100%" border="0" '+((olNs4||!cpIsMultiple) ? 'cellpadding="'+o3_cellpad+'" ' : '')+'cellspacing="0" '+(o3_fgclass ? 'class="'+o3_fgclass+'"' : o3_fgcolor+' '+o3_fgbackground+' '+o3_height)+'><tr><td valign="TOP"'+(o3_textfontclass ? ' class="'+o3_textfontclass+'">' :((!olNs4&&cpIsMultiple) ? ' style="'+setCellPadStr(o3_cellpad)+'">' : '>'))+(o3_textfontclass ? '' : wrapStr(0,o3_textsize,'text'))+text+(o3_textfontclass ? '' : wrapStr(1,o3_textsize)) + '</td></tr></table></td></tr></table>';

	set_background("");
	return txt;
}

// Sets the background picture,padding and lots more. :)
function ol_content_background(text,picture,hasfullhtml) {
	if (hasfullhtml) {
		txt=text;
	} else {
		txt='<table width="'+o3_width+'" border="0" cellpadding="0" cellspacing="0" height="'+o3_height+'"><tr><td colspan="3" height="'+o3_padyt+'"></td></tr><tr><td width="'+o3_padxl+'"></td><td valign="TOP" width="'+(o3_width-o3_padxl-o3_padxr)+(o3_textfontclass ? '" class="'+o3_textfontclass : '')+'">'+(o3_textfontclass ? '' : wrapStr(0,o3_textsize,'text'))+text+(o3_textfontclass ? '' : wrapStr(1,o3_textsize))+'</td><td width="'+o3_padxr+'"></td></tr><tr><td colspan="3" height="'+o3_padyb+'"></td></tr></table>';
	}

	set_background(picture);
	return txt;
}

// Loads a picture into the div.
function set_background(pic) {
	if (pic == "") {
		if (olNs4) {
			over.background.src = null; 
		} else if (over.style) {
			over.style.backgroundImage = "none";
		}
	} else {
		if (olNs4) {
			over.background.src = pic;
		} else if (over.style) {
			over.style.width=o3_width + 'px';
			over.style.backgroundImage = "url("+pic+")";
		}
	}
}

////////
// HANDLING FUNCTIONS
////////
var olShowId=-1;

// Displays the popup
function disp(statustext) {
	runHook("disp", FBEFORE);
	
	if (o3_allowmove == 0) {
		runHook("placeLayer", FREPLACE);
		(olNs6&&olShowId<0) ? olShowId=setTimeout("runHook('showObject', FREPLACE, over)", 1) : runHook("showObject", FREPLACE, over);
		o3_allowmove = (o3_sticky || o3_followmouse==0) ? 0 : 1;
	}
	
	runHook("disp", FAFTER);

	if (statustext != "") self.status = statustext;
}

// Creates the actual popup structure
function createPopup(lyrContent){
	runHook("createPopup", FBEFORE);
	
	if (o3_wrap) {
		var wd,ww,theObj = (olNs4 ? over : over.style);
		theObj.top = theObj.left = ((olIe4&&!olOp) ? 0 : -10000) + (!olNs4 ? 'px' : 0);
		layerWrite(lyrContent);
		wd = (olNs4 ? over.clip.width : over.offsetWidth);
		if (wd > (ww=windowWidth())) {
			lyrContent=lyrContent.replace(/\&nbsp;/g, ' ');
			o3_width=ww;
			o3_wrap=0;
		} 
	}

	layerWrite(lyrContent);
	
	// Have to set o3_width for placeLayer() routine if o3_wrap is turned on
	if (o3_wrap) o3_width=(olNs4 ? over.clip.width : over.offsetWidth);
	
	runHook("createPopup", FAFTER, lyrContent);

	return true;
}

// Decides where we want the popup.
function placeLayer() {
	var placeX, placeY, widthFix = 0;
	
	// HORIZONTAL PLACEMENT, re-arranged to work in Safari
	if (o3_frame.innerWidth) widthFix=18; 
	iwidth = windowWidth();

	// Horizontal scroll offset
	winoffset=(olIe4) ? eval('o3_frame.'+docRoot+'.scrollLeft') : o3_frame.pageXOffset;

	placeX = runHook('horizontalPlacement',FCHAIN,iwidth,winoffset,widthFix);

	// VERTICAL PLACEMENT, re-arranged to work in Safari
	if (o3_frame.innerHeight) {
		iheight=o3_frame.innerHeight;
	} else if (eval('o3_frame.'+docRoot)&&eval("typeof o3_frame."+docRoot+".clientHeight=='number'")&&eval('o3_frame.'+docRoot+'.clientHeight')) { 
		iheight=eval('o3_frame.'+docRoot+'.clientHeight');
	}			

	// Vertical scroll offset
	scrolloffset=(olIe4) ? eval('o3_frame.'+docRoot+'.scrollTop') : o3_frame.pageYOffset;
	placeY = runHook('verticalPlacement',FCHAIN,iheight,scrolloffset);

	// Actually move the object.
	repositionTo(over, placeX, placeY);
}

// Moves the layer
function olMouseMove(e) {
	var e = (e) ? e : event;

	if (e.pageX) {
		o3_x = e.pageX;
		o3_y = e.pageY;
	} else if (e.clientX) {
		o3_x = eval('e.clientX+o3_frame.'+docRoot+'.scrollLeft');
		o3_y = eval('e.clientY+o3_frame.'+docRoot+'.scrollTop');
	}
	
	if (o3_allowmove == 1) runHook("placeLayer", FREPLACE);

	// MouseOut handler
	if (hoveringSwitch && !olNs4 && runHook("cursorOff", FREPLACE)) {
		(olHideDelay ? hideDelay(olHideDelay) : cClick());
		hoveringSwitch = !hoveringSwitch;
	}
}

// Fake function for 3.0 users.
function no_overlib() { return ver3fix; }

// Capture the mouse and chain other scripts.
function olMouseCapture() {
	capExtent = document;
	var fN, str = '', l, k, f, wMv, sS, mseHandler = olMouseMove;
	var re = /function[ ]*(\w*)\(/;
	
	wMv = (!olIe4 && window.onmousemove);
	if (document.onmousemove || wMv) {
		if (wMv) capExtent = window;
		f = capExtent.onmousemove.toString();
		fN = f.match(re);
		if (fN == null) {
			str = f+'(e); ';
		} else if (fN[1] == 'anonymous' || fN[1] == 'olMouseMove' || (wMv && fN[1] == 'onmousemove')) {
			if (!olOp && wMv) {
				l = f.indexOf('{')+1;
				k = f.lastIndexOf('}');
				sS = f.substring(l,k);
				if ((l = sS.indexOf('(')) != -1) {
					sS = sS.substring(0,l).replace(/^\s+/,'').replace(/\s+$/,'');
					if (eval("typeof " + sS + " == 'undefined'")) window.onmousemove = null;
					else str = sS + '(e);';
				}
			}
			if (!str) {
				olCheckMouseCapture = false;
				return;
			}
		} else {
			if (fN[1]) str = fN[1]+'(e); ';
			else {
				l = f.indexOf('{')+1;
				k = f.lastIndexOf('}');
				str = f.substring(l,k) + '\n';
			}
		}
		str += 'olMouseMove(e); ';
		mseHandler = new Function('e', str);
	}

	capExtent.onmousemove = mseHandler;
	if (olNs4) capExtent.captureEvents(Event.MOUSEMOVE);
}

////////
// PARSING FUNCTIONS
////////

// Does the actual command parsing.
function parseTokens(pf, ar) {
	// What the next argument is expected to be.
	var v, i, mode=-1, par = (pf != 'ol_');	
	var fnMark = (par && !ar.length ? 1 : 0);

	for (i = 0; i < ar.length; i++) {
		if (mode < 0) {
			// Arg is maintext,unless its a number between pmStart and pmUpper
			// then its a command.
			if (typeof ar[i] == 'number' && ar[i] > pmStart && ar[i] < pmUpper) {
				fnMark = (par ? 1 : 0);
				i--;   // backup one so that the next block can parse it
			} else {
				switch(pf) {
					case 'ol_':
						ol_text = ar[i].toString();
						break;
					default:
						o3_text=ar[i].toString();  
				}
			}
			mode = 0;
		} else {
			// Note: NS4 doesn't like switch cases with vars.
			if (ar[i] >= pmCount || ar[i]==DONOTHING) { continue; }
			if (ar[i]==INARRAY) { fnMark = 0; eval(pf+'text=ol_texts['+ar[++i]+'].toString()'); continue; }
			if (ar[i]==CAPARRAY) { eval(pf+'cap=ol_caps['+ar[++i]+'].toString()'); continue; }
			if (ar[i]==STICKY) { if (pf!='ol_') eval(pf+'sticky=1'); continue; }
			if (ar[i]==BACKGROUND) { eval(pf+'background="'+ar[++i]+'"'); continue; }
			if (ar[i]==NOCLOSE) { if (pf!='ol_') opt_NOCLOSE(); continue; }
			if (ar[i]==CAPTION) { eval(pf+"cap='"+escSglQuote(ar[++i])+"'"); continue; }
			if (ar[i]==CENTER || ar[i]==LEFT || ar[i]==RIGHT) { eval(pf+'hpos='+ar[i]); if(pf!='ol_') olHautoFlag=1; continue; }
			if (ar[i]==OFFSETX) { eval(pf+'offsetx='+ar[++i]); continue; }
			if (ar[i]==OFFSETY) { eval(pf+'offsety='+ar[++i]); continue; }
			if (ar[i]==FGCOLOR) { eval(pf+'fgcolor="'+ar[++i]+'"'); continue; }
			if (ar[i]==BGCOLOR) { eval(pf+'bgcolor="'+ar[++i]+'"'); continue; }
			if (ar[i]==TEXTCOLOR) { eval(pf+'textcolor="'+ar[++i]+'"'); continue; }
			if (ar[i]==CAPCOLOR) { eval(pf+'capcolor="'+ar[++i]+'"'); continue; }
			if (ar[i]==CLOSECOLOR) { eval(pf+'closecolor="'+ar[++i]+'"'); continue; }
			if (ar[i]==WIDTH) { eval(pf+'width='+ar[++i]); continue; }
			if (ar[i]==BORDER) { eval(pf+'border='+ar[++i]); continue; }
			if (ar[i]==CELLPAD) { i=opt_MULTIPLEARGS(++i,ar,(pf+'cellpad')); continue; }
			if (ar[i]==STATUS) { eval(pf+"status='"+escSglQuote(ar[++i])+"'"); continue; }
			if (ar[i]==AUTOSTATUS) { eval(pf +'autostatus=('+pf+'autostatus == 1) ? 0 : 1'); continue; }
			if (ar[i]==AUTOSTATUSCAP) { eval(pf +'autostatus=('+pf+'autostatus == 2) ? 0 : 2'); continue; }
			if (ar[i]==HEIGHT) { eval(pf+'height='+pf+'aboveheight='+ar[++i]); continue; } // Same param again.
			if (ar[i]==CLOSETEXT) { eval(pf+"close='"+escSglQuote(ar[++i])+"'"); continue; }
			if (ar[i]==SNAPX) { eval(pf+'snapx='+ar[++i]); continue; }
			if (ar[i]==SNAPY) { eval(pf+'snapy='+ar[++i]); continue; }
			if (ar[i]==FIXX) { eval(pf+'fixx='+ar[++i]); continue; }
			if (ar[i]==FIXY) { eval(pf+'fixy='+ar[++i]); continue; }
			if (ar[i]==RELX) { eval(pf+'relx='+ar[++i]); continue; }
			if (ar[i]==RELY) { eval(pf+'rely='+ar[++i]); continue; }
			if (ar[i]==FGBACKGROUND) { eval(pf+'fgbackground="'+ar[++i]+'"'); continue; }
			if (ar[i]==BGBACKGROUND) { eval(pf+'bgbackground="'+ar[++i]+'"'); continue; }
			if (ar[i]==PADX) { eval(pf+'padxl='+ar[++i]); eval(pf+'padxr='+ar[++i]); continue; }
			if (ar[i]==PADY) { eval(pf+'padyt='+ar[++i]); eval(pf+'padyb='+ar[++i]); continue; }
			if (ar[i]==FULLHTML) { if (pf!='ol_') eval(pf+'fullhtml=1'); continue; }
			if (ar[i]==BELOW || ar[i]==ABOVE) { eval(pf+'vpos='+ar[i]); if (pf!='ol_') olVautoFlag=1; continue; }
			if (ar[i]==CAPICON) { eval(pf+'capicon="'+ar[++i]+'"'); continue; }
			if (ar[i]==TEXTFONT) { eval(pf+"textfont='"+escSglQuote(ar[++i])+"'"); continue; }
			if (ar[i]==CAPTIONFONT) { eval(pf+"captionfont='"+escSglQuote(ar[++i])+"'"); continue; }
			if (ar[i]==CLOSEFONT) { eval(pf+"closefont='"+escSglQuote(ar[++i])+"'"); continue; }
			if (ar[i]==TEXTSIZE) { eval(pf+'textsize="'+ar[++i]+'"'); continue; }
			if (ar[i]==CAPTIONSIZE) { eval(pf+'captionsize="'+ar[++i]+'"'); continue; }
			if (ar[i]==CLOSESIZE) { eval(pf+'closesize="'+ar[++i]+'"'); continue; }
			if (ar[i]==TIMEOUT) { eval(pf+'timeout='+ar[++i]); continue; }
			if (ar[i]==FUNCTION) { if (pf=='ol_') { if (typeof ar[i+1]!='number') { v=ar[++i]; ol_function=(typeof v=='function' ? v : null); }} else {fnMark = 0; v = null; if (typeof ar[i+1]!='number') v = ar[++i];  opt_FUNCTION(v); } continue; }
			if (ar[i]==DELAY) { eval(pf+'delay='+ar[++i]); continue; }
			if (ar[i]==HAUTO) { eval(pf+'hauto=('+pf+'hauto == 0) ? 1 : 0'); continue; }
			if (ar[i]==VAUTO) { eval(pf+'vauto=('+pf+'vauto == 0) ? 1 : 0'); continue; }
			if (ar[i]==CLOSECLICK) { eval(pf +'closeclick=('+pf+'closeclick == 0) ? 1 : 0'); continue; }
			if (ar[i]==WRAP) { eval(pf +'wrap=('+pf+'wrap == 0) ? 1 : 0'); continue; }
			if (ar[i]==FOLLOWMOUSE) { eval(pf +'followmouse=('+pf+'followmouse == 1) ? 0 : 1'); continue; }
			if (ar[i]==MOUSEOFF) { eval(pf +'mouseoff=('+pf+'mouseoff==0) ? 1 : 0'); v=ar[i+1]; if (pf != 'ol_' && eval(pf+'mouseoff') && typeof v == 'number' && (v < pmStart || v > pmUpper)) olHideDelay=ar[++i]; continue; }
			if (ar[i]==CLOSETITLE) { eval(pf+"closetitle='"+escSglQuote(ar[++i])+"'"); continue; }
			if (ar[i]==CSSOFF||ar[i]==CSSCLASS) { eval(pf+'css='+ar[i]); continue; }
			if (ar[i]==COMPATMODE) { eval(pf+'compatmode=('+pf+'compatmode==0) ? 1 : 0'); continue; }
			if (ar[i]==FGCLASS) { eval(pf+'fgclass="'+ar[++i]+'"'); continue; }
			if (ar[i]==BGCLASS) { eval(pf+'bgclass="'+ar[++i]+'"'); continue; }
			if (ar[i]==TEXTFONTCLASS) { eval(pf+'textfontclass="'+ar[++i]+'"'); continue; }
			if (ar[i]==CAPTIONFONTCLASS) { eval(pf+'captionfontclass="'+ar[++i]+'"'); continue; }
			if (ar[i]==CLOSEFONTCLASS) { eval(pf+'closefontclass="'+ar[++i]+'"'); continue; }
			i = parseCmdLine(pf, i, ar);
		}
	}

	if (fnMark && o3_function) o3_text = o3_function();
	
	if ((pf == 'o3_') && o3_wrap) {
		o3_width = 0;
		
		var tReg=/<.*\n*>/ig;
		if (!tReg.test(o3_text)) o3_text = o3_text.replace(/[ ]+/g, '&nbsp;');
		if (!tReg.test(o3_cap))o3_cap = o3_cap.replace(/[ ]+/g, '&nbsp;');
	}
	if ((pf == 'o3_') && o3_sticky) {
		if (!o3_close && (o3_frame != ol_frame)) o3_close = ol_close;
		if (o3_mouseoff && (o3_frame == ol_frame)) opt_NOCLOSE(' ');
	}
}


////////
// LAYER FUNCTIONS
////////

// Writes to a layer
function layerWrite(txt) {
	txt += "\n";
	if (olNs4) {
		var lyr = o3_frame.document.layers['overDiv'].document
		lyr.write(txt)
		lyr.close()
	} else if (typeof over.innerHTML != 'undefined') {
		if (olIe5 && isMac) over.innerHTML = '';
		over.innerHTML = txt;
	} else {
		range = o3_frame.document.createRange();
		range.setStartAfter(over);
		domfrag = range.createContextualFragment(txt);
		
		while (over.hasChildNodes()) {
			over.removeChild(over.lastChild);
		}
		
		over.appendChild(domfrag);
	}
}

// Make an object visible
function showObject(obj) {
	runHook("showObject", FBEFORE);

	var theObj=(olNs4 ? obj : obj.style);
	theObj.visibility = 'visible';

	runHook("showObject", FAFTER);
}

// Hides an object
function hideObject(obj) {
	runHook("hideObject", FBEFORE);

	var theObj=(olNs4 ? obj : obj.style);
	if (olNs6 && olShowId>0) { clearTimeout(olShowId); olShowId=0; }
	theObj.visibility = 'hidden';
	theObj.top = theObj.left = ((olIe4&&!olOp) ? 0 : -10000) + (!olNs4 ? 'px' : 0);

	if (o3_timerid > 0) clearTimeout(o3_timerid);
	if (o3_delayid > 0) clearTimeout(o3_delayid);

	o3_timerid = 0;
	o3_delayid = 0;
	self.status = "";

	if (obj.onmouseout||obj.onmouseover) {
		if (olNs4) obj.releaseEvents(Event.MOUSEOUT || Event.MOUSEOVER);
		obj.onmouseout = obj.onmouseover = null;
	}

	runHook("hideObject", FAFTER);
}

// Move a layer
function repositionTo(obj, xL, yL) {
	var theObj=(olNs4 ? obj : obj.style);
	theObj.left = xL + (!olNs4 ? 'px' : 0);
	theObj.top = yL + (!olNs4 ? 'px' : 0);
}

// Check position of cursor relative to overDiv DIVision; mouseOut function
function cursorOff() {
	var left = parseInt(over.style.left);
	var top = parseInt(over.style.top);
	var right = left + (over.offsetWidth >= parseInt(o3_width) ? over.offsetWidth : parseInt(o3_width));
	var bottom = top + (over.offsetHeight >= o3_aboveheight ? over.offsetHeight : o3_aboveheight);

	if (o3_x < left || o3_x > right || o3_y < top || o3_y > bottom) return true;

	return false;
}


////////
// COMMAND FUNCTIONS
////////

// Calls callme or the default function.
function opt_FUNCTION(callme) {
	o3_text = (callme ? (typeof callme=='string' ? (/.+\(.*\)/.test(callme) ? eval(callme) : callme) : callme()) : (o3_function ? o3_function() : 'No Function'));

	return 0;
}

// Handle hovering
function opt_NOCLOSE(unused) {
	if (!unused) o3_close = "";

	if (olNs4) {
		over.captureEvents(Event.MOUSEOUT || Event.MOUSEOVER);
		over.onmouseover = function () { if (o3_timerid > 0) { clearTimeout(o3_timerid); o3_timerid = 0; } }
		over.onmouseout = function (e) { if (olHideDelay) hideDelay(olHideDelay); else cClick(e); }
	} else {
		over.onmouseover = function () {hoveringSwitch = true; if (o3_timerid > 0) { clearTimeout(o3_timerid); o3_timerid =0; } }
	}

	return 0;
}

// Function to scan command line arguments for multiples
function opt_MULTIPLEARGS(i, args, parameter) {
  var k=i, re, pV, str='';

  for(k=i; k<args.length; k++) {
		if(typeof args[k] == 'number' && args[k]>pmStart) break;
		str += args[k] + ',';
	}
	if (str) str = str.substring(0,--str.length);

	k--;  // reduce by one so the for loop this is in works correctly
	pV=(olNs4 && /cellpad/i.test(parameter)) ? str.split(',')[0] : str;
	eval(parameter + '="' + pV + '"');

	return k;
}

// Remove &nbsp; in texts when done.
function nbspCleanup() {
	if (o3_wrap) {
		o3_text = o3_text.replace(/\&nbsp;/g, ' ');
		o3_cap = o3_cap.replace(/\&nbsp;/g, ' ');
	}
}

// Escape embedded single quotes in text strings
function escSglQuote(str) {
  return str.toString().replace(/'/g,"\\'");
}

// Onload handler for window onload event
function OLonLoad_handler(e) {
	var re = /\w+\(.*\)[;\s]+/g, olre = /overlib\(|nd\(|cClick\(/, fn, l, i;

	if(!olLoaded) olLoaded=1;

  // Remove it for Gecko based browsers
	if(window.removeEventListener && e.eventPhase == 3) window.removeEventListener("load",OLonLoad_handler,false);
	else if(window.detachEvent) { // and for IE and Opera 4.x but execute calls to overlib, nd, or cClick()
		window.detachEvent("onload",OLonLoad_handler);
		var fN = document.body.getAttribute('onload');
		if (fN) {
			fN=fN.toString().match(re);
			if (fN && fN.length) {
				for (i=0; i<fN.length; i++) {
					if (/anonymous/.test(fN[i])) continue;
					while((l=fN[i].search(/\)[;\s]+/)) != -1) {
						fn=fN[i].substring(0,l+1);
						fN[i] = fN[i].substring(l+2);
						if (olre.test(fn)) eval(fn);
					}
				}
			}
		}
	}
}

// Wraps strings in Layer Generation Functions with the correct tags
//    endWrap true(if end tag) or false if start tag
//    fontSizeStr - font size string such as '1' or '10px'
//    whichString is being wrapped -- 'text', 'caption', or 'close'
function wrapStr(endWrap,fontSizeStr,whichString) {
	var fontStr, fontColor, isClose=((whichString=='close') ? 1 : 0), hasDims=/[%\-a-z]+$/.test(fontSizeStr);
	fontSizeStr = (olNs4) ? (!hasDims ? fontSizeStr : '1') : fontSizeStr;
	if (endWrap) return (hasDims&&!olNs4) ? (isClose ? '</span>' : '</div>') : '</font>';
	else {
		fontStr='o3_'+whichString+'font';
		fontColor='o3_'+((whichString=='caption')? 'cap' : whichString)+'color';
		return (hasDims&&!olNs4) ? (isClose ? '<span style="font-family: '+quoteMultiNameFonts(eval(fontStr))+'; color: '+eval(fontColor)+'; font-size: '+fontSizeStr+';">' : '<div style="font-family: '+quoteMultiNameFonts(eval(fontStr))+'; color: '+eval(fontColor)+'; font-size: '+fontSizeStr+';">') : '<font face="'+eval(fontStr)+'" color="'+eval(fontColor)+'" size="'+(parseInt(fontSizeStr)>7 ? '7' : fontSizeStr)+'">';
	}
}

// Quotes Multi word font names; needed for CSS Standards adherence in font-family
function quoteMultiNameFonts(theFont) {
	var v, pM=theFont.split(',');
	for (var i=0; i<pM.length; i++) {
		v=pM[i];
		v=v.replace(/^\s+/,'').replace(/\s+$/,'');
		if(/\s/.test(v) && !/['"]/.test(v)) {
			v="\'"+v+"\'";
			pM[i]=v;
		}
	}
	return pM.join();
}

// dummy function which will be overridden 
function isExclusive(args) {
	return false;
}

// Sets cellpadding style string value
function setCellPadStr(parameter) {
	var Str='', j=0, ary = new Array(), top, bottom, left, right;

	Str+='padding: ';
	ary=parameter.replace(/\s+/g,'').split(',');

	switch(ary.length) {
		case 2:
			top=bottom=ary[j];
			left=right=ary[++j];
			break;
		case 3:
			top=ary[j];
			left=right=ary[++j];
			bottom=ary[++j];
			break;
		case 4:
			top=ary[j];
			right=ary[++j];
			bottom=ary[++j];
			left=ary[++j];
			break;
	}

	Str+= ((ary.length==1) ? ary[0] + 'px;' : top + 'px ' + right + 'px ' + bottom + 'px ' + left + 'px;');

	return Str;
}

// function will delay close by time milliseconds
function hideDelay(time) {
	if (time&&!o3_delay) {
		if (o3_timerid > 0) clearTimeout(o3_timerid);

		o3_timerid=setTimeout("cClick()",(o3_timeout=time));
	}
}

// Was originally in the placeLayer() routine; separated out for future ease
function horizontalPlacement(browserWidth, horizontalScrollAmount, widthFix) {
	var placeX, iwidth=browserWidth, winoffset=horizontalScrollAmount;
	var parsedWidth = parseInt(o3_width);

	if (o3_fixx > -1 || o3_relx != null) {
		// Fixed position
		placeX=(o3_relx != null ? ( o3_relx < 0 ? winoffset +o3_relx+ iwidth - parsedWidth - widthFix : winoffset+o3_relx) : o3_fixx);
	} else {  
		// If HAUTO, decide what to use.
		if (o3_hauto == 1) {
			if ((o3_x - winoffset) > (iwidth / 2)) {
				o3_hpos = LEFT;
			} else {
				o3_hpos = RIGHT;
			}
		}  		

		// From mouse
		if (o3_hpos == CENTER) { // Center
			placeX = o3_x+o3_offsetx-(parsedWidth/2);

			if (placeX < winoffset) placeX = winoffset;
		}

		if (o3_hpos == RIGHT) { // Right
			placeX = o3_x+o3_offsetx;

			if ((placeX+parsedWidth) > (winoffset+iwidth - widthFix)) {
				placeX = iwidth+winoffset - parsedWidth - widthFix;
				if (placeX < 0) placeX = 0;
			}
		}
		if (o3_hpos == LEFT) { // Left
			placeX = o3_x-o3_offsetx-parsedWidth;
			if (placeX < winoffset) placeX = winoffset;
		}  	

		// Snapping!
		if (o3_snapx > 1) {
			var snapping = placeX % o3_snapx;

			if (o3_hpos == LEFT) {
				placeX = placeX - (o3_snapx+snapping);
			} else {
				// CENTER and RIGHT
				placeX = placeX+(o3_snapx - snapping);
			}

			if (placeX < winoffset) placeX = winoffset;
		}
	}	

	return placeX;
}

// was originally in the placeLayer() routine; separated out for future ease
function verticalPlacement(browserHeight,verticalScrollAmount) {
	var placeY, iheight=browserHeight, scrolloffset=verticalScrollAmount;
	var parsedHeight=(o3_aboveheight ? parseInt(o3_aboveheight) : (olNs4 ? over.clip.height : over.offsetHeight));

	if (o3_fixy > -1 || o3_rely != null) {
		// Fixed position
		placeY=(o3_rely != null ? (o3_rely < 0 ? scrolloffset+o3_rely+iheight - parsedHeight : scrolloffset+o3_rely) : o3_fixy);
	} else {
		// If VAUTO, decide what to use.
		if (o3_vauto == 1) {
			if ((o3_y - scrolloffset) > (iheight / 2) && o3_vpos == BELOW && (o3_y + parsedHeight + o3_offsety - (scrolloffset + iheight) > 0)) {
				o3_vpos = ABOVE;
			} else if (o3_vpos == ABOVE && (o3_y - (parsedHeight + o3_offsety) - scrolloffset < 0)) {
				o3_vpos = BELOW;
			}
		}

		// From mouse
		if (o3_vpos == ABOVE) {
			if (o3_aboveheight == 0) o3_aboveheight = parsedHeight; 

			placeY = o3_y - (o3_aboveheight+o3_offsety);
			if (placeY < scrolloffset) placeY = scrolloffset;
		} else {
			// BELOW
			placeY = o3_y+o3_offsety;
		} 

		// Snapping!
		if (o3_snapy > 1) {
			var snapping = placeY % o3_snapy;  			

			if (o3_aboveheight > 0 && o3_vpos == ABOVE) {
				placeY = placeY - (o3_snapy+snapping);
			} else {
				placeY = placeY+(o3_snapy - snapping);
			} 			

			if (placeY < scrolloffset) placeY = scrolloffset;
		}
	}

	return placeY;
}

// checks positioning flags
function checkPositionFlags() {
	if (olHautoFlag) olHautoFlag = o3_hauto=0;
	if (olVautoFlag) olVautoFlag = o3_vauto=0;
	return true;
}

// get Browser window width
function windowWidth() {
	var w;
	if (o3_frame.innerWidth) w=o3_frame.innerWidth;
	else if (eval('o3_frame.'+docRoot)&&eval("typeof o3_frame."+docRoot+".clientWidth=='number'")&&eval('o3_frame.'+docRoot+'.clientWidth')) 
		w=eval('o3_frame.'+docRoot+'.clientWidth');
	return w;			
}

// create the div container for popup content if it doesn't exist
function createDivContainer(id,frm,zValue) {
	id = (id || 'overDiv'), frm = (frm || o3_frame), zValue = (zValue || 1000);
	var objRef, divContainer = layerReference(id);

	if (divContainer == null) {
		if (olNs4) {
			divContainer = frm.document.layers[id] = new Layer(window.innerWidth, frm);
			objRef = divContainer;
		} else {
			var body = (olIe4 ? frm.document.all.tags('BODY')[0] : frm.document.getElementsByTagName("BODY")[0]);
			if (olIe4&&!document.getElementById) {
				body.insertAdjacentHTML("beforeEnd",'<div id="'+id+'"></div>');
				divContainer=layerReference(id);
			} else {
				divContainer = frm.document.createElement("DIV");
				divContainer.id = id;
				body.appendChild(divContainer);
			}
			objRef = divContainer.style;
		}

		objRef.position = 'absolute';
		objRef.visibility = 'hidden';
		objRef.zIndex = zValue;
		if (olIe4&&!olOp) objRef.left = objRef.top = '0px';
		else objRef.left = objRef.top =  -10000 + (!olNs4 ? 'px' : 0);
	}

	return divContainer;
}

// get reference to a layer with ID=id
function layerReference(id) {
	return (olNs4 ? o3_frame.document.layers[id] : (document.all ? o3_frame.document.all[id] : o3_frame.document.getElementById(id)));
}
////////
//  UTILITY FUNCTIONS
////////

// Checks if something is a function.
function isFunction(fnRef) {
	var rtn = true;

	if (typeof fnRef == 'object') {
		for (var i = 0; i < fnRef.length; i++) {
			if (typeof fnRef[i]=='function') continue;
			rtn = false;
			break;
		}
	} else if (typeof fnRef != 'function') {
		rtn = false;
	}
	
	return rtn;
}

// Converts an array into an argument string for use in eval.
function argToString(array, strtInd, argName) {
	var jS = strtInd, aS = '', ar = array;
	argName=(argName ? argName : 'ar');
	
	if (ar.length > jS) {
		for (var k = jS; k < ar.length; k++) aS += argName+'['+k+'], ';
		aS = aS.substring(0, aS.length-2);
	}
	
	return aS;
}

// Places a hook in the correct position in a hook point.
function reOrder(hookPt, fnRef, order) {
	var newPt = new Array(), match, i, j;

	if (!order || typeof order == 'undefined' || typeof order == 'number') return hookPt;
	
	if (typeof order=='function') {
		if (typeof fnRef=='object') {
			newPt = newPt.concat(fnRef);
		} else {
			newPt[newPt.length++]=fnRef;
		}
		
		for (i = 0; i < hookPt.length; i++) {
			match = false;
			if (typeof fnRef == 'function' && hookPt[i] == fnRef) {
				continue;
			} else {
				for(j = 0; j < fnRef.length; j++) if (hookPt[i] == fnRef[j]) {
					match = true;
					break;
				}
			}
			if (!match) newPt[newPt.length++] = hookPt[i];
		}

		newPt[newPt.length++] = order;

	} else if (typeof order == 'object') {
		if (typeof fnRef == 'object') {
			newPt = newPt.concat(fnRef);
		} else {
			newPt[newPt.length++] = fnRef;
		}
		
		for (j = 0; j < hookPt.length; j++) {
			match = false;
			if (typeof fnRef == 'function' && hookPt[j] == fnRef) {
				continue;
			} else {
				for (i = 0; i < fnRef.length; i++) if (hookPt[j] == fnRef[i]) {
					match = true;
					break;
				}
			}
			if (!match) newPt[newPt.length++]=hookPt[j];
		}

		for (i = 0; i < newPt.length; i++) hookPt[i] = newPt[i];
		newPt.length = 0;
		
		for (j = 0; j < hookPt.length; j++) {
			match = false;
			for (i = 0; i < order.length; i++) {
				if (hookPt[j] == order[i]) {
					match = true;
					break;
				}
			}
			if (!match) newPt[newPt.length++] = hookPt[j];
		}
		newPt = newPt.concat(order);
	}

	hookPt = newPt;

	return hookPt;
}

////////
//  PLUGIN ACTIVATION FUNCTIONS
////////

// Runs plugin functions to set runtime variables.
function setRunTimeVariables(){
	if (typeof runTime != 'undefined' && runTime.length) {
		for (var k = 0; k < runTime.length; k++) {
			runTime[k]();
		}
	}
}

// Runs plugin functions to parse commands.
function parseCmdLine(pf, i, args) {
	if (typeof cmdLine != 'undefined' && cmdLine.length) { 
		for (var k = 0; k < cmdLine.length; k++) { 
			var j = cmdLine[k](pf, i, args);
			if (j >- 1) {
				i = j;
				break;
			}
		}
	}

	return i;
}

// Runs plugin functions to do things after parse.
function postParseChecks(pf,args){
	if (typeof postParse != 'undefined' && postParse.length) {
		for (var k = 0; k < postParse.length; k++) {
			if (postParse[k](pf,args)) continue;
			return false;  // end now since have an error
		}
	}
	return true;
}


////////
//  PLUGIN REGISTRATION FUNCTIONS
////////

// Registers commands and creates constants.
function registerCommands(cmdStr) {
	if (typeof cmdStr!='string') return;

	var pM = cmdStr.split(',');
	pms = pms.concat(pM);

	for (var i = 0; i< pM.length; i++) {
		eval(pM[i].toUpperCase()+'='+pmCount++);
	}
}

// Registers no-parameter commands
function registerNoParameterCommands(cmdStr) {
	if (!cmdStr && typeof cmdStr != 'string') return;
	pmt=(!pmt) ? cmdStr : pmt + ',' + cmdStr;
}

// Register a function to hook at a certain point.
function registerHook(fnHookTo, fnRef, hookType, optPm) {
	var hookPt, last = typeof optPm;
	
	if (fnHookTo == 'plgIn'||fnHookTo == 'postParse') return;
	if (typeof hookPts[fnHookTo] == 'undefined') hookPts[fnHookTo] = new FunctionReference();

	hookPt = hookPts[fnHookTo];

	if (hookType != null) {
		if (hookType == FREPLACE) {
			hookPt.ovload = fnRef;  // replace normal overlib routine
			if (fnHookTo.indexOf('ol_content_') > -1) hookPt.alt[pms[CSSOFF-1-pmStart]]=fnRef; 

		} else if (hookType == FBEFORE || hookType == FAFTER) {
			var hookPt=(hookType == 1 ? hookPt.before : hookPt.after);

			if (typeof fnRef == 'object') {
				hookPt = hookPt.concat(fnRef);
			} else {
				hookPt[hookPt.length++] = fnRef;
			}

			if (optPm) hookPt = reOrder(hookPt, fnRef, optPm);

		} else if (hookType == FALTERNATE) {
			if (last=='number') hookPt.alt[pms[optPm-1-pmStart]] = fnRef;
		} else if (hookType == FCHAIN) {
			hookPt = hookPt.chain; 
			if (typeof fnRef=='object') hookPt=hookPt.concat(fnRef); // add other functions 
			else hookPt[hookPt.length++]=fnRef;
		}

		return;
	}
}

// Register a function that will set runtime variables.
function registerRunTimeFunction(fn) {
	if (isFunction(fn)) {
		if (typeof fn == 'object') {
			runTime = runTime.concat(fn);
		} else {
			runTime[runTime.length++] = fn;
		}
	}
}

// Register a function that will handle command parsing.
function registerCmdLineFunction(fn){
	if (isFunction(fn)) {
		if (typeof fn == 'object') {
			cmdLine = cmdLine.concat(fn);
		} else {
			cmdLine[cmdLine.length++] = fn;
		}
	}
}

// Register a function that does things after command parsing. 
function registerPostParseFunction(fn){
	if (isFunction(fn)) {
		if (typeof fn == 'object') {
			postParse = postParse.concat(fn);
		} else {
			postParse[postParse.length++] = fn;
		}
	}
}

////////
//  PLUGIN REGISTRATION FUNCTIONS
////////

// Runs any hooks registered.
function runHook(fnHookTo, hookType) {
	var l = hookPts[fnHookTo], k, rtnVal = null, optPm, arS, ar = runHook.arguments;

	if (hookType == FREPLACE) {
		arS = argToString(ar, 2);

		if (typeof l == 'undefined' || !(l = l.ovload)) rtnVal = eval(fnHookTo+'('+arS+')');
		else rtnVal = eval('l('+arS+')');

	} else if (hookType == FBEFORE || hookType == FAFTER) {
		if (typeof l != 'undefined') {
			l=(hookType == 1 ? l.before : l.after);
	
			if (l.length) {
				arS = argToString(ar, 2);
				for (var k = 0; k < l.length; k++) eval('l[k]('+arS+')');
			}
		}
	} else if (hookType == FALTERNATE) {
		optPm = ar[2];
		arS = argToString(ar, 3);

		if (typeof l == 'undefined' || (l = l.alt[pms[optPm-1-pmStart]]) == 'undefined') {
			rtnVal = eval(fnHookTo+'('+arS+')');
		} else {
			rtnVal = eval('l('+arS+')');
		}
	} else if (hookType == FCHAIN) {
		arS=argToString(ar,2);
		l=l.chain;

		for (k=l.length; k > 0; k--) if((rtnVal=eval('l[k-1]('+arS+')'))!=void(0)) break;
	}

	return rtnVal;
}

////////
// OBJECT CONSTRUCTORS
////////

// Object for handling hooks.
function FunctionReference() {
	this.ovload = null;
	this.before = new Array();
	this.after = new Array();
	this.alt = new Array();
	this.chain = new Array();
}

// Object for simple access to the overLIB version used.
// Examples: simpleversion:351 major:3 minor:5 revision:1
function Info(version, prerelease) {
	this.version = version;
	this.prerelease = prerelease;

	this.simpleversion = Math.round(this.version*100);
	this.major = parseInt(this.simpleversion / 100);
	this.minor = parseInt(this.simpleversion / 10) - this.major * 10;
	this.revision = parseInt(this.simpleversion) - this.major * 100 - this.minor * 10;
	this.meets = meets;
}

// checks for Core Version required
function meets(reqdVersion) {
	return (!reqdVersion) ? false : this.simpleversion >= Math.round(100*parseFloat(reqdVersion));
}


////////
// STANDARD REGISTRATIONS
////////
registerHook("ol_content_simple", ol_content_simple, FALTERNATE, CSSOFF);
registerHook("ol_content_caption", ol_content_caption, FALTERNATE, CSSOFF);
registerHook("ol_content_background", ol_content_background, FALTERNATE, CSSOFF);
registerHook("ol_content_simple", ol_content_simple, FALTERNATE, CSSCLASS);
registerHook("ol_content_caption", ol_content_caption, FALTERNATE, CSSCLASS);
registerHook("ol_content_background", ol_content_background, FALTERNATE, CSSCLASS);
registerPostParseFunction(checkPositionFlags);
registerHook("hideObject", nbspCleanup, FAFTER);
registerHook("horizontalPlacement", horizontalPlacement, FCHAIN);
registerHook("verticalPlacement", verticalPlacement, FCHAIN);
if (olNs4||(olIe5&&isMac)||olKq) olLoaded=1;
registerNoParameterCommands('sticky,autostatus,autostatuscap,fullhtml,hauto,vauto,closeclick,wrap,followmouse,mouseoff,compatmode');
///////
// ESTABLISH MOUSECAPTURING
///////

// Capture events, alt. diffuses the overlib function.
var olCheckMouseCapture=true;
if ((olNs4 || olNs6 || olIe4)) {
	olMouseCapture();
} else {
	overlib = no_overlib;
	nd = no_overlib;
	ver3fix = true;
}

// Copyright й 2000 by Apple Computer, Inc., All Rights Reserved.
//
// You may incorporate this Apple sample code into your own code
// without restriction. This Apple sample code has been provided "AS IS"
// and the responsibility for its operation is yours. You may redistribute
// this code, but you are not permitted to redistribute it as
// "Apple sample code" after having made changes.
//
// ************************
// layer utility routines *
// ************************

function getStyleObject(objectId) {
    // cross-browser function to get an object's style object given its id
    if(document.getElementById && document.getElementById(objectId)) {
	// W3C DOM
	return document.getElementById(objectId).style;
    } else if (document.all && document.all(objectId)) {
	// MSIE 4 DOM
	return document.all(objectId).style;
    } else if (document.layers && document.layers[objectId]) {
	// NN 4 DOM.. note: this won't find nested layers
	return document.layers[objectId];
    } else {
	return false;
    }
} // getStyleObject

function changeObjectVisibility(objectId, newVisibility) {
    // get a reference to the cross-browser style object and make sure the object exists
    var styleObject = getStyleObject(objectId);
    if(styleObject) {
	styleObject.visibility = newVisibility;
	return true;
    } else {
	// we couldn't find the object, so we can't change its visibility
	return false;
    }
} // changeObjectVisibility

function moveObject(objectId, newXCoordinate, newYCoordinate) {
    // get a reference to the cross-browser style object and make sure the object exists
    var styleObject = getStyleObject(objectId);
    if(styleObject) {
	styleObject.left = newXCoordinate;
	styleObject.top = newYCoordinate;
	return true;
    } else {
	// we couldn't find the object, so we can't very well move it
	return false;
    }
} // moveObject



/*--------------------------------------------------|

| dTree 2.05 | www.destroydrop.com/javascript/tree/ |

|---------------------------------------------------|

| Copyright (c) 2002-2003 Geir LandrЎ               |

|                                                   |

| This script can be used freely as long as all     |

| copyright messages are intact.                    |

|                                                   |

| Updated: 17.04.2003                               |

|--------------------------------------------------*/



// Node object

function tNode(id, pid, name, url, title, target, icon, iconOpen, open) {

	this.id = id;

	this.pid = pid;

	this.name = name;

	this.url = url;

	this.title = title;

	this.target = target;

	this.icon = icon;

	this.iconOpen = iconOpen;

	this._io = open || false;

	this._is = false;

	this._ls = false;

	this._hc = false;

	this._ai = 0;

	this._p;

};



// Tree object

function dTree(objName) {

	this.config = {

		target					: null,

		folderLinks			: true,

		useSelection		: true,

		useCookies			: false,

		useLines				: true,

		useIcons				: true,

		useStatusText		: false,

		closeSameLevel	: false,

		inOrder					: false

	}

	this.icon = {

		root		: 'dtree/img/base.gif',

		folder		: 'dtree/img/folder.gif',

		folderOpen	: 'dtree/img/folderopen.gif',

		node		: 'dtree/img/page.gif',

		empty		: 'dtree/img/empty.gif',

		line		: 'dtree/img/line.gif',
		
		join		: 'dtree/img/join.gif',

		joinBottom	: 'dtree/img/joinbottom.gif',

		plus		: 'dtree/img/plus.gif',

		plusBottom	: 'dtree/img/plusbottom.gif',

		minus		: 'dtree/img/minus.gif',

		minusBottom	: 'dtree/img/minusbottom.gif',

		nlPlus		: 'dtree/img/nolines_plus.gif',

		nlMinus		: 'dtree/img/nolines_minus.gif'

	};

	this.obj = objName;

	this.aNodes = [];

	this.aIndent = [];

	this.root = new tNode(-1);

	this.selectedNode = null;

	this.selectedFound = false;

	this.completed = false;

};



// Adds a new node to the node array

dTree.prototype.add = function(id, pid, name, url, title, target, icon, iconOpen, open) {

	this.aNodes[this.aNodes.length] = new tNode(id, pid, name, url, title, target, icon, iconOpen, open);

};



// Open/close all nodes

dTree.prototype.openAll = function() {

	this.oAll(true);

};

dTree.prototype.closeAll = function() {

	this.oAll(false);

};



// Outputs the tree to the page

dTree.prototype.toString = function() {

	var str = '<div class="dtree">\n';

	if (document.getElementById) {

		if (this.config.useCookies) this.selectedNode = this.getSelected();

		str += this.addNode(this.root);

	} else str += 'Browser not supported.';

	str += '</div>';

	if (!this.selectedFound) this.selectedNode = null;

	this.completed = true;

	return str;

};



// Creates the tree structure

dTree.prototype.addNode = function(pNode) {

	var str = '';

	var n=0;

	if (this.config.inOrder) n = pNode._ai;

	for (n; n<this.aNodes.length; n++) {

		if (this.aNodes[n].pid == pNode.id) {

			var cn = this.aNodes[n];

			cn._p = pNode;

			cn._ai = n;

			this.setCS(cn);

			if (!cn.target && this.config.target) cn.target = this.config.target;

			if (cn._hc && !cn._io && this.config.useCookies) cn._io = this.isOpen(cn.id);

			if (!this.config.folderLinks && cn._hc) cn.url = null;

			if (this.config.useSelection && cn.id == this.selectedNode && !this.selectedFound) {

					cn._is = true;

					this.selectedNode = n;

					this.selectedFound = true;

			}

			str += this.node(cn, n);

			if (cn._ls) break;

		}

	}

	return str;

};



// Creates the node icon, url and text

dTree.prototype.node = function(node, nodeId) {

	var str = '<div class="dTreeNode">' + this.indent(node, nodeId);

	if (this.config.useIcons) {

		if (!node.icon) node.icon = (this.root.id == node.pid) ? this.icon.root : ((node._hc) ? this.icon.folder : this.icon.node);

		if (!node.iconOpen) node.iconOpen = (node._hc) ? this.icon.folderOpen : this.icon.node;

		if (this.root.id == node.pid) {

			node.icon = this.icon.root;

			node.iconOpen = this.icon.root;

		}

		str += '<img id="i' + this.obj + nodeId + '" src="' + ((node._io) ? node.iconOpen : node.icon) + '" alt="" />';

	}

	if (node.url) {

		str += '<a id="s' + this.obj + nodeId + '" class="' + ((this.config.useSelection) ? ((node._is ? 'nodeSel' : 'node')) : 'node') + '" href="' + node.url + '"';

		if (node.title) str += ' title="' + node.title + '"';

		if (node.target) str += ' target="' + node.target + '"';

		if (this.config.useStatusText) str += ' onmouseover="window.status=\'' + node.name + '\';return true;" onmouseout="window.status=\'\';return true;" ';

		if (this.config.useSelection && ((node._hc && this.config.folderLinks) || !node._hc))

			str += ' onclick="javascript: ' + this.obj + '.s(' + nodeId + ');"';

		str += '>';

	}

	else if ((!this.config.folderLinks || !node.url) && node._hc && node.pid != this.root.id)

		str += '<a href="javascript: ' + this.obj + '.o(' + nodeId + ');" class="node">';

	str += node.name;

	if (node.url || ((!this.config.folderLinks || !node.url) && node._hc)) str += '</a>';

	str += '</div>';

	if (node._hc) {

		str += '<div id="d' + this.obj + nodeId + '" class="clip" style="display:' + ((this.root.id == node.pid || node._io) ? 'block' : 'none') + ';">';

		str += this.addNode(node);

		str += '</div>';

	}

	this.aIndent.pop();

	return str;

};



// Adds the empty and line icons

dTree.prototype.indent = function(node, nodeId) {

	var str = '';

	if (this.root.id != node.pid) {

		for (var n=0; n<this.aIndent.length; n++)

			str += '<img src="' + ( (this.aIndent[n] == 1 && this.config.useLines) ? this.icon.line : this.icon.empty ) + '" alt="" />';

		(node._ls) ? this.aIndent.push(0) : this.aIndent.push(1);

		if (node._hc) {

			str += '<a href="javascript: ' + this.obj + '.o(' + nodeId + ');"><img id="j' + this.obj + nodeId + '" src="';

			if (!this.config.useLines) str += (node._io) ? this.icon.nlMinus : this.icon.nlPlus;

			else str += ( (node._io) ? ((node._ls && this.config.useLines) ? this.icon.minusBottom : this.icon.minus) : ((node._ls && this.config.useLines) ? this.icon.plusBottom : this.icon.plus ) );

			str += '" alt="" /></a>';

		} else str += '<img src="' + ( (this.config.useLines) ? ((node._ls) ? this.icon.joinBottom : this.icon.join ) : this.icon.empty) + '" alt="" />';

	}

	return str;

};



// Checks if a node has any children and if it is the last sibling

dTree.prototype.setCS = function(node) {

	var lastId;

	for (var n=0; n<this.aNodes.length; n++) {

		if (this.aNodes[n].pid == node.id) node._hc = true;

		if (this.aNodes[n].pid == node.pid) lastId = this.aNodes[n].id;

	}

	if (lastId==node.id) node._ls = true;

};



// Returns the selected node

dTree.prototype.getSelected = function() {

	var sn = this.getCookie('cs' + this.obj);

	return (sn) ? sn : null;

};



// Highlights the selected node

dTree.prototype.s = function(id) {

	if (!this.config.useSelection) return;

	var cn = this.aNodes[id];

	if (cn._hc && !this.config.folderLinks) return;

	if (this.selectedNode != id) {

		if (this.selectedNode || this.selectedNode==0) {

			eOld = document.getElementById("s" + this.obj + this.selectedNode);

			eOld.className = "node";

		}

		eNew = document.getElementById("s" + this.obj + id);

		eNew.className = "nodeSel";

		this.selectedNode = id;

		if (this.config.useCookies) this.setCookie('cs' + this.obj, cn.id);

	}

};



// Toggle Open or close

dTree.prototype.o = function(id) {

	var cn = this.aNodes[id];

	this.nodeStatus(!cn._io, id, cn._ls);

	cn._io = !cn._io;

	if (this.config.closeSameLevel) this.closeLevel(cn);

	if (this.config.useCookies) this.updateCookie();

};



// Open or close all nodes

dTree.prototype.oAll = function(status) {

	for (var n=0; n<this.aNodes.length; n++) {

		if (this.aNodes[n]._hc && this.aNodes[n].pid != this.root.id) {

			this.nodeStatus(status, n, this.aNodes[n]._ls)

			this.aNodes[n]._io = status;

		}

	}

	if (this.config.useCookies) this.updateCookie();

};



// Opens the tree to a specific node

dTree.prototype.openTo = function(nId, bSelect, bFirst) {

	if (!bFirst) {

		for (var n=0; n<this.aNodes.length; n++) {

			if (this.aNodes[n].id == nId) {

				nId=n;

				break;

			}

		}

	}

	var cn=this.aNodes[nId];

	if (cn.pid==this.root.id || !cn._p) return;

	cn._io = true;

	cn._is = bSelect;

	if (this.completed && cn._hc) this.nodeStatus(true, cn._ai, cn._ls);

	if (this.completed && bSelect) this.s(cn._ai);

	else if (bSelect) this._sn=cn._ai;

	this.openTo(cn._p._ai, false, true);

};



// Closes all nodes on the same level as certain node

dTree.prototype.closeLevel = function(node) {

	for (var n=0; n<this.aNodes.length; n++) {

		if (this.aNodes[n].pid == node.pid && this.aNodes[n].id != node.id && this.aNodes[n]._hc) {

			this.nodeStatus(false, n, this.aNodes[n]._ls);

			this.aNodes[n]._io = false;

			this.closeAllChildren(this.aNodes[n]);

		}

	}

}



// Closes all children of a node

dTree.prototype.closeAllChildren = function(node) {

	for (var n=0; n<this.aNodes.length; n++) {

		if (this.aNodes[n].pid == node.id && this.aNodes[n]._hc) {

			if (this.aNodes[n]._io) this.nodeStatus(false, n, this.aNodes[n]._ls);

			this.aNodes[n]._io = false;

			this.closeAllChildren(this.aNodes[n]);		

		}

	}

}



// Change the status of a node(open or closed)

dTree.prototype.nodeStatus = function(status, id, bottom) {

	eDiv	= document.getElementById('d' + this.obj + id);

	eJoin	= document.getElementById('j' + this.obj + id);

	if (this.config.useIcons) {

		eIcon	= document.getElementById('i' + this.obj + id);

		eIcon.src = (status) ? this.aNodes[id].iconOpen : this.aNodes[id].icon;

	}

	eJoin.src = (this.config.useLines)?

	((status)?((bottom)?this.icon.minusBottom:this.icon.minus):((bottom)?this.icon.plusBottom:this.icon.plus)):

	((status)?this.icon.nlMinus:this.icon.nlPlus);

	eDiv.style.display = (status) ? 'block': 'none';

};





// [Cookie] Clears a cookie

dTree.prototype.clearCookie = function() {

	var now = new Date();

	var yesterday = new Date(now.getTime() - 1000 * 60 * 60 * 24);

	this.setCookie('co'+this.obj, 'cookieValue', yesterday);

	this.setCookie('cs'+this.obj, 'cookieValue', yesterday);

};



// [Cookie] Sets value in a cookie

dTree.prototype.setCookie = function(cookieName, cookieValue, expires, path, domain, secure) {

	document.cookie =

		escape(cookieName) + '=' + escape(cookieValue)

		+ (expires ? '; expires=' + expires.toGMTString() : '')

		+ (path ? '; path=' + path : '')

		+ (domain ? '; domain=' + domain : '')

		+ (secure ? '; secure' : '');

};



// [Cookie] Gets a value from a cookie

dTree.prototype.getCookie = function(cookieName) {

	var cookieValue = '';

	var posName = document.cookie.indexOf(escape(cookieName) + '=');

	if (posName != -1) {

		var posValue = posName + (escape(cookieName) + '=').length;

		var endPos = document.cookie.indexOf(';', posValue);

		if (endPos != -1) cookieValue = unescape(document.cookie.substring(posValue, endPos));

		else cookieValue = unescape(document.cookie.substring(posValue));

	}

	return (cookieValue);

};



// [Cookie] Returns ids of open nodes as a string

dTree.prototype.updateCookie = function() {

	var str = '';

	for (var n=0; n<this.aNodes.length; n++) {

		if (this.aNodes[n]._io && this.aNodes[n].pid != this.root.id) {

			if (str) str += '.';

			str += this.aNodes[n].id;

		}

	}

	this.setCookie('co' + this.obj, str);

};



// [Cookie] Checks if a node id is in a cookie

dTree.prototype.isOpen = function(id) {

	var aOpen = this.getCookie('co' + this.obj).split('.');

	for (var n=0; n<aOpen.length; n++)

		if (aOpen[n] == id) return true;

	return false;

};



// If Push and pop is not implemented by the browser

if (!Array.prototype.push) {

	Array.prototype.push = function array_push() {

		for(var i=0;i<arguments.length;i++)

			this[this.length]=arguments[i];

		return this.length;

	}

};

if (!Array.prototype.pop) {

	Array.prototype.pop = function array_pop() {

		lastElement = this[this.length-1];

		this.length = Math.max(this.length-1,0);

		return lastElement;

	}

};

function get_css(rule_name, stylesheet, delete_flag) {
	if (!document.styleSheets) return false;
	rule_name = rule_name.toLowerCase(); stylesheet = stylesheet || 0;
	for (var i = stylesheet; i < document.styleSheets.length; i++) { 
		var styleSheet = document.styleSheets[i]; css_rules = document.styleSheets[i].cssRules || document.styleSheets[i].rules;
		if(!css_rules) continue;
		var j = 0;
		do {
			if(css_rules[j].selectorText.toLowerCase() == rule_name) {
				if(delete_flag == true) {
					if(document.styleSheets[i].removeRule) document.styleSheets[i].removeRule(j);
					if(document.styleSheets[i].deleteRule) document.styleSheets[i].deleteRule(j);
					return true;
				}
				else return css_rules[j];
			}
		}
		while (css_rules[++j]);
	}
	return false;
}
function add_css(rule_name, stylesheet) {
	rule_name = rule_name.toLowerCase(); stylesheet = stylesheet || 0;
	if (!document.styleSheets || get_css(rule_name, stylesheet)) return false;
	(document.styleSheets[stylesheet].addRule) ? document.styleSheets[stylesheet].addRule(rule_name, null, 0) : document.styleSheets[stylesheet].insertRule(rule_name+' { }', 0);
	return get_css(rule_name);
}
function get_sheet_num (href_name) {
	if (!document.styleSheets) return false;
	for (var i = 0; i < document.styleSheets.length; i++) { if(document.styleSheets[i].href && document.styleSheets[i].href.toString().match(href_name)) return i; } 
	return false;
}
function remove_css(rule_name, stylesheet) { return get_css(rule_name, stylesheet, true); }

function add_sheet(url, media) {
	if(document.createStyleSheet) {
		document.createStyleSheet(url);
	}
	else {
		var newSS	= document.createElement('link');
		newSS.rel	= 'stylesheet';
		newSS.type	= 'text/css';
		newSS.media	= media || "all";

		newSS.href	= url;
		// var styles	= "@import url(' " + url + " ');";
		// newSS.href	='data:text/css,'+escape(styles);
		document.getElementsByTagName("head")[0].appendChild(newSS);
	}
}
/*
 * jsTree 0.9.8
 * http://jstree.com/
 *
 * Copyright (c) 2009 Ivan Bozhanov (vakata.com)
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Date: 2009-05-15
 *
 */

(function($) {
	// jQuery plugin
	$.fn.tree = function (opts) {
		return this.each(function() {
			var conf = $.extend({},opts);
			if(tree_component.inst && tree_component.inst[$(this).attr('id')]) tree_component.inst[$(this).attr('id')].destroy();
			if(conf !== false) new tree_component().init(this, conf);
		});
	};
	$.tree_create = function() {
		return new tree_component();
	};
	$.tree_focused = function() {
		return tree_component.inst[tree_component.focused];
	};
	$.tree_reference = function(id) {
		return tree_component.inst[id] || null;
	};

	// rollback
	$.tree_rollback = function(data) {
		for(var i in data) {
			if(typeof data[i] == "function") continue;
			var tmp = tree_component.inst[i];
			var lock = !tmp.locked;

			// if not locked - lock the tree
			if(lock) tmp.lock(true);
			// Cancel ongoing rename
			if(tmp.inp) tmp.inp.val("").blur();
			tmp.context.append = false;
			tmp.container.html(data[i].html).find(".dragged").removeClass("dragged").end().find("div.context").remove();

			if(data[i].selected) {
				tmp.selected = $("#" + data[i].selected);
				tmp.selected_arr = [];
				tmp.container
					.find("a.clicked").each( function () {
						tmp.selected_arr.push(tmp.get_node(this));
					});
			}
			// if this function set the lock - unlock
			if(lock) tmp.lock(false);

			delete lock;
			delete tmp;
		}
	};

	// core
	function tree_component () {
		// instance manager
		if(typeof tree_component.inst == "undefined") {
			tree_component.cntr = 0;
			tree_component.inst = {};

			// DRAG'N'DROP STUFF
			tree_component.drag_drop = {
				isdown		: false,	// Is there a drag
				drag_node	: false,	// The actual node
				drag_help	: false,	// The helper

				init_x		: false,
				init_y		: false,
				moving		: false,

				origin_tree	: false,
				marker		: false,

				move_type	: false,	// before, after or inside
				ref_node	: false,	// reference node
				appended	: false,	// is helper appended

				foreign		: false,	// Is the dragged node a foreign one
				droppable	: [],		// Array of classes that can be dropped onto the tree

				open_time	: false,	// Timeout for opening nodes
				scroll_time	: false		// Timeout for scrolling
			};
			// listening for clicks on foreign nodes
			tree_component.mousedown = function(event) {
				var tmp = $(event.target);
				if(tree_component.drag_drop.droppable.length && tmp.is("." + tree_component.drag_drop.droppable.join(", .")) ) {
					tree_component.drag_drop.drag_help	= $("<div id='jstree-dragged' class='tree tree-default'><ul><li class='last dragged foreign " + event.target.className + "'><a href='#'>" + tmp.text() + "</a></li></ul></div>");
					tree_component.drag_drop.drag_node	= tree_component.drag_drop.drag_help.find("li:eq(0)");
					tree_component.drag_drop.isdown		= true;
					tree_component.drag_drop.foreign	= tmp;
					tmp.blur();
					event.preventDefault(); 
					event.stopPropagation();
					return false;
				}
				event.stopPropagation();
				return true;
			};
			tree_component.mouseup = function(event) {
				var tmp = tree_component.drag_drop;
				if(tmp.open_time)	clearTimeout(tmp.open_time);
				if(tmp.scroll_time)	clearTimeout(tmp.scroll_time);
				if(tmp.foreign === false && tmp.drag_node && tmp.drag_node.size()) {
					tmp.drag_help.remove();
					if(tmp.move_type) {
						var tree1 = tree_component.inst[tmp.ref_node.parents(".tree:eq(0)").attr("id")];
						if(tree1) tree1.moved(tmp.origin_tree.container.find("li.dragged"), tmp.ref_node, tmp.move_type, false, (tmp.origin_tree.settings.rules.drag_copy == "on" || (tmp.origin_tree.settings.rules.drag_copy == "ctrl" && event.ctrlKey) ) );
					}
					tmp.move_type	= false;
					tmp.ref_node	= false;
				}
				if(tmp.drag_node && tmp.foreign !== false) {
					tmp.drag_help.remove();
					if(tmp.move_type) {
						var tree1 = tree_component.inst[tmp.ref_node.parents(".tree:eq(0)").attr("id")];
						if(tree1) tree1.settings.callback.ondrop.call(null, tmp.foreign.get(0), tree1.get_node(tmp.ref_node).get(0), tmp.move_type, tree1);
					}
					tmp.foreign		= false;
					tmp.move_type	= false;
					tmp.ref_node	= false;
				}
				// RESET EVERYTHING
				tree_component.drag_drop.marker.hide();
				tmp.drag_help	= false;
				tmp.drag_node	= false;
				tmp.isdown		= false;
				tmp.init_x		= false;
				tmp.init_y		= false;
				tmp.moving		= false;
				tmp.appended	= false;
				$("li.dragged").removeClass("dragged");
				tmp.origin_tree	= false;
				event.preventDefault(); 
				event.stopPropagation();
				return false;
			};
			tree_component.mousemove = function(event) {
				var tmp		= tree_component.drag_drop;

				if(tmp.isdown) {
					if(!tmp.moving && Math.abs(tmp.init_x - event.pageX) < 5 && Math.abs(tmp.init_y - event.pageY) < 5) {
						event.preventDefault();
						event.stopPropagation();
						return false;
					}
					else tree_component.drag_drop.moving = true;

					if(tmp.open_time) clearTimeout(tmp.open_time);
					if(!tmp.appended) {
						if(tmp.foreign !== false) tmp.origin_tree = $.tree_focused();
						$("body").append(tmp.drag_help);
						tmp.w = tmp.drag_help.width();
						tmp.appended = true;
					}
					tmp.drag_help.css({ "left" : (event.pageX - (tmp.origin_tree.settings.ui.rtl ? tmp.w : -5 ) ), "top" : (event.pageY + 15) });

					if(event.target.tagName == "IMG" && event.target.id == "marker") return false;

					var et = $(event.target);
					var cnt = et.is(".tree") ? et : et.parents(".tree:eq(0)");

					// if not moving over a tree
					if(cnt.size() == 0 || !tree_component.inst[cnt.attr("id")]) {
						if(tmp.scroll_time) clearTimeout(tmp.scroll_time);
						if(tmp.drag_help.find("IMG").size() == 0) {
							tmp.drag_help.find("li:eq(0)").append("<img style='position:absolute; " + (tmp.origin_tree.settings.ui.rtl ? "right" : "left" ) + ":4px; top:0px; background:white; padding:2px;' src='" + tmp.origin_tree.settings.ui.theme_path + "remove.png' />");
						}
						tmp.move_type	= false;
						tmp.ref_node	= false;
						tree_component.drag_drop.marker.hide();
						return false;
					}

					var tree2 = tree_component.inst[cnt.attr("id")];
					tree2.off_height();

					// if moving over another tree and multitree is false
					if( tmp.foreign === false && tmp.origin_tree.container.get(0) != tree2.container.get(0) && (!tmp.origin_tree.settings.rules.multitree || !tree2.settings.rules.multitree) ) {
						if(tmp.drag_help.find("IMG").size() == 0) {
							tmp.drag_help.find("li:eq(0)").append("<img style='position:absolute; " + (tmp.origin_tree.settings.ui.rtl ? "right" : "left" ) + ":4px; top:0px; background:white; padding:2px;' src='" + tmp.origin_tree.settings.ui.theme_path + "remove.png' />");
						}
						tmp.move_type	= false;
						tmp.ref_node	= false;
						tree_component.drag_drop.marker.hide();
						return false;
					}

					if(tmp.scroll_time) clearTimeout(tmp.scroll_time);
					tmp.scroll_time = setTimeout( function() { tree2.scrollCheck(event.pageX,event.pageY); }, 50);

					var mov = false;
					var st = cnt.scrollTop();

					if(event.target.tagName == "A" ) {
						// just in case if hover is over the draggable
						if(et.is("#jstree-dragged")) return false;
						if(tree2.get_node(event.target).hasClass("closed")) {
							tmp.open_time = setTimeout( function () { tree2.open_branch(et); }, 500);
						}

						var et_off = et.offset();
						var goTo = { 
							x : (et_off.left - 1),
							y : (event.pageY - et_off.top)
						};

						if(cnt.children("ul:eq(0)").hasClass("rtl")) goTo.x += et.width() - 8;
						var arr = [];

						if(goTo.y < tree2.li_height/3 + 1 )			arr = ["before","inside","after"];
						else if(goTo.y > tree2.li_height*2/3 - 1 )	arr = ["after","inside","before"];
						else {
							if(goTo.y < tree2.li_height/2)			arr = ["inside","before","after"];
							else									arr = ["inside","after","before"];
						}
						var ok	= false;
						$.each(arr, function(i, val) {
							if(tree2.checkMove(tmp.origin_tree.container.find("li.dragged"), et, val)) {
								mov = val;
								ok = true;
								return false;
							}
						});
						if(ok) {
							switch(mov) {
								case "before":
									goTo.y = et_off.top - 2;
									if(cnt.children("ul:eq(0)").hasClass("rtl"))	{ tree_component.drag_drop.marker.attr("src", tree2.settings.ui.theme_path + "marker_rtl.gif").width(40); }
									else											{ tree_component.drag_drop.marker.attr("src", tree2.settings.ui.theme_path + "marker.gif").width(40); }
									break;
								case "after":
									goTo.y = et_off.top - 2 + tree2.li_height;
									if(cnt.children("ul:eq(0)").hasClass("rtl"))	{ tree_component.drag_drop.marker.attr("src", tree2.settings.ui.theme_path + "marker_rtl.gif").width(40); }
									else											{ tree_component.drag_drop.marker.attr("src", tree2.settings.ui.theme_path + "marker.gif").width(40); }
									break;
								case "inside":
									goTo.x -= 2;
									if(cnt.children("ul:eq(0)").hasClass("rtl")) {
										goTo.x += 36;
									}
									goTo.y = et_off.top - 2 + tree2.li_height/2;
									tree_component.drag_drop.marker.attr("src", tree2.settings.ui.theme_path + "plus.gif").width(11);
									break;
							}
							tmp.move_type	= mov;
							tmp.ref_node	= $(event.target);
							tmp.drag_help.find("IMG").remove();
							tree_component.drag_drop.marker.css({ "left" : goTo.x , "top" : goTo.y }).show();
						}
					}

					if( (et.is(".tree") || et.is("ul") ) && et.find("li:eq(0)").size() == 0) {
						var et_off = et.offset();
						tmp.move_type	= "inside";
						tmp.ref_node	= cnt.children("ul:eq(0)");
						tmp.drag_help.find("IMG").remove();
						tree_component.drag_drop.marker.attr("src", tree2.settings.ui.theme_path + "plus.gif").width(11);
						tree_component.drag_drop.marker.css({ "left" : et_off.left + ( cnt.children("ul:eq(0)").hasClass("rtl") ? (cnt.width() - 10) : 10 ) , "top" : et_off.top + 15 }).show();
					}
					else if(event.target.tagName != "A" || !ok) {
						if(tmp.drag_help.find("IMG").size() == 0) {
							tmp.drag_help.find("li:eq(0)").append("<img style='position:absolute; " + (tmp.origin_tree.settings.ui.rtl ? "right" : "left" ) + ":4px; top:0px; background:white; padding:2px;' src='" + tmp.origin_tree.settings.ui.theme_path + "remove.png' />");
						}
						tmp.move_type	= false;
						tmp.ref_node	= false;
						tree_component.drag_drop.marker.hide();
					}
					event.preventDefault();
					event.stopPropagation();
					return false;
				}
				return true;
			};
		};
		return {
			cntr : ++tree_component.cntr,
			settings : {
				data	: {
					type	: "predefined",	// ENUM [json, xml_flat, xml_nested, predefined]
					method	: "GET",		// HOW TO REQUEST FILES
					async	: false,		// BOOL - async loading onopen
					async_data : function (NODE, TREE_OBJ) { return { id : $(NODE).attr("id") || 0 } }, // PARAMETERS PASSED TO SERVER
					url		: false,		// FALSE or STRING - url to document to be used (async or not)
					json	: false,		// FALSE or OBJECT if type is JSON and async is false - the tree dump as json
					xml		: false			// FALSE or STRING if type is XML_FLAT or XML_NESTED and async is false - a string to generate the tree from
				},
				selected	: false,		// FALSE or STRING or ARRAY
				opened		: [],			// ARRAY OF INITIALLY OPENED NODES
				languages	: [],			// ARRAY of string values (which will be used as CSS classes - so they must be valid)
				path		: false,		// FALSE or STRING (if false - will be autodetected)
				cookies		: false,		// FALSE or OBJECT (prefix, open, selected, opts - from jqCookie - expires, path, domain, secure)
				ui		: {
					dots		: true,		// BOOL - dots or no dots
					rtl			: false,	// BOOL - is the tree right-to-left
					animation	: 0,		// INT - duration of open/close animations in miliseconds
					hover_mode	: true,		// SHOULD get_* functions chage focus or change hovered item
					scroll_spd	: 4,
					theme_path	: false,	// Path to themes
					theme_name	: "default",// Name of theme
					context		: [ 
						{
							id		: "create",
							label	: "Create", 
							icon	: "create.png",
							visible	: function (NODE, TREE_OBJ) { if(NODE.length != 1) return false; return TREE_OBJ.check("creatable", NODE); }, 
							action	: function (NODE, TREE_OBJ) { TREE_OBJ.create(false, TREE_OBJ.get_node(NODE[0])); } 
						},
						"separator",
						{ 
							id		: "rename",
							label	: "Rename", 
							icon	: "rename.png",
							visible	: function (NODE, TREE_OBJ) { if(NODE.length != 1) return false; return TREE_OBJ.check("renameable", NODE); }, 
							action	: function (NODE, TREE_OBJ) { TREE_OBJ.rename(NODE); } 
						},
						{ 
							id		: "delete",
							label	: "Delete",
							icon	: "remove.png",
							visible	: function (NODE, TREE_OBJ) { var ok = true; $.each(NODE, function () { if(TREE_OBJ.check("deletable", this) == false) ok = false; return false; }); return ok; }, 
							action	: function (NODE, TREE_OBJ) { $.each(NODE, function () { TREE_OBJ.remove(this); }); } 
						}
					]
				},
				rules	: {
					multiple	: false,	// FALSE | CTRL | ON - multiple selection off/ with or without holding Ctrl
					metadata	: false,	// FALSE or STRING - attribute name (use metadata plugin)
					type_attr	: "rel",	// STRING attribute name (where is the type stored if no metadata)
					multitree	: false,	// BOOL - is drag n drop between trees allowed
					createat	: "bottom",	// STRING (top or bottom) new nodes get inserted at top or bottom
					use_inline	: false,	// CHECK FOR INLINE RULES - REQUIRES METADATA
					clickable	: "all",	// which node types can the user select | default - all
					renameable	: "all",	// which node types can the user select | default - all
					deletable	: "all",	// which node types can the user delete | default - all
					creatable	: "all",	// which node types can the user create in | default - all
					draggable	: "none",	// which node types can the user move | default - none | "all"
					dragrules	: "all",	// what move operations between nodes are allowed | default - none | "all"
					drag_copy	: false,	// FALSE | CTRL | ON - drag to copy off/ with or without holding Ctrl
					droppable	: [],
					drag_button	: "left"	// left, right or both
				},
				lang : {
					new_node	: "New folder",
					loading		: "Loading ..."
				},
				callback	: {				// various callbacks to attach custom logic to
					// before focus  - should return true | false
					beforechange: function(NODE,TREE_OBJ) { return true },
					beforeopen	: function(NODE,TREE_OBJ) { return true },
					beforeclose	: function(NODE,TREE_OBJ) { return true },
					// before move   - should return true | false
					beforemove  : function(NODE,REF_NODE,TYPE,TREE_OBJ) { return true }, 
					// before create - should return true | false
					beforecreate: function(NODE,REF_NODE,TYPE,TREE_OBJ) { return true }, 
					// before rename - should return true | false
					beforerename: function(NODE,LANG,TREE_OBJ) { return true }, 
					// before delete - should return true | false
					beforedelete: function(NODE,TREE_OBJ) { return true }, 

					onJSONdata	: function(DATA,TREE_OBJ) { return DATA; },
					onselect	: function(NODE,TREE_OBJ) { },					// node selected
					ondeselect	: function(NODE,TREE_OBJ) { },					// node deselected
					onchange	: function(NODE,TREE_OBJ) { },					// focus changed
					onrename	: function(NODE,LANG,TREE_OBJ,RB) { },				// node renamed ISNEW - TRUE|FALSE, current language
					onmove		: function(NODE,REF_NODE,TYPE,TREE_OBJ,RB) { },	// move completed (TYPE is BELOW|ABOVE|INSIDE)
					oncopy		: function(NODE,REF_NODE,TYPE,TREE_OBJ,RB) { },	// copy completed (TYPE is BELOW|ABOVE|INSIDE)
					oncreate	: function(NODE,REF_NODE,TYPE,TREE_OBJ,RB) { },	// node created, parent node (TYPE is createat)
					ondelete	: function(NODE,TREE_OBJ,RB) { },				// node deleted
					onopen		: function(NODE,TREE_OBJ) { },					// node opened
					onopen_all	: function(TREE_OBJ) { },						// all nodes opened
					onclose		: function(NODE,TREE_OBJ) { },					// node closed
					error		: function(TEXT,TREE_OBJ) { },					// error occured
					// double click on node - defaults to open/close & select
					ondblclk	: function(NODE,TREE_OBJ) { TREE_OBJ.toggle_branch.call(TREE_OBJ, NODE); TREE_OBJ.select_branch.call(TREE_OBJ, NODE); },
					// right click - to prevent use: EV.preventDefault(); EV.stopPropagation(); return false
					onrgtclk	: function(NODE,TREE_OBJ,EV) { },
					onload		: function(TREE_OBJ) { },
					onfocus		: function(TREE_OBJ) { },
					ondrop		: function(NODE,REF_NODE,TYPE,TREE_OBJ) {}
				}
			},
			// INITIALIZATION
			init : function(elem, conf) {
				var _this = this;
				this.container		= $(elem);
				if(this.container.size == 0) { alert("Invalid container node!"); return }

				tree_component.inst[this.cntr] = this;
				if(!this.container.attr("id")) this.container.attr("id","jstree_" + this.cntr); 
				tree_component.inst[this.container.attr("id")] = tree_component.inst[this.cntr];
				tree_component.focused = this.cntr;

				var opts = $.extend({},conf);

				// MERGE OPTIONS WITH DEFAULTS
				if(opts && opts.cookies) {
					this.settings.cookies = $.extend({},this.settings.cookies,opts.cookies);
					delete opts.cookies;
					if(!this.settings.cookies.opts) this.settings.cookies.opts = {};
				}
				if(opts && opts.callback) {
					this.settings.callback = $.extend({},this.settings.callback,opts.callback);
					delete opts.callback;
				}
				if(opts && opts.data) {
					this.settings.data = $.extend({},this.settings.data,opts.data);
					delete opts.data;
				}
				if(opts && opts.ui) {
					this.settings.ui = $.extend({},this.settings.ui,opts.ui);
					delete opts.ui;
				}
				if(opts && opts.rules) {
					this.settings.rules = $.extend({},this.settings.rules,opts.rules);
					delete opts.rules;
				}
				if(opts && opts.lang) {
					this.settings.lang = $.extend({},this.settings.lang,opts.lang);
					delete opts.lang;
				}
				this.settings		= $.extend({},this.settings,opts);

				// PATH TO IMAGES AND XSL
				if(this.settings.path == false) {
					this.path = "";
					$("script").each( function () { 
						if(this.src.toString().match(/tree_component.*?js$/)) {
							_this.path = this.src.toString().replace(/tree_component.*?js$/, "");
						}
					});
				}
				else this.path = this.settings.path;

				// DEAL WITH LANGUAGE VERSIONS
				this.current_lang	= this.settings.languages && this.settings.languages.length ? this.settings.languages[0] : false;
				if(this.settings.languages && this.settings.languages.length) {
					this.sn = get_sheet_num("tree_component.css");
					if(this.sn === false && document.styleSheets.length) this.sn = document.styleSheets.length;
					var st = false;
					var id = this.container.attr("id") ? "#" + this.container.attr("id") : ".tree";
					for(var ln = 0; ln < this.settings.languages.length; ln++) {
						st = add_css(id + " ." + this.settings.languages[ln], this.sn);
						if(st !== false) {
							if(this.settings.languages[ln] == this.current_lang)	st.style.display = "";
							else													st.style.display = "none";
						}
					}
				}

				// DROPPABLES 
				if(this.settings.rules.droppable.length) {
					for(var i in this.settings.rules.droppable) {
						if(typeof this.settings.rules.droppable[i] == "function") continue;
						tree_component.drag_drop.droppable.push(this.settings.rules.droppable[i]);
					}
					tree_component.drag_drop.droppable = $.unique(tree_component.drag_drop.droppable);
				}

				// THEMES
				if(this.settings.ui.theme_path === false) this.settings.ui.theme_path = this.path + "themes/";
				this.theme = this.settings.ui.theme_path; 
				if(_this.settings.ui.theme_name) {
					this.theme += _this.settings.ui.theme_name + "/";
					if(_this.settings.ui.theme_name != "themeroller" && !tree_component.def_style) { add_sheet(_this.settings.ui.theme_path + "default/style.css"); tree_component.def_style = true; }
					add_sheet(_this.theme + "style.css");
				}

				this.container.addClass("tree");
				if(_this.settings.ui.theme_name != "themeroller") this.container.addClass("tree-default");
				if(this.settings.ui.theme_name && this.settings.ui.theme_name != "default") this.container.addClass("tree-" + _this.settings.ui.theme_name);
				if(this.settings.ui.theme_name == "themeroller") this.container.addClass("ui-widget ui-widget-content");
				if(this.settings.rules.multiple) this.selected_arr = [];
				this.offset = false;

				// CONTEXT MENU
				this.context_menu();

				this.hovered = false;
				this.locked = false;

				// CREATE DUMMY FOR MOVING
				if(this.settings.rules.draggable != "none" && tree_component.drag_drop.marker === false) {
					var _this = this;
					tree_component.drag_drop.marker = $("<img>")
						.attr({
							id		: "marker", 
							src	: _this.settings.ui.theme_path + "marker.gif"
						})
						.css({
							height		: "5px",
							width		: "40px",
							display		: "block",
							position	: "absolute",
							left		: "30px",
							top			: "30px",
							zIndex		: "1000"
						}).hide().appendTo("body");
				}
				this.refresh();
				this.attachEvents();
				this.focus();
			},
			off_height : function () {
				if(this.offset === false) {
					this.container.css({ position : "relative" });
					this.offset = this.container.offset();
					var tmp = 0;
					tmp = parseInt($.curCSS(this.container.get(0), "paddingTop", true),10);
					if(tmp) this.offset.top += tmp;
					tmp = parseInt($.curCSS(this.container.get(0), "borderTopWidth", true),10);
					if(tmp) this.offset.top += tmp;
					this.container.css({ position : "" });
				}
				if(!this.li_height) {
					var tmp = this.container.find("ul li.closed, ul li.leaf").eq(0);
					this.li_height = tmp.height();
					if(tmp.children("ul:eq(0)").size()) this.li_height -= tmp.children("ul:eq(0)").height();
					if(!this.li_height) this.li_height = 18;
				}
			},
			context_menu : function () {
				this.context = false;
				if(this.settings.ui.context != false) {
					var str = '<div class="tree-context tree-default-context tree-' + this.settings.ui.theme_name + '-context">';
					for(var i in this.settings.ui.context) {
						if(typeof this.settings.ui.context[i] == "function") continue;
						if(this.settings.ui.context[i] == "separator") {
							str += "<span class='separator'>&nbsp;</span>";
							continue;
						}
						var icn = "";
						if(this.settings.ui.context[i].icon) icn = 'background-image:url(\'' + ( this.settings.ui.context[i].icon.indexOf("/") == -1 ? this.theme + this.settings.ui.context[i].icon : this.settings.ui.context[i].icon ) + '\');';
						str += '<a rel="' + this.settings.ui.context[i].id + '" href="#" style="' + icn + '">' + this.settings.ui.context[i].label + '</a>';
					}
					str += '</div>';
					this.context = $(str);
					this.context.hide();
					this.context.append = false;
				}
			},
			// REPAINT TREE
			refresh : function (obj) {
				if(this.locked) return this.error("LOCKED");
				var _this = this;

				this.is_partial_refresh = obj ? true : false;

				// SAVE OPENED
				this.opened = Array();
				if(this.settings.cookies && $.cookie(this.settings.cookies.prefix + '_open')) {
					var str = $.cookie(this.settings.cookies.prefix + '_open');
					var tmp = str.split(",");
					$.each(tmp, function () {
						if(this.replace(/^#/,"").length > 0) { _this.opened.push("#" + this.replace(/^#/,"")); }
					});
					this.settings.opened = false;
				}
				else if(this.settings.opened != false) {
					$.each(this.settings.opened, function (i, item) {
						if(this.replace(/^#/,"").length > 0) { _this.opened.push("#" + this.replace(/^#/,"")); }
					});
					this.settings.opened = false;
				}
				else {
					this.container.find("li.open").each(function (i) { if(this.id) { _this.opened.push("#" + this.id); } });
				}

				// SAVE SELECTED
				if(this.selected) {
					this.settings.selected = Array();
					if(obj) {
						$(obj).find("li:has(a.clicked)").each(function () {
							$this = $(this);
							if($this.attr("id")) _this.settings.selected.push("#" + $this.attr("id"));
						});
					}
					else {
						if(this.selected_arr) {
							$.each(this.selected_arr, function () {
								if(this.attr("id")) _this.settings.selected.push("#" + this.attr("id"));
							});
						}
						else {
							if(this.selected.attr("id")) this.settings.selected.push("#" + this.selected.attr("id"));
						}
					}
				}
				else if(this.settings.cookies && $.cookie(this.settings.cookies.prefix + '_selected')) {
					this.settings.selected = Array();
					var str = $.cookie(this.settings.cookies.prefix + '_selected');
					var tmp = str.split(",");
					$.each(tmp, function () {
						if(this.replace(/^#/,"").length > 0) { _this.settings.selected.push("#" + this.replace(/^#/,"")); }
					});
				}
				else if(this.settings.selected !== false) {
					var tmp = Array();
					if((typeof this.settings.selected).toLowerCase() == "object") {
						$.each(this.settings.selected, function () {
							if(this.replace(/^#/,"").length > 0) tmp.push("#" + this.replace(/^#/,""));
						});
					}
					else {
						if(this.settings.selected.replace(/^#/,"").length > 0) tmp.push("#" + this.settings.selected.replace(/^#/,""));
					}
					this.settings.selected = tmp;
				}

				if(obj && this.settings.data.async) {
					this.opened = Array();
					obj = this.get_node(obj);
					obj.find("li.open").each(function (i) { _this.opened.push("#" + this.id); });
					if(obj.hasClass("open")) obj.removeClass("open").addClass("closed");
					if(obj.hasClass("leaf")) obj.removeClass("leaf");
					obj.children("ul:eq(0)").html("");
					return this.open_branch(obj, true, function () { _this.reselect.apply(_this); });
				}

				if(this.settings.data.type == "xml_flat" || this.settings.data.type == "xml_nested") {
					this.scrtop = this.container.get(0).scrollTop;
					var xsl = (this.settings.data.type == "xml_flat") ? "flat.xsl" : "nested.xsl";
					if(this.settings.data.xml)	this.container.getTransform(this.path + xsl, this.settings.data.xml, { params : { theme_name : _this.settings.ui.theme_name, theme_path : _this.theme }, meth : _this.settings.data.method, dat : _this.settings.data.async_data.apply(_this,[obj, _this]), callback: function () { _this.context_menu.apply(_this); _this.reselect.apply(_this); } });
					else						this.container.getTransform(this.path + xsl, this.settings.data.url, { params : { theme_name : _this.settings.ui.theme_name, theme_path : _this.theme }, meth : _this.settings.data.method, dat : _this.settings.data.async_data.apply(_this,[obj, _this]), callback: function () { _this.context_menu.apply(_this); _this.reselect.apply(_this); } });
					return;
				}
				else if(this.settings.data.type == "json") {
					if(this.settings.data.json) {
						var str = "";
						if(this.settings.data.json.length) {
							for(var i = 0; i < this.settings.data.json.length; i++) {
								str += this.parseJSON(this.settings.data.json[i]);
							}
						} else str = this.parseJSON(this.settings.data.json);
						this.container.html("<ul>" + str + "</ul>");
						this.container.find("li:last-child").addClass("last").end().find("li:has(ul)").not(".open").addClass("closed");
						this.container.find("li").not(".open").not(".closed").addClass("leaf");
						this.context_menu();
						this.reselect();
					}
					else {
						var _this = this;
						$.ajax({
							type		: this.settings.data.method,
							url			: this.settings.data.url, 
							data		: this.settings.data.async_data(false, this), 
							dataType	: "json",
							success		: function (data) {
								data = _this.settings.callback.onJSONdata.call(null, data, _this);
								var str = "";
								if(data.length) {
									for(var i = 0; i < data.length; i++) {
										str += _this.parseJSON(data[i]);
									}
								} else str = _this.parseJSON(data);
								_this.container.html("<ul>" + str + "</ul>");
								_this.container.find("li:last-child").addClass("last").end().find("li:has(ul)").not(".open").addClass("closed");
								_this.container.find("li").not(".open").not(".closed").addClass("leaf");
								_this.context_menu.apply(_this);
								_this.reselect.apply(_this);
							},
							error : function (xhttp, textStatus, errorThrown) { _this.error(errorThrown + " " + textStatus); }
						});
					}
				}
				else {
					this.container.children("ul:eq(0)");
					this.container.find("li:last-child").addClass("last").end().find("li:has(ul)").not(".open").addClass("closed");
					this.container.find("li").not(".open").not(".closed").addClass("leaf");
					this.reselect();
				}
			},
			// CONVERT JSON TO HTML
			parseJSON : function (data) {
				if(!data || !data.data) return "";
				var str = "";
				str += "<li ";
				var cls = false;
				if(data.attributes) {
					for(var i in data.attributes) {
						if(typeof data.attributes[i] == "function") continue;
						if(i == "class") {
							str += " class='" + data.attributes[i] + " ";
							if(data.state == "closed" || data.state == "open") str += " " + data.state + " ";
							str += "' ";
							cls = true;
						}
						else str += " " + i + "='" + data.attributes[i] + "' ";
					}
				}
				if(!cls && (data.state == "closed" || data.state == "open")) str += " class='" + data.state + "' ";
				str += ">";
				if(this.settings.languages.length) {
					for(var i = 0; i < this.settings.languages.length; i++) {
						var attr = {};
						attr["href"] = "#";
						attr["style"] = "";
						attr["class"] = this.settings.languages[i];
						if(data.data[this.settings.languages[i]] && (typeof data.data[this.settings.languages[i]].attributes).toLowerCase() != "undefined") {
							for(var j in data.data[this.settings.languages[i]].attributes) {
								if(typeof data.data[this.settings.languages[i]].attributes[j] == "function") continue;
								if(j == "style" || j == "class")	attr[j] += " " + data.data[this.settings.languages[i]].attributes[j];
								else								attr[j]  = data.data[this.settings.languages[i]].attributes[j];
							}
						}
						if(data.data[this.settings.languages[i]] && data.data[this.settings.languages[i]].icon && this.settings.theme_name != "themeroller") {
							var icn = data.data[this.settings.languages[i]].icon.indexOf("/") == -1 ? this.theme + data.data[this.settings.languages[i]].icon : data.data[this.settings.languages[i]].icon;
							attr["style"] += " ; background-image:url('" + icn + "'); ";
						}
						str += "<a";
						for(var j in attr) {
							if(typeof attr[j] == "function") continue;
							str += ' ' + j + '="' + attr[j] + '" ';
						}
						str += ">";
						if(data.data[this.settings.languages[i]] && data.data[this.settings.languages[i]].icon && this.settings.theme_name == "themeroller") {
							str += "<ins class='ui-icon " + data.data[this.settings.languages[i]].icon + "'>&nbsp;</ins>";
						}
						str += ( (typeof data.data[this.settings.languages[i]].title).toLowerCase() != "undefined" ? data.data[this.settings.languages[i]].title : data.data[this.settings.languages[i]] ) + "</a>";
					}
				}
				else {
					var attr = {};
					attr["href"] = "#";
					attr["style"] = "";
					attr["class"] = "";
					if((typeof data.data.attributes).toLowerCase() != "undefined") {
						for(var i in data.data.attributes) {
							if(typeof data.data.attributes[i] == "function") continue;
							if(i == "style" || i == "class")	attr[i] += " " + data.data.attributes[i];
							else								attr[i]  = data.data.attributes[i];
						}
					}
					if(data.data.icon && this.settings.ui.theme_name != "themeroller") {
						var icn = data.data.icon.indexOf("/") == -1 ? this.theme + data.data.icon : data.data.icon;
						attr["style"] += " ; background-image:url('" + icn + "');";
					}
					str += "<a";
					for(var i in attr) {
						if(typeof attr[j] == "function") continue;
						str += ' ' + i + '="' + attr[i] + '" ';
					}
					str += ">";
					if(data.data.icon && this.settings.ui.theme_name == "themeroller") {
						str += "<ins class='ui-icon " + data.data.icon + "'>&nbsp;</ins>";
					}
					str += ( (typeof data.data.title).toLowerCase() != "undefined" ? data.data.title : data.data ) + "</a>";
				}
				if(data.children && data.children.length) {
					str += '<ul>';
					for(var i = 0; i < data.children.length; i++) {
						str += this.parseJSON(data.children[i]);
					}
					str += '</ul>';
				}
				str += "</li>";
				return str;
			},
			// getJSON from HTML
			getJSON : function (nod, outer_attrib, inner_attrib, force) {
				var _this = this;
				if(!nod || $(nod).size() == 0) {
					nod = this.container.children("ul").children("li");
				}
				else nod = $(nod);

				if(nod.size() > 1) {
					var arr = [];
					nod.each(function () {
						arr.push(_this.getJSON(this, outer_attrib, inner_attrib, force));
					});
					return arr;
				}

				if(!outer_attrib) outer_attrib = [ "id", "rel", "class" ];
				if(!inner_attrib) inner_attrib = [ ];
				var obj = { attributes : {}, data : false };
				for(var i in outer_attrib) {
					if(typeof outer_attrib[i] == "function") continue;
					var val = (outer_attrib[i] == "class") ? nod.attr(outer_attrib[i]).replace("last","").replace("leaf","").replace("closed","").replace("open","") : nod.attr(outer_attrib[i]);
					if(typeof val != "undefined" && val.replace(" ","").length > 0) obj.attributes[outer_attrib[i]] = val;
					delete val;
				}
				if(this.settings.languages.length) {
					obj.data = {};
					for(var i in this.settings.languages) {
						if(typeof this.settings.languages[i] == "function") continue;
						var a = nod.children("a." + this.settings.languages[i]);
						if(force || inner_attrib.length || a.get(0).style.backgroundImage.toString().length) {
							obj.data[this.settings.languages[i]] = {};
							obj.data[this.settings.languages[i]].title = a.text();
							if(a.get(0).style.backgroundImage.length) {
								obj.data[this.settings.languages[i]].icon = a.get(0).style.backgroundImage.replace("url(","").replace(")","");
							}
							if(this.settings.ui.theme_name == "themeroller" && a.children("ins").size()) {
								var tmp = a.children("ins").attr("class");
								var cls = false;
								$.each(tmp.split(" "), function (i, val) {
									if(val.indexOf("ui-icon-") == 0) {
										cls = val;
										return false;
									}
								});
								if(cls) obj.data[this.settings.languages[i]].icon = cls;
							}
							if(inner_attrib.length) {
								obj.data[this.settings.languages[i]].attributes = {};
								for(var j in inner_attrib) {
									if(typeof inner_attrib[j] == "function") continue;
									var val = a.attr(inner_attrib[j]);
									if(typeof val != "undefined" && val.replace(" ","").length > 0) obj.data[this.settings.languages[i]].attributes[inner_attrib[j]] = val;
									delete val;
								}
							}
						}
						else {
							obj.data[this.settings.languages[i]] = a.text();
						}
					}
				}
				else {
					var a = nod.children("a");
					if(force || inner_attrib.length || a.get(0).style.backgroundImage.toString().length) {
						obj.data = {};
						obj.data.title = a.text();
						if(a.get(0).style.backgroundImage.length) {
							obj.data.icon = a.get(0).style.backgroundImage.replace("url(","").replace(")","");
						}
						if(this.settings.ui.theme_name == "themeroller" && a.children("ins").size()) {
							var tmp = a.children("ins").attr("class");
							var cls = false;
							$.each(tmp.split(" "), function (i, val) {
								if(val.indexOf("ui-icon-") == 0) {
									cls = val;
									return false;
								}
							});
							if(cls) obj.data[this.settings.languages[i]].icon = cls;
						}
						if(inner_attrib.length) {
							obj.data.attributes = {};
							for(var j in inner_attrib) {
								if(typeof inner_attrib[j] == "function") continue;
								var val = a.attr(inner_attrib[j]);
								if(typeof val != "undefined" && val.replace(" ","").length > 0) obj.data.attributes[inner_attrib[j]] = val;
								delete val;
							}
						}
					}
					else {
						obj.data = a.text();
					}
				}

				if(nod.children("ul").size() > 0) {
					obj.children = [];
					nod.children("ul").children("li").each(function () {
						obj.children.push(_this.getJSON(this, outer_attrib, inner_attrib, force));
					});
				}
				return obj;
			},
			// getXML from HTML
			getXML : function (tp, nod, outer_attrib, inner_attrib, cb) {
				var _this = this;
				if(tp != "flat") tp = "nested";
				if(!nod || $(nod).size() == 0) {
					nod = this.container.children("ul").children("li");
				}
				else nod = $(nod);

				if(nod.size() > 1) {
					var obj = '<root>';
					nod.each(function () {
						obj += _this.getXML(tp, this, outer_attrib, inner_attrib, true);
					});
					obj += '</root>';
					return obj;
				}

				if(!outer_attrib) outer_attrib = [ "id", "rel", "class" ];
				if(!inner_attrib) inner_attrib = [ ];
				var obj = '';

				if(!cb) obj = '<root>';

				obj += '<item ';
				
				if(tp == "flat") {
					var tmp_id = nod.parents("li:eq(0)").size() ? nod.parents("li:eq(0)").attr("id") : 0;
					obj += ' parent_id="' + tmp_id + '" ';
					delete tmp_id;
				}
				for(var i in outer_attrib) {
					if(typeof outer_attrib[i] == "function") continue;
					var val = (outer_attrib[i] == "class") ? nod.attr(outer_attrib[i]).replace("last","").replace("leaf","").replace("closed","").replace("open","") : nod.attr(outer_attrib[i]);
					if(typeof val != "undefined" && val.replace(" ","").length > 0) obj += ' ' + outer_attrib[i] + '="' + val + '" ';
					delete val;
				}
				obj += '>';

				obj += '<content>';
				if(this.settings.languages.length) {
					for(var i in this.settings.languages) {
						if(typeof this.settings.languages[i] == "function") continue;
						var a = nod.children("a." + this.settings.languages[i]);
						obj += '<name ';
						if(inner_attrib.length || a.get(0).style.backgroundImage.toString().length || this.settings.ui.theme_name == "themeroller") {
							if(a.get(0).style.backgroundImage.length) {
								obj += ' icon="' + a.get(0).style.backgroundImage.replace("url(","").replace(")","") + '" ';
							}
							if(this.settings.ui.theme_name == "themeroller" && a.children("ins").size()) {
								var tmp = a.children("ins").attr("class");
								var cls = false;
								$.each(tmp.split(" "), function (i, val) {
									if(val.indexOf("ui-icon-") == 0) {
										cls = val;
										return false;
									}
								});
								if(cls) obj += ' icon="' + cls + '" ';
							}
							if(inner_attrib.length) {
								for(var j in inner_attrib) {
									if(typeof inner_attrib[j] == "function") continue;
									var val = a.attr(inner_attrib[j]);
									if(typeof val != "undefined" && val.replace(" ","").length > 0) obj += ' ' + inner_attrib[j] + '="' + val + '" ';
									delete val;
								}
							}
						}
						obj += '><![CDATA[' + a.text() + ']]></name>';
					}
				}
				else {
					var a = nod.children("a");
					obj += '<name ';
					if(inner_attrib.length || a.get(0).style.backgroundImage.toString().length || this.settings.ui.theme_name == "themeroller") {
						if(a.get(0).style.backgroundImage.length) {
							obj += ' icon="' + a.get(0).style.backgroundImage.replace("url(","").replace(")","") + '" ';
						}
						if(this.settings.ui.theme_name == "themeroller" && a.children("ins").size()) {
							var tmp = a.children("ins").attr("class");
							var cls = false;
							$.each(tmp.split(" "), function (i, val) {
								if(val.indexOf("ui-icon-") == 0) {
									cls = val;
									return false;
								}
							});
							if(cls) obj += ' icon="' + cls + '" ';
						}
						if(inner_attrib.length) {
							for(var j in inner_attrib) {
								if(typeof inner_attrib[j] == "function") continue;
								var val = a.attr(inner_attrib[j]);
								if(typeof val != "undefined" && val.replace(" ","").length > 0) obj += ' ' + inner_attrib[j] + '="' + val + '" ';
								delete val;
							}
						}
					}
					obj += '><![CDATA[' + a.text() + ']]></name>';
				}
				obj += '</content>';

				if(tp == "flat") obj += '</item>';

				if(nod.children("ul").size() > 0) {
					nod.children("ul").children("li").each(function () {
						obj += _this.getXML(tp, this, outer_attrib, inner_attrib, true);
					});
				}

				if(tp == "nested") obj += '</item>';

				if(!cb) obj += '</root>';
				return obj;
			},
			focus : function () {
				if(this.locked) return false;
				if(tree_component.focused != this.cntr) {
					tree_component.focused = this.cntr;
					this.settings.callback.onfocus.call(null, this);
				}
			},
			show_context : function (obj) {
				this.context.show();
				var tmp = $(obj).children("a:visible").offset();
				this.context.css({ "left" : (tmp.left), "top" : (tmp.top + parseInt(obj.children("a:visible").height()) + 2) });
			},
			hide_context : function () {
				if(this.context.to_remove && this.context.apply_to) this.context.apply_to.children("a").removeClass("clicked");
				this.context.apply_to = false;
				this.context.hide();
			},
			// ALL EVENTS
			attachEvents : function () {
				var _this = this;

				this.container
					.bind("mousedown.jstree", function (event) {
						if(tree_component.drag_drop.isdown) {
							tree_component.drag_drop.move_type = false;
							event.preventDefault();
							event.stopPropagation();
							event.stopImmediatePropagation();
							return false;
						}
					})
					.bind("mouseup.jstree", function (event) {
						setTimeout( function() { _this.focus.apply(_this); }, 5);
					})
					.bind("click.jstree", function (event) { 
						//event.stopPropagation(); 
						return true;
					});
				$("#" + this.container.attr("id") + " li")
					.live("click", function(event) { // WHEN CLICK IS ON THE ARROW
						if(event.target.tagName != "LI") return true;
						_this.off_height();
						if(event.pageY - $(event.target).offset().top > _this.li_height) return true;
						_this.toggle_branch.apply(_this, [event.target]);
						event.stopPropagation();
						return false;
					});
				$("#" + this.container.attr("id") + " li a")
					.live("click.jstree", function (event) { // WHEN CLICK IS ON THE TEXT OR ICON
						if(event.which && event.which == 3) return true;
						if(_this.locked) {
							event.preventDefault(); 
							event.target.blur();
							return _this.error("LOCKED");
						}
						_this.select_branch.apply(_this, [event.target, event.ctrlKey || _this.settings.rules.multiple == "on"]);
						if(_this.inp) { _this.inp.blur(); }
						event.preventDefault(); 
						event.target.blur();
						return false;
					})
					.live("dblclick.jstree", function (event) { // WHEN DOUBLECLICK ON TEXT OR ICON
						if(_this.locked) {
							event.preventDefault(); 
							event.stopPropagation();
							event.target.blur();
							return _this.error("LOCKED");
						}
						_this.settings.callback.ondblclk.call(null, _this.get_node(event.target).get(0), _this);
						event.preventDefault(); 
						event.stopPropagation();
						event.target.blur();
					})
					.live("contextmenu.jstree", function (event) {
						if(_this.locked) {
							event.target.blur();
							return _this.error("LOCKED");
						}
						var val = _this.settings.callback.onrgtclk.call(null, _this.get_node(event.target).get(0), _this, event);
						if(_this.context) {
							if(_this.context.append == false) {
								$("body").append(_this.context);
								_this.context.append = true;
								for(var i in _this.settings.ui.context) {
									if(typeof _this.settings.ui.context[i] == "function") continue;
									if(_this.settings.ui.context[i] == "separator") continue;
									(function () {
										var func = _this.settings.ui.context[i].action;
										_this.context.children("[rel=" + _this.settings.ui.context[i].id +"]")
											.bind("click", function (event) {
												if(!$(this).hasClass("disabled")) {
													func.call(null, _this.context.apply_to || null, _this);
													_this.hide_context();
												}
												event.stopPropagation();
												event.preventDefault();
												return false;
											})
											.bind("mouseup", function (event) {
												this.blur();
												if($(this).hasClass("disabled")) {
													event.stopPropagation();
													event.preventDefault();
													return false;
												}
											})
											.bind("mousedown", function (event) {
												event.stopPropagation();
												event.preventDefault();
											});
									})();
								}
							}
							var obj = _this.get_node(event.target);
							if(_this.inp) { _this.inp.blur(); }
							if(obj) {
								if(!obj.children("a:eq(0)").hasClass("clicked")) {
									// _this.select_branch.apply(_this, [event.target, event.ctrlKey || _this.settings.rules.multiple == "on"]);
									_this.context.apply_to = obj;
									_this.context.to_remove = true;
									_this.context.apply_to.children("a").addClass("clicked");
									event.target.blur();
								}
								else { 
									_this.context.to_remove = false; 
									_this.context.apply_to = (_this.selected_arr && _this.selected_arr.length > 1) ? _this.selected_arr : _this.selected;
								}

								_this.context.children("a").removeClass("disabled").show();
								var go = false;
								for(var i in _this.settings.ui.context) {
									if(typeof _this.settings.ui.context[i] == "function") continue;
									if(_this.settings.ui.context[i] == "separator") continue;
									var state = _this.settings.ui.context[i].visible.call(null, _this.context.apply_to, _this);
									if(state === false)	_this.context.children("[rel=" + _this.settings.ui.context[i].id +"]").addClass("disabled");
									if(state === -1)	_this.context.children("[rel=" + _this.settings.ui.context[i].id +"]").hide();
									else				go = true;
								}
								if(go == true) _this.show_context(obj);
								event.preventDefault(); 
								event.stopPropagation();
								return false;
							}
						}
						return val;
					})
					.live("mouseover.jstree", function (event) {
						if(_this.locked) {
							event.preventDefault();
							event.stopPropagation();
							return _this.error("LOCKED");
						}
						if( (_this.settings.ui.hover_mode || _this.settings.ui.theme_name == "themeroller" ) && _this.hovered !== false && event.target.tagName == "A") {
							_this.hovered.children("a").removeClass("hover ui-state-hover");
							_this.hovered = false;
						}
						if(_this.settings.ui.theme_name == "themeroller") {
							_this.hover_branch.apply(_this, [event.target]);
						}
					});
				if(_this.settings.ui.theme_name == "themeroller") {
					$("#" + this.container.attr("id") + " li a").live("mouseout", function (event) {
						if(_this.hovered) _this.hovered.children("a").removeClass("hover ui-state-hover");
					});
				}

				// ATTACH DRAG & DROP ONLY IF NEEDED
				if(this.settings.rules.draggable != "none") {
					$("#" + this.container.attr("id") + " li a")
						.live("mousedown.jstree", function (event) {
							if(_this.settings.rules.drag_button == "left" && event.which && event.which != 1)	return true;
							if(_this.settings.rules.drag_button == "right" && event.which && event.which != 3)	return true;
							_this.focus.apply(_this);
							if(_this.locked) return _this.error("LOCKED");
							// SELECT LIST ITEM NODE
							var obj = _this.get_node(event.target);
							// IF ITEM IS DRAGGABLE
							if(_this.settings.rules.multiple != false && _this.selected_arr.length > 1 && obj.children("a:eq(0)").hasClass("clicked")) {
								var counter = 0;
								for(var i in _this.selected_arr) {
									if(typeof _this.selected_arr[i] == "function") continue;
									if(_this.check("draggable", _this.selected_arr[i])) {
										_this.selected_arr[i].addClass("dragged");
										tree_component.drag_drop.origin_tree = _this;
										counter ++;
									}
								}
								if(counter > 0) {
									if(_this.check("draggable", obj))	tree_component.drag_drop.drag_node = obj;
									else								tree_component.drag_drop.drag_node = _this.container.find("li.dragged:eq(0)");
									tree_component.drag_drop.isdown		= true;
									tree_component.drag_drop.drag_help	= $("<div id='jstree-dragged' class='tree " + (_this.container.hasClass("tree-default") ? " tree-default" : "" ) + (_this.settings.ui.theme_name && _this.settings.ui.theme_name != "default" ? " tree-" + _this.settings.ui.theme_name : "" ) + "' />").append("<ul class='" + _this.container.children("ul:eq(0)").get(0).className + "' />");
									var tmp = $(tree_component.drag_drop.drag_node.get(0).cloneNode(true));
									if(_this.settings.languages.length > 0) tmp.find("a").not("." + _this.current_lang).hide();
									tree_component.drag_drop.drag_help.children("ul:eq(0)").append(tmp);
									tree_component.drag_drop.drag_help.find("li:eq(0)").removeClass("last").addClass("last").children("a").html("Multiple selection").end().children("ul").remove();
								}
							}
							else {
								if(_this.check("draggable", obj)) {
									tree_component.drag_drop.drag_node	= obj;
									tree_component.drag_drop.drag_help	= $("<div id='jstree-dragged' class='tree " + (_this.container.hasClass("tree-default") ? " tree-default" : "" ) + (_this.settings.ui.theme_name && _this.settings.ui.theme_name != "default" ? " tree-" + _this.settings.ui.theme_name : "" ) + "' />").append("<ul class='" + _this.container.children("ul:eq(0)").get(0).className + "' />");
									var tmp = $(obj.get(0).cloneNode(true));
									if(_this.settings.languages.length > 0) tmp.find("a").not("." + _this.current_lang).hide();
									tree_component.drag_drop.drag_help.children("ul:eq(0)").append(tmp);
									tree_component.drag_drop.drag_help.find("li:eq(0)").removeClass("last").addClass("last");
									tree_component.drag_drop.isdown		= true;
									tree_component.drag_drop.foreign	= false;
									tree_component.drag_drop.origin_tree = _this;
									obj.addClass("dragged");
								}
							}
							tree_component.drag_drop.init_x = event.pageX;
							tree_component.drag_drop.init_y = event.pageY;
							obj.blur();
							event.preventDefault(); 
							event.stopPropagation();
							return false;
						});
					$(document)
						.bind("mousedown.jstree",	tree_component.mousedown)
						.bind("mouseup.jstree",		tree_component.mouseup)
						.bind("mousemove.jstree",	tree_component.mousemove);
				} 
				// ENDIF OF DRAG & DROP FUNCTIONS
				if(_this.context) $(document).bind("mousedown", function() { _this.hide_context(); });
			},
			checkMove : function (NODES, REF_NODE, TYPE) {
				if(this.locked) return this.error("LOCKED");
				var _this = this;

				// OVER SELF OR CHILDREN
				if(REF_NODE.parents("li.dragged").size() > 0 || REF_NODE.is(".dragged")) return this.error("MOVE: NODE OVER SELF");
				// CHECK AGAINST DRAG_RULES
				if(NODES.size() == 1) {
					var NODE = NODES.eq(0);
					if(tree_component.drag_drop.foreign) {
						if(this.settings.rules.droppable.length == 0) return false;
						if(!NODE.is("." + this.settings.rules.droppable.join(", ."))) return false;
						var ok = false;
						for(var i in this.settings.rules.droppable) {
							if(typeof this.settings.rules.droppable[i] == "function") continue;
							if(NODE.is("." + this.settings.rules.droppable[i])) {
								if(this.settings.rules.metadata) {
									$.metadata.setType("attr", this.settings.rules.metadata);
									NODE.attr(this.settings.rules.metadata, "type: '" + this.settings.rules.droppable[i] + "'");
								}
								else {
									NODE.attr(this.settings.rules.type_attr, this.settings.rules.droppable[i]);
								}
								ok = true;
								break;
							}
						}
						if(!ok) return false;
					}
					if(!this.check("dragrules", [NODE, TYPE, REF_NODE.parents("li:eq(0)")])) return this.error("MOVE: AGAINST DRAG RULES");
				}
				else {
					var ok = true;
					NODES.each(function (i) {
						if(ok == false) return false;
						//if(i > 0) {
						//	var ref = NODES.eq( (i - 1) );
						//	var mv = "after";
						//}
						//else {
							var ref = REF_NODE;
							var mv = TYPE;
						//}
						if(!_this.check.apply(_this,["dragrules", [$(this), mv, ref]])) ok = false;
					});
					if(ok == false) return this.error("MOVE: AGAINST DRAG RULES");
				}
				// CHECK AGAINST METADATA
				if(this.settings.rules.use_inline && this.settings.rules.metadata) {
					var nd = false;
					if(TYPE == "inside")	nd = REF_NODE.parents("li:eq(0)");
					else					nd = REF_NODE.parents("li:eq(1)");
					if(nd.size()) {
						// VALID CHILDREN CHECK
						if(typeof nd.metadata()["valid_children"] != "undefined") {
							var tmp = nd.metadata()["valid_children"];
							var ok = true;
							NODES.each(function (i) {
								if(ok == false) return false;
								if($.inArray(_this.get_type(this), tmp) == -1) ok = false;
							});
							if(ok == false) return this.error("MOVE: NOT A VALID CHILD");
						}
						// CHECK IF PARENT HAS FREE SLOTS FOR CHILDREN
						if(typeof nd.metadata()["max_children"] != "undefined") {
							if((nd.children("ul:eq(0)").children("li").not(".dragged").size() + NODES.size()) > nd.metadata().max_children) return this.error("MOVE: MAX CHILDREN REACHED");
						}
						// CHECK FOR MAXDEPTH UP THE CHAIN
						var incr = 0;
						NODES.each(function (j) {
							var i = 1;
							var t = $(this);
							while(i < 100) {
								t = t.children("ul").children("li");
								if(t.size() == 0) break;
								i ++
							}
							incr = Math.max(i,incr);
						});
						var ok = true;

						if((typeof $(nd).metadata().max_depth).toLowerCase() != "undefined" && $(nd).metadata().max_depth < incr) ok = false;
						else {
							nd.parents("li").each(function(i) {
								if(ok == false) return false;
								if((typeof $(this).metadata().max_depth).toLowerCase() != "undefined") {
									if( (i + incr) >= $(this).metadata().max_depth) ok = false;
								}
							});
						}
						if(ok == false) return this.error("MOVE: MAX_DEPTH REACHED");
					}
				}
				return true;
			},
			// USED AFTER REFRESH
			reselect : function (is_callback) {
				var _this = this;

				if(!is_callback)	this.cl_count = 0;
				else				this.cl_count --;
				// REOPEN BRANCHES
				if(this.opened && this.opened.length) {
					var opn = false;
					for(var j = 0; this.opened && j < this.opened.length; j++) {
						if(this.settings.data.async) {
							if(this.get_node(this.opened[j]).size() > 0) {
								opn = true;
								var tmp = this.opened[j];
								delete this.opened[j];
								this.open_branch(tmp, true, function () { _this.reselect.apply(_this, [true]); } );
								this.cl_count ++;
							}
						}
						else this.open_branch(this.opened[j], true);
					}
					if(this.settings.data.async && opn) return;
					delete this.opened;
				}
				if(this.cl_count > 0) return;

				// DOTS and RIGHT TO LEFT
				if(this.settings.ui.rtl)			this.container.css("direction","rtl").children("ul:eq(0)").addClass("rtl");
				else								this.container.css("direction","ltr").children("ul:eq(0)").addClass("ltr");
				if(this.settings.ui.dots == false)	this.container.children("ul:eq(0)").addClass("no_dots");

				// REPOSITION SCROLL
				if(this.scrtop) {
					this.container.scrollTop(_this.scrtop);
					delete this.scrtop;
				}
				// RESELECT PREVIOUSLY SELECTED
				if(this.settings.selected !== false) {
					$.each(this.settings.selected, function (i) {
						if(_this.is_partial_refresh)	_this.select_branch($(_this.settings.selected[i], _this.container), (_this.settings.rules.multiple !== false) );
						else							_this.select_branch($(_this.settings.selected[i], _this.container), (_this.settings.rules.multiple !== false && i > 0) );
					});
					this.settings.selected = false;
				}
				if(this.settings.ui.theme_name == "themeroller") this.container.find("a").addClass("ui-state-default");
				this.settings.callback.onload.call(null, _this);
			},
			// GET THE EXTENDED LI ELEMENT
			get_node : function (obj) {
				var obj = $(obj);
				return obj.is("li") ? obj : obj.parents("li:eq(0)");
			},
			// GET THE TYPE OF THE NODE
			get_type : function (obj) {
				obj = !obj ? this.selected : this.get_node(obj);
				if(!obj) return;
				if(this.settings.rules.metadata) {
					$.metadata.setType("attr", this.settings.rules.metadata);
					var tmp = obj.metadata().type;
					if(tmp) return tmp;
				} 
				return obj.attr(this.settings.rules.type_attr);
			},
			// SCROLL CONTAINER WHILE DRAGGING
			scrollCheck : function (x,y) { 
				var _this = this;
				var cnt = _this.container;
				var off = _this.container.offset();

				var st = cnt.scrollTop();
				var sl = cnt.scrollLeft();
				// DETECT HORIZONTAL SCROLL
				var h_cor = (cnt.get(0).scrollWidth > cnt.width()) ? 40 : 20;

				if(y - off.top < 20)						cnt.scrollTop(Math.max( (st - _this.settings.ui.scroll_spd) ,0));	// NEAR TOP
				if(cnt.height() - (y - off.top) < h_cor)	cnt.scrollTop(st + _this.settings.ui.scroll_spd);					// NEAR BOTTOM
				if(x - off.left < 20)						cnt.scrollLeft(Math.max( (sl - _this.settings.ui.scroll_spd),0));	// NEAR LEFT
				if(cnt.width() - (x - off.left) < 40)		cnt.scrollLeft(sl + _this.settings.ui.scroll_spd);					// NEAR RIGHT

				if(cnt.scrollLeft() != sl || cnt.scrollTop() != st) {
					_this.moveType = false;
					_this.moveRef = false;
					tree_component.drag_drop.marker.hide();
				}
				tree_component.drag_drop.scroll_time = setTimeout( function() { _this.scrollCheck(x,y); }, 50);
			},
			check : function (rule, nodes) {
				if(this.locked) return this.error("LOCKED");
				// CHECK LOCAL RULES IF METADATA
				if(rule != "dragrules" && this.settings.rules.use_inline && this.settings.rules.metadata) {
					$.metadata.setType("attr", this.settings.rules.metadata);
					if(typeof this.get_node(nodes).metadata()[rule] != "undefined") return this.get_node(nodes).metadata()[rule];
				}
				if(!this.settings.rules[rule])			return false;
				if(this.settings.rules[rule] == "none")	return false;
				if(this.settings.rules[rule] == "all")	return true;

				if(rule == "dragrules") {
					var nds = new Array();
					nds[0] = this.get_type(nodes[0]);
					nds[1] = nodes[1];
					nds[2] = this.get_type(nodes[2]);
					for(var i = 0; i < this.settings.rules.dragrules.length; i++) {
						var r = this.settings.rules.dragrules[i];
						var n = (r.indexOf("!") === 0) ? false : true;
						if(!n) r = r.replace("!","");
						var tmp = r.split(" ");
						for(var j = 0; j < 3; j++) {
							if(tmp[j] == nds[j] || tmp[j] == "*") tmp[j] = true;
						}
						if(tmp[0] === true && tmp[1] === true && tmp[2] === true) return n;
					}
					return false;
				}
				else 
					return ($.inArray(this.get_type(nodes),this.settings.rules[rule]) != -1) ? true : false;
			},
			hover_branch : function (obj) {
				if(this.locked) return this.error("LOCKED");
				if(this.settings.ui.hover_mode == false && this.settings.ui.theme_name != "themeroller") return this.select_branch(obj);
				var _this = this;
				var obj = _this.get_node(obj);
				if(!obj.size()) return this.error("HOVER: NOT A VALID NODE");
				// CHECK AGAINST RULES FOR SELECTABLE NODES
				if(!_this.check("clickable", obj)) return this.error("SELECT: NODE NOT SELECTABLE");
				if(this.hovered) this.hovered.children("A").removeClass("hover ui-state-hover");

				// SAVE NEWLY SELECTED
				this.hovered = obj;

				// FOCUS NEW NODE AND OPEN ALL PARENT NODES IF CLOSED
				this.hovered.children("a").removeClass("hover ui-state-hover").addClass( this.settings.ui.theme_name == "themeroller" ? "hover ui-state-hover" : "hover");

				// SCROLL SELECTED NODE INTO VIEW
				var off_t = this.hovered.offset().top;
				var beg_t = this.container.offset().top;
				var end_t = beg_t + this.container.height();
				var h_cor = (this.container.get(0).scrollWidth > this.container.width()) ? 40 : 20;
				if(off_t + 5 < beg_t) this.container.scrollTop(this.container.scrollTop() - (beg_t - off_t + 5) );
				if(off_t + h_cor > end_t) this.container.scrollTop(this.container.scrollTop() + (off_t + h_cor - end_t) );
			},
			select_branch : function (obj, multiple) {
				if(this.locked) return this.error("LOCKED");
				if(!obj && this.hovered !== false) obj = this.hovered;
				var _this = this;
				obj = _this.get_node(obj);
				if(!obj.size()) return this.error("SELECT: NOT A VALID NODE");
				obj.children("a").removeClass("hover ui-state-hover");
				// CHECK AGAINST RULES FOR SELECTABLE NODES
				if(!_this.check("clickable", obj)) return this.error("SELECT: NODE NOT SELECTABLE");
				if(_this.settings.callback.beforechange.call(null,obj.get(0),_this) === false) return this.error("SELECT: STOPPED BY USER");
				// IF multiple AND obj IS ALREADY SELECTED - DESELECT IT
				if(this.settings.rules.multiple != false && multiple && obj.children("a.clicked").size() > 0) {
					return this.deselect_branch(obj);
				}
				if(this.settings.rules.multiple != false && multiple) {
					this.selected_arr.push(obj);
				}
				if(this.settings.rules.multiple != false && !multiple) {
					for(var i in this.selected_arr) {
						if(typeof this.selected_arr[i] == "function") continue;
						this.selected_arr[i].children("A").removeClass("clicked ui-state-active");
						this.settings.callback.ondeselect.call(null, this.selected_arr[i].get(0), _this);
					}
					this.selected_arr = [];
					this.selected_arr.push(obj);
					if(this.selected && this.selected.children("A").hasClass("clicked")) {
						this.selected.children("A").removeClass("clicked ui-state-active");
						this.settings.callback.ondeselect.call(null, this.selected.get(0), _this);
					}
				}
				if(!this.settings.rules.multiple) {
					if(this.selected) {
						this.selected.children("A").removeClass("clicked ui-state-active");
						this.settings.callback.ondeselect.call(null, this.selected.get(0), _this);
					}
				}
				// SAVE NEWLY SELECTED
				this.selected = obj;
				if( (this.settings.ui.hover_mode || this.settings.ui.theme_name == "themeroller") && this.hovered !== false) {
					this.hovered.children("A").removeClass("hover ui-state-hover");
					this.hovered = obj;
				}

				// FOCUS NEW NODE AND OPEN ALL PARENT NODES IF CLOSED
				this.selected.children("a").removeClass("clicked ui-state-active").addClass( this.settings.ui.theme_name == "themeroller" ? "clicked ui-state-active" : "clicked").end().parents("li.closed").each( function () { _this.open_branch(this, true); });

				// SCROLL SELECTED NODE INTO VIEW
				var off_t = this.selected.offset().top;
				var beg_t = this.container.offset().top;
				var end_t = beg_t + this.container.height();
				var h_cor = (this.container.get(0).scrollWidth > this.container.width()) ? 40 : 20;
				if(off_t + 5 < beg_t) this.container.scrollTop(this.container.scrollTop() - (beg_t - off_t + 5) );
				if(off_t + h_cor > end_t) this.container.scrollTop(this.container.scrollTop() + (off_t + h_cor - end_t) );

				this.set_cookie("selected");
				this.settings.callback.onselect.call(null, this.selected.get(0), _this);
				this.settings.callback.onchange.call(null, this.selected.get(0), _this);
			},
			deselect_branch : function (obj) {
				if(this.locked) return this.error("LOCKED");
				var _this = this;
				var obj = this.get_node(obj);
				obj.children("a").removeClass("clicked ui-state-active");
				this.settings.callback.ondeselect.call(null, obj.get(0), _this);
				if(this.settings.rules.multiple != false && this.selected_arr.length > 1) {
					this.selected_arr = [];
					this.container.find("a.clicked").filter(":first-child").parent().each(function () {
						_this.selected_arr.push($(this));
					});
					if(obj.get(0) == this.selected.get(0)) {
						this.selected = this.selected_arr[0];
						this.set_cookie("selected");
					}
				}
				else {
					if(this.settings.rules.multiple != false) this.selected_arr = [];
					this.selected = false;
					this.set_cookie("selected");
				}
				if(this.selected)	this.settings.callback.onchange.call(null, this.selected.get(0), _this);
				else				this.settings.callback.onchange.call(null, false, _this);
			},
			toggle_branch : function (obj) {
				if(this.locked) return this.error("LOCKED");
				var obj = this.get_node(obj);
				if(obj.hasClass("closed"))	return this.open_branch(obj);
				if(obj.hasClass("open"))	return this.close_branch(obj); 
			},
			open_branch : function (obj, disable_animation, callback) {
				if(this.locked) return this.error("LOCKED");
				var obj = this.get_node(obj);
				if(!obj.size()) return this.error("OPEN: NO SUCH NODE");
				if(obj.hasClass("leaf")) return this.error("OPEN: OPENING LEAF NODE");

				if(this.settings.data.async && obj.find("li").size() == 0) {
					if(this.settings.callback.beforeopen.call(null,obj.get(0),this) === false) return this.error("OPEN: STOPPED BY USER");
					var _this = this;
					obj.children("ul:eq(0)").remove().end().append("<ul><li class='last'><a class='loading' href='#'>" + (_this.settings.lang.loading || "Loading ...") + "</a></li></ul>");
					obj.removeClass("closed").addClass("open");
					if(this.settings.data.type == "xml_flat" || this.settings.data.type == "xml_nested") {
						var xsl = (this.settings.data.type == "xml_flat") ? "flat.xsl" : "nested.xsl";
						obj.children("ul:eq(0)").getTransform(this.path + xsl, this.settings.data.url, { params : { theme_path : _this.theme }, meth : this.settings.data.method, dat : this.settings.data.async_data(obj, this), repl : true, 
							callback: function (str, json) { 
								if(str.length < 15) {
									obj.removeClass("closed").removeClass("open").addClass("leaf").children("ul").remove();
									if(callback) callback.call();
									return;
								}
								_this.open_branch.apply(_this, [obj]); 
								if(callback) callback.call();
							},
							error : function () { obj.removeClass("open").addClass("closed").children("ul:eq(0)").remove(); }
						});
					}
					else {
						$.ajax({
							type		: this.settings.data.method,
							url			: this.settings.data.url, 
							data		: this.settings.data.async_data(obj, this), 
							dataType	: "json",
							success		: function (data, textStatus) {
								data = _this.settings.callback.onJSONdata.call(null, data, _this);
								if(!data || data.length == 0) {
									obj.removeClass("closed").removeClass("open").addClass("leaf").children("ul").remove();
									if(callback) callback.call();
									return;
								}
								var str = "";
								if(data.length) {
									for(var i = 0; i < data.length; i++) {
										str += _this.parseJSON(data[i]);
									}
								}
								else str = _this.parseJSON(data);
								if(str.length > 0) {
									obj.children("ul:eq(0)").replaceWith("<ul>" + str + "</ul>");
									obj.find("li:last-child").addClass("last").end().find("li:has(ul)").not(".open").addClass("closed");
									obj.find("li").not(".open").not(".closed").addClass("leaf");
									_this.open_branch.apply(_this, [obj]);
								}
								else obj.removeClass("closed").removeClass("open").addClass("leaf").children("ul").remove();
								if(callback) callback.call();
							},
							error : function (xhttp, textStatus, errorThrown) { obj.removeClass("open").addClass("closed").children("ul:eq(0)").remove(); _this.error(errorThrown + " " + textStatus); }
						});
					}
					return true;
				}
				else {
					if(!this.settings.data.async) {
						if(this.settings.callback.beforeopen.call(null,obj.get(0),this) === false) return this.error("OPEN: STOPPED BY USER");
					}
					if(this.settings.ui.theme_name == "themeroller") obj.find("a").not(".ui-state-default").addClass("ui-state-default");
					if(parseInt(this.settings.ui.animation) > 0 && !disable_animation ) {
						obj.children("ul:eq(0)").css("display","none");
						obj.removeClass("closed").addClass("open");
						obj.children("ul:eq(0)").slideDown(parseInt(this.settings.ui.animation), function() {
							$(this).css("display","");
							if(callback) callback.call();
						});
					} else {
						obj.removeClass("closed").addClass("open");
						if(callback) callback.call();
					}
					this.set_cookie("open");
					this.settings.callback.onopen.call(null, obj.get(0), this);
					return true;
				}
			},
			close_branch : function (obj, disable_animation) {
				if(this.locked) return this.error("LOCKED");
				var _this = this;
				var obj = this.get_node(obj);
				if(!obj.size()) return this.error("CLOSE: NO SUCH NODE");
				if(_this.settings.callback.beforeclose.call(null,obj.get(0),_this) === false) return this.error("CLOSE: STOPPED BY USER");
				if(parseInt(this.settings.ui.animation) > 0 && !disable_animation && obj.children("ul:eq(0)").size() == 1) {
					obj.children("ul:eq(0)").slideUp(parseInt(this.settings.ui.animation), function() {
						if(obj.hasClass("open")) obj.removeClass("open").addClass("closed");
						_this.set_cookie("open");
						$(this).css("display","");
					});
				} 
				else {
					if(obj.hasClass("open")) obj.removeClass("open").addClass("closed");
					this.set_cookie("open");
				}
				if(this.selected && obj.children("ul:eq(0)").find("a.clicked").size() > 0) {
					obj.find("li:has(a.clicked)").each(function() {
						_this.deselect_branch(this);
					});
					if(obj.children("a.clicked").size() == 0) this.select_branch(obj, (this.settings.rules.multiple != false && this.selected_arr.length > 0) );
				}
				this.settings.callback.onclose.call(null, obj.get(0), this);
			},
			open_all : function (obj, callback) {
				if(this.locked) return this.error("LOCKED");
				var _this = this;
				obj = obj ? this.get_node(obj).parent() : this.container;

				var s = obj.find("li.closed").size();
				if(!callback)	this.cl_count = 0;
				else			this.cl_count --;
				if(s > 0) {
					this.cl_count += s;
					obj.find("li.closed").each( function () { var __this = this; _this.open_branch.apply(_this, [this, true, function() { _this.open_all.apply(_this, [__this, true]); } ]); });
				}
				else if(this.cl_count == 0) this.settings.callback.onopen_all.call(null,this);
			},
			close_all : function () {
				if(this.locked) return this.error("LOCKED");
				var _this = this;
				this.container.find("li.open").each( function () { _this.close_branch(this, true); });
			},
			show_lang : function (i) { 
				if(this.locked) return this.error("LOCKED");
				if(this.settings.languages[i] == this.current_lang) return true;
				var st = false;
				var id = this.container.attr("id") ? "#" + this.container.attr("id") : ".tree";
				st = get_css(id + " ." + this.current_lang, this.sn);
				if(st !== false) st.style.display = "none";
				st = get_css(id + " ." + this.settings.languages[i], this.sn);
				if(st !== false) st.style.display = "";
				this.current_lang = this.settings.languages[i];
				return true;
			},
			cycle_lang : function() {
				if(this.locked) return this.error("LOCKED");
				var i = $.inArray(this.current_lang, this.settings.languages);
				i ++;
				if(i > this.settings.languages.length - 1) i = 0;
				this.show_lang(i);
			},
			create : function (obj, ref_node, position) { 
				if(this.locked) return this.error("LOCKED");
				
				var root = false;
				if(ref_node == -1) { root = true; ref_node = this.container; }
				else ref_node = ref_node ? this.get_node(ref_node) : this.selected;

				if(!root && (!ref_node || !ref_node.size())) return this.error("CREATE: NO NODE SELECTED");

				var pos = position;

				var tmp = ref_node; // for type calculation
				if(position == "before") {
					position = ref_node.parent().children().index(ref_node);
					ref_node = ref_node.parents("li:eq(0)");
				}
				if(position == "after") {
					position = ref_node.parent().children().index(ref_node) + 1;
					ref_node = ref_node.parents("li:eq(0)");
				}
				if(!root && ref_node.size() == 0) { root = true; ref_node = this.container; }

				if(!root) {
					if(!this.check("creatable", ref_node)) return this.error("CREATE: CANNOT CREATE IN NODE");
					if(ref_node.hasClass("closed")) {
						if(this.settings.data.async && ref_node.children("ul").size() == 0) {
							var _this = this;
							return this.open_branch(ref_node, true, function () { _this.create.apply(_this, [obj, ref_node, position]); } );
						}
						else this.open_branch(ref_node, true);
					}
				}

				// creating new object to pass to parseJSON
				var torename = false; 
				if(!obj)	obj = {};
				else		obj = $.extend(true, {}, obj);
				if(!obj.attributes) obj.attributes = {};
				if(this.settings.rules.metadata) {
					if(!obj.attributes[this.settings.rules.metadata]) obj.attributes[this.settings.rules.metadata] = '{ "type" : "' + (this.get_type(tmp) || "") + '" }';
				}
				else {
					if(!obj.attributes[this.settings.rules.type_attr]) obj.attributes[this.settings.rules.type_attr] = this.get_type(tmp) || "";
				}
				if(this.settings.languages.length) {
					if(!obj.data) { obj.data = {}; torename = true; }
					for(var i = 0; i < this.settings.languages.length; i++) {
						if(!obj.data[this.settings.languages[i]]) obj.data[this.settings.languages[i]] = ((typeof this.settings.lang.new_node).toLowerCase() != "string" && this.settings.lang.new_node[i]) ? this.settings.lang.new_node[i] : this.settings.lang.new_node;
					}
				}
				else {
					if(!obj.data) { obj.data = this.settings.lang.new_node; torename = true; }
				}

				var $li = $(this.parseJSON(obj));
				if($li.children("ul").size()) {
					if(!$li.is(".open")) $li.addClass("closed");
				}
				else $li.addClass("leaf");
				$li.find("li:last-child").addClass("last").end().find("li:has(ul)").not(".open").addClass("closed");
				$li.find("li").not(".open").not(".closed").addClass("leaf");

				if(!root && this.settings.rules.use_inline && this.settings.rules.metadata) {
					var t = this.get_type($li) || "";
					$.metadata.setType("attr", this.settings.rules.metadata);
					if(typeof ref_node.metadata()["valid_children"] != "undefined") {
						if($.inArray(t, ref_node.metadata()["valid_children"]) == -1) return this.error("CREATE: NODE NOT A VALID CHILD");
					}
					if(typeof ref_node.metadata()["max_children"] != "undefined") {
						if( (ref_node.children("ul:eq(0)").children("li").size() + 1) > ref_node.metadata().max_children) return this.error("CREATE: MAX_CHILDREN REACHED");
					}
					var ok = true;
					if((typeof $(ref_node).metadata().max_depth).toLowerCase() != "undefined" && $(ref_node).metadata().max_depth === 0) ok = false;
					else {
						ref_node.parents("li").each(function(i) {
							if($(this).metadata().max_depth) {
								if( (i + 1) >= $(this).metadata().max_depth) {
									ok = false;
									return false;
								}
							}
						});
					}
					if(!ok) return this.error("CREATE: MAX_DEPTH REACHED");
				}

				if((typeof position).toLowerCase() == "undefined" || position == "inside") 
					position = (this.settings.rules.createat == "top") ? 0 : ref_node.children("ul:eq(0)").children("li").size();
				if(ref_node.children("ul").size() == 0 || (root == true && ref_node.children("ul").children("li").size() == 0) ) {
					if(!root)	var a = this.moved($li,ref_node.children("a:eq(0)"),"inside", true);
					else		var a = this.moved($li,this.container.children("ul:eq(0)"),"inside", true);
				}
				else if(pos == "before" && ref_node.children("ul:eq(0)").children("li:nth-child(" + (position + 1) + ")").size())
					var a = this.moved($li,ref_node.children("ul:eq(0)").children("li:nth-child(" + (position + 1) + ")").children("a:eq(0)"),"before", true);
				else if(pos == "after" &&  ref_node.children("ul:eq(0)").children("li:nth-child(" + (position) + ")").size())
					var a = this.moved($li,ref_node.children("ul:eq(0)").children("li:nth-child(" + (position) + ")").children("a:eq(0)"),"after", true);
				else if(ref_node.children("ul:eq(0)").children("li:nth-child(" + (position + 1) + ")").size())
					var a = this.moved($li,ref_node.children("ul:eq(0)").children("li:nth-child(" + (position + 1) + ")").children("a:eq(0)"),"before", true);
				else
					var a = this.moved($li,ref_node.children("ul:eq(0)").children("li:last").children("a:eq(0)"),"after",true);

				if(a === false) return this.error("CREATE: ABORTED");

				if(torename) {
					this.select_branch($li.children("a:eq(0)"));
					this.rename();
				}
				return $li;
			},
			rename : function (obj) {
				if(this.locked) return this.error("LOCKED");
				obj = obj ? this.get_node(obj) : this.selected;
				var _this = this;
				if(!obj || !obj.size()) return this.error("RENAME: NO NODE SELECTED");
				if(!this.check("renameable", obj)) return this.error("RENAME: NODE NOT RENAMABLE");
				if(!this.settings.callback.beforerename.call(null,obj.get(0), _this.current_lang, _this)) return this.error("RENAME: STOPPED BY USER");

				obj.parents("li.closed").each(function () { _this.open_branch(this) });
				if(this.current_lang)	obj = obj.find("a." + this.current_lang).get(0);
				else					obj = obj.find("a:first").get(0);
				last_value = obj.innerHTML;
				_this.inp = $("<input type='text' autocomplete='off' />");
				_this.inp
					.val(last_value.replace(/&amp;/g,"&").replace(/&gt;/g,">").replace(/&lt;/g,"<"))
					.bind("mousedown",		function (event) { event.stopPropagation(); })
					.bind("mouseup",		function (event) { event.stopPropagation(); })
					.bind("click",			function (event) { event.stopPropagation(); })
					.bind("keyup",			function (event) { 
							var key = event.keyCode || event.which;
							if(key == 27) { this.value = last_value; this.blur(); return }
							if(key == 13) { this.blur(); return }
						});

				// Rollback
				var rb = {}; 
				rb[this.container.attr("id")] = this.get_rollback();
					
				_this.inp.blur(function(event) {
						if(this.value == "") this.value = last_value; 
						$(obj).text( $(obj).parent().find("input").eq(0).attr("value") ).get(0).style.display = ""; 
						$(obj).prevAll("span").remove(); 
						_this.settings.callback.onrename.call(null, _this.get_node(obj).get(0), _this.current_lang, _this, rb);
						_this.inp = false;
					});
				var spn = $("<span />").addClass(obj.className).append(_this.inp);
				spn.attr("style", $(obj).attr("style"));
				obj.style.display = "none";
				$(obj).parent().prepend(spn);
				if (_this.inp) {
				    _this.inp.get(0).focus();
				}
                if (_this.inp) {
                    _this.inp.get(0).select();
                }
			},
			// REMOVE NODES
			remove : function(obj) {
				if(this.locked) return this.error("LOCKED");

				// Rollback
				var rb = {}; 
				rb[this.container.attr("id")] = this.get_rollback();

				if(obj && (!this.selected || this.get_node(obj).get(0) != this.selected.get(0) )) {
					obj = this.get_node(obj);
					if(obj.size()) {
						if(!this.check("deletable", obj)) return this.error("DELETE: NODE NOT DELETABLE");
						if(!this.settings.callback.beforedelete.call(null,obj.get(0), _this)) return this.error("DELETE: STOPPED BY USER");
						$parent = obj.parent();
						obj = obj.remove();
						$parent.children("li:last").addClass("last");
						if($parent.children("li").size() == 0) {
							$li = $parent.parents("li:eq(0)");
							$li.removeClass("open").removeClass("closed").addClass("leaf").children("ul").remove();
							this.set_cookie("open");
						}
						this.settings.callback.ondelete.call(null, obj.get(0), this, rb);
					}
				}
				else if(this.selected) {
					if(!this.check("deletable", this.selected)) return this.error("DELETE: NODE NOT DELETABLE");
					if(!this.settings.callback.beforedelete.call(null,this.selected.get(0), _this)) return this.error("DELETE: STOPPED BY USER");
					$parent = this.selected.parent();
					var obj = this.selected;
					if(this.settings.rules.multiple == false || this.selected_arr.length == 1) {
						var stop = true;
						var tmp = (this.selected.prev("li:eq(0)").size()) ? this.selected.prev("li:eq(0)") : this.selected.parents("li:eq(0)");
						// this.get_prev(true);
					}
					obj = obj.remove();
					$parent.children("li:last").addClass("last");
					if($parent.children("li").size() == 0) {
						$li = $parent.parents("li:eq(0)");
						$li.removeClass("open").removeClass("closed").addClass("leaf").children("ul").remove();
						this.set_cookie("open");
					}
					//this.selected = false;
					this.settings.callback.ondelete.call(null, obj.get(0), this, rb);
					if(stop && tmp) this.select_branch(tmp);
					if(this.settings.rules.multiple != false && !stop) {
						var _this = this;
						this.selected_arr = [];
						this.container.find("a.clicked").filter(":first-child").parent().each(function () {
							_this.selected_arr.push($(this));
						});
						if(this.selected_arr.length > 0) {
							this.selected = this.selected_arr[0];
							this.remove();
						}
					}
				}
				else return this.error("DELETE: NO NODE SELECTED");
			},

			next : function (obj, strict) {
				obj = this.get_node(obj);
				if(!obj.size()) return false;
				if(strict) return (obj.nextAll("li").size() > 0) ? obj.nextAll("li:eq(0)") : false;

				if(obj.hasClass("open")) return obj.find("li:eq(0)");
				else if(obj.nextAll("li").size() > 0) return obj.nextAll("li:eq(0)");
				else return obj.parents("li").next("li").eq(0);
			},
			prev : function(obj, strict) {
				obj = this.get_node(obj);
				if(!obj.size()) return false;
				if(strict) return (obj.prevAll("li").size() > 0) ? obj.prevAll("li:eq(0)") : false;

				if(obj.prev("li").size()) {
					var obj = obj.prev("li").eq(0);
					while(obj.hasClass("open")) obj = obj.children("ul:eq(0)").children("li:last");
					return obj;
				}
				else return obj.parents("li:eq(0)").size() ? obj.parents("li:eq(0)") : false;
			},
			parent : function(obj) {
				obj = this.get_node(obj);
				if(!obj.size()) return false;
				return obj.parents("li:eq(0)").size() ? obj.parents("li:eq(0)") : false;
			},
			children : function(obj) {
				obj = this.get_node(obj);
				if(!obj.size()) return false;
				return obj.children("ul:eq(0)").children("li");
			},

			// FOR EXPLORER-LIKE KEYBOARD SHORTCUTS
			get_next : function(force) {
				var obj = this.hovered || this.selected;
				return force ? this.select_branch(this.next(obj)) : this.hover_branch(this.next(obj));
			},
			get_prev : function(force) {
				var obj = this.hovered || this.selected;
				return force ? this.select_branch(this.prev(obj)) : this.hover_branch(this.prev(obj));
			},
			get_left : function(force, rtl) {
				if(this.settings.ui.rtl && !rtl) return this.get_right(force, true);
				var obj = this.hovered || this.selected;
				if(obj) {
					if(obj.hasClass("open"))	this.close_branch(obj);
					else {
						return force ? this.select_branch(this.parent(obj)) : this.hover_branch(this.parent(obj));
					}
				}
			},
			get_right : function(force, rtl) {
				if(this.settings.ui.rtl && !rtl) return this.get_left(force, true);
				var obj = this.hovered || this.selected;
				if(obj) {
					if(obj.hasClass("closed"))	this.open_branch(obj);
					else {
						return force ? this.select_branch(obj.find("li:eq(0)")) : this.hover_branch(obj.find("li:eq(0)"));
					}
				}
			},
			toggleDots : function () {
				if(this.settings.ui.dots) {
					this.settings.ui.dots = false;
					this.container.children("ul:eq(0)").addClass("no_dots");
				}
				else {
					this.settings.ui.dots = true;
					this.container.children("ul:eq(0)").removeClass("no_dots");
				}
			},
			toggleRTL : function () {
				if(this.settings.ui.rtl) {
					this.settings.ui.rtl = false;
					this.container.css("direction","ltr").children("ul:eq(0)").removeClass("rtl").addClass("ltr");
				}
				else {
					this.settings.ui.rtl = true;
					this.container.css("direction","rtl").children("ul:eq(0)").removeClass("ltr").addClass("rtl");
				}
			},
			set_cookie : function (type) {
				if(this.settings.cookies === false) return false;
				if(this.settings.cookies[type] === false) return false;
				switch(type) {
					case "selected":
						if(this.settings.rules.multiple != false && this.selected_arr.length > 1) {
							var val = Array();
							$.each(this.selected_arr, function () {
								if(this.attr("id")) { val.push(this.attr("id")); }
							});
							val = val.join(",");
						}
						else var val = this.selected ? this.selected.attr("id") : false;
						$.cookie(this.settings.cookies.prefix + '_selected',val,this.settings.cookies.opts);
						break;
					case "open":
						var str = "";
						this.container.find("li.open").each(function (i) { if(this.id) { str += this.id + ","; } });
						$.cookie(this.settings.cookies.prefix + '_open',str.replace(/,$/ig,""),this.settings.cookies.opts);
						break;
				}
			},
			get_rollback : function () {
				var rb = {};
				if(this.context.to_remove && this.context.apply_to) this.context.apply_to.children("a").removeClass("clicked");
				rb.html = this.container.html();
				if(this.context.to_remove && this.context.apply_to) this.context.apply_to.children("a").addClass("clicked");
				rb.selected = this.selected ? this.selected.attr("id") : false;
				return rb;
			},
			moved : function (what, where, how, is_new, is_copy, rb) {
				var what	= $(what);
				var $parent	= $(what).parents("ul:eq(0)");
				var $where	= $(where);

				// Rollback
				if(!rb) {
					var rb = {}; 
					rb[this.container.attr("id")] = this.get_rollback();
					if(!is_new) {
						var tmp = what.size() > 1 ? what.eq(0).parents(".tree:eq(0)") : what.parents(".tree:eq(0)");
						if(tmp.get(0) != this.container.get(0)) {
							tmp = tree_component.inst[tmp.attr("id")];
							rb[tmp.container.attr("id")] = tmp.get_rollback();
						}
						delete tmp;
					}
				}

				if(how == "inside" && this.settings.data.async && this.get_node($where).hasClass("closed")) {
					var _this = this;
					return this.open_branch(this.get_node($where), true, function () { _this.moved.apply(_this, [what, where, how, is_new, is_copy, rb]); });
				}

				// IF MULTIPLE
				if(what.size() > 1) {
					var _this = this;
					var tmp = this.moved(what.eq(0), where, how, false, is_copy, rb);
					what.each(function (i) {
						if(i == 0) return;
						if(tmp) { // if tmp is false - the previous move was a no-go
							tmp = _this.moved(this, tmp.children("a:eq(0)"), "after", false, is_copy, rb);
						}
					});
					return;
				}

				if(is_copy) {
					_what = what.clone();
					_what.each(function (i) {
						this.id = this.id + "_copy";
						$(this).find("li").each(function () {
							this.id = this.id + "_copy";
						});
						$(this).removeClass("dragged").find("a.clicked").removeClass("clicked ui-state-active").end().find("li.dragged").removeClass("dragged");
					});
				}
				else _what = what;
				if(is_new) {
					if(!this.settings.callback.beforecreate.call(null,this.get_node(what).get(0), this.get_node(where).get(0),how,this)) return false;
				}
				else {
					if(!this.settings.callback.beforemove.call(null,this.get_node(what).get(0), this.get_node(where).get(0),how,this)) return false;
				}

				if(!is_new) {
					var tmp = what.parents(".tree:eq(0)");
					// if different trees
					if(tmp.get(0) != this.container.get(0)) {
						tmp = tree_component.inst[tmp.attr("id")];

						// if there are languages - otherwise - no cleanup needed
						if(tmp.settings.languages.length) {
							var res = [];
							// if new tree has no languages - use current visible
							if(this.settings.languages.length == 0) res.push("." + tmp.current_lang);
							else {
								for(var i in this.settings.languages) {
									if(typeof this.settings.languages[i] == "function") continue;
									for(var j in tmp.settings.languages) {
										if(typeof tmp.settings.languages[j] == "function") continue;
										if(this.settings.languages[i] == tmp.settings.languages[j]) res.push("." + this.settings.languages[i]);
									}
								}
							}
							if(res.length == 0) return this.error("MOVE: NO COMMON LANGUAGES");
							what.find("a").not(res.join(",")).remove();
						}
						what.find("a.clicked").removeClass("clicked ui-state-active");
					}
				}
				what = _what;

				// ADD NODE TO NEW PLACE
				switch(how) {
					case "before":
						$where.parents("ul:eq(0)").children("li.last").removeClass("last");
						$where.parent().before(what.removeClass("last"));
						$where.parents("ul:eq(0)").children("li:last").addClass("last");
						break;
					case "after":
						$where.parents("ul:eq(0)").children("li.last").removeClass("last");
						$where.parent().after(what.removeClass("last"));
						$where.parents("ul:eq(0)").children("li:last").addClass("last");
						break;
					case "inside":
						if($where.parent().children("ul:first").size()) {
							if(this.settings.rules.createat == "top")	$where.parent().children("ul:first").prepend(what.removeClass("last")).children("li:last").addClass("last");
							else										$where.parent().children("ul:first").children(".last").removeClass("last").end().append(what.removeClass("last")).children("li:last").addClass("last");
						}
						else {
							what.addClass("last");
							$where.parent().append("<ul/>").removeClass("leaf").addClass("closed");
							$where.parent().children("ul:first").prepend(what);
						}
						if($where.parent().hasClass("closed")) { this.open_branch($where); }
						break;
					default:
						break;
				}
				// CLEANUP OLD PARENT
				if($parent.find("li").size() == 0) {
					var $li = $parent.parent();
					$li.removeClass("open").removeClass("closed").addClass("leaf");
					if(!$li.is(".tree")) $li.children("ul").remove();
					$li.parents("ul:eq(0)").children("li.last").removeClass("last").end().children("li:last").addClass("last");
					this.set_cookie("open");
				}
				else {
					$parent.children("li.last").removeClass("last");
					$parent.children("li:last").addClass("last");
				}

				// NO LONGER CORRECT WITH position PARAM - if(is_new && how != "inside") where = this.get_node(where).parents("li:eq(0)");
				if(is_copy)		this.settings.callback.oncopy.call(null, this.get_node(what).get(0), this.get_node(where).get(0), how, this, rb);
				else if(is_new)	this.settings.callback.oncreate.call(null, this.get_node(what).get(0), ($where.is("ul") ? -1 : this.get_node(where).get(0) ), how, this, rb);
				else			this.settings.callback.onmove.call(null, this.get_node(what).get(0), this.get_node(where).get(0), how, this, rb);
				return what;
			},
			error : function (code) {
				this.settings.callback.error.call(null,code,this);
				return false;
			},
			lock : function (state) {
				this.locked = state;
				if(this.locked)	this.container.children("ul:eq(0)").addClass("locked");
				else			this.container.children("ul:eq(0)").removeClass("locked");
			},
			cut : function (obj) {
				if(this.locked) return this.error("LOCKED");
				obj = obj ? this.get_node(obj) : this.container.find("a.clicked").filter(":first-child").parent();
				if(!obj || !obj.size()) return this.error("CUT: NO NODE SELECTED");
				this.copy_nodes = false;
				this.cut_nodes = obj;
			},
			copy : function (obj) {
				if(this.locked) return this.error("LOCKED");
				obj = obj ? this.get_node(obj) : this.container.find("a.clicked").filter(":first-child").parent();
				if(!obj || !obj.size()) return this.error("COPY: NO NODE SELECTED");
				this.copy_nodes = obj;
				this.cut_nodes = false;
			},
			paste : function (obj, position) {
				if(this.locked) return this.error("LOCKED");

				var root = false;
				if(obj == -1) { root = true; obj = this.container; }
				else obj = obj ? this.get_node(obj) : this.selected;

				if(!root && (!obj || !obj.size())) return this.error("PASTE: NO NODE SELECTED");
				if(!this.copy_nodes && !this.cut_nodes) return this.error("PASTE: NOTHING TO DO");

				var _this = this;

				var pos = position;

				if(position == "before") {
					position = obj.parent().children().index(obj);
					obj = obj.parents("li:eq(0)");
				}
				else if(position == "after") {
					position = obj.parent().children().index(obj) + 1;
					obj = obj.parents("li:eq(0)");
				}
				else if((typeof position).toLowerCase() == "undefined" || position == "inside") {
					position = (this.settings.rules.createat == "top") ? 0 : obj.children("ul:eq(0)").children("li").size();
				}
				if(!root && obj.size() == 0) { root = true; obj = this.container; }

				if(this.copy_nodes && this.copy_nodes.size()) {
					var ok = true;
					// This is copy - why forbid this?
					//obj.parents().andSelf().each(function () {
					//	if(_this.copy_nodes.index(this) != -1) {
					//		ok = false;
					//		return false;
					//	}
					//});
					if(!ok) return this.error("Invalid paste");
					if(!root && !this.checkMove(this.copy_nodes, obj.children("a:eq(0)"), "inside")) return false;

					if(obj.children("ul").size() == 0 || (root == true && obj.children("ul").children("li").size() == 0) ) {
						if(!root)	var a = this.moved(this.copy_nodes,obj.children("a:eq(0)"),"inside", false, true);
						else		var a = this.moved(this.copy_nodes,this.container.children("ul:eq(0)"),"inside", false, true);
					}
					else if(pos == "before" && obj.children("ul:eq(0)").children("li:nth-child(" + (position + 1) + ")").size())
						var a = this.moved(this.copy_nodes,obj.children("ul:eq(0)").children("li:nth-child(" + (position + 1) + ")").children("a:eq(0)"),"before", false, true);
					else if(pos == "after" && obj.children("ul:eq(0)").children("li:nth-child(" + (position) + ")").size())
						var a = this.moved(this.copy_nodes,obj.children("ul:eq(0)").children("li:nth-child(" + (position) + ")").children("a:eq(0)"),"after", false, true);
					else if(obj.children("ul:eq(0)").children("li:nth-child(" + (position + 1) + ")").size())
						var a = this.moved(this.copy_nodes,obj.children("ul:eq(0)").children("li:nth-child(" + (position + 1) + ")").children("a:eq(0)"),"before", false, true);
					else
						var a = this.moved(this.copy_nodes,obj.children("ul:eq(0)").children("li:last").children("a:eq(0)"),"after", false, true);
					this.copy_nodes = false;
				}
				if(this.cut_nodes && this.cut_nodes.size()) {
					var ok = true;
					obj.parents().andSelf().each(function () {
						if(_this.cut_nodes.index(this) != -1) {
							ok = false;
							return false;
						}
					});
					if(!ok) return this.error("Invalid paste");
					if(!root && !this.checkMove(this.cut_nodes, obj.children("a:eq(0)"), "inside")) return false;

					if(obj.children("ul").size() == 0 || (root == true && obj.children("ul").children("li").size() == 0) ) {
						if(!root)	var a = this.moved(this.cut_nodes,obj.children("a:eq(0)"),"inside");
						else		var a = this.moved(this.cut_nodes,this.container.children("ul:eq(0)"),"inside");
					}
					else if(pos == "before" && obj.children("ul:eq(0)").children("li:nth-child(" + (position + 1) + ")").size())
						var a = this.moved(this.cut_nodes,obj.children("ul:eq(0)").children("li:nth-child(" + (position + 1) + ")").children("a:eq(0)"),"before");
					else if(pos == "after" && obj.children("ul:eq(0)").children("li:nth-child(" + (position) + ")").size())
						var a = this.moved(this.cut_nodes,obj.children("ul:eq(0)").children("li:nth-child(" + (position) + ")").children("a:eq(0)"),"after");
					else if(obj.children("ul:eq(0)").children("li:nth-child(" + (position + 1) + ")").size())
						var a = this.moved(this.cut_nodes,obj.children("ul:eq(0)").children("li:nth-child(" + (position + 1) + ")").children("a:eq(0)"),"before");
					else
						var a = this.moved(this.cut_nodes,obj.children("ul:eq(0)").children("li:last").children("a:eq(0)"),"after");
					this.cut_nodes = false;
				}
			},
			search : function(str) {
				var _this = this;
				if(!str || (this.srch && str != this.srch) ) {
					this.srch = "";
					this.srch_opn = false;
					this.container.find("a.search").removeClass("search ui-state-highlight");
				}
				this.srch = str;
				if(!str) return;
				if(this.settings.data.async) {
					if(!this.srch_opn) {
						var dd = $.extend( { "search" : str } , this.settings.data.async_data(false, this) );
						$.ajax({
							type		: this.settings.data.method,
							url			: this.settings.data.url, 
							data		: dd, 
							dataType	: "text",
							success		: function (data) {
								_this.srch_opn = $.unique(data.split(","));
								_this.search.apply(_this,[str]);
							} 
						});
					}
					else if(this.srch_opn.length) {
						if(this.srch_opn && this.srch_opn.length) {
							var opn = false;
							for(var j = 0; j < this.srch_opn.length; j++) {
								if(this.get_node("#" + this.srch_opn[j]).size() > 0) {
									opn = true;
									var tmp = "#" + this.srch_opn[j];
									delete this.srch_opn[j];
									this.open_branch(tmp, true, function () { _this.search.apply(_this,[str]); } );
								}
							}
							if(!opn) {
								this.srch_opn = [];
								 _this.search.apply(_this,[str]);
							}
						}
					}
					else {
						var selector = "a";
						// IF LANGUAGE VERSIONS
						if(this.settings.languages.length) selector += "." + this.current_lang;
						this.container.find(selector + ":contains('" + str + "')").addClass( this.settings.ui.theme_name == "themeroller" ? "search ui-state-highlight" : "search");
						this.srch_opn = false;
					}
				}
				else {
					var selector = "a";
					// IF LANGUAGE VERSIONS
					if(this.settings.languages.length) selector += "." + this.current_lang;
					this.container.find(selector + ":contains('" + str + "')").addClass( this.settings.ui.theme_name == "themeroller" ? "search ui-state-highlight" : "search").parents("li.closed").each( function () { _this.open_branch(this, true); });
				}
			},

			destroy : function() {
				this.hide_context();
				this.container.unbind(".jstree");
				$("#" + this.container.attr("id")).die("click.jstree").die("dblclick.jstree").die("contextmenu.jstree").die("mouseover.jstree").die("mouseout.jstree").die("mousedown.jstree");
				this.container.removeClass("tree ui-widget ui-widget-content tree-default tree-" + this.settings.ui.theme_name).children("ul").removeClass("no_dots rtl ltr locked").find("li").removeClass("leaf").removeClass("open").removeClass("closed").removeClass("last").children("a").removeClass("clicked hover search ui-state-active ui-state-hover ui-state-highlight ui-state-default");

				if(this.cntr == tree_component.focused) {
					for(var i in tree_component.inst) {
						if(i != this.cntr && i != this.container.attr("id")) {
							tree_component.inst[i].focus();
							break;
						}
					}
				}

				tree_component.inst[this.cntr] = false;
				tree_component.inst[this.container.attr("id")] = false;
				delete tree_component.inst[this.cntr];
				delete tree_component.inst[this.container.attr("id")];
				tree_component.cntr --;
			}
		}
	};
})(jQuery);
function ManageCategories_confirmDelete(tree_obj, node) {
    if (node.getAttribute('ctg_id') == '1') {
        return false;
    }
    return confirm(msg_ctg_del_cfm);
}

var flag_is_tree_changed = false;
function ManageCategories_isTreeChanged()
{
    return flag_is_tree_changed;
}
function ManageCategories_onTreeChanged(tree_obj) 
{
    if (tree_obj.settings.save_alert) {
        tree_obj.settings.save_alert.css('left', 
                tree_obj.settings.save_alert.parent().width() - tree_obj.settings.save_alert.width() - 36);
        tree_obj.settings.save_alert.fadeIn(300);
    }
    enableButton('btn_saveCategoriesTree', function() { SaveCategoriesTree(tree_obj); });
    //enableButton('btn_saveCategoriesTree2', function() { SaveCategoriesTree(tree_obj); });
    flag_is_tree_changed = true;
}
function ManageCategories_onTreeUnchanged(tree_obj, after_fadeout) 
{
    if (tree_obj.settings.save_alert) {
        tree_obj.settings.save_alert.fadeOut(200, after_fadeout);
    }
    disableButton('btn_saveCategoriesTree');
    //disableButton('btn_saveCategoriesTree2');
    flag_is_tree_changed = false;
}

function CopyTreeState(tree_obj, node, node_json)
{
    if (node.is('.closed')) node_json.state = 'closed';
    if (node.is('.open')) node_json.state = 'open';
    var a_obj = node.children('a');
    if (node.attr('id') == undefined || node.attr('id') == '') {
        node.attr('id', node_json.attributes.id);
        node.attr('ctg_id', node_json.attributes.ctg_id);
    }
    if (node_json.children && node_json.children.length) {
        node.children('ul').children('li').each(function() {
        	var $cat = $(this);
        	var cat_name = $cat.children('a').text();
        	for (var i = 0; i < node_json.children.length; i++) {
            	if (cat_name == node_json.children[i].data) {
            		CopyTreeState(tree_obj, $cat, node_json.children[i]);
            	}
        	}
        });
    }
}

function SaveCategoriesTree(tree_obj) 
{
    ManageCategories_onTreeUnchanged(tree_obj, function() { 
        tree_obj.settings.saving_msg.css('left', 
                tree_obj.settings.saving_msg.parent().width() - tree_obj.settings.saving_msg.width() - 36);
        tree_obj.settings.saving_msg.fadeIn(300); 
        });
    
    var root_node = tree_obj.container.children("ul").children("li");
    if (root_node.size() > 0) {
        var tree_str = ConvertTreeToStr(root_node, 0);
    } else {
        var tree_str = '';
    }
    var ctg_id = '';
    if (tree_obj.selected) {
        ctg_id = tree_obj.selected.attr('ctg_id');
    }
    
    jQuery.post(
            'jquery_ajax_handler.php', // backend
            {
                'asc_action': 'save_ctg_tree',
                'tree_id' : tree_obj.settings.uniq_id,
                'ctg_id' : ctg_id,
                'tree_str': tree_str
            },
            function(result, errors) 
            {
                var tree_json = 'tree_json = ' + result.tree_json;
                tree_json = eval(tree_json);
                var root_node = tree_obj.container.children('ul').children('li');
                CopyTreeState(tree_obj, root_node, tree_json);
                tree_obj.settings.data.json = tree_json;
                tree_obj.refresh();
                if (tree_obj.settings.saving_msg) {
                    tree_obj.settings.saving_msg.fadeOut(200);
                }
                ManageCategories_adjustUndoOrigin();
                ManageCategories_turnButtons(tree_obj);
                ResetWholeCache(tree_obj);
            },
            'json'
    );    
}

function ConvertTreeToStr(node, level)
{
    var ctg_id = node.attr('ctg_id') || 'new';
    var node_name = node.children('a').text().replace('\t',' ').replace('\n',' ');
    var str = ctg_id+'\t'+level+'\t'+node_name+'\n';
    if(node.children('ul').size() > 0) {
        node.children('ul').children('li').each(function (idx, elm) {
            str += ConvertTreeToStr($(elm), level+1);
        });
    }
    return str;
}

function ManageCategories_editCategory(tree_obj, node)
{
    OpenCtgWindow('EditCat', url_ctg_edit, 'Edit', tree_obj, node);
}

var current_category_id = null;
function ManageCategories_onNodeSelected(tree_obj, node) 
{
    if (node != false) {
        var ctg_id = node.getAttribute('ctg_id');
        if (ctg_id != null) {
        	current_category_id = ctg_id;
            enableButton('mng_ctg_add',  function() { OpenCtgWindow('AddCat',  url_ctg_add, 'AddCat', tree_obj, node); } );
            enableButton('mng_ctg_edit', function() { OpenCtgWindow('EditCat', url_ctg_edit, 'Edit', tree_obj, node); } );
            LoadCategoryReview(tree_obj, ctg_id);
        }
        else {
            disableButton('mng_ctg_add');
            disableButton('mng_ctg_edit');
            hideBlock('cat_review_loading');
            hideBlock('cat_review_content');
            hideBlock('cat_review_choose');
            showBlock('cat_review_save', 1000);
        }
        if (ctg_id == '1') {
            disableButton('mng_ctg_del');
        }
        else {
            enableButton('mng_ctg_del',  function() { tree_obj.remove(node); } );
        }
    } else {
        disableButton('mng_ctg_add');
        disableButton('mng_ctg_edit');
        disableButton('mng_ctg_del');
        hideBlock('cat_review_loading');
        hideBlock('cat_review_content');
        hideBlock('cat_review_choose');
        showBlock('cat_review_save', 1000);
    }
}
function ManageCategories_onGoToProducts(node, url)
{
    if (node != false) {
        var ctg_id = node.getAttribute('ctg_id');
        window.location.href = url + ctg_id;
    }    
}

var categories_undo_stack = [];
var categories_undo_pointer = 0;
var categories_undo_origin = 0;
function ManageCategories_incrementUndoOrigin(tree_obj)
{
    if (! ManageCategories_isTreeChanged()) {
        categories_undo_origin ++;
    }
}
function ManageCategories_adjustUndoOrigin()
{
    categories_undo_origin = categories_undo_pointer;
}
function ManageCategories_turnButtons(tree_obj)
{
    if (categories_undo_pointer == categories_undo_origin) {
        if (ManageCategories_isTreeChanged(tree_obj)) {
            ManageCategories_onTreeUnchanged(tree_obj);
        }
    }
    else {
        if (! ManageCategories_isTreeChanged(tree_obj)) {
            ManageCategories_onTreeChanged(tree_obj);
        }
    }
    
    if (categories_undo_pointer > 0) {
        enableButton('btn_undoCategoriesTree', function() { ManageCategories_undo(tree_obj); return false; });
    }
    else {
        disableButton('btn_undoCategoriesTree');
    }
    
    if (categories_undo_stack.length > 0 && 
            categories_undo_pointer < categories_undo_stack.length - 1) {
        enableButton('btn_redoCategoriesTree', function () { ManageCategories_redo(tree_obj); return false; });
    }
    else {
        disableButton('btn_redoCategoriesTree');
    }
}
function ManageCategories_pushRollback(tree_obj, rb, current_state)
{
    if (categories_undo_pointer > categories_undo_stack.length) {
        categories_undo_pointer = categories_undo_stack.length;
    }
    categories_undo_stack[ categories_undo_pointer++ ] = rb;
    categories_undo_stack.length = categories_undo_pointer;
    categories_undo_stack[ categories_undo_pointer ] = current_state;
    ManageCategories_turnButtons(tree_obj);
}
function ManageCategories_overwriteRollback(tree_obj, rb, current_state)
{
    if (categories_undo_pointer > categories_undo_stack.length) {
        categories_undo_pointer = categories_undo_stack.length;
    }
    if (categories_undo_pointer > 0) {
        categories_undo_stack[ categories_undo_pointer ] = current_state;
    }
}
function ManageCategories_undo(tree_obj)
{
    if (categories_undo_stack.length > 0) {
        var rb = categories_undo_stack[ --categories_undo_pointer ];
        $.tree_rollback(rb);
        tree_obj.reselect();
        ManageCategories_onNodeSelected(tree_obj, tree_obj.selected.get(0));
    }
    ManageCategories_turnButtons(tree_obj);
}

function ManageCategories_redo(tree_obj)
{
    if (categories_undo_stack.length > 0 && 
            categories_undo_pointer < categories_undo_stack.length - 1) {
        var cs = categories_undo_stack[ ++categories_undo_pointer ];
        $.tree_rollback(cs);
        tree_obj.reselect();
        ManageCategories_onNodeSelected(tree_obj, tree_obj.selected.get(0));
    }
    ManageCategories_turnButtons(tree_obj);
}

var categories_reviews_cache = [];
function ResetCategoryReview(tree_obj, ctg_id)
{
    categories_reviews_cache[ctg_id] = undefined;
}
function ResetWholeCache(tree_obj)
{
	categories_reviews_cache = [];
    ReloadCategoryReview(tree_obj, tree_obj.selected.attr('ctg_id'))
}
function ReloadCategoryReview(tree_obj, ctg_id)
{
    ResetCategoryReview(tree_obj, ctg_id);
    LoadCategoryReview(tree_obj, ctg_id);
}
function LoadCategoryReview(tree_obj, ctg_id)
{
    if (categories_reviews_cache[ctg_id]) {
        putHtmlToElement('cat_review_content', categories_reviews_cache[ctg_id]);
        hideBlock('cat_review_choose');
        hideBlock('cat_review_save');
        hideBlock('cat_review_loading');
        showBlock('cat_review_content', 1000);
    }
    else {
        var current_ctg_id = tree_obj.selected.attr('ctg_id');
        if (current_ctg_id == ctg_id) {
        	hideBlock('cat_review_choose');
        	hideBlock('cat_review_save');
        	hideBlock('cat_review_content');
        	showBlock('cat_review_loading', 1000);
        }
        jQuery.post(
                'jquery_ajax_handler.php', // backend
                {
                    'asc_action': 'get_ctg_review',
                    'category_id': ctg_id
                },
                function(result, errors) 
                {
                    if (result == null) {
                        var loading = document.getElementById('cat_review_loading');
                        if (loading.style.display != 'none') {
                            hideBlock('cat_review_save');
                            hideBlock('cat_review_loading');
                            hideBlock('cat_review_content');
                            showBlock('cat_review_choose', 1000);
                        }                        
                    }
                    else {
                        categories_reviews_cache[ctg_id] = result['review'];
                        if (current_ctg_id == ctg_id) {
                            putHtmlToElement('cat_review_content', result['review']);
                            var loading = document.getElementById('cat_review_loading');
                            if (loading.style.display != 'none') {
                                hideBlock('cat_review_save');
                                hideBlock('cat_review_loading');
                                showBlock('cat_review_content', 1000);
                            }
                        }
                    }
                },
                'json'
        );
    }
}

function OpenCtgWindow(windowName, windowURL, action, tree_obj, node)
{
    var URL = windowURL + node.getAttribute('ctg_id') + '&tree_id=' + tree_obj.container[0].id;
    var newWin = openURLinNewWindow(URL, windowName);
    newWin.focus();
}

function ManageCategories_updateCategoryName(tree_id, ctg_id, new_name)
{
    $('#'+tree_id).find('li').filter(
            function(i){ return $(this).attr('ctg_id') == ctg_id; }
            ).children('a').html(new_name);
    ManageCategories_reloadCategoryReview(tree_id, ctg_id);
}

function ManageCategories_reloadCategoryReview(tree_id, ctg_id)
{
    ReloadCategoryReview($.tree_reference(tree_id), ctg_id);
}

function ManageCategories_addSubCategory(tree_id, parent_id, ctg_id, new_name)
{
    var tree_obj = $.tree_reference(tree_id);
    var parent = $('#'+tree_id).find('li').filter(
            function(i){ return $(this).attr('ctg_id') == parent_id; }
            ).get(0);
    if (tree_obj && parent) {
        var child_obj = { 
                attributes: { 
                    id: tree_id+'_cat_'+ctg_id, 
                    ctg_id: ctg_id, 
                    rel: 'folder' 
                },
                data: new_name
            };
        var new_node = tree_obj.create(child_obj, parent);
        tree_obj.select_branch(new_node);
    }
}

// Copyright й 2000 by Apple Computer, Inc., All Rights Reserved.
//
// You may incorporate this Apple sample code into your own code
// without restriction. This Apple sample code has been provided "AS IS"
// and the responsibility for its operation is yours. You may redistribute
// this code, but you are not permitted to redistribute it as
// "Apple sample code" after having made changes.
//
// ************************
// layer utility routines *
// ************************

function getStyleObject(objectId) {
    // cross-browser function to get an object's style object given its id
    if(document.getElementById && document.getElementById(objectId)) {
	// W3C DOM
	return document.getElementById(objectId).style;
    } else if (document.all && document.all(objectId)) {
	// MSIE 4 DOM
	return document.all(objectId).style;
    } else if (document.layers && document.layers[objectId]) {
	// NN 4 DOM.. note: this won't find nested layers
	return document.layers[objectId];
    } else {
	return false;
    }
} // getStyleObject

function changeObjectVisibility(objectId, newVisibility) {
    // get a reference to the cross-browser style object and make sure the object exists
    var styleObject = getStyleObject(objectId);
    if(styleObject) {
	styleObject.visibility = newVisibility;
	return true;
    } else {
	// we couldn't find the object, so we can't change its visibility
	return false;
    }
} // changeObjectVisibility

function moveObject(objectId, newXCoordinate, newYCoordinate) {
    // get a reference to the cross-browser style object and make sure the object exists
    var styleObject = getStyleObject(objectId);
    if(styleObject) {
	styleObject.left = newXCoordinate;
	styleObject.top = newYCoordinate;
	return true;
    } else {
	// we couldn't find the object, so we can't very well move it
	return false;
    }
} // moveObject



/*
 * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
 * Digest Algorithm, as defined in RFC 1321.
 * Version 2.2 Copyright (C) Paul Johnston 1999 - 2009
 * Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
 * Distributed under the BSD License
 * See http://pajhome.org.uk/crypt/md5 for more info.
 */

/*
 * Configurable variables. You may need to tweak these to be compatible with
 * the server-side, but the defaults work in most cases.
 */
var hexcase = 0;   /* hex output format. 0 - lowercase; 1 - uppercase        */
var b64pad  = "";  /* base-64 pad character. "=" for strict RFC compliance   */

/*
 * These are the functions you'll usually want to call
 * They take string arguments and return either hex or base-64 encoded strings
 */
function hex_md5(s)    { return rstr2hex(rstr_md5(str2rstr_utf8(s))); }
function b64_md5(s)    { return rstr2b64(rstr_md5(str2rstr_utf8(s))); }
function any_md5(s, e) { return rstr2any(rstr_md5(str2rstr_utf8(s)), e); }
function hex_hmac_md5(k, d)
  { return rstr2hex(rstr_hmac_md5(str2rstr_utf8(k), str2rstr_utf8(d))); }
function b64_hmac_md5(k, d)
  { return rstr2b64(rstr_hmac_md5(str2rstr_utf8(k), str2rstr_utf8(d))); }
function any_hmac_md5(k, d, e)
  { return rstr2any(rstr_hmac_md5(str2rstr_utf8(k), str2rstr_utf8(d)), e); }

/*
 * Perform a simple self-test to see if the VM is working
 */
function md5_vm_test()
{
  return hex_md5("abc").toLowerCase() == "900150983cd24fb0d6963f7d28e17f72";
}

/*
 * Calculate the MD5 of a raw string
 */
function rstr_md5(s)
{
  return binl2rstr(binl_md5(rstr2binl(s), s.length * 8));
}

/*
 * Calculate the HMAC-MD5, of a key and some data (raw strings)
 */
function rstr_hmac_md5(key, data)
{
  var bkey = rstr2binl(key);
  if(bkey.length > 16) bkey = binl_md5(bkey, key.length * 8);

  var ipad = Array(16), opad = Array(16);
  for(var i = 0; i < 16; i++)
  {
    ipad[i] = bkey[i] ^ 0x36363636;
    opad[i] = bkey[i] ^ 0x5C5C5C5C;
  }

  var hash = binl_md5(ipad.concat(rstr2binl(data)), 512 + data.length * 8);
  return binl2rstr(binl_md5(opad.concat(hash), 512 + 128));
}

/*
 * Convert a raw string to a hex string
 */
function rstr2hex(input)
{
  try { hexcase } catch(e) { hexcase=0; }
  var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
  var output = "";
  var x;
  for(var i = 0; i < input.length; i++)
  {
    x = input.charCodeAt(i);
    output += hex_tab.charAt((x >>> 4) & 0x0F)
           +  hex_tab.charAt( x        & 0x0F);
  }
  return output;
}

/*
 * Convert a raw string to a base-64 string
 */
function rstr2b64(input)
{
  try { b64pad } catch(e) { b64pad=''; }
  var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
  var output = "";
  var len = input.length;
  for(var i = 0; i < len; i += 3)
  {
    var triplet = (input.charCodeAt(i) << 16)
                | (i + 1 < len ? input.charCodeAt(i+1) << 8 : 0)
                | (i + 2 < len ? input.charCodeAt(i+2)      : 0);
    for(var j = 0; j < 4; j++)
    {
      if(i * 8 + j * 6 > input.length * 8) output += b64pad;
      else output += tab.charAt((triplet >>> 6*(3-j)) & 0x3F);
    }
  }
  return output;
}

/*
 * Convert a raw string to an arbitrary string encoding
 */
function rstr2any(input, encoding)
{
  var divisor = encoding.length;
  var i, j, q, x, quotient;

  /* Convert to an array of 16-bit big-endian values, forming the dividend */
  var dividend = Array(Math.ceil(input.length / 2));
  for(i = 0; i < dividend.length; i++)
  {
    dividend[i] = (input.charCodeAt(i * 2) << 8) | input.charCodeAt(i * 2 + 1);
  }

  /*
   * Repeatedly perform a long division. The binary array forms the dividend,
   * the length of the encoding is the divisor. Once computed, the quotient
   * forms the dividend for the next step. All remainders are stored for later
   * use.
   */
  var full_length = Math.ceil(input.length * 8 /
                                    (Math.log(encoding.length) / Math.log(2)));
  var remainders = Array(full_length);
  for(j = 0; j < full_length; j++)
  {
    quotient = Array();
    x = 0;
    for(i = 0; i < dividend.length; i++)
    {
      x = (x << 16) + dividend[i];
      q = Math.floor(x / divisor);
      x -= q * divisor;
      if(quotient.length > 0 || q > 0)
        quotient[quotient.length] = q;
    }
    remainders[j] = x;
    dividend = quotient;
  }

  /* Convert the remainders to the output string */
  var output = "";
  for(i = remainders.length - 1; i >= 0; i--)
    output += encoding.charAt(remainders[i]);

  return output;
}

/*
 * Encode a string as utf-8.
 * For efficiency, this assumes the input is valid utf-16.
 */
function str2rstr_utf8(input)
{
  var output = "";
  var i = -1;
  var x, y;

  while(++i < input.length)
  {
    /* Decode utf-16 surrogate pairs */
    x = input.charCodeAt(i);
    y = i + 1 < input.length ? input.charCodeAt(i + 1) : 0;
    if(0xD800 <= x && x <= 0xDBFF && 0xDC00 <= y && y <= 0xDFFF)
    {
      x = 0x10000 + ((x & 0x03FF) << 10) + (y & 0x03FF);
      i++;
    }

    /* Encode output as utf-8 */
    if(x <= 0x7F)
      output += String.fromCharCode(x);
    else if(x <= 0x7FF)
      output += String.fromCharCode(0xC0 | ((x >>> 6 ) & 0x1F),
                                    0x80 | ( x         & 0x3F));
    else if(x <= 0xFFFF)
      output += String.fromCharCode(0xE0 | ((x >>> 12) & 0x0F),
                                    0x80 | ((x >>> 6 ) & 0x3F),
                                    0x80 | ( x         & 0x3F));
    else if(x <= 0x1FFFFF)
      output += String.fromCharCode(0xF0 | ((x >>> 18) & 0x07),
                                    0x80 | ((x >>> 12) & 0x3F),
                                    0x80 | ((x >>> 6 ) & 0x3F),
                                    0x80 | ( x         & 0x3F));
  }
  return output;
}

/*
 * Encode a string as utf-16
 */
function str2rstr_utf16le(input)
{
  var output = "";
  for(var i = 0; i < input.length; i++)
    output += String.fromCharCode( input.charCodeAt(i)        & 0xFF,
                                  (input.charCodeAt(i) >>> 8) & 0xFF);
  return output;
}

function str2rstr_utf16be(input)
{
  var output = "";
  for(var i = 0; i < input.length; i++)
    output += String.fromCharCode((input.charCodeAt(i) >>> 8) & 0xFF,
                                   input.charCodeAt(i)        & 0xFF);
  return output;
}

/*
 * Convert a raw string to an array of little-endian words
 * Characters >255 have their high-byte silently ignored.
 */
function rstr2binl(input)
{
  var output = Array(input.length >> 2);
  for(var i = 0; i < output.length; i++)
    output[i] = 0;
  for(var i = 0; i < input.length * 8; i += 8)
    output[i>>5] |= (input.charCodeAt(i / 8) & 0xFF) << (i%32);
  return output;
}

/*
 * Convert an array of little-endian words to a string
 */
function binl2rstr(input)
{
  var output = "";
  for(var i = 0; i < input.length * 32; i += 8)
    output += String.fromCharCode((input[i>>5] >>> (i % 32)) & 0xFF);
  return output;
}

/*
 * Calculate the MD5 of an array of little-endian words, and a bit length.
 */
function binl_md5(x, len)
{
  /* append padding */
  x[len >> 5] |= 0x80 << ((len) % 32);
  x[(((len + 64) >>> 9) << 4) + 14] = len;

  var a =  1732584193;
  var b = -271733879;
  var c = -1732584194;
  var d =  271733878;

  for(var i = 0; i < x.length; i += 16)
  {
    var olda = a;
    var oldb = b;
    var oldc = c;
    var oldd = d;

    a = md5_ff(a, b, c, d, x[i+ 0], 7 , -680876936);
    d = md5_ff(d, a, b, c, x[i+ 1], 12, -389564586);
    c = md5_ff(c, d, a, b, x[i+ 2], 17,  606105819);
    b = md5_ff(b, c, d, a, x[i+ 3], 22, -1044525330);
    a = md5_ff(a, b, c, d, x[i+ 4], 7 , -176418897);
    d = md5_ff(d, a, b, c, x[i+ 5], 12,  1200080426);
    c = md5_ff(c, d, a, b, x[i+ 6], 17, -1473231341);
    b = md5_ff(b, c, d, a, x[i+ 7], 22, -45705983);
    a = md5_ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
    d = md5_ff(d, a, b, c, x[i+ 9], 12, -1958414417);
    c = md5_ff(c, d, a, b, x[i+10], 17, -42063);
    b = md5_ff(b, c, d, a, x[i+11], 22, -1990404162);
    a = md5_ff(a, b, c, d, x[i+12], 7 ,  1804603682);
    d = md5_ff(d, a, b, c, x[i+13], 12, -40341101);
    c = md5_ff(c, d, a, b, x[i+14], 17, -1502002290);
    b = md5_ff(b, c, d, a, x[i+15], 22,  1236535329);

    a = md5_gg(a, b, c, d, x[i+ 1], 5 , -165796510);
    d = md5_gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
    c = md5_gg(c, d, a, b, x[i+11], 14,  643717713);
    b = md5_gg(b, c, d, a, x[i+ 0], 20, -373897302);
    a = md5_gg(a, b, c, d, x[i+ 5], 5 , -701558691);
    d = md5_gg(d, a, b, c, x[i+10], 9 ,  38016083);
    c = md5_gg(c, d, a, b, x[i+15], 14, -660478335);
    b = md5_gg(b, c, d, a, x[i+ 4], 20, -405537848);
    a = md5_gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
    d = md5_gg(d, a, b, c, x[i+14], 9 , -1019803690);
    c = md5_gg(c, d, a, b, x[i+ 3], 14, -187363961);
    b = md5_gg(b, c, d, a, x[i+ 8], 20,  1163531501);
    a = md5_gg(a, b, c, d, x[i+13], 5 , -1444681467);
    d = md5_gg(d, a, b, c, x[i+ 2], 9 , -51403784);
    c = md5_gg(c, d, a, b, x[i+ 7], 14,  1735328473);
    b = md5_gg(b, c, d, a, x[i+12], 20, -1926607734);

    a = md5_hh(a, b, c, d, x[i+ 5], 4 , -378558);
    d = md5_hh(d, a, b, c, x[i+ 8], 11, -2022574463);
    c = md5_hh(c, d, a, b, x[i+11], 16,  1839030562);
    b = md5_hh(b, c, d, a, x[i+14], 23, -35309556);
    a = md5_hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
    d = md5_hh(d, a, b, c, x[i+ 4], 11,  1272893353);
    c = md5_hh(c, d, a, b, x[i+ 7], 16, -155497632);
    b = md5_hh(b, c, d, a, x[i+10], 23, -1094730640);
    a = md5_hh(a, b, c, d, x[i+13], 4 ,  681279174);
    d = md5_hh(d, a, b, c, x[i+ 0], 11, -358537222);
    c = md5_hh(c, d, a, b, x[i+ 3], 16, -722521979);
    b = md5_hh(b, c, d, a, x[i+ 6], 23,  76029189);
    a = md5_hh(a, b, c, d, x[i+ 9], 4 , -640364487);
    d = md5_hh(d, a, b, c, x[i+12], 11, -421815835);
    c = md5_hh(c, d, a, b, x[i+15], 16,  530742520);
    b = md5_hh(b, c, d, a, x[i+ 2], 23, -995338651);

    a = md5_ii(a, b, c, d, x[i+ 0], 6 , -198630844);
    d = md5_ii(d, a, b, c, x[i+ 7], 10,  1126891415);
    c = md5_ii(c, d, a, b, x[i+14], 15, -1416354905);
    b = md5_ii(b, c, d, a, x[i+ 5], 21, -57434055);
    a = md5_ii(a, b, c, d, x[i+12], 6 ,  1700485571);
    d = md5_ii(d, a, b, c, x[i+ 3], 10, -1894986606);
    c = md5_ii(c, d, a, b, x[i+10], 15, -1051523);
    b = md5_ii(b, c, d, a, x[i+ 1], 21, -2054922799);
    a = md5_ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
    d = md5_ii(d, a, b, c, x[i+15], 10, -30611744);
    c = md5_ii(c, d, a, b, x[i+ 6], 15, -1560198380);
    b = md5_ii(b, c, d, a, x[i+13], 21,  1309151649);
    a = md5_ii(a, b, c, d, x[i+ 4], 6 , -145523070);
    d = md5_ii(d, a, b, c, x[i+11], 10, -1120210379);
    c = md5_ii(c, d, a, b, x[i+ 2], 15,  718787259);
    b = md5_ii(b, c, d, a, x[i+ 9], 21, -343485551);

    a = safe_add(a, olda);
    b = safe_add(b, oldb);
    c = safe_add(c, oldc);
    d = safe_add(d, oldd);
  }
  return Array(a, b, c, d);
}

/*
 * These functions implement the four basic operations the algorithm uses.
 */
function md5_cmn(q, a, b, x, s, t)
{
  return safe_add(bit_rol(safe_add(safe_add(a, q), safe_add(x, t)), s),b);
}
function md5_ff(a, b, c, d, x, s, t)
{
  return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
}
function md5_gg(a, b, c, d, x, s, t)
{
  return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
}
function md5_hh(a, b, c, d, x, s, t)
{
  return md5_cmn(b ^ c ^ d, a, b, x, s, t);
}
function md5_ii(a, b, c, d, x, s, t)
{
  return md5_cmn(c ^ (b | (~d)), a, b, x, s, t);
}

/*
 * Add integers, wrapping at 2^32. This uses 16-bit operations internally
 * to work around bugs in some JS interpreters.
 */
function safe_add(x, y)
{
  var lsw = (x & 0xFFFF) + (y & 0xFFFF);
  var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
  return (msw << 16) | (lsw & 0xFFFF);
}

/*
 * Bitwise rotate a 32-bit number to the left.
 */
function bit_rol(num, cnt)
{
  return (num << cnt) | (num >>> (32 - cnt));
}
/*
 *********************************************
 * Vertical Menu
 * @date 03.06.2014
 * @author HBWSL
 *********************************************
 */

$(document).ready(function(){

/* for top-right menu */
	$("#TopRightHeader .HBars").click(function(){
		$("#adminUserInfo").toggle(150);
	});
/* for search box */
	$('#div_search').hover(function(){
		 $("#search_text").toggle();
		 $("#search_text_box").focus();
	});

/* to add onhover effects */
	$("#mainmenu > li > a").click(function(){
		var $isVis = $(this).siblings("div").is(":visible");
		$("#mainmenu").find("div").hide();
		$("#mainmenu > li > a").parent().removeClass("hovered");
		if($isVis){
			$(this).siblings("div").hide(100);
			$(this).parent().removeClass("hovered");
		}
		else{
			$(this).siblings("div").show(100);
			$(this).parent().addClass("hovered");
			$(".current > div").addClass("hovered");
		}
	});

/* to collapse-expand menus */
	$(".toggleMenus").click(function(){
		$("#mainmenu > li > a").children("span").toggle();
		$("#menu").toggleClass("hideMenu");
		$(".expandmenu").toggle();
		$(".collapsemenu").toggle();

		var $showMenu = $(".collapsemenu").is(":visible");
		$.get("index.php?showMenu="+$showMenu,function(data,status){});
	});
/* for small screen devices */
	$(window).resize(function(){
		if ($(window).width() <= 480) {
			$("#mainmenu > li > a").children("span").hide();
			$("#menu").addClass("hideMenu");
			$(".expandmenu").show();
			$(".collapsemenu").hide();
		}
 	});

});

/*!
	Colorbox v1.4.33 - 2013-10-31
	jQuery lightbox and modal window plugin
	(c) 2013 Jack Moore - http://www.jacklmoore.com/colorbox
	license: http://www.opensource.org/licenses/mit-license.php
*/
(function(e,t,i){function o(i,o,n){var r=t.createElement(i);return o&&(r.id=Z+o),n&&(r.style.cssText=n),e(r)}function n(){return i.innerHeight?i.innerHeight:e(i).height()}function r(e){var t=k.length,i=(z+e)%t;return 0>i?t+i:i}function h(e,t){return Math.round((/%/.test(e)?("x"===t?E.width():n())/100:1)*parseInt(e,10))}function l(e,t){return e.photo||e.photoRegex.test(t)}function s(e,t){return e.retinaUrl&&i.devicePixelRatio>1?t.replace(e.photoRegex,e.retinaSuffix):t}function a(e){"contains"in g[0]&&!g[0].contains(e.target)&&(e.stopPropagation(),g.focus())}function d(){var t,i=e.data(N,Y);null==i?(B=e.extend({},X),console&&console.log&&console.log("Error: cboxElement missing settings object")):B=e.extend({},i);for(t in B)e.isFunction(B[t])&&"on"!==t.slice(0,2)&&(B[t]=B[t].call(N));B.rel=B.rel||N.rel||e(N).data("rel")||"nofollow",B.href=B.href||e(N).attr("href"),B.title=B.title||N.title,"string"==typeof B.href&&(B.href=e.trim(B.href))}function c(i,o){e(t).trigger(i),lt.triggerHandler(i),e.isFunction(o)&&o.call(N)}function u(i){q||(N=i,d(),k=e(N),z=0,"nofollow"!==B.rel&&(k=e("."+et).filter(function(){var t,i=e.data(this,Y);return i&&(t=e(this).data("rel")||i.rel||this.rel),t===B.rel}),z=k.index(N),-1===z&&(k=k.add(N),z=k.length-1)),w.css({opacity:parseFloat(B.opacity),cursor:B.overlayClose?"pointer":"auto",visibility:"visible"}).show(),J&&g.add(w).removeClass(J),B.className&&g.add(w).addClass(B.className),J=B.className,B.closeButton?K.html(B.close).appendTo(y):K.appendTo("<div/>"),U||(U=$=!0,g.css({visibility:"hidden",display:"block"}),H=o(st,"LoadedContent","width:0; height:0; overflow:hidden"),y.css({width:"",height:""}).append(H),O=x.height()+C.height()+y.outerHeight(!0)-y.height(),_=b.width()+T.width()+y.outerWidth(!0)-y.width(),D=H.outerHeight(!0),A=H.outerWidth(!0),B.w=h(B.initialWidth,"x"),B.h=h(B.initialHeight,"y"),H.css({width:"",height:B.h}),Q.position(),c(tt,B.onOpen),P.add(L).hide(),g.focus(),B.trapFocus&&t.addEventListener&&(t.addEventListener("focus",a,!0),lt.one(rt,function(){t.removeEventListener("focus",a,!0)})),B.returnFocus&&lt.one(rt,function(){e(N).focus()})),m())}function f(){!g&&t.body&&(V=!1,E=e(i),g=o(st).attr({id:Y,"class":e.support.opacity===!1?Z+"IE":"",role:"dialog",tabindex:"-1"}).hide(),w=o(st,"Overlay").hide(),F=e([o(st,"LoadingOverlay")[0],o(st,"LoadingGraphic")[0]]),v=o(st,"Wrapper"),y=o(st,"Content").append(L=o(st,"Title"),S=o(st,"Current"),I=e('<button type="button"/>').attr({id:Z+"Previous"}),R=e('<button type="button"/>').attr({id:Z+"Next"}),M=o("button","Slideshow"),F),K=e('<button type="button"/>').attr({id:Z+"Close"}),v.append(o(st).append(o(st,"TopLeft"),x=o(st,"TopCenter"),o(st,"TopRight")),o(st,!1,"clear:left").append(b=o(st,"MiddleLeft"),y,T=o(st,"MiddleRight")),o(st,!1,"clear:left").append(o(st,"BottomLeft"),C=o(st,"BottomCenter"),o(st,"BottomRight"))).find("div div").css({"float":"left"}),W=o(st,!1,"position:absolute; width:9999px; visibility:hidden; display:none; max-width:none;"),P=R.add(I).add(S).add(M),e(t.body).append(w,g.append(v,W)))}function p(){function i(e){e.which>1||e.shiftKey||e.altKey||e.metaKey||e.ctrlKey||(e.preventDefault(),u(this))}return g?(V||(V=!0,R.click(function(){Q.next()}),I.click(function(){Q.prev()}),K.click(function(){Q.close()}),w.click(function(){B.overlayClose&&Q.close()}),e(t).bind("keydown."+Z,function(e){var t=e.keyCode;U&&B.escKey&&27===t&&(e.preventDefault(),Q.close()),U&&B.arrowKey&&k[1]&&!e.altKey&&(37===t?(e.preventDefault(),I.click()):39===t&&(e.preventDefault(),R.click()))}),e.isFunction(e.fn.on)?e(t).on("click."+Z,"."+et,i):e("."+et).live("click."+Z,i)),!0):!1}function m(){var n,r,a,u=Q.prep,f=++at;$=!0,j=!1,N=k[z],d(),c(ht),c(it,B.onLoad),B.h=B.height?h(B.height,"y")-D-O:B.innerHeight&&h(B.innerHeight,"y"),B.w=B.width?h(B.width,"x")-A-_:B.innerWidth&&h(B.innerWidth,"x"),B.mw=B.w,B.mh=B.h,B.maxWidth&&(B.mw=h(B.maxWidth,"x")-A-_,B.mw=B.w&&B.w<B.mw?B.w:B.mw),B.maxHeight&&(B.mh=h(B.maxHeight,"y")-D-O,B.mh=B.h&&B.h<B.mh?B.h:B.mh),n=B.href,G=setTimeout(function(){F.show()},100),B.inline?(a=o(st).hide().insertBefore(e(n)[0]),lt.one(ht,function(){a.replaceWith(H.children())}),u(e(n))):B.iframe?u(" "):B.html?u(B.html):l(B,n)?(n=s(B,n),j=t.createElement("img"),e(j).addClass(Z+"Photo").bind("error",function(){B.title=!1,u(o(st,"Error").html(B.imgError))}).one("load",function(){var t;f===at&&(e.each(["alt","longdesc","aria-describedby"],function(t,i){var o=e(N).attr(i)||e(N).attr("data-"+i);o&&j.setAttribute(i,o)}),B.retinaImage&&i.devicePixelRatio>1&&(j.height=j.height/i.devicePixelRatio,j.width=j.width/i.devicePixelRatio),B.scalePhotos&&(r=function(){j.height-=j.height*t,j.width-=j.width*t},B.mw&&j.width>B.mw&&(t=(j.width-B.mw)/j.width,r()),B.mh&&j.height>B.mh&&(t=(j.height-B.mh)/j.height,r())),B.h&&(j.style.marginTop=Math.max(B.mh-j.height,0)/2+"px"),k[1]&&(B.loop||k[z+1])&&(j.style.cursor="pointer",j.onclick=function(){Q.next()}),j.style.width=j.width+"px",j.style.height=j.height+"px",setTimeout(function(){u(j)},1))}),setTimeout(function(){j.src=n},1)):n&&W.load(n,B.data,function(t,i){f===at&&u("error"===i?o(st,"Error").html(B.xhrError):e(this).contents())})}var w,g,v,y,x,b,T,C,k,E,H,W,F,L,S,M,R,I,K,P,B,O,_,D,A,N,z,j,U,$,q,G,Q,J,V,X={html:!1,photo:!1,iframe:!1,inline:!1,transition:"elastic",speed:300,fadeOut:300,width:!1,initialWidth:"600",innerWidth:!1,maxWidth:!1,height:!1,initialHeight:"450",innerHeight:!1,maxHeight:!1,scalePhotos:!0,scrolling:!0,href:!1,title:!1,rel:!1,opacity:.9,preloading:!0,className:!1,overlayClose:!0,escKey:!0,arrowKey:!0,top:!1,bottom:!1,left:!1,right:!1,fixed:!1,data:void 0,closeButton:!0,fastIframe:!0,open:!1,reposition:!0,loop:!0,slideshow:!1,slideshowAuto:!0,slideshowSpeed:2500,slideshowStart:"start slideshow",slideshowStop:"stop slideshow",photoRegex:/\.(gif|png|jp(e|g|eg)|bmp|ico|webp)((#|\?).*)?$/i,retinaImage:!1,retinaUrl:!1,retinaSuffix:"@2x.$1",current:"image {current} of {total}",previous:"previous",next:"next",close:"close",xhrError:"This content failed to load.",imgError:"This image failed to load.",returnFocus:!0,trapFocus:!0,onOpen:!1,onLoad:!1,onComplete:!1,onCleanup:!1,onClosed:!1},Y="colorbox",Z="cbox",et=Z+"Element",tt=Z+"_open",it=Z+"_load",ot=Z+"_complete",nt=Z+"_cleanup",rt=Z+"_closed",ht=Z+"_purge",lt=e("<a/>"),st="div",at=0,dt={},ct=function(){function e(){clearTimeout(h)}function t(){(B.loop||k[z+1])&&(e(),h=setTimeout(Q.next,B.slideshowSpeed))}function i(){M.html(B.slideshowStop).unbind(s).one(s,o),lt.bind(ot,t).bind(it,e),g.removeClass(l+"off").addClass(l+"on")}function o(){e(),lt.unbind(ot,t).unbind(it,e),M.html(B.slideshowStart).unbind(s).one(s,function(){Q.next(),i()}),g.removeClass(l+"on").addClass(l+"off")}function n(){r=!1,M.hide(),e(),lt.unbind(ot,t).unbind(it,e),g.removeClass(l+"off "+l+"on")}var r,h,l=Z+"Slideshow_",s="click."+Z;return function(){r?B.slideshow||(lt.unbind(nt,n),n()):B.slideshow&&k[1]&&(r=!0,lt.one(nt,n),B.slideshowAuto?i():o(),M.show())}}();e.colorbox||(e(f),Q=e.fn[Y]=e[Y]=function(t,i){var o=this;if(t=t||{},f(),p()){if(e.isFunction(o))o=e("<a/>"),t.open=!0;else if(!o[0])return o;i&&(t.onComplete=i),o.each(function(){e.data(this,Y,e.extend({},e.data(this,Y)||X,t))}).addClass(et),(e.isFunction(t.open)&&t.open.call(o)||t.open)&&u(o[0])}return o},Q.position=function(t,i){function o(){x[0].style.width=C[0].style.width=y[0].style.width=parseInt(g[0].style.width,10)-_+"px",y[0].style.height=b[0].style.height=T[0].style.height=parseInt(g[0].style.height,10)-O+"px"}var r,l,s,a=0,d=0,c=g.offset();if(E.unbind("resize."+Z),g.css({top:-9e4,left:-9e4}),l=E.scrollTop(),s=E.scrollLeft(),B.fixed?(c.top-=l,c.left-=s,g.css({position:"fixed"})):(a=l,d=s,g.css({position:"absolute"})),d+=B.right!==!1?Math.max(E.width()-B.w-A-_-h(B.right,"x"),0):B.left!==!1?h(B.left,"x"):Math.round(Math.max(E.width()-B.w-A-_,0)/2),a+=B.bottom!==!1?Math.max(n()-B.h-D-O-h(B.bottom,"y"),0):B.top!==!1?h(B.top,"y"):Math.round(Math.max(n()-B.h-D-O,0)/2),g.css({top:c.top,left:c.left,visibility:"visible"}),v[0].style.width=v[0].style.height="9999px",r={width:B.w+A+_,height:B.h+D+O,top:a,left:d},t){var u=0;e.each(r,function(e){return r[e]!==dt[e]?(u=t,void 0):void 0}),t=u}dt=r,t||g.css(r),g.dequeue().animate(r,{duration:t||0,complete:function(){o(),$=!1,v[0].style.width=B.w+A+_+"px",v[0].style.height=B.h+D+O+"px",B.reposition&&setTimeout(function(){E.bind("resize."+Z,Q.position)},1),i&&i()},step:o})},Q.resize=function(e){var t;U&&(e=e||{},e.width&&(B.w=h(e.width,"x")-A-_),e.innerWidth&&(B.w=h(e.innerWidth,"x")),H.css({width:B.w}),e.height&&(B.h=h(e.height,"y")-D-O),e.innerHeight&&(B.h=h(e.innerHeight,"y")),e.innerHeight||e.height||(t=H.scrollTop(),H.css({height:"auto"}),B.h=H.height()),H.css({height:B.h}),t&&H.scrollTop(t),Q.position("none"===B.transition?0:B.speed))},Q.prep=function(i){function n(){return B.w=B.w||H.width(),B.w=B.mw&&B.mw<B.w?B.mw:B.w,B.w}function h(){return B.h=B.h||H.height(),B.h=B.mh&&B.mh<B.h?B.mh:B.h,B.h}if(U){var a,d="none"===B.transition?0:B.speed;H.empty().remove(),H=o(st,"LoadedContent").append(i),H.hide().appendTo(W.show()).css({width:n(),overflow:B.scrolling?"auto":"hidden"}).css({height:h()}).prependTo(y),W.hide(),e(j).css({"float":"none"}),a=function(){function i(){e.support.opacity===!1&&g[0].style.removeAttribute("filter")}var n,h,a=k.length,u="frameBorder",f="allowTransparency";U&&(h=function(){clearTimeout(G),F.hide(),c(ot,B.onComplete)},L.html(B.title).add(H).show(),a>1?("string"==typeof B.current&&S.html(B.current.replace("{current}",z+1).replace("{total}",a)).show(),R[B.loop||a-1>z?"show":"hide"]().html(B.next),I[B.loop||z?"show":"hide"]().html(B.previous),ct(),B.preloading&&e.each([r(-1),r(1)],function(){var i,o,n=k[this],r=e.data(n,Y);r&&r.href?(i=r.href,e.isFunction(i)&&(i=i.call(n))):i=e(n).attr("href"),i&&l(r,i)&&(i=s(r,i),o=t.createElement("img"),o.src=i)})):P.hide(),B.iframe?(n=o("iframe")[0],u in n&&(n[u]=0),f in n&&(n[f]="true"),B.scrolling||(n.scrolling="no"),e(n).attr({src:B.href,name:(new Date).getTime(),"class":Z+"Iframe",allowFullScreen:!0,webkitAllowFullScreen:!0,mozallowfullscreen:!0}).one("load",h).appendTo(H),lt.one(ht,function(){n.src="//about:blank"}),B.fastIframe&&e(n).trigger("load")):h(),"fade"===B.transition?g.fadeTo(d,1,i):i())},"fade"===B.transition?g.fadeTo(d,0,function(){Q.position(0,a)}):Q.position(d,a)}},Q.next=function(){!$&&k[1]&&(B.loop||k[z+1])&&(z=r(1),u(k[z]))},Q.prev=function(){!$&&k[1]&&(B.loop||z)&&(z=r(-1),u(k[z]))},Q.close=function(){U&&!q&&(q=!0,U=!1,c(nt,B.onCleanup),E.unbind("."+Z),w.fadeTo(B.fadeOut||0,0),g.stop().fadeTo(B.fadeOut||0,0,function(){g.add(w).css({opacity:1,cursor:"auto"}).hide(),c(ht),H.empty().remove(),setTimeout(function(){q=!1,c(rt,B.onClosed)},1)}))},Q.remove=function(){g&&(g.stop(),e.colorbox.close(),g.stop().remove(),w.remove(),q=!1,g=null,e("."+et).removeData(Y).removeClass(et),e(t).unbind("click."+Z))},Q.element=function(){return e(N)},Q.settings=X)})(jQuery,document,window);
